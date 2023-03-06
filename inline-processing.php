<script>
$(document).ready(function() {
  $('tbody tr').click(function() {
    var id = $(this).data('id');
    var number = $(this).find('.editable').text();
    var formHtml = '<form><input type="number" name="number" value="' + number + '"> ';
    formHtml += '<input type="hidden" name="id" value="' + id + '"> ';
    formHtml += '<input type="submit" value="OK"></form>';

    // Replace the current cell contents with the form
    $(this).find('.editable').html(formHtml);
  });

  $('tbody').on('submit', 'form', function(e) {
    e.preventDefault();

    // Get the form data
    var formData = $(this).serialize();

    // Submit the form data via AJAX to update the database
    $.ajax({
      type: 'POST',
      url: 'update_number.php',
      data: formData,
      success: function(response) {
        // Replace the form with the new number
        var newNumber = $(response).find('.editable').html();
        $(this).parent().html(newNumber);
      }
    });
  });
});
</script>

<?php
echo "awesome";
?>