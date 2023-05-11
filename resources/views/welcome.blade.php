<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords"
    content="inventory management, BOMs (bills of materials), BOM, production tracking, stock keeping, supply chain management, warehouse management, parts tracking, component tracking, parts inventory, component inventory, self-hosted inventory management, cloud-based inventory management, inventory software, open-source, electronic part inventory, BOM creation, BOM execution">
  <meta name="description"
    content="Simplify your electronic parts inventory and BOM management. Free tiers and self-hosting. Aimed at small electronic makers and tinkerers">
  <title>PartHub - {{ $title }}
  </title>

  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="/PartHub/assets/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/PartHub/assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/PartHub/assets/favicon/favicon-16x16.png">
  <link rel="manifest" href="/PartHub/site.webmanifest">

  <!-- JQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- JQuery UI -->
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
  <!-- Selectize -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap5.min.css"
    integrity="sha512-Ars0BmSwpsUJnWMw+KoUKGKunT7+T8NGK0ORRKj+HT8naZzLSIQoOSIIM3oyaJljgLxFi0xImI5oZkAWEFARSA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- JSTree -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>



  <!-- Custom Bootstrap Theme-->
  <!-- <link href="/PartHub/assets/scss/quartz-bootstrap.min.css" rel="stylesheet"> -->
  <!-- OG Bootstrap Theme-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap -->
  <!-- <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet"> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

  <!-- Bootstrap Table -->
  <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.21.3/dist/bootstrap-table.min.css">
  <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/bootstrap-table.min.js"></script>
  <!-- Bootstrap Table Editable -->
  <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/editable/bootstrap-table-editable.js"></script>
  <!-- Bootstrap Table Resizable -->
  <link href="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.css" rel="stylesheet">
  <script src="https://unpkg.com/jquery-resizable-columns@0.2.3/dist/jquery.resizableColumns.min.js"></script>
  <script
    src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/resizable/bootstrap-table-resizable.min.js"></script>
  <!-- Bootstrap Table Reorder Columns -->
  <link rel="stylesheet" href="/PartHub/assets/dragtable/dragtable.css">
  <script src="/PartHub/assets/dragtable/jquery.dragtable.js"></script>
  <script
    src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/reorder-columns/bootstrap-table-reorder-columns.js"></script>
  <!-- Bootstrap Table Cookie to remember table state -->
  <script src="https://unpkg.com/bootstrap-table@1.21.3/dist/extensions/cookie/bootstrap-table-cookie.min.js"></script>

  <!-- Custom js -->
  <script src="/PartHub/assets/js/custom.js"></script>
  <script src='/PartHub/assets/js/tables.js'></script>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="/PartHub/assets/css/custom.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-06SX4YZKH2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  gtag('js', new Date());

  gtag('config', 'G-06SX4YZKH2');
</script>

<!-- Setting height to full viewport for themes to work properly -->

<body style="min-height: 100vh;">

<?php
// Landing page
$basename = basename(__FILE__);
$title = 'Open Source Inventory and BOM Management';
// include 'includes/head.html';
// //! Currently including this for getting the user name in the navbar - not ideal
// include 'config/credentials.php';
// include 'includes/SQL.php';
// include 'includes/navbar.php';
?>

<style>
  h1.display-1::after {
    content: 'BETA';
    font-size: 12px;
    vertical-align: top;
  }
</style>

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
  <div class="greeting d-flex align-items-center">
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
      <tr>
        <td colspan="3">
          <h1 class="display-1">PartHub</h1><br>
          <h1>Inventory and BOM management</h1><br>
          <?php if ((isset($_GET['login'])) && $_SESSION['user_id'] != '-1') {
            echo '<div class="alert alert-info" role="alert">Nice to have you back, ' . $user_name . '!</div>';
          } ?>
          Hello internet stranger that has found their way to PartHub!<br>
          PartHub is <strong>not yet fully functional</strong> but many parts do work.<br><br>
          So, if you like, start looking at some parts, create a BOM, move some stock and just see what happens. Go
          ahead and explore!
          <br>The database will reset every full hour, then all your changes will be lost :(<br><br>
        </td>
      </tr>
      <tr>
        <td><a href="/PartHub/pages/inventory.php">
            <h1><i class="bi bi-cpu"></i></h1>Parts<br><br>
          </a></td>
        <td><a href="/PartHub/pages/bom-list.php">
            <h1><i class="bi bi-clipboard-check"></i></h1>BOMs<br>
          </a></td>
        <td><a href="/PartHub/pages/locations.php">
            <h1><i class="bi bi-buildings"></i></h1>Storage<br>
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
            <h1><i class="bi bi-outlet"></i></h1>Footprints
          </a></td>
      </tr>
      <?php
      if (!isset($_SESSION['user_id'])) {
        echo '<tr>';
        echo '<td colspan="3">';
        echo '<table class="table table-borderless">';
        echo '<tbody class="alert alert-danger">';
        echo '<tr>';
        echo '<td><button type="button" class="btn btn-primary" id="continueDemo">Continue as demo user</button></td>';
        echo '<td><button type="button" class="btn btn-primary" id="logIn" onclick="window.location.href=\'pages/login.php\'">Log into your account</button></td>';
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

</body>

</html>

<?php
// Show the login modal if user is not logged in yet
if (isset($show_modal) && $show_modal == 1) {
  echo "<script>var myModal = new bootstrap.Modal(document.getElementById('myModal'));</script>";
  echo '<script>myModal.show();</script>';
}
?>

<script>
  $(document).ready(function () {
    continueAsDemoUser();
  });
</script>