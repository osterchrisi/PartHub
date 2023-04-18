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

    <!-- BOM Creation Tabs -->
    <ul class="nav nav-tabs" id="bomCreationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="bomUploadCsv" data-bs-toggle="tab" data-bs-target="#bomAddCSV"
                type="button" role="tab">Upload</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="bomCreateManually" data-bs-toggle="tab" data-bs-target="#bomAddManually"
                type="button" role="tab">Manually</button>
        </li>
    </ul>

    <!-- BOM Creation Tabs Content -->
    <div class="tab-content" id="bomCreationTabsContent">
        <!-- Fixed BOM Name and Description fields -->
        <div class="mt-3">

            <div class="row">
                <div class="col">
                    <input class="form-control form-control-sm" id="bom_name" name="bom_name" placeholder="BOM Name" required>
                </div>
                <div class="col">
                    <input class="form-control form-control-sm" id="bom_description" name="bom_description" placeholder="BOM Description" required>
                </div>
            </div>
        </div>

        <!-- Upload CSV Tab -->
        <div class="tab-pane fade show active mt-3" id="bomAddCSV" role="tabpanel" tabindex="0">
            <form action="../includes/import-csv.php" method="post" enctype="multipart/form-data">
                <input class="form-control form-control-sm mb-3" type="file" id="formFile" name="csvFile" accept=".csv">
                <button type="button" class="btn btn-sm btn-primary" id="submitBomUpload" disabled>Upload</button>
            </form>
        </div>

        <!-- Manually add BOM Tab -->
        <div class="tab-pane fade mt-3" id="bomAddManually" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
            <!-- <form action="../includes/bom-processing.php" method="post" onsubmit="return checkBomName()"> -->
                <div class="row">
                    <div class="col-3">
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick='addFields(<?php echo json_encode($parts); ?>)'>Add Parts</button>
                        <br><br>
                        <button class="btn btn-sm btn-primary" onclick='addBomManually()'>Submit</button>
                    </div>
                    <div class="col-9" id="dynamicAddParts">
                        <!-- Added Parts go here -->
                    </div>
                </div>
            <!-- </form> -->
        </div>
    </div>


</div>

<script>
     <?php include '../assets/js/bomCreation.js'; ?>
</script>

<?php
// Send data to server
$bom_name = $_POST['bom_name'];
$dynamicFields = $_POST['dynamic_field'];
echo $dynamicFields;
?>