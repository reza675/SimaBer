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

//edit foto profil
if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
    $id = $_POST['idPemilik'];
    $fotoProfil = $_FILES['photo']['name'];
    $targetDir = "../../gambar/pemilikUsaha/photoProfile/";
    $targetFile = $targetDir . basename($fotoProfil);
    $ext = strtolower(pathinfo($fotoProfil, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG files are allowed.";
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $q = "UPDATE pemilikusaha SET fotoProfil='$fotoProfil' WHERE idPemilik='$id'";
        if (mysqli_query($conn, $q)) {
            $_SESSION['success'] = "Profile photo successfully changed!";
            $_SESSION['fotoProfil'] = $fotoProfil;
        } else {
            $_SESSION['error'] = "Error DB: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Failed to upload file.";
    }
    header("Location:../../../pages/pemilikUsaha/settingsBusinessOwner.php");
    exit();
}
//edit data business owner
if (isset($_POST['submitEdit'])) {
    $id = $_POST['idPemilik'];
    $nama = $_POST['nama'];

    $q = "UPDATE pelanggan SET
          namaPelanggan='$nama',
          teleponPelanggan='$telepon',
          alamatPelanggan='$alamat',
          kodeposPelanggan='$kodepos'
          WHERE idPelanggan='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Profile data successfully updated!";
        $_SESSION['namaPemilik'] = $nama;
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pemilikUsaha/settingsBusinessOwner.php");
    exit();
}

?>