<?php
session_start();
include "../connect.php";
//customer logout
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location:../../../pages/login/loginCustomer.php?logout=true");
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
//update data Customer
if(isset($_POST['submitEdit'])) {
    $id = $_POST['idPelanggan'];
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $kodepos = $_POST['kodepos'];
    
    $fotoProfil = $_FILES['photo']['name'];
    $targetDir = "../../gambar/pelanggan/photoProfile/";
    $targetFile = $targetDir . basename($fotoProfil);
    
    if(!empty($fotoProfil)) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($fotoProfil, PATHINFO_EXTENSION));

        if(!in_array($fileExtension, $allowedExtensions)) {
            $_SESSION['error'] = "Only JPG, JPEG, PNG files are allowed";
            header("Location:../../../pages/pelanggan/settingsCustomer.php");
            exit();
        }
        if(move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $query = "UPDATE pelanggan SET
                      emailPelanggan = '$email',
                      namaPelanggan = '$nama',
                      teleponPelanggan = '$telepon',
                      alamatPelanggan = '$alamat',
                      kodeposPelanggan = '$kodepos',
                      fotoProfil = '$fotoProfil'
                      WHERE id = '$id'";
        } else {
            $_SESSION['error'] = "Failed to upload profile photo";
            header("Location:../../../pages/pelanggan/settingsCustomer.php");
            exit();
        }
    } else {
        $query = "UPDATE pelanggan SET 
                  emailPelanggan = '$email',
                  namaPelanggan = '$nama',
                  teleponPelanggan = '$telepon',
                  alamatPelanggan = '$alamat',
                  kodeposPelanggan = '$kodepos'
                  WHERE id = '$id'";
    }

    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Profile successfully updated!";
        $_SESSION['namaPelanggan'] = $nama;
        if(!empty($fotoProfil)) {
            $_SESSION['fotoProfil'] = $fotoProfil;
        }
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}
?>