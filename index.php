<?php
$basename = basename(__FILE__);
$title = 'Open-source inventory and BOM management';
include 'includes/head.html';
include 'includes/navbar.php'; ?>

<style>
  h1.display-1::after {
    content: 'BETA';
    font-size: 12px;
    vertical-align: top;
  }

  .greeting {
    /* border: 1px solid white; */
    min-height: 90vh;
  }

  .full-height {
    /* border: 1px solid red; */
  }
</style>
<div class="d-flex full-height flex-grow-1 justify-content-center align-items-center">
  <div class="greeting d-flex align-items-center">
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
      <tr>
        <td colspan="3">
          <h1 class="display-1">PartHub</h1><br>
          <h1>Inventory and BOM management</h1><br>
          Start looking at some parts, create a BOM and then execute it. Go ahead and explore!
          <br>The database will reset every full hour, then all your changes will be lost :(<br><br>
        </td>
      </tr>
      <tr>
        <td><a href="/PartHub/pages/inventory.php">
            <h1><i class="bi bi-list-columns"></i></h1>Parts<br><br>
          </a></td>
        <td><a href="/PartHub/pages/bom-search.php">
            <h1><i class="bi bi-bezier2"></i></h1>BOMs<br>
          </a></td>
        <td><a href="/PartHub/pages/locations.php">
            <h1><i class="bi bi-buildings"></i></h1>Locations<br>
          </a></td>
      </tr>
      <tr>
        <td><a href="/PartHub/pages/categories.php">
            <h1><i class="bi bi-boxes"></i></h1>Categories
          </a></td>
        <td><a href="/PartHub/pages/suppliers.php">
            <h1><i class="bi bi-cart"></i></h1>Suppliers
          </a></td>
        <td><a href="/PartHub/pages/footprints.php">
            <h1><i class="bi bi-postage"></i></h1>Footprints
          </a></td>
      </tr>
      <?php
      if (!isset($_SESSION['user_id'])){
      echo '<tr>';
        echo '<td colspan="3">';
          echo '<table class="table table-borderless">';
            echo '<tbody class="alert alert-danger">';
              echo '<tr>';
                echo '<td><button type="button" class="btn btn-primary" id="continueDemo">Continue as demo user</button></td>';
                echo '<td><button type="button" class="btn btn-primary" id="logIn">Log into your account</button></td>';
               echo '</tr>';
              echo '</tbody>';
          echo '</table>';
        echo '</td>';
      echo '</tr>';
      }
      ?>
    </table>
  </div>
</div>
</div>

<!-- The user stuff modal -->
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User stuff</h4>
            </div>
            <div class="modal-body">
                <p>You are currently not logged in.</p>
                <br>
                <p>Please continue as demo user or log in!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
echo "User ID: " . $_SESSION['user_id'];
echo "<br>";
echo "Session ID: " . session_id();

?>

</body>

</html>

<?php
if ($show_modal == 1){
  echo "<script>var myModal = new bootstrap.Modal(document.getElementById('myModal'));</script>";
  echo '<script>myModal.show();</script>';
}