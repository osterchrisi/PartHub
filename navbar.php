<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="scss/vapor-bootstrap.min.css" rel="stylesheet">
    <!-- <link href="scss/custom.css" rel="stylesheet"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>PartHub</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  <body class="p-3 m-0 border-0 bd-example">

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
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">BOMS</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="bom-create.php">Create BOM</a></li>
                <li><a class="dropdown-item" href="bom-build.php">Build BOM</a></li>
                <li><a class="dropdown-item" href="bom-search.php">Show BOM</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Backorders</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="backorders-entry.php">Enter Backorder</a></li>
                <li><a class="dropdown-item" href="backorders-search.php">Search Backorders</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

  </body>

</html>
