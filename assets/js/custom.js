// Send form upon changing the results per page dropdown
$(function sendFormOnDropdownChange() {
    var dropdown = document.getElementById("resultspp");

    dropdown.addEventListener("change", function () {
        var form = document.getElementById("search_form");
        form.submit();
    });
});

// ClickListener for "Continue as demo user" button
$(document).ready(function () {
    $('#continueDemo').click(function () {
        $.post('/PartHub/includes/demo.php', { myVariable: 'myValue' }, function (response) {
            console.log(response);
            window.location.href = "/PartHub/index.php?login";
        });
    });
});

// Load the parts-info page and pass the id variable as a parameter
function updatePartsInfo(id) {
    $.ajax({
        url: 'parts-info.php',
        type: 'GET',
        data: { part_id: id, hideNavbar: true },
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
        },
        error: function () {
            // Display an error message if the PHP page failed to load
            $('#info-window').html('Failed to load additional part data.');
        }
    });
}

// Load the stockModals page and pass the id variable as a parameter
function updateStockModal(id) {
    $.ajax({
        url: '../includes/stockModals.php',
        type: 'GET',
        data: { part_id: id },
        success: function (data) {
            // Replace the content of the stock modal with the loaded PHP page
            $('#mAddStock').html(data);
        },
        error: function () {
            // Display an error message if the PHP page failed to load
            $('#mAddStock').html('Failed to load modal.');
        }
    });
}

// Resizable Divs
$(function () {
    $('#table-window').resizable({
        handles: 'e',
        resize: function () {
            var parentWidth = $('#table-window').parent().width();
            var tableWidth = $('#table-window').width();
            var infoWidth = parentWidth - tableWidth;
            $('#info-window').width(infoWidth);
        }
    });
});

// Filter categories
// $(document).ready(function () {
//     $('#categories-filter').on('input', function () {
//         var filterText = $(this).val().toLowerCase();
//         $('#cat-select option').each(function () {
//             var optionText = $(this).text().toLowerCase();
//             if (optionText.indexOf(filterText) !== -1) {
//                 $(this).show();
//             } else {
//                 $(this).hide();
//             }
//         });
//     });
// });

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