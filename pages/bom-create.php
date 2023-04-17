<?php
// BOM creation page
$basename = basename(__FILE__);
$title = 'Create BOM';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';

// Connect to database and get available parts
try {
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    $parts = getAllParts($conn, $user_id);
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}
?>

<div class="container-fluid">
<?php
  // Check if called within the info window
  if (isset($_GET['hideNavbar']) && $_GET['hideNavbar'] == 'true') {
    // Don't include the navbar
  }
  else {
    require_once('../includes/navbar.php');
  } ?>
    <br>
    <h4>Create new BOM</h4>
    <form action="../includes/bom-processing.php" method="post" onsubmit="return checkBomName()">
        <div class="row">
            <div class="col-3">
                <input type="text" class="form-control" id="bom_name" name="bom_name" placeholder="Enter BOM title">
                <br>
                <button type="button" class="btn btn-primary"
                    onclick='addFields(<?php echo json_encode($parts); ?>)'>Add Parts</button>
                <br><br>
                <br>
                <button type="submit" name="submit" class="btn btn-secondary">Submit</button>
            </div>
            <div class="col-6" id="dynamicAddParts">
                <!-- Added Parts go here -->
            </div>
        </div>
    </form>
</div>


<script>
    console.log();
    // Using this variable to give each select element a unique ID for selectize-ing it
    let n = 0;
    function addFields(parts) {
        n++;
        var container = document.getElementById("dynamicAddParts");

        // Create a row and three columns for all the elements
        var row = document.createElement("div");
        row.className = "row";
        row.style.marginBottom = "24px";

        var col1 = document.createElement("div");
        col1.className = "col-6";

        var col2 = document.createElement("div");
        col2.className = "col-3";

        var col3 = document.createElement("div");
        col3.className = "col-1";

        // Create a new select (dropdown) element
        var select = document.createElement("select");
        select.name = "dynamic_field[]";
        select.className = "form-select";
        select.id = "bom-element-" + n;

        // Create options for the select element using the passed parts array/JSON
        for (var i = 0; i < parts.length; i++) {
            var option = document.createElement("option");
            option.value = parts[i]['part_id'];
            option.text = parts[i]['part_name'];
            select.appendChild(option);
        }

        // Create input field for entering the amount of elements
        var amount = document.createElement("input");
        amount.type = "text";
        amount.name = "dynamic_field[]";
        amount.className = "form-control";
        amount.placeholder = "Pcs";

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

        id = "bom-element-" + n;
        $('#' + id).selectize();
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
echo $dynamicFields;
?>