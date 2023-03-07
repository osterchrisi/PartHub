<script>

$(document).ready(function() {
  $('tbody td.editable').click(function() {
    console.log();
    // Get current value
    var cell = $(this);
    var currentValue = cell.text();

    // Create input field
    var input = $('<input type="text" class="form-control">').val(currentValue);
    cell.empty().append(input);
    input.focus();

    // Enter upon pressing Enter key
    input.keypress(function(event) {
        if (event.keyCode === 13) {
            input.blur();
        }
    }); 

    // Close input on "Escape" key press
    input.on('keydown', function(event) {
      if (event.key === "Escape") {
        input.remove();
        cell.text(currentValue);
        return;
      }
    });


    input.blur(function() {
      // Get newly entered value
      var new_value = input.val();

      // This updates the table with the new value. Better would be an SQL query to not fool anyone
      cell.text(new_value);
      
      // Get cell part_id, column name and database table
      var part_id = cell.closest('td').data('id');
      var column = cell.closest('td').data('column');
      var table_name = cell.closest('td').data('table_name')
    //   console.log(part_id, column, table_name, new_value);

      $.ajax({
        type: 'GET',
        url: 'update-cell.php',
        data: {
          part_id: part_id,
          column: column,
          table_name: table_name,
          new_value: new_value
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