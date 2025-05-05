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


//edit foto profil
if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
    $id = $_POST['idPelanggan'];
    $fotoProfil = $_FILES['photo']['name'];
    $targetDir = "../../gambar/pelanggan/photoProfile/";
    $targetFile = $targetDir . basename($fotoProfil);
    $ext = strtolower(pathinfo($fotoProfil, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        $_SESSION['error'] = "Hanya file JPG, JPEG, PNG yang diperbolehkan.";
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $q = "UPDATE pelanggan SET fotoProfil='$fotoProfil' WHERE idPelanggan='$id'";
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
//edit data customer
if (isset($_POST['submitEdit'])) {
    $id = $_POST['idPelanggan'];
    $nama = $_POST['nama'];
    $telepon= $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $kodepos= $_POST['kodepos'];
    $q = "UPDATE pelanggan SET
          namaPelanggan='$nama',
          teleponPelanggan='$telepon',
          alamatPelanggan='$alamat',
          kodeposPelanggan='$kodepos'
          WHERE idPelanggan='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Data profil berhasil diperbarui!";
        $_SESSION['namaPelanggan'] = $nama;
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}

//beli Beras
if(isset($_POST['submitBeli'])) {
    $idPelanggan = $_POST['idPelanggan'];
    $idBeras = $_POST['idBeras'];
    $jumlahBeras = $_POST['quantity'];
    $hargaBeras = $_POST['harga'];
    $q = "INSERT INTO transaksi (idPelanggan, idBeras, jumlah) VALUES ('$idPelanggan', '$idBeras', '$jumlah')";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Beras berhasil dibeli!";
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pelanggan/orderCustomer.php");
    exit();
}
?>