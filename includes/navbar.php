<?php
//! Get user name - navbar is not a cool place for this
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$user_name = getUserName($conn);
$_SESSION['user_name'] = $user_name;
?>

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
        <li class="nav-item">
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/bom-list.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/bom-list.php">BOMs</a>
        </li>
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
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/signup.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/pricing.php">Pricing</a>
            </li>
          <li>
          <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/signup.php') ? 'active' : ''; ?>"
            href="/PartHub/pages/signup.php">Sign up</a>
            </li>
          <li>
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i
                class="fas fa-user"></i></a>
            <ul class="dropdown-menu dropdown-menu-end text-end w-auto" style="min-width: 0;">
              <!-- Logged in -->
              <?php echo ($_SESSION['user_id'] ? '<li>'.$user_name.'</li><li><hr class="dropdown-divider"></li>' : '');?>
              <?php echo ($_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/pages/settings.php">Settings</a></li>' : '');?>
              <?php echo ($_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/includes/logout.php">Log Out</a></li>' : '');?>
              <!-- Not logged in -->
              <?php echo (!$_SESSION['user_id'] ? '<li><a class="nav-link" href="/PartHub/pages/login.php">Log In</a></li>' : '');?>
            </ul>
          </li>
        </ul>
      </div>
      </form>
    </div>
  </div>
</nav>