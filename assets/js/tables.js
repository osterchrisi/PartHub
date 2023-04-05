// BootstrapTable a table
//* I THINK I should convert these back into a document-ready function because sometimes after manipulating the table, it's not responsive anymore
function bootstrapPartsTable() {
    $('#parts_table').bootstrapTable({
    });
};

function bootstrapHistTable() {
    $('#partStockHistoryTable').bootstrapTable({
    });
};

function bootstrapPartInBomsTable() {
    $('#partInBomsTable').bootstrapTable({
    });
};

function bootstrapBomListTable() {
    $('#BomListTable').bootstrapTable({
    });
};

function bootstrapBomDetailsTable() {
    $('#BomDetailsTable').bootstrapTable({
    });
};

// Custom Sorter for my stock URLs
function NumberURLSorter(a, b) {
    // Remove the href tag and return only the string values
    // Otherwise cells get sorted by the URL which contains part_id
    return $(a).text() - $(b).text();
};

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
//TODO: Extract functions
//TODO: Remove dropdown upon clicking out of the box or selecting same option again
$(document).ready(function inlineProcessing() {
    $('.bootstrap-table').on('dbl-click-cell.bs.table', function (e, field, value, row, $element) {
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
                    select.selectize();
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
                        new_value = categories[new_value - 1]['category_name']
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

//* Not using any of the code below this point, it's for appending a part row. Maybe useful later...
//* You need this button for it: 
//* <button class="btn btn-primary" name="AddNew" id="AddNew" type="button">New Entry</button>
// Click listener for the New Entry button
$(document).ready(function () {
    $('#AddNew').click(function () {
        console.log("New Entry button has been buttoned");
        $.ajax({
            type: "POST",
            url: "../includes/create-part.php",
            dataType: "json",
            success: function (response) {
                console.log("Succes");
                var newId = response.id;
                console.log("new parts id: ", newId);
                createNewRow(newId);
            }
        });
    });
});

// Prepend new row to parts table
function createNewRow(part_id) {
    var $table = $('#parts_table');
    var newRowHtml = '<tr data-id="'+part_id+'">' +
        '<td><input type="text" class="form-control" name="name" value="" required></td>' +
        '<td><input type="text" class="form-control" name="email" value=""></td>' +
        '<td><input type="text" class="form-control" name="phone" value=""></td>' +
        '<td><input type="text" class="form-control" name="phone" value=""></td>' +
        '<td><input type="text" class="form-control" name="phone" value=""></td>' +
        '<td><input type="text" class="form-control" name="phone" value=""></td>' +
        '<td><input type="text" class="form-control" name="phone" value=""></td>' +
        '<td><button class="btn btn-sm btn-success save-new-row">OK</button><button class="btn btn-sm btn-danger cancel-new-row">Cncl</button></td>' +
        '</tr>';
    $table.prepend(newRowHtml);
}

// Placeholder function for inserting new row into DB
$('.save-new-row').click(function () {
    var name = $(this).closest('tr').find('input[name="name"]').val();
    var email = $(this).closest('tr').find('input[name="email"]').val();
    var phone = $(this).closest('tr').find('input[name="phone"]').val();
    var data = {
        'name': name,
        'email': email,
        'phone': phone
    };
    $.ajax({
        url: 'insert.php',
        type: 'POST',
        data: data,
        success: function (response) {
            // Refresh the table
            $('#parts_table').bootstrapTable('refresh');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
});