<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>KOMA Elektronik Inventory Thing</title>
<style>
body {font-family: Verdana;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

/* Table borders */
table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
}

</style>
</head>
<body>

<h2>KOMA Elektronik Inventory Thingy</h2>

<script>
function openTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

<div class="tab">
  <button class="tablinks" onclick="openTab(event, 'Inventory')">Inventory</button>
  <button class="tablinks" onclick="openTab(event, 'searchBackorders')">Search Backorders</button>
  <button class="tablinks" onclick="openTab(event, 'enterBackorders')">Enter Backorders</button>
</div>

<!-- Tab content -->
<div id="Inventory" class="tabcontent">
  <h3>Inventory</h3>
  <?php include ("inventory.php");?>
</div>

<div id="searchBackorders" class="tabcontent">
  <h3>Search Backorders</h3>
  <?php include ("backorders-search.php");?>
</div>

<div id="enterBackorders" class="tabcontent">
  <h3>Enter Backorders</h3>
  <?php include("backorders-entry.php");?>
</div>
   
</body>
</html>

