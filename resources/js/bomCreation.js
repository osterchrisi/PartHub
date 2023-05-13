$(document).ready(function () {
    toogleUploadButton;
    checkBomName;
})
/**
 * Toggles the disabled state of the upload button based on the selected file type. Enables only on CSV files.
 * @return void
 */
function toogleUploadButton() {
    const fileInput = document.getElementById("formFile");

    fileInput.addEventListener("change", () => {
        const button = document.getElementById('submitBomUpload');
        if (fileInput.files.length > 0 && fileInput.files[0].type === "text/csv") {
            button.removeAttribute('disabled');
        }
        else {
            button.disabled = "true";
        }
    });
}

/**
 * Validates if the BOM name input field is filled out
 * @returns {boolean} Returns true if BOM name input field is filled out, false otherwise
 */
function checkBomName() {
    var bom_name = document.getElementById("bom_name").value;
    if (bom_name === "") {
        alert("BOM name must be filled out");
        return false;
    }
    return true;
}

/**
 * Collects all necessary fields from the form to create a BOM and sends an AJAX post request to the server-side 'bom-processing.php' script.
 * This function is called on the 'Submit' button in the 'Manually' add BOM section
 * @returns {void}
 */
function addBomManually() {

    bn = $("#bom_name").val(); // BOM Name
    bd = $("#bom_description").val(); // BOM Description

    // BOM Elements
    var df = {};
    var selects = document.getElementsByName("dynamic_field[]");
    for (var i = 0; i < selects.length; i += 2) {
        var partId = selects[i].value;
        var quantity = selects[i + 1].value;
        df[i] = partId;
        df[i + 1] = quantity;
    }

    // Create BOM on server side
    $.post('/PartHub/includes/bom-processing.php',
        { bom_name: bn, bom_description: bd, dynamic_field: df },
        function (response) {
            // Extract 'BOM ID' from response
            var bomId = JSON.parse(response)["BOM ID"];
            updateBomInfo(bomId);

            // Rebuild BOM list table and select new row
            var queryString = window.location.search;
            $.when(rebuildBomListTable(queryString)).done(function () {
                $('tr[data-id="' + bomId + '"]').addClass('selected selected-last');
            });
        });
}

// Using this variable to give each select element a unique ID for selectize-ing it
let n = 0;
/**
 * Adds a new row of Part dropdown, Quantity field and Remove button to the dynamicAddParts container.
 * 
 * @param {Array} parts - An array of part objects containing the ID and Name of each Part.
 */
function addFields(parts) {
    n++;
    var container = document.getElementById("dynamicAddParts");

    // Create a row and three columns for all the elements
    var row = document.createElement("div");
    row.className = "row mx-0 px-0";
    // row.style.marginBottom = "24px";

    // Part Dropdown
    var col1 = document.createElement("div");
    col1.className = "col-8";

    // Quantity Field
    var col2 = document.createElement("div");
    col2.className = "col-3";

    // Remove Button
    var col3 = document.createElement("div");
    col3.className = "col-1";

    // Create a new select (dropdown) element
    var select = document.createElement("select");
    select.name = "dynamic_field[]";
    select.className = "form-select form-select-sm";
    select.id = "bom-element-" + n;

    // Create options for the select element using the passed parts array/JSON
    for (var i = 0; i < parts.length; i++) {
        var option = document.createElement("option");
        option.value = parts[i]['part_id'];
        option.text = parts[i]['part_name'];
        select.appendChild(option);
    }

    // Create input field for entering the amount of elements
    var amountGroup = document.createElement("div");
    amountGroup.className = "input-group input-group-sm";

    var amount = document.createElement("input");
    amount.type = "text";
    amount.name = "dynamic_field[]";
    amount.className = "form-control form-control-sm";
    amount.placeholder = "Pcs";
    amount.value = "1";

    var amountAddon = document.createElement("span");
    amountAddon.className = "input-group-text";
    amountAddon.textContent = "Pcs";

    amountGroup.appendChild(amount);
    amountGroup.appendChild(amountAddon);


    // Allow only numbers to be typed in amount field
    amount.addEventListener("keypress", function (evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            evt.preventDefault();
        }
    })

    // Allow only numbers to be pasted into amount field
    amount.addEventListener("paste", function (evt) {
        evt.preventDefault();
        var pastedData = evt.clipboardData.getData('text/plain').replace(/[^0-9]/g, '');
        document.execCommand('insertText', false, pastedData);
    });

    // Create a new button to remove this row
    var removeBtn = document.createElement("button");
    removeBtn.textContent = "X";
    removeBtn.className = "btn btn-sm";

    // Remove row functionality
    removeBtn.addEventListener("click", function () {
        container.removeChild(row);
    });

    // Add the select element, text input and remove row button
    col1.appendChild(select);
    col2.appendChild(amountGroup);
    col3.appendChild(removeBtn);

    // Add the row to the container and the columns to the row
    container.insertBefore(row, container.firstChild);
    row.appendChild(col1);
    row.appendChild(col2);
    row.appendChild(col3);

    id = "bom-element-" + n;
    $('#' + id).selectize();
}