<?php
session_start();

if (!isset($_SESSION['namaPemasok']) || !isset($_SESSION['idPemasok'])) {
    header("Location: ../login/loginSupplier.php?login=error");
    exit();
}

if (isset($_GET['inputSearch'])) {
    $input   = trim($_GET['inputSearch']);
    $keyword = strtolower($input);

    if (strpos($keyword, 'dashboard') !== false) {
        header("Location: dashboardSupplier.php");
        exit();

    } elseif (strpos($keyword, 'rice') !== false) {
        header("Location: riceManagement.php");
        exit();

    } elseif ((strpos($keyword, 'order') !== false && strpos($keyword, 'confirmation') !== false) || strpos($keyword, 'confirmation') !== false
    ) {
        header("Location: orderConfirmation.php");
        exit();

    } elseif ((strpos($keyword, 'order') !== false && strpos($keyword, 'status') !== false) || strpos($keyword, 'status') !== false) {
        header("Location: orderStatusSupplier.php");
        exit();

    } elseif (
        (strpos($keyword, 'order') !== false && strpos($keyword, 'history') !== false)
        || strpos($keyword, 'history') !== false
    ) {
        header("Location: orderHistorySupplier.php");
        exit();

    } else {
        header("Location: dashboardSupplier.php?search=notfound");
        exit();
    }
}
header("Location: dashboardSupplier.php");
exit();
