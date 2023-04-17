<?php
// BOM execution page
$basename = basename(__FILE__);
$title = 'Build BOM';
require_once('../includes/head.html');
include '../includes/navbar.php';
include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';

// Connect to database and get avail customers and products
try {
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    $boms = getAllBoms($conn, $user_id);
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}
?>

<div class="container-fluid">
    <?php require_once('../includes/navbar.php'); ?>
    <br>
    <h4>Build BOM</h4>
    <form action="bom-build-processing.php" method="post">
        <div class="row">
            <div class="col-4">
                <div class="row">
                    <div class="col-9">
                        <?php
                        try {
                            generateBomNamesDropdown($conn);
                        } catch (Exception $e) {
                            echo "<br>Error: " . $e->getMessage();
                        }
                        ?>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Qty">
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6" id="dynamicAddBoms">
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <button type="button" class="btn btn-primary" onclick='addFields(<?php echo json_encode($boms); ?>)'>Add
                    more</button>
                <button type="submit" name="submit" class="btn btn-secondary">Submit</button>
            </div>
        </div>
    </form>
</div>


<script>
    console.log();
    function addFields(boms) {
        console.log();

        var container = document.getElementById("dynamicAddBoms");

        // Create a row and three columns for all the elements
        var row = document.createElement("div");
        row.className = "row";
        row.style.marginBottom = "24px";

        var col1 = document.createElement("div");
        col1.className = "col-6";

        var col2 = document.createElement("div");
        col2.className = "col-2";

        var col3 = document.createElement("div");
        col3.className = "col-1";

        // Create a new select (dropdown) element
        var select = document.createElement("select");
        select.name = "dynamic_field[]";
        select.className = "form-select";

        // Create options for the select element using the passed parts array/JSON
        for (var i = 0; i < boms.length; i++) {
            var option = document.createElement("option");
            option.value = boms[i]['bom_id'];
            option.text = boms[i]['bom_name'];
            select.appendChild(option);
        }

        // Create input field for entering the amount of elements
        var amount = document.createElement("input");
        amount.type = "text";
        amount.name = "dynamic_field[]";
        amount.className = "form-control";
        amount.placeholder = "Qty";

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
        removeBtn.className = "btn btn-primary";

        // Remove row functionality
        removeBtn.addEventListener("click", function () {
            container.removeChild(row);
        });

        // Add the select element, text input and remove row button
        col1.appendChild(select);
        col2.appendChild(amount);
        col3.appendChild(removeBtn);

        // Add the row to the container and the columns to the row
        container.appendChild(row);
        row.appendChild(col1);
        row.appendChild(col2);
        row.appendChild(col3);
    }

    function checkBomName() {
        var bom_name = document.getElementById("bom_name").value;
        if (bom_name === "") {
            alert("BOM name must be filled out");
            return false;
        }
        return true;
    }
</script>

<?php
// Send data to server
$bom_name = $_POST['bom_name'];
$dynamicFields = $_POST['dynamic_field'];
?>