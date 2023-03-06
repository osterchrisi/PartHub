<?php
require_once('head.html');
include 'config/credentials.php';
include 'lib/SQL.php';
include 'lib/forms.php';

// Connect to database and get available customers and products
try {
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    $customers = getBackordersCustomers($conn);
    $products = getBackordersProducts($conn);
}
catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}
?>

<div class = "container-fluid">
<?php require_once('navbar.php');?>
<br>

<h4>Enter new backorder</h4>
    <form action="backorders-processing.php" method="post" onsubmit="return checkCustomerPO()">
    <div class="row">
        <div class="col-3">
            <?php
                try {generateBackordersCustomersDropdown($conn);
                }
                catch (Exception $e) {
                    echo "<br>Error: " . $e->getMessage();
                }
            ?>
            <br>
            <input type="text" class="form-control" id="customer_po" name="customer_po" placeholder="Customer PO Number">
            <br>
            <button type="button" class="btn btn-primary" onclick='addFields(<?php echo json_encode($products); ?>)'>Add Products</button>
            <br><br>
            <br>
            <button type="submit" name="submit" class="btn btn-secondary">Submit</button>
        </div>
        <div id="dynamicAddProducts" class="col-6">
            <!-- Added products go here -->
        </div>
    </div>     
    </form>

</div>


<script>
    console.log();
    function addFields(products) {
        console.log();

        var container = document.getElementById("dynamicAddProducts");

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

        // Create options for the select element using the passed products array/JSON
        for (var i = 0; i < products.length; i++) {
            var option = document.createElement("option");
            option.value = products[i]['id'];
            option.text = products[i]['product_name'];
            select.appendChild(option);
        }
        
        // Create input field for entering the amount of products
        var amount = document.createElement("input");
        amount.type = "text";
        amount.name = "dynamic_field[]";
        amount.className = "form-control";
        amount.placeholder = "Pcs";

        // Allow only numbers to be typed in amount field
        amount.addEventListener("keypress", function(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                evt.preventDefault();
            }
        })

        // Allow only numbers to be pasted into amount field
        amount.addEventListener("paste", function(evt) {
            evt.preventDefault();
            var pastedData = evt.clipboardData.getData('text/plain').replace(/[^0-9]/g, '');
            document.execCommand('insertText', false, pastedData);
        });

        // Create a new button to remove this row
        var removeBtn = document.createElement("button");
        removeBtn.textContent = "X";
        removeBtn.className ="btn btn-primary";

        // Remove row functionality
        removeBtn.addEventListener("click", function() {
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

    function checkCustomerPO() {
    var customer_po = document.getElementById("customer_po").value;
    if (customer_po === "") {
        alert("Customer PO must be filled out");
        return false;
    }
    return true;
    }
</script>

<?php
    // Send data to server
    $customer_id = $_POST['customer_id'];
    $customer_po = $_POST['customer_po'];
    $dynamicFields = $_POST['dynamic_field'];
?>
