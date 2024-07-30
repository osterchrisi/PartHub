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
     *
     * @param [type] $changes
     * @return void
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
            } else {
                $processed_boms = [];
            }
        }

        if (!empty($processed_boms)) {
            $this->processBomRuns($processed_boms, $user_id);
        }

        return $result;
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
}
