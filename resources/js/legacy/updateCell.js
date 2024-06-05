/**
 * Updates a cell value in the database using AJAX.
 * @param {number} id - The ID of the row containing the cell to be updated.
 * @param {string} column - The name of the column containing the cell to be updated.
 * @param {string} table_name - The name of the database table containing the cell to be updated.
 * @param {string} new_value - The new value to be assigned to the cell.
 * @param {string} id_field - The name of the primary key field in the database table.
 * @returns {object} - A jQuery AJAX object that can be used to handle the success and error events of the request.
 */
function updateCell(id, column, table_name, new_value, id_field) {

    var token = $('input[name="_token"]').attr('value');
    return $.ajax({
      url: '/updateRow',
      type: 'POST',
      data: {
        id: id,
        column: column,
        table_name: table_name,
        new_value: new_value,
        id_field: id_field
      },
      headers: {
        'X-CSRF-TOKEN': token
      },
      success: function (data) {
        // console.log('Data updated successfully');
      },
      error: function (xhr) {
        // Handle the error
        if (xhr.status === 419) {
          // Token mismatch error
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          // Other errors
          alert('Error updating data');
        }
      }
    });
  }