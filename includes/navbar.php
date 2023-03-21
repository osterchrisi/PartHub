<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/PartHub/index.php">PartHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/inventory.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/inventory.php">Parts</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/bom-create.php' or $_SERVER['PHP_SELF'] == '/PartHub/pages/bom-build.php' or $_SERVER['PHP_SELF'] == '/PartHub/pages/bom-search.php') ? 'active' : ''; ?>"
            href="/PartHub/pages//bom-search.php" role="button" data-bs-toggle="dropdown">BOMS</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/PartHub/pages/bom-create.php">Create BOM</a></li>
            <li><a class="dropdown-item" href="/PartHub/pages/bom-build.php">Build BOM</a></li>
            <li><a class="dropdown-item" href="/PartHub/pages/bom-search.php">Show BOM</a></li>
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
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/locations.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/locations.php">Storage Locations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/categories.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/categories.php">Categories</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/suppliers.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/suppliers.php">Suppliers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/footprints.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/footprints.php">Footprints</a>
        </li>
      </ul>
      <div class="d-flex">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li>
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i
                class="fas fa-user"></i></a>
            <ul class="dropdown-menu dropdown-menu-end text-end w-auto" style="min-width: 0;">
              <?php echo ($_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/pages/settings.php">Settings</a></li>' : '');?>
              <?php echo ($_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/includes/logout.php">Log Out</a></li>' : '');?>
              <?php echo (!$_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/pages/login.php">Log In</a></li>' : '');?>
            </ul>
          </li>
        </ul>
      </div>
      </form>
    </div>
  </div>
</nav>