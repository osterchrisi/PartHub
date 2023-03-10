<?php
$title = 'Start';
include 'includes/head.html';
include 'includes/navbar.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<!-- <style>
  table {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
</style> -->

<div class="container-fluid vh-100 d-flex flex-column">
  <div class="row d-flex flex-grow-1">
    <div class="col-md-6 flex-grow-1 mx-auto my-auto">
      <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
          <td colspan="3">Welcome to PartHub Live Demo!<br><br>
          You can use any aspect of this demo but the database will reset every full hour, so all your changes will be lost<br><br></td>
        </tr>
        <tr>
          <td><a href="/PartHub/pages/inventory.php"><h1><i class="bi bi-list-columns"></i></h1>Parts<br></a></td>
          <td><a href="/PartHub/pages/bom-search.php"><h1><i class="bi bi-bezier2"></i></h1>BOMs<br></a></td>
          <td><a href="/PartHub/pages/locations.php"><h1><i class="bi bi-buildings"></i></h1>Locations<br></a></td>
        </tr>
        <tr>
          <td><a href="/PartHub/pages/categories.php"><h1><i class="bi bi-boxes"></i></h1>Categories</a></td>
          <td><a href="/PartHub/pages/suppliers.php"><h1><i class="bi bi-cart"></i></h1>Suppliers</a></td>
          <td><a href="/PartHub/pages/footprints.php"><h1><i class="bi bi-postage"></i></h1>Footprints</a></td>
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