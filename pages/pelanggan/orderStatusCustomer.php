<?php
session_start();
if (!isset($_SESSION['namaPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$currentPage = 'orderStatusCustomer.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen flex">
<?php include '../../layout/sidebarCustomer.php'; ?>



</body>

<script src="../../assets/cdn/flowbite.min.js"></script>
<script src="../../assets/cdn/flowbite.bundle.js"></script>

</html>