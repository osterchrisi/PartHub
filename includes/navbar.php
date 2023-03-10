<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">PartHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="inventory.php">Parts</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="../pages/bom-search.php" role="button" data-bs-toggle="dropdown">BOMS</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="../pages/bom-create.php">Create BOM</a></li>
            <li><a class="dropdown-item" href="../pages/bom-build.php">Build BOM</a></li>
            <li><a class="dropdown-item" href="../pages/bom-search.php">Show BOM</a></li>
          </ul>
        </li>
        <!-- <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Backorders</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="backorders-entry.php">Enter Backorder</a></li>
            <li><a class="dropdown-item" href="backorders-search.php">Search Backorders</a></li>
          </ul>
        </li> -->
        <li class="nav-item">
          <a class="nav-link active" href="../pages/locations.php">Storage Locations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="../pages/categories.php">Categories</a>
        </li>
        <div class="d-flex flex-row-reverse">
          <li class="nav-item">
            <a class="nav-link active" href="../pages/suppliers.php">Suppliers</a>
          </li>
        </div>
        <div class="d-flex flex-row-reverse">
          <li class="nav-item">
            <a class="nav-link active" href="../pages/footprints.php">Footprints</a>
          </li>
        </div>
      </ul>
    </div>
  </div>
</nav>
