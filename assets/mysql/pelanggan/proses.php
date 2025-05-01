<?php
include "../connect.php";
//customer logout
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location:../../../pages/login/loginCustomer.php?logout=true");
    exit();
}

?>