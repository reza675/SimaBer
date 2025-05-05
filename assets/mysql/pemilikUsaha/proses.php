<?php
session_start();
include "../connect.php";
//pemilikUsaha logout
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location:../../../pages/login/loginBusinessOwner.php?logout=true");
    exit();
}
?>