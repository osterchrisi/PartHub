// Custom Sorter for my stock URLs
function NumberURLSorter(a, b) {
    // Remove the href tag and return only the string values
    // Otherwise cells get sorted by the URL which contains part_id
    return $(a).text() - $(b).text();
};

// Bootstrap-Table
$(function bootstrapTable() {
    $('#parts_table').bootstrapTable({
    });
});

// Inline table cell manipulation of parts_table
$(document).ready(function inlineProcessing() {
    $('#parts_table').on('dbl-click-cell.bs.table', function (e, field, value, row, $element) {
        var cell = $element;

        // Check if the cell is already being edited
        if (cell.hasClass('editing')) {
            return;
        }

        // Add editing class to the cell
        cell.addClass('editing');

        // Get current value
        var currentValue = $element.text();

        // Create input field
        var input = $('<textarea class="form-control">').val(currentValue);
        cell.empty().append(input);
        input.focus();

        var label = $('<small class="text-muted">Enter: Confirm</small>');
        cell.append(label);

        // Confirm upon pressing Enter key
        input.keypress(function (event) {
            if (event.keyCode === 13) {
                input.blur();
            }
        });

        // Close input on "Escape" key press
        input.on('keydown', function (event) {
            if (event.key === "Escape") {
                input.remove();
                cell.text(currentValue);
                cell.removeClass('editing');
                return;
            }
        });

        // Close input on click outside the cell
        $(document).on('mousedown', function (event) {
            if (!$(event.target).closest(cell).length) {
                input.remove();
                cell.text(currentValue);
                cell.removeClass('editing');
                event.stopPropagation();
                return;
            }
        });

        // Enter new value
        input.blur(function () {
            // Get newly entered value
            var new_value = input.val();

            // TODO This updates the HTML cell with the new value.
            // TODO Better would be an SQL query to not fool anyone.
            cell.text(new_value);

            // Get cell part_id, column name and database table
            // These are encoded in the table data cells
            var part_id = cell.closest('td').data('id');
            var column = cell.closest('td').data('column');
            var table_name = cell.closest('td').data('table_name')
            console.log(part_id, column, table_name, new_value);

            // Call the updating function
            $.ajax({
                type: 'GET',
                url: '../includes/update-cell.php',
                data: {
                    part_id: part_id,
                    column: column,
                    table_name: table_name,
                    new_value: new_value
                },
                success: function (data) {
                    console.log('Data updated successfully');
                },
                error: function (xhr, status, error) {
                    console.error('Error updating data');
                }
            });
            cell.removeClass('editing');
        });
    });
});

// Send form upon changing the results per page dropdown
$(function sendFormOnDropdownChange() {
    var dropdown = document.getElementById("resultspp");

    dropdown.addEventListener("change", function () {
        var form = document.getElementById("search_form");
        form.submit();
    });
});

// ! Get right-click column-visibility menu for parts_table
// ! Does NOT work yet, implemented with bootstrap-table for now
$(function() {
    var $table = $('#parts_table');
    var $header = $table.find('thead tr');
    var $columnsDropdown = $('<ul>').addClass('dropdown-menu');
  
    // Create dropdown list with checkboxes for each column
    $header.find('th').each(function(index, column) {
      var $checkbox = $('<input>').attr({
        type: 'checkbox',
        id: 'column-' + index,
        checked: !$table.bootstrapTable('getColumnVisible', $(column).data('field'))
      });
  
      var $label = $('<label>').attr('for', 'column-' + index).text($(column).text());
  
      var $item = $('<li>').addClass('dropdown-item').append($checkbox).append($label);
      $columnsDropdown.append($item);
  
      // Add click event listener to toggle column visibility
      $checkbox.on('click', function() {
        var fieldName = $(column).data('field');
        var visible = !$table.bootstrapTable('getColumnVisible', fieldName);
        $table.bootstrapTable('toggleColumn', fieldName, visible);
      });
    });
  
    // Add right-click event listener to header row
    $header.on('contextmenu', function(event) {
      event.preventDefault();
      $columnsDropdown.appendTo($('body')).show();
      $columnsDropdown.css({
        position: 'absolute',
        left: event.pageX + 'px',
        top: event.pageY + 'px'
      });
      $(document).one('click', function() {
        $columnsDropdown.hide();
      });
    });
  });
  