<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">User stuff</h4>
            </div>
            <div class="modal-body">
                <p>You are currently not logged in.</p>
                <br>
                <p>Please continue as demo user or log in!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script defer>
    function showModal() {
        $("#myModal").modal('show');
    };
</script>


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// This is not index.php
if ($basename != 'index.php') {
    echo "this is not index.php";
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        echo "You are logged in with user id $user_id";
        // $user_id = 1; //it's me, chrisi
    }
    else {
        // $user_id = 0;
        // $_SESSION['user_id'] = $user_id;
        header("Location: /PartHub/index.php?redirect=1");
    }
}
// This is index.php
else {
    if (isset($_GET['redirect'])) {
        echo "You came here from a redirect";
        echo "<br>";
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        echo "You are logged in with user id $user_id";
        // $user_id = 1; //it's me, chrisi
    }
    else {
        $show_modal = 1;
    }

}

?>