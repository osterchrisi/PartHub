<script>
$(document).ready(function() {
  $('tbody td.editable').click(function() {
    var cell = $(this);
    var currentValue = cell.text();
    var input = $('<input type="number">').val(currentValue);
    cell.empty().append(input);
    input.focus();
    input.blur(function() {
      var newValue = input.val();
      cell.text(newValue);
      var id = cell.closest('tr').data('id');
      $.ajax({
        type: 'POST',
        url: 'update.php',
        data: {
          id: id,
          number: newValue
        },
        success: function(data) {
          console.log('Data updated successfully');
        },
        error: function(xhr, status, error) {
          console.error('Error updating data');
        }
      });
    });
  });
});
</script>

<?php
echo "awesome";
?>