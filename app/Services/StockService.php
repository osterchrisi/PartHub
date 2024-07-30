<?php

namespace App\Services;

use App\Models\StockLevel;
use App\Models\StockLevelHistory;
use App\Models\BomRun;
use App\Events\StockMovementOccured;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Takes an array of stock level entries and returns the total
     */
    public function calculateTotalStock($stockLevels)
    {
        $total_stock = 0;

        foreach ($stockLevels as $stockLevel) {
            $total_stock += $stockLevel['stock_level_quantity'];
        }

        return $total_stock;
    }

    public function updateOrCreateStockLevel($part_id, $quantity, $location_id)
    {
        return StockLevel::updateOrCreateStockLevelRecord($part_id, $quantity, $location_id);
    }

    public function createStockLevelHistory($part_id, $from_location, $to_location, $quantity, $comment, $user_id)
    {
        return StockLevelHistory::createStockLevelHistoryRecord($part_id, $from_location, $to_location, $quantity, $comment, $user_id);
    }

    public function handleStockMovement($part_id, $quantity, $location_id, $event_user)
    {
        $stock_level = [$part_id, $quantity, $location_id];
        event(new StockMovementOccured($stock_level, $event_user));
    }

    /**
     * Process stock changes once they've been approved or no approval was necessary
     */
    public function processApprovedChanges($changes)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $result = [];
        $processed_boms = [];

        foreach ($changes as $approved_change) {
            $part_id = $approved_change['part_id'];
            $bom_id = $approved_change['bom_id'];
            $change = $approved_change['change'];
            $quantity = $approved_change['quantity'];
            $to_quantity = $approved_change['to_quantity'] ?? null;
            $from_quantity = $approved_change['from_quantity'] ?? null;
            $new_quantity = $approved_change['new_quantity'] ?? null;
            $to_location = $approved_change['to_location'];
            $from_location = $approved_change['from_location'];
            $comment = $approved_change['comment'];

            // Add Stock
            if ($change == 1) {
                $stock_level_id = $this->updateOrCreateStockLevel($part_id, $new_quantity, $to_location);
                $this->handleStockMovement($part_id, $new_quantity, $to_location, $user);
            }
            // Reduce Stock
            elseif ($change == -1) {
                $stock_level_id = $this->updateOrCreateStockLevel($part_id, $new_quantity, $from_location);
                $this->handleStockMovement($part_id, $new_quantity, $from_location, $user);
            }
            // Move Stock
            elseif ($change == 0) {
                $stock_level_id = $this->updateOrCreateStockLevel($part_id, $to_quantity, $to_location);
                $this->handleStockMovement($part_id, $to_quantity, $to_location, $user);
                $stock_level_id = $this->updateOrCreateStockLevel($part_id, $from_quantity, $from_location);
                $this->handleStockMovement($part_id, $from_quantity, $from_location, $user);
            }

            $hist_id = $this->createStockLevelHistory($part_id, $from_location, $to_location, $quantity, $comment, $user_id);
            $stock = StockLevel::getStockLevelsByPartID($part_id);
            $total_stock = $this->calculateTotalStock($stock);
            $result[] = ['hist_id' => $hist_id, 'stock_level_id' => $stock_level_id, 'new_total_stock' => $total_stock];

            // If stock changes came from BOM changes, prepare array of BOM ID and assemble quantity
            //! Bit of a hick-hack right now - could be written better?
            if ($bom_id != null) {
                $processed_boms[] = [
                    'bom_id' => $bom_id,
                    'assemble_quantity' => $approved_change['assemble_quantity']
                ];
            }
            else {
                $processed_boms = [];
            }
        }

        if (!empty($processed_boms)) {
            $this->processBomRuns($processed_boms, $user_id);
        }

        return [
            'status' => 'success',
            'result' => $result
        ];
    }

    /**
     * Extract BOM IDs and processed quantities for creating Bom Run entries
     * @return void
     */
    private function processBomRuns($processed_boms, $user_id)
    {
        $unique_processed_boms = [];
        $unique_bom_ids = [];

        foreach ($processed_boms as $processed_bom) {
            $bomId = $processed_bom["bom_id"];
            if (!in_array($bomId, $unique_bom_ids)) {
                $unique_processed_boms[] = $processed_bom;
                $unique_bom_ids[] = $bomId;
            }
        }

        //* Create BOM Run entries
        foreach ($unique_processed_boms as $unique_processed_bom) {
            $processed_bom = $unique_processed_bom['bom_id'];
            $quantity = $unique_processed_bom['assemble_quantity'];
            BomRun::createBomRun($processed_bom, $quantity, $user_id);
        }
    }

    public function parseRequestedChangeDetails($requested_change)
    {
        $change = $requested_change['change'];
        $part_id = $requested_change['part_id'];
        $quantity = $requested_change['quantity'];
        $comment = $requested_change['comment'];
        $to_location = $requested_change['to_location'];
        $from_location = $requested_change['from_location'];
        $status = $requested_change['status'] ?? NULL;
        $bom_id = $requested_change['bom_id'] ?? NULL;
        $assemble_quantity = $requested_change['assemble_quantity'] ?? NULL;

        return [
            'change' => $change,
            'bom_id' => $bom_id,
            'assemble_quantity' => $assemble_quantity,
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'from_location' => $from_location,
            'comment' => $comment,
            'status' => $status
        ];
    }

    public function getRelevantStockLevelsForChange($requested_change_details)
    {
        $stock_levels = StockLevel::getStockLevelsByPartID($requested_change_details['part_id']);
        $current_stock_level_to = StockLevel::getStockInLocation($stock_levels, $requested_change_details['to_location']);
        $current_stock_level_from = StockLevel::getStockInLocation($stock_levels, $requested_change_details['from_location']);

        return [
            'current_stock_level_to' => $current_stock_level_to,
            'current_stock_level_from' => $current_stock_level_from
        ];
    }

    public function prepareStockChangesArrays($requested_change_details, $requested_change_stock_levels, $negative_stock)
    {
        $changes = $requested_change_details;
        $change = $requested_change_details['change'];

        if ($change == 1) {
            $new_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
            $changes['new_quantity'] = $new_quantity;
            $status = 'gtg';
        }
        elseif ($change == -1) {
            $new_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];
            $changes['new_quantity'] = $new_quantity;

            if ($new_quantity < 0 && $requested_change_details['status'] != 'gtg') {
                $status = 'permission_required';
            }
            else {
                $status = 'gtg';
            }
        }
        elseif ($change == 0) {
            $to_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
            $from_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];

            if ($from_quantity < 0 && $requested_change_details['status'] != 'gtg') {
                $status = 'permission_required';
            }
            else {
                $status = 'gtg';
            }

            $changes['to_quantity'] = $to_quantity;
            $changes['from_quantity'] = $from_quantity;
        }

        $changes['status'] = $status;
        $assemble_quantity = $requested_change_details['assemble_quantity'];
        $changes['assemble_quantity'] = $assemble_quantity;

        $result = ['changes' => $changes];

        if ($status == 'permission_required') {
            $negative_stock = $changes;
            $result['negative_stock'] = $negative_stock;
        }

        return $result;
    }

    public function generateStockShortageResponse($negative_stock, $changes, $change)
    {
        if (!is_null($changes[0]['bom_id'])) {
            $column_names = ['bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity'];
            $nice_columns = ['BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity'];
        }
        else {
            if ($change == 0) {
                $column_names = ['part_id', 'quantity', 'from_location', 'from_quantity'];
            }
            else {
                $column_names = ['part_id', 'quantity', 'from_location', 'new_quantity'];
            }

            $nice_columns = ['Part ID', 'Quantity needed', 'Location', 'Resulting Quantity'];
        }

        $negative_stock_table = \buildHTMLTable($column_names, $nice_columns, $negative_stock);

        return [
            'changes' => $changes,
            'negative_stock' => $negative_stock,
            'negative_stock_table' => $negative_stock_table,
            'status' => 'permission_requested'
        ];
    }
}
