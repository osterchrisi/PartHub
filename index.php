<?php
$title = 'Open-source inventory and BOM management';
include 'includes/head.html';
include 'includes/navbar.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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
    </table>
  </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
  integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
  integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>


</body>

</html>