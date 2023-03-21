<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        // exit();
    }
}
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
        echo "Not yet logged in";
    }

}

?>