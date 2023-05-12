<?php
// The navbar in the top

use App\Models\User;
//TODO: There is actually a "Blade" way to figure this out without PHP
if (auth()->check()) {
    $user = User::findOrFail(auth()->id());
    $user_name = $user['name'];
    $user_id = $user['user_id'];
} else {
    $user_id = 0;
}
?>

<div class="container-fluid px-0">
  <nav class="navbar navbar-expand-lg bg-primary bg-gradient"
    style="background-color: rgba(var(--bs-primary-rgb), 0.5);">
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
              <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/pricing.php') ? 'active' : ''; ?>"
                href="/PartHub/pages/pricing.php">Pricing</a>
            </li>
            <li>
              <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/PartHub/pages/signup.php') ? 'active' : ''; ?>"
                href="/PartHub/pages/signup.php">Sign up</a>
            </li>
            <li>
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i
                  class="fas fa-user"></i></a>
              <ul class="dropdown-menu dropdown-menu-end text-end w-auto px-2" style="min-width: 0;">
                @if (auth()->check())
                <!-- Logged in -->
                <li> {{ $user_name }}</li><li><hr class="dropdown-divider"></li>
                <li><a class="nav-link" href="/PartHub/pages/settings.php">Settings</a></li>
                <li><form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link" style="background: none; border: none;">Log Out</button>
            </form></li>
                @else 
                <!-- Not logged in -->
                <li><a class="nav-link" href="/login">Log In</a></li>
                @endif
              </ul>
            </li>
          </ul>
        </div>
        </form>
      </div>
    </div>
  </nav>
</div>