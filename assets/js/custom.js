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

// Select element for the category dropdown in parts table
function createCategorySelect(categories, currentValue) {
    var select = $('<select class="form-select-sm">');
    for (var i = 0; i < categories.length; i++) {
        var option = $('<option>').text(categories[i]['category_name']).attr('value', categories[i]['category_id']);
        if (categories[i]['category_name'] === currentValue) {
            option.attr('selected', true);
        }
        select.append(option);
    }
    return select;
}

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

        // * It's a category cell
        if (cell.hasClass('category')) {
            // Get list of available categories and populate dropdown
            categories = $.ajax({
                type: 'GET',
                url: '../includes/getCategories.php',
                dataType: 'JSON',
                success: function (response) {
                    categories = response;
                    console.log("categories1: ", categories);

                    // Create select element
                    var select = createCategorySelect(categories, currentValue);
                    cell.empty().append(select);
                    select.focus();

                    select.on('change', function () {
                        var new_value = $(this).val(); // Get new selected value
                        // console.log("new value = ", new_value);

                        // Get cell part_id, column name and database table
                        // These are encoded in the table data cells
                        var part_id = cell.closest('td').data('id');
                        var column = 'part_category_fk';
                        var table_name = cell.closest('td').data('table_name');
                        // console.log(part_id, column, table_name, new_value);

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
                            },
                            error: function (xhr, status, error) {
                                console.error(error);
                            }
                        });
                        // Update cell with new value, need to subtract 1 to account for array starting at 0 but categories at 1
                        new_value = categories[new_value-1]['category_name']
                        cell.text(new_value);
                        select.remove();
                        cell.removeClass('editing');
                    });
                }
            });
        }
        else { // * It's a text cell
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
            // Enter new value
            input.blur(function () {
                // Get newly entered value
                var new_value = input.val();

                // Update cell with new value
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
        }
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
// $(function () {
//     var $table = $('#parts_table');
//     var $header = $table.find('thead tr');
//     var $columnsDropdown = $('<ul>').addClass('dropdown-menu');

//     // Create dropdown list with checkboxes for each column
//     $header.find('th').each(function (index, column) {
//         var $checkbox = $('<input>').attr({
//             type: 'checkbox',
//             id: 'column-' + index,
//             checked: !$table.bootstrapTable('getVisibleColumns', $(column).data('field'))
//         });

//         // console.log("Visible columns: ", $table.bootstrapTable('getVisibleColumns'));

//         var $label = $('<label>').attr('for', 'column-' + index).text($(column).text());

//         var $item = $('<li>').addClass('dropdown-item').append($checkbox).append($label);
//         $columnsDropdown.append($item);

//         // Add click event listener to toggle column visibility
//         $checkbox.on('click', function () {
//             var fieldName = $(column).data('field');
//             console.log("field = ", fieldName);
//             var visible = !$table.bootstrapTable('getVisibleColumns', fieldName);
//             $table.bootstrapTable('toggleColumn', fieldName, visible);
//         });
//     });

//     // Add right-click event listener to header row
//     $header.on('contextmenu', function (event) {
//         event.preventDefault();
//         $columnsDropdown.appendTo($('body')).show();
//         $columnsDropdown.css({
//             position: 'absolute',
//             left: event.pageX + 'px',
//             top: event.pageY + 'px'
//         });
//         $(document).one('click', function () {
//             $columnsDropdown.hide();
//         });
//     });
// });

$(document).ready(function() {
    $('#continueDemo').click(function() {
      $.post('/PartHub/includes/demo.php', {myVariable: 'myValue'}, function(response) {
        console.log(response);
        window.location.href = "/PartHub/index.php?login";
      });
    });
  });