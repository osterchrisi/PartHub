// ClickListener for "Save Changes" button in Add Stock Modal
$(document).ready(function () {
    $('#AddStock').click(function () {
        q = $("#addStockQuantity").val();
        c = $("#addStockDescription").val();
        l = $("#addStockLocation").val();

        //? Okay, this looks weird, maybe there is a cleaner way?
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        $.post('/PartHub/includes/stockChanges.php',
            { quantity: q, to_location: l, comment: c, user_id: uid, part_id: pid },
            function (response) {
                console.log("Succesfully created new stock history entry with number: ", response);
                $("#mAddStock").hide();
            });

        updatePartsInfo(pid);
    });
});