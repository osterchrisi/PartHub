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

// Load the parts-info page and pass the id variable as a parameter - upon clicking a row in the parts table
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

// Load the contents of stockModals page, pass the id and replace HTML in modal - upon clicking a row in the parts table
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