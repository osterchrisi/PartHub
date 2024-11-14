import {
  removeClickListeners,
  updateInfoWindow,
} from "./custom";



/**
 * Bootstrap the BOM details table
 * @return void
 */
export function bootstrapBomDetailsTable() {
  $('#BomDetailsTable').bootstrapTable({
  });

  // Find the element with the class "fixed-table-toolbar"
  var $fixedTableToolbar = $('#bomInfo .fixed-table-toolbar');

  //* Tryout for a way to display storage places in the BOM details table
  $fixedTableToolbar.append('<div class="row"><div class="col"><div class="columns columns-right btn-group float-right"><div class="keep-open btn-group" title="Columns"><button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-label="Columns" title="Columns" aria-expanded="false"><i class="bi bi-buildings"></i><span class="caret"></span></button><div class="dropdown-menu dropdown-menu-right" style=""><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Part Name" value="0" checked="checked"> <span>Storage 1</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Quantity needed" value="1" checked="checked"> <span>Storage 2</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Total stock available" value="2" checked="checked"> <span>Storage 3</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Can build" value="3" checked="checked"> <span>Storage 4</span></label></div></div></div></div></div>');
};



/**
* Displays a modal for assembling one or more BOMs and sends an AJAX request to the server to assemble the BOMs.
* If there are stock shortages the user is notified after the AJAX request is complete and can chose to continue.
* @param {Array} selectedRows - An array of selected rows from the table.
* @param {Array} ids - An array of BOM IDs.
* @returns {void}
*/
export function assembleBoms(selectedRows, ids) {

  if (ids.length === 0) {
    alert("Please select BOM(s) to be assembled.\nYou can use Ctrl and Shift to select multiple rows");
    return
  }
  $('#mBomAssembly').modal('show');         // Show Modal

  // Attach click listener to the main "Cancel" button of the modal
  $('#btnCancelAssembly').off('click').on('click', function () {
    hideBOMAssemblyModalAndCleanup();
  });

  $('#btnAssembleBOMs').click(function () { // Attach clicklistener

    var q = $("#bomAssembleQuantity").val();                // Quantity
    var fl = $("#fromStockLocation").val();                 // From Location
    var token = $('input[name="_token"]').attr('value');    // X-CSRF token

    $.ajax({
      url: '/bom.assemble',
      type: 'POST',
      data: {
        ids: ids,
        assemble_quantity: q,
        from_location: fl
      },
      headers: {
        'X-CSRF-TOKEN': token
      },
      success: function (response) {

        var r = response;
        if (r.status === 'success') {
          //* Do the normal thing here, all requested stock available

          $('#mBomAssembly').modal('hide');     // Hide Modal
          updateInfoWindow('bom', ids[ids.length - 1]); // Update BOM info window with last BOM ID in array
          //TODO: Also select in table
        }
        else if (r.status === 'permission_requested') {
          //* User permission required

          // Display warning and missing stock table
          $('#btnAssembleBOMs').attr('disabled', true);  // Disable main "Assemble" button of modal
          var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " parts. Do you want to continue anyway?<br>";
          message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal' id='btnCancelAnywayAssembly'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnAssembleBOMsAnyway'>Do It Anyway</button></div></div>"
          message += r.negative_stock_table;
          $('#mBomAssemblyInfo').html(message);

          // Attach click listener to "Do It Anyway" button
          $('#btnAssembleBOMsAnyway').off('click').on('click', function () {
            //TODO: Passing ids for updating table after success but this won't work in the future for selectively updating
            continueAnyway(r, ids, token);
          });

          // Attach click listener to "Cancel" button next to the "Do It Anyway" button
          $('#btnCancelAnywayAssembly').off('click').on('click', function () {
            // Hide modal and perform cleanup operations
            hideBOMAssemblyModalAndCleanup();
          });
        }
        removeClickListeners('#btnAssembleBOMs'); // Remove click listener assembly
      },
      error: function (xhr) {
        // Handle the error
        if (xhr.status === 419) {
          // Token mismatch error
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          // Other errors
          alert('Error assembling BOM');
        }
      }
    });
  })
}


/*
* Hides BOM Assembly modal and cleans all info and click listeners from it
*/
function hideBOMAssemblyModalAndCleanup() {
  // Empty form
  $('#bomAssemblyForm')[0].reset();
  $('#mBomAssemblyInfo').empty();
  $('#btnAssembleBOMs').attr('disabled', false);
  // Hide modal
  $('#mBomAssembly').modal('hide');
  // Remove click listeners
  removeClickListeners('#btnAssembleBOMs'); // Remove click listener assembly
  removeClickListeners('#btnAssembleBOMsAnyway'); // Remove click listener assembly
  // Dispose modal after it's hidden
  $('#mBomAssembly').on('hidden.bs.modal', function (e) {
    $(this).modal('dispose');
  });
}

/**
* Send back the changes array with all statuses set to "gtg" (good to got)
* when the user chooses to continue with assembling BOMs even if there isn't enough stock for some parts.
*
* @param {Object} r - An object containing the changes array received from the server for updating the stock changes.
* @param {Array} ids - An array containing the IDs of the BOMs that need to be updated.
* @returns {void}
*/
function continueAnyway(r, ids, token) {
  //TODO: Recieving ids for updating table after success but this won't work in the future for selectively updating
  // Change all statuses to "good to go"
  for (const change of r.changes) {
    change.status = 'gtg';
  }

  // Call the stock changing script with the already prepared stock changes
  $.ajax({
    url: '/parts.requestStockChange',
    type: 'POST',
    data: { stock_changes: r.changes },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      // console.log(response);
      $('#mBomAssembly').on('hidden.bs.modal', function (e) {
        $('#bomAssemblyForm')[0].reset();
        $('#mBomAssemblyInfo').empty();
        $('#btnAssembleBOMs').attr('disabled', false);
        $(this).modal('dispose');
      }).modal('hide');
      updateInfoWindow('bom', ids[ids.length - 1]) // Update BOM info with last BOM ID in array
      // $('#mBomAssembly').modal('dispose'); // Hide Modal
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