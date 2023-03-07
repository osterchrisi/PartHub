<script>

$(document).ready(function() {
  $('tbody td.editable').click(function() {
    var cell = $(this);

    // Check if the cell is being edited
    if (cell.hasClass('editing')) {
      return;
    }
    
    // Add editing class to the cell
    cell.addClass('editing');
    
    // Get current value
    var currentValue = cell.text();

    // Create input field
    var input = $('<textarea class="form-control">').val(currentValue);
    cell.empty().append(input);
    input.focus();

    var label = $('<small id="passwordHelpInline" class="text-muted">Enter: Confirm</small>');
    cell.append(label);

    

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
        cell.removeClass('editing');
        return;
      }
    });

    // Close input on click outside the cell
    $(document).on('mousedown', function(event) {
      if (!$(event.target).closest(cell).length) {
        input.remove();
        cell.text(currentValue);
        cell.removeClass('editing');
        event.stopPropagation();
        return;
      }
    });

    input.blur(function() {
      // Get newly entered value
      var new_value = input.val();

      // This updates the table with the new value. Better would be an SQL query to not fool anyone.
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
      cell.removeClass('editing');
    });
  });
});
</script>