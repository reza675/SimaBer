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
            $_SESSION['success'] = "Photo profile successfully changed!";
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

//add data beras
if(isset($_POST['addBeras'])) {
    $idBeras = $_POST['idBeras'];
    $namaBeras = $_POST['namaBeras'];
    $tipeBeras = $_POST['jenisBeras'];
    $beratBeras = $_POST['beratBeras'];
    $hargaJualBeras = $_POST['hargaJualBeras'];
    $hargaBeliBeras = $_POST['hargaBeliBeras'];
    $stokBeras = $_POST['stokBeras'];
    $idPemasok = $_POST['idPemasok'];
    $deskripsiBeras = $_POST['deskripsiBeras'];

    $gambarBeras = '';
    if(isset($_FILES['gambarBeras'])) {
        $file = $_FILES['gambarBeras'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if($fileError === 0) {
            if(in_array($fileExtension, $allowedExtensions)) {
                if($fileSize < 5000000) {
                    $newFileName = uniqid('IMG-', true).'.'.$fileExtension;
                    $uploadPath = '../../gambar/beras/'.$newFileName;
                    
                    if(move_uploaded_file($fileTmp, $uploadPath)) {
                        $gambarBeras = $newFileName;
                    } else {
                        $_SESSION['error'] = "Failed to upload image";
                        header("Location: ../../../pages/pemilikUsaha/riceStock.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Image size is too large (Max 5MB))";
                    header("Location: ../../../pages/pemilikUsaha/riceStock.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Unsupported file formats (JPG, JPEG, PNG, WEBP only)";
                header("Location: ../../../pages/pemilikUsaha/riceStock.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "An error occurred while uploading the image";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
    }

    // 3. Query insert ke database
    $query = "INSERT INTO stokberas  (
        idBeras, 
        namaBeras, 
        jenisBeras, 
        beratBeras, 
        hargaJualBeras, 
        hargaBeliBeras, 
        stokBeras, 
        idPemasok, 
        deskripsiBeras, 
        gambarBeras
    ) VALUES (
        '$idBeras',
        '$namaBeras',
        '$tipeBeras',
        '$beratBeras',
        '$hargaJualBeras',
        '$hargaBeliBeras',
        '$stokBeras',
        '$idPemasok',
        '$deskripsiBeras',
        '$gambarBeras'
    )";

    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Rice data successfully added";
    } else {
        $_SESSION['error'] = "Failed to add data: " . mysqli_error($conn);
    }
    header("Location: ../../../pages/pemilikUsaha/riceStock.php");
    exit();
}

//edit data beras
if(isset($_POST['editBeras'])) {
    $idBeras = $_POST['idBeras'];
    $namaBeras = $_POST['namaBeras'];
    $tipeBeras = $_POST['jenisBeras'];
    $beratBeras = $_POST['beratBeras'];
    $hargaJualBeras = $_POST['hargaJualBeras'];
    $hargaBeliBeras = $_POST['hargaBeliBeras'];
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
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
        // Cek ukuran file (contoh: maks 5MB)
        if($_FILES['gambarBeras']['size'] > 5000000) {
            $_SESSION['error'] = "Ukuran file terlalu besar (Maks 5MB)!";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
        // Cek ekstensi file
        $allowed = ['jpg', 'jpeg', 'png'];
        if(!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "Only JPG, JPEG and PNG formats are allowed!";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
        if(move_uploaded_file($_FILES['gambarBeras']['tmp_name'], $targetFile)) {
            $gambarBaru = $targetFile;
            if(file_exists($gambarLama)) {
                unlink($gambarLama);
            }
        } else {
            $_SESSION['error'] = "Failed to upload image!";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
    }
    $q = "UPDATE stokberas SET
          namaBeras='$namaBeras',
          jenisBeras='$tipeBeras',
          beratBeras='$beratBeras',
          hargaJualBeras='$hargaJualBeras',
          hargaBeliBeras='$hargaBeliBeras',
          stokBeras='$stokBeras',
          idPemasok='$idPemasok',
          gambarBeras='$gambarBaru' 
          WHERE idBeras='$idBeras'";
    if(mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Rice data successfully updated!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    
    header("Location: ../../../pages/pemilikUsaha/riceStock.php");
    exit();
}

//delete beras
if(isset($_POST['deleteBeras'])) {
    $idBeras = $_POST['idBeras'];
    $query = "SELECT gambarBeras FROM stokberas WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gambarBeras = $row['gambarBeras'];
        $deleteQuery = "DELETE FROM stokberas WHERE idBeras = '$idBeras'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM stokberas WHERE gambarBeras = '$gambarBeras'";
            $checkResult = mysqli_query($conn, $checkQuery);
            $checkData = mysqli_fetch_assoc($checkResult);
            if($checkData['total'] == 0) {
                $gambarPath = "../../gambar/beras/" . $gambarBeras;
                if(file_exists($gambarPath) && is_file($gambarPath)) {
                    if(!unlink($gambarPath)) {
                        $_SESSION['error'] = "Failed to delete image!";
                    }
                }
            }
            
            $_SESSION['success'] = "Rice data successfully deleted!";
        } else {
            $_SESSION['error'] = "Failed to delete data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data not found!";
    }
    header("Location: ../../../pages/pemilikUsaha/riceStock.php");
    exit();
}


//edit data pemasok
if(isset($_POST['editSupplier'])) {
    $idPemasok = $_POST['idPemasok'];
    $namaPemasok = $_POST['namaPemasok'];
    $alamatPemasok = $_POST['alamatPemasok'];
    $nomorPemasok = $_POST['noTelpPemasok'];

    $queryGetGambar = "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'";
    $result = mysqli_query($conn, $queryGetGambar);
    $row = mysqli_fetch_assoc($result);
    $gambarLama = $row['fotoProfil'];
    
    $gambarBaru = $gambarLama;
    if(!empty($_FILES['fotoProfil']['name'])) {
        $targetDir = "../../gambar/pemasok/photoProfile/";
        $namaFile = basename($_FILES['fotoProfil']['name']);
        $targetFile = $targetDir . uniqid() . '_' . $namaFile;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        // Validasi gambar
        $check = getimagesize($_FILES['fotoProfil']['tmp_name']);
        if($check === false) {
            $_SESSION['error'] = "File is not an image!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
        // Cek ukuran file (contoh: maks 5MB)
        if($_FILES['fotoProfil']['size'] > 5000000) {
            $_SESSION['error'] = "Ukuran file terlalu besar (Maks 5MB)!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
        // Cek ekstensi file
        $allowed = ['jpg', 'jpeg', 'png'];
        if(!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "Only JPG, JPEG and PNG formats are allowed!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
        if(move_uploaded_file($_FILES['fotoProfil']['tmp_name'], $targetFile)) {
            $gambarBaru = $targetFile;
            if(file_exists($gambarLama)) {
                unlink($gambarLama);
            }
        } else {
            $_SESSION['error'] = "Failed to upload image!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
    }
    $q = "UPDATE pemasok SET
          namaPemasok='$namaPemasok',
          alamatPemasok='$alamatPemasok',
          nomorHPPemasok='$nomorPemasok',
          fotoProfil='$gambarBaru' 
          WHERE idPemasok='$idPemasok'";
    if(mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Data supplier successfully updated!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    
    header("Location: ../../../pages/pemilikUsaha/supplier.php");
    exit();
}

//delete beras
if(isset($_POST['deleteBeras'])) {
    $idBeras = $_POST['idBeras'];
    $query = "SELECT gambarBeras FROM stokberas WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gambarBeras = $row['gambarBeras'];
        $deleteQuery = "DELETE FROM stokberas WHERE idBeras = '$idBeras'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM stokberas WHERE gambarBeras = '$gambarBeras'";
            $checkResult = mysqli_query($conn, $checkQuery);
            $checkData = mysqli_fetch_assoc($checkResult);
            if($checkData['total'] == 0) {
                $gambarPath = "../../gambar/beras/" . $gambarBeras;
                if(file_exists($gambarPath) && is_file($gambarPath)) {
                    if(!unlink($gambarPath)) {
                        $_SESSION['error'] = "Failed to delete image!";
                    }
                }
            }
            
            $_SESSION['success'] = "Rice data successfully deleted!";
        } else {
            $_SESSION['error'] = "Failed to delete data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data not found!";
    }
    header("Location: ../../../pages/pemilikUsaha/riceStock.php");
    exit();
}
?>