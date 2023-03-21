<?php
include 'session.php';
session_destroy();
header('Location: /PartHub/index.php?logout');
exit();