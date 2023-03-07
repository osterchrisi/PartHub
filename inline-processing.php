<script>
// Find row x column position of clicked cell in table
$(document).ready(function() {
  $('td').click(function(event) {
    var clickedCell = $(event.target).closest('td'); // get the closest <td> element to the clicked element
    var columnNumber = clickedCell.index() + 1; // get the 1-based index of the column
    var rowNumber = clickedCell.closest('tr').index() + 1; // get the 1-based index of the row
    console.log('Clicked cell at row ' + rowNumber + ', column ' + columnNumber);
  });
});

$(document).ready(function() {
  $('tbody td.editable').click(function() {
    console.log();
    // Get current value
    var cell = $(this);
    var currentValue = cell.text();
    console.log(currentValue);

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
        input.blur();
      }
    });


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

<!-- Another version with ajax (not tested yet)
<scrip>
$(document).ready(function() {
  $('tbody tr').click(function() {
    var id = $(this).data('id');
    var number = $(this).find('.editable').text();
    var formHtml = '<form><input type="number" name="number" value="' + number + '"> ';
    formHtml += '<input type="hidden" name="id" value="' + id + '"> ';
    formHtml += '<input type="submit" value="Save"></form>';

    // Replace the current cell contents with the form
    $(this).find('.editable').html(formHtml);
  });

  $('tbody').on('submit', 'form', function(e) {
    e.preventDefault();

    // Get the form data
    var formData = $(this).serialize();

    // Submit the form data via AJAX to update the database
    $.ajax({
      type: 'POST',
      url: 'update_number.php',
      data: formData,
      success: function(response) {
        // Replace the form with the new number
        var newNumber = $(response).find('.editable').html();
        $(this).parent().html(newNumber);
      }
    });
  });
});
</script> -->

<?php
echo "awesome";
?>