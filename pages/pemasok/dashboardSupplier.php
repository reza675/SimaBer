<?php
session_start();
if (!isset($_SESSION['namaPemasok'])) {
    header("Location:../login/loginSupplier.php?login=error");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SimaBer</title>
</head>
<body>
    hello
</body>
</html>