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

            // This updates the HTML cell with the new value. Better would be an SQL query to not fool anyone.
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