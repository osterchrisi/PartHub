//TODO: Wrap these two in functions and call them in the document ready of inventory.php
// Focus fields in modals
$(document).ready(function () {
    // Focus the Quantity field in the stock changes modal after showing
    $('#mAddStock').on('shown.bs.modal', function () {
        $('#addStockQuantity').focus();
    });

    // Focus the Part Name field in the part entry modal after showing
    $('#mPartEntry').on('shown.bs.modal', function () {
        $('#addPartName').focus();
    });

    // Prohibit text selection when pressing shift (for selecting multiple rows)
    var table = document.getElementById("parts_table");

    // Shift is pressed
    document.addEventListener("keydown", function (event) {
        if (event.shiftKey) {
            table.classList.add("table-no-select");
        }
    });

    // Shift is released
    document.addEventListener("keyup", function (event) {
        if (!event.shiftKey) {
            table.classList.remove("table-no-select");
        }
    });
});

// Remove a click listener
function removeClickListeners(id) {
    $(id).off('click');
    console.log("Removed old click listener from ", id);
}

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

/**
 * Load the parts-info page and pass the id variable as a parameter
 * upon clicking a row in the parts table
 * @param {int} id The part ID for which to update the stock modal content
 */
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

/**
 * Load the contents of stockModals page, pass the id and replace HTML in modal
 * upon clicking a row in the parts table
 * @param {int} id The part ID for which to update the stock modal content
 */
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

function updateBomInfo(id){
$.ajax({
    url: 'show-bom.php',
    type: 'GET',
    data: { id: id, hideNavbar: true },
    success: function (data) {
      // Replace the content of the info window with the loaded PHP page
      $('#info-window').html(data);
    },
    error: function () {
      // Display an error message if the PHP page failed to load
      $('#info-window').html('Failed to load additional BOM data.');
    }
  });
};

/**
 * Make the table-window and the info-window resizable
 */
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

/**
 * 'Selectize' the category multi select, prepare values and append to the hidden input field
 *
 * @return void
 */
function initializeMultiSelect(id) {
    var $select = $('#' + id).selectize({
        plugins: ["remove_button", "clear_button"]
    });

    $('form').on('submit', function () {
        // Get the selected options from the selectize instance
        var selectedValues = $select[0].selectize.getValue();

        // Prepare values to look like an array
        for (var i = 0; i < selectedValues.length; i++) {
            selectedValues[i] = [selectedValues[i]];
        }
        selectedValues = JSON.stringify(selectedValues);

        // Update the value of the hidden input element
        $('#selected-categories').val(selectedValues);
    });
};

/**
 * Delete selected rows in the database table
 * @param {array} ids Array of IDs to delete
 * @param {string} table_name Name of the table in the database
 * @param {string} column Name of the column that holds the ID, e.g. part_id
 */
function deleteSelectedRows(ids, table_name, column) {
    // Like, delete 'em
    $.ajax({
        type: 'POST',
        url: '../includes/deleteRowInTable.php',
        data: {
            ids: ids,
            table: table_name,
            column: column
        },
        success: function (response) {
            console.log(response);
            console.log('success');
            // Updating table here because otherwise it rebuilds too fast
            var queryString = window.location.search;
            rebuildPartsTable(queryString);
        }
    });

}