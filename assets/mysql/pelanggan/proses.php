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


$id = $_POST['idPelanggan'];
// 1) Cek apakah ini request upload foto saja
if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
    $fotoProfil = $_FILES['photo']['name'];
    $targetDir = "../../gambar/pelanggan/photoProfile/";
    $targetFile = $targetDir . basename($fotoProfil);
    $ext = strtolower(pathinfo($fotoProfil, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        $_SESSION['error'] = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $q = "UPDATE pelanggan SET fotoProfil='$fotoProfil' WHERE id='$id'";
        if (mysqli_query($conn, $q)) {
            $_SESSION['success'] = "Foto profil berhasil diubah!";
            $_SESSION['fotoProfil'] = $fotoProfil;
        } else {
            $_SESSION['error'] = "Error DB: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Gagal mengunggah file.";
    }
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}

// 2) Kalau bukan upload foto, berarti update data profil
if (isset($_POST['submitEdit'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon= mysqli_real_escape_string($conn, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kodepos= mysqli_real_escape_string($conn, $_POST['kodepos']);
    $q = "UPDATE pelanggan SET
          namaPelanggan='$nama',
          teleponPelanggan='$telepon',
          alamatPelanggan='$alamat',
          kodeposPelanggan='$kodepos'
          WHERE id='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Data profil berhasil diperbarui!";
        $_SESSION['namaPelanggan'] = $nama;
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}

?>