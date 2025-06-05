<?php
session_start();
include "../connect.php";
//supplier logout
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location:../../../pages/login/loginSupplier.php?logout=true");
    exit();
}

//edit foto profil
if (isset($_FILES['photo']) && $_FILES['photo']['name'] != '') {
    $id = $_POST['idPemasok'];
    $fotoProfil = $_FILES['photo']['name'];
    $targetDir = "../../gambar/pemasok/photoProfile/";
    $targetFile = $targetDir . basename($fotoProfil);
    $ext = strtolower(pathinfo($fotoProfil, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png'])) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG files are allowed.";
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $q = "UPDATE pemasok SET fotoProfil='$fotoProfil' WHERE idPemasok='$id'";
        if (mysqli_query($conn, $q)) {
            $_SESSION['success'] = "Photo profile successfully changed!";
            $_SESSION['fotoProfil'] = $fotoProfil;
        } else {
            $_SESSION['error'] = "Error DB: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Failed to upload file.";
    }
    header("Location:../../../pages/pemasok/settingsSupplier.php");
    exit();
}
//edit data pemasok
if (isset($_POST['submitEdit'])) {
    $id = $_POST['idPemasok'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    
    $q = "UPDATE pemasok SET
          namaPemasok='$nama',
          emailPemasok='$email'
          WHERE idPemasok='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Profile data successfully updated!";
        $_SESSION['namaPemasok'] = $nama;
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pemasok/settingsSupplier.php");
    exit();
}

//edit data beras
if(isset($_POST['editBeras'])) {
    $idBeras = $_POST['idBeras'];
    $namaBeras = $_POST['namaBeras'];
    $tipeBeras = $_POST['jenisBeras'];
    $beratBeras = $_POST['beratBeras'];
    $hargaJualBeras = $_POST['hargaJualBeras'];
    $stokBeras = $_POST['stokBeras'];
    $idPemasok = $_POST['supplierBeras'];

    $queryGetGambar = "SELECT gambarBeras FROM stokberas WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $queryGetGambar);
    $row = mysqli_fetch_assoc($result);
    $gambarLama = $row['gambarBeras'];
    
    $gambarBaru = $gambarLama;
    if(!empty($_FILES['gambarBeras']['name'])) {
        $targetDir = "../../gambar/beras/";
        $namaFile = basename($_FILES['gambarBeras']['name']);
        $targetFile = $targetDir . uniqid() . '_' . $namaFile;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        // Validasi gambar
        $check = getimagesize($_FILES['gambarBeras']['tmp_name']);
        if($check === false) {
            $_SESSION['error'] = "File is not an image!";
            header("Location: ../../../pages/pemasok/riceManagement.php");
            exit();
        }
        // Cek ukuran file (contoh: maks 5MB)
        if($_FILES['gambarBeras']['size'] > 5000000) {
            $_SESSION['error'] = "Ukuran file terlalu besar (Maks 5MB)!";
            header("Location: ../../../pages/pemasok/riceManagement.php");
            exit();
        }
        // Cek ekstensi file
        $allowed = ['jpg', 'jpeg', 'png'];
        if(!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "Only JPG, JPEG and PNG formats are allowed!";
            header("Location: ../../../pages/pemasok/riceManagement.php");
            exit();
        }
        if(move_uploaded_file($_FILES['gambarBeras']['tmp_name'], $targetFile)) {
            $gambarBaru = $targetFile;
            if(file_exists($gambarLama)) {
                unlink($gambarLama);
            }
        } else {
            $_SESSION['error'] = "Failed to upload image!";
            header("Location: ../../../pages/pemasok/riceManagement.php");
            exit();
        }
    }
    $q = "UPDATE stokberas SET
          namaBeras='$namaBeras',
          jenisBeras='$tipeBeras',
          beratBeras='$beratBeras',
          hargaJualBeras='$hargaJualBeras', 
          stokBeras='$stokBeras',
          idPemasok='$idPemasok',
          gambarBeras='$gambarBaru' 
          WHERE idBeras='$idBeras'";
    if(mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Rice data successfully updated!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    
    header("Location: ../../../pages/pemasok/riceManagement.php");
    exit();
}



?>