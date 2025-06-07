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
    $email = $_POST['email'];

    $q = "UPDATE pemilikusaha SET
          namaPemilik='$nama',
          emailPemilik='$email'
          WHERE idPemilik='$id'";

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
    $query = "INSERT INTO stokberaspemilik  (
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
    $deskripsiBeras = $_POST['deskripsiBeras'];
    $idPemasok = $_POST['supplierBeras'];

    $queryGetGambar = "SELECT gambarBeras FROM stokberaspemilik WHERE idBeras = '$idBeras'";
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
        $allowed = ['jpg', 'jpeg', 'png','webp'];
        if(!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "Only JPG, JPEG, PNG, WEBP formats are allowed!";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
        if(move_uploaded_file($_FILES['gambarBeras']['tmp_name'], $targetFile)) {
            $gambarBaru = basename($targetFile);
           if(file_exists("../../gambar/beras/" . $gambarLama)) {
                unlink("../../gambar/beras/" . $gambarLama);
            }
        } else {
            $_SESSION['error'] = "Failed to upload image!";
            header("Location: ../../../pages/pemilikUsaha/riceStock.php");
            exit();
        }
    }
    $q = "UPDATE stokberaspemilik SET
          namaBeras='$namaBeras',
          jenisBeras='$tipeBeras',
          beratBeras='$beratBeras',
          hargaJualBeras='$hargaJualBeras',
          hargaBeliBeras='$hargaBeliBeras',
          stokBeras='$stokBeras',
          idPemasok='$idPemasok',
          deskripsiBeras='$deskripsiBeras',
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
    $query = "SELECT gambarBeras FROM stokberaspemilik WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gambarBeras = $row['gambarBeras'];
        $deleteQuery = "DELETE FROM stokberaspemilik WHERE idBeras = '$idBeras'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM stokberaspemilik WHERE gambarBeras = '$gambarBeras'";
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
//tambah supplier
   if (isset($_POST['addSupplier'])) {
    $idPemasok       = $_POST['idPemasok'];
    $namaPemasok     = $_POST['namaPemasok'];
    $emailPemasok    = $_POST['emailPemasok'];
    $passwordPemasok = $_POST['passwordPemasok'];
    $nomorHPPemasok  = $_POST['nomorHPPemasok'];
    $alamatPemasok   = $_POST['alamatPemasok'];
    
    $passwordHash = password_hash($passwordPemasok, PASSWORD_DEFAULT);
    $fotoProfil = 'profil.jpeg';
    
    $cekQ = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM pemasok WHERE idPemasok = '$idPemasok'");
    $row  = mysqli_fetch_assoc($cekQ);
    if ($row['jumlah'] > 0) {
        $_SESSION['error'] = "ID Supplier '$idPemasok' already exists.";
        header("Location: ../../../pages/pemilikUsaha/supplier.php");
        exit();
    }
    $insertSQL = "
        INSERT INTO pemasok (
            idPemasok, 
            namaPemasok, 
            emailPemasok, 
            passwordPemasok, 
            nomorHPPemasok, 
            alamatPemasok, 
            fotoProfil
        ) VALUES (
            '$idPemasok',
            '$namaPemasok',
            '$emailPemasok',
            '$passwordHash',
            '$nomorHPPemasok',
            '$alamatPemasok',
            '$fotoProfil'
        )
    ";

    if (mysqli_query($conn, $insertSQL)) {
        $_SESSION['success'] = "Data supplier successfully added!";
    } else {
        $_SESSION['error'] = "Failed to add data supplier: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/pemilikUsaha/supplier.php");
    exit();
}


// Edit data pemasok
if (isset($_POST['editSupplier'])) {
    $idPemasok     = $_POST['idPemasok'];
    $namaPemasok   = $_POST['namaPemasok'];
    $alamatPemasok = $_POST['alamatPemasok'];
    $nomorPemasok  = $_POST['noTelpPemasok'];
    $queryGetGambar = "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'";
    $result = mysqli_query($conn, $queryGetGambar);
    $row    = mysqli_fetch_assoc($result);
    $gambarLama = $row['fotoProfil']; 

    $gambarBaru = $gambarLama;

    // 3. Cek apakah user mengupload file baru
    if (!empty($_FILES['fotoProfil']['name'])) {
        $targetDir = "../../gambar/pemasok/photoProfile/";
        $namaAsli  = basename($_FILES['fotoProfil']['name']); 
        $namaUnik   = uniqid() . '_' . $namaAsli; 
        $targetFile = $targetDir . $namaUnik;
        $check = getimagesize($_FILES['fotoProfil']['tmp_name']);
        if ($check === false) {
            $_SESSION['error'] = "File is not a valid image!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
        if ($_FILES['fotoProfil']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran file terlalu besar (Maks 5MB)!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "only JPG, JPEG, and PNG files are allowed!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }

        if (move_uploaded_file($_FILES['fotoProfil']['tmp_name'], $targetFile)) {
            $gambarBaru = $namaUnik; 
            if (!empty($gambarLama) && file_exists($targetDir . $gambarLama)) {
                unlink($targetDir . $gambarLama);
            }
        } else {
            $_SESSION['error'] = "failed to upload image!";
            header("Location: ../../../pages/pemilikUsaha/supplier.php");
            exit();
        }
    }

    $q = "UPDATE pemasok SET
            namaPemasok     = '$namaPemasok',
            alamatPemasok   = '$alamatPemasok',
            nomorHPPemasok  = '$nomorPemasok',
            fotoProfil      = '$gambarBaru'
          WHERE idPemasok   = '$idPemasok'";

    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Data supplier successfully updated!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/pemilikUsaha/supplier.php");
    exit();
}



//delete pemasok
if(isset($_POST['deletePemasok'])) {
    $idPemasok = $_POST['idPemasok'];
    $query = "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $fotoProfil = $row['fotoProfil'];
        $deleteQuery = "DELETE FROM pemasok WHERE idPemasok = '$idPemasok'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM pemasok WHERE fotoProfil = '$fotoProfil'";
            $checkResult = mysqli_query($conn, $checkQuery);
            $checkData = mysqli_fetch_assoc($checkResult);
            if($checkData['total'] == 0) {
                $gambarPath = "../../gambar/pemasok/photoProfile/" . $fotoProfil;
                if(file_exists($gambarPath) && is_file($gambarPath)) {
                    if(!unlink($gambarPath)) {
                        $_SESSION['error'] = "Failed to delete image!";
                    }
                }
            }
            
            $_SESSION['success'] = "Supplier data successfully deleted!";
        } else {
            $_SESSION['error'] = "Failed to delete data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data not found!";
    }
    header("Location: ../../../pages/pemilikUsaha/supplier.php");
    exit();
}

//tambah customer
   if (isset($_POST['addCustomer'])) {
    $idPelanggan       = $_POST['idPelanggan'];
    $namaPelanggan     = $_POST['namaPelanggan'];
    $emailPelanggan    = $_POST['emailPelanggan'];
    $passwordPelanggan = $_POST['passwordPelanggan'];
    $nomorHPPelanggan  = $_POST['nomorHPPelanggan'];
    $alamatPelanggan   = $_POST['alamatPelanggan'];
    
    $passwordHash = password_hash($passwordPelanggan, PASSWORD_DEFAULT);
    $fotoProfil = 'profil.jpeg';
    
    $cekQ = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM pelanggan WHERE idPelanggan = '$idPelanggan'");
    $row  = mysqli_fetch_assoc($cekQ);
    if ($row['jumlah'] > 0) {
        $_SESSION['error'] = "ID Customer '$idPelanggan' already exists.";
        header("Location: ../../../pages/pemilikUsaha/customer.php");
        exit();
    }
    $insertSQL = "
        INSERT INTO pelanggan (
            idPelanggan, 
            namaPelanggan, 
            emailPelanggan, 
            passwordPelanggan, 
            nomorHPPelanggan, 
            alamatPelanggan, 
            fotoProfil
        ) VALUES (
            '$idPelanggan',
            '$namaPelanggan',
            '$emailPelanggan',
            '$passwordHash',
            '$nomorHPPelanggan',
            '$alamatPelanggan',
            '$fotoProfil'
        )
    ";

    if (mysqli_query($conn, $insertSQL)) {
        $_SESSION['success'] = "Data customer successfully added!";
    } else {
        $_SESSION['error'] = "Failed to add data customer: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/pemilikUsaha/customer.php");
    exit();
}


// Edit data pelanggan
if (isset($_POST['editCustomer'])) {
    $idPelanggan     = $_POST['idPelanggan'];
    $namaPelanggan   = $_POST['namaPelanggan'];
    $alamatPelanggan = $_POST['alamatPelanggan'];
    $nomorHPPelanggan  = $_POST['nomorHPPelanggan'];
    $queryGetGambar = "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'";
    $result = mysqli_query($conn, $queryGetGambar);
    $row    = mysqli_fetch_assoc($result);
    $gambarLama = $row['fotoProfil']; 

    $gambarBaru = $gambarLama;

    // 3. Cek apakah user mengupload file baru
    if (!empty($_FILES['fotoProfil']['name'])) {
        $targetDir = "../../gambar/pelanggan/photoProfile/";
        $namaAsli  = basename($_FILES['fotoProfil']['name']); 
        $namaUnik   = uniqid() . '_' . $namaAsli; 
        $targetFile = $targetDir . $namaUnik;
        $check = getimagesize($_FILES['fotoProfil']['tmp_name']);
        if ($check === false) {
            $_SESSION['error'] = "File is not a valid image!";
            header("Location: ../../../pages/pemilikUsaha/customer.php");
            exit();
        }
        if ($_FILES['fotoProfil']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran file terlalu besar (Maks 5MB)!";
            header("Location: ../../../pages/pemilikUsaha/customer.php");
            exit();
        }
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileType, $allowed)) {
            $_SESSION['error'] = "Hanya format JPG, JPEG, dan PNG yang diizinkan!";
            header("Location: ../../../pages/pemilikUsaha/customer.php");
            exit();
        }

        if (move_uploaded_file($_FILES['fotoProfil']['tmp_name'], $targetFile)) {
            $gambarBaru = $namaUnik; 
            if (!empty($gambarLama) && file_exists($targetDir . $gambarLama)) {
                unlink($targetDir . $gambarLama);
            }
        } else {
            $_SESSION['error'] = "failed to upload the image";
            header("Location: ../../../pages/pemilikUsaha/customer.php");
            exit();
        }
    }

    $q = "UPDATE pelanggan SET
            namaPelanggan     = '$namaPelanggan',
            alamatPelanggan   = '$alamatPelanggan',
            nomorHPPelanggan  = '$nomorHPPelanggan',
            fotoProfil      = '$gambarBaru'
          WHERE idPelanggan   = '$idPelanggan'";

    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Data customer successfully updated!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    header("Location: ../../../pages/pemilikUsaha/customer.php");
    exit();
}

//delete pelanggan
if(isset($_POST['deletePelanggan'])) {
    $idPelanggan = $_POST['idPelanggan'];
    $query = "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $fotoProfil = $row['fotoProfil'];
        $deleteQuery = "DELETE FROM pelanggan WHERE idPelanggan = '$idPelanggan'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM pelanggan WHERE fotoProfil = '$fotoProfil'";
            $checkResult = mysqli_query($conn, $checkQuery);
            $checkData = mysqli_fetch_assoc($checkResult);
            if($checkData['total'] == 0) {
                $gambarPath = "../../gambar/pelanggan/photoProfile/" . $fotoProfil;
                if(file_exists($gambarPath) && is_file($gambarPath)) {
                    if(!unlink($gambarPath)) {
                        $_SESSION['error'] = "Failed to delete image!";
                    }
                }
            }
            
            $_SESSION['success'] = "Customer data successfully deleted!";
        } else {
            $_SESSION['error'] = "Failed to delete data: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Data not found!";
    }
    header("Location: ../../../pages/pemilikUsaha/customer.php");
    exit();
}

//beli beras ke pemasok
if (isset($_POST["submitOrder"])) {
    $idPemilik = $_POST['idPemilik'];
    $idBeras       = $_POST['idBeras'];
    $idPemasok     = $_POST['idPemasok'];
    $jumlahPesanan = $_POST['jumlahPesanan'];
    $hargaBeli     = $_POST['hargaBeli']; 
    $tanggalPesanan = date('Y-m-d');
    $status        = "pending";
    
    $idBeras = mysqli_real_escape_string($conn, $idBeras);
    $idPemasok = mysqli_real_escape_string($conn, $idPemasok);
    $jumlahPesanan = (int)$jumlahPesanan;
    
    $queryInsert = mysqli_query(
        $conn,
        "INSERT INTO pesananpemasok 
           (tanggalPesanan, status, idPemasok, idBeras, jumlahPesanan, hargaBeli,idPemilik) 
         VALUES 
           ('$tanggalPesanan', '$status', '$idPemasok', '$idBeras', '$jumlahPesanan', '$hargaBeli','$idPemilik')"   
    );  

    if ($queryInsert) {
        $_SESSION['success'] = "Your order has been successfully created!";
        header("Location: ../../../pages/pemilikUsaha/orderSupplier.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to create order: " . mysqli_error($conn);
        header("Location: ../../../pages/pemilikUsaha/orderSupplier.php");
        exit();
    }
}

//buat nerima atau nolak pesanan pelanggan
if (isset($_POST['action']) && $_POST['action'] === 'confirm_order') {
    $idPesanan = intval($_POST['idPesanan']);
    $status    = $_POST['status'];

    // Validasi dasar
    if ($idPesanan <= 0 || ($status !== 'approved' && $status !== 'rejected')) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID or status'
        ]);
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        if ($status === 'approved') {
            $query = "SELECT idBeras, jumlahPesanan 
                      FROM pesananpemilik 
                      WHERE idPesanan = $idPesanan";
            $result = mysqli_query($conn, $query);
            $pesanan = mysqli_fetch_assoc($result);
            
            if (!$pesanan) {
                throw new Exception('Order not found');
            }
            
            $idBeras = $pesanan['idBeras'];
            $jumlahPesanan = $pesanan['jumlahPesanan'];

            // Update status pesanan
            $query = "UPDATE pesananpemilik 
                      SET status = 'approved', waktu_approve = NOW() 
                      WHERE idPesanan = $idPesanan";
            
            if (!mysqli_query($conn, $query)) {
                throw new Exception('Failed to approve order: ' . mysqli_error($conn));
            }

            // Kurangi stok
            $query = "UPDATE stokberaspemilik 
                      SET stokBeras = stokBeras - $jumlahPesanan 
                      WHERE idBeras = '$idBeras'";
            
            if (!mysqli_query($conn, $query)) {
                throw new Exception('Failed to update stock: ' . mysqli_error($conn));
            }
            mysqli_commit($conn);

            echo json_encode([
                'success' => true,
                'message' => 'Order approved successfully! Redirecting to order status...'
            ]);
        }
        elseif ($status === 'rejected') {
            $query = "DELETE FROM pesananpemilik 
                      WHERE idPesanan = $idPesanan";
            
            if (mysqli_query($conn, $query)) {
                mysqli_commit($conn);
                echo json_encode([
                    'success' => true,
                    'message' => 'Order rejected and removed successfully!'
                ]);
            } else {
                throw new Exception('Failed to reject order: ' . mysqli_error($conn));
            }
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

// Fungsi untuk update status pengiriman
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_status') {
        $orderId = $_POST['idPesanan'];
        $status = $_POST['status'];
        
        error_log("Update status request: idPesanan=$orderId, status=$status");
        if (!is_numeric($orderId)) {
            echo "error: Invalid order ID";
            exit();
        }
        $allowedStatus = ['Order Placed', 'Packaging', 'On The Road', 'Delivered', 'Completed'];
        if (!in_array($status, $allowedStatus)) {
            echo "error: Invalid status value";
            exit();
        }
        $query = "UPDATE pesananpemilik SET status_pengiriman = ? WHERE idPesanan = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
            
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo "success";
                } else {
                    echo "error: No rows affected. Order ID $orderId not found or status unchanged.";
                }
            } else {
                echo "error: " . mysqli_stmt_error($stmt);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "error: " . mysqli_error($conn);
        }
    }
    exit();
}

?>