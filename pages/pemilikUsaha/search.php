<?php
session_start();
if (!isset($_SESSION['namaPemilik']) || !isset($_SESSION['idPemilik'])) {
    header("Location: ../login/loginBusinessOwner.php?login=error");
    exit();
}

if (isset($_GET['inputSearch'])) {
    $input = trim($_GET['inputSearch']);
    $keyword = strtolower($input);
    if (strpos($keyword, 'dashboard') !== false) {
        header("Location: dashboardBusinessOwner.php");
        exit();
    } else if (strpos($keyword, 'rice') !== false && strpos($keyword, 'stock') !== false) {
        header("Location: riceStock.php");
        exit();
    } elseif (strpos($keyword, 'order') !== false && strpos($keyword, 'supplier') !== false) {
        header("Location: orderSupplier.php");
        exit();
    } elseif (strpos($keyword, 'order') !== false && strpos($keyword, 'status') !== false) {
        header("Location: orderStatusSupplier.php");
        exit();
    } elseif (strpos($keyword, 'history') !== false && strpos($keyword, 'supplier') !== false) {
        header("Location: historySupplier.php");
        exit();
    } elseif ($keyword === 'supplier') {
        header("Location: supplier.php");
        exit();
    } elseif ($keyword === 'customer') {
        header("Location: customer.php");
        exit();
    } elseif (strpos($keyword, 'order') !== false && strpos($keyword, 'confirmation') !== false) {
        header("Location: orderConfirmation.php");
        exit();
    } elseif (strpos($keyword, 'order') !== false && strpos($keyword, 'status') !== false && strpos($keyword, 'customer') !== false) {
        header("Location: orderStatusCustomer.php");
        exit();
    } elseif (strpos($keyword, 'history') !== false && strpos($keyword, 'order') !== false) {
        header("Location: historyOrder.php");
        exit();
    } elseif ($keyword === 'report') {
        header("Location: report.php");
        exit();
    } else {
        header("Location: dashboardBusinessOwner.php?search=notfound");
        exit();
    }
}

include '../../assets/mysql/connect.php';

$idPemilik = $_SESSION['idPemilik'];
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '$idPemilik'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <?php
    if (isset($_GET['search']) && $_GET['search'] === 'notfound') {
        echo "<p class='text-red-600 mt-4 ml-4'>Keyword tidak ditemukan.</p>";
    }
    ?>
</body>
</html>
