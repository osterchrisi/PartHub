// ClickListener for "Save Changes" button in Add Stock Modal
$(document).ready(function() {
    $('#AddStock').click(function() {
        q = $("#addStockQuantity").val();
        console.log(q);
        d = $("#addStockDescription").val();
        console.log(d);
        //* Okay, this looks strange but works?!
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;
        console.log(id);
      $.post('/PartHub/includes/stockChanges.php', {quant: q, desc: d, user_id: uid, part_id: pid}, function(response) {
        console.log(response);
      });
    });
  });