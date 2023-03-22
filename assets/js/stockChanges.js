// ClickListener for "Continue as demo user" button
$(document).ready(function() {
    $('#AddStocks').click(function() {
        console.log("Someone clicked Save Changes");
        var quant = document.getElementById("addStockQuantity").text();
        console.log(quant);
      $.post('/PartHub/includes/stockChanges.php', {quant: quant}, function(response) {
        console.log(response);
      });
    });
  });