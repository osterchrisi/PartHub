<!-- Stock Modals -->
<html>
<div class="modal fade" id="mAddStock" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Add Stock</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body mx-1">
        Add Stock for
        <?php echo $part_name; ?><br><br>
        <form>
          <input class="form-control stockModalNumber" placeholder="Quantity" id="addStockQuantity"><br>
          <input class="form-control" placeholder="Description / PO" id="addStockDescription">
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="AddStock">Save changes</button>
      </div>
    </div>
  </div>
</div>

</html>