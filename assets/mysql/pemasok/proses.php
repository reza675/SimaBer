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
//add data beras
if(isset($_POST['addBeras'])) {
    $namaBeras = $_POST['namaBeras'];
    $tipeBeras = $_POST['jenisBeras'];
    $beratBeras = $_POST['beratBeras'];
    $hargaJual = $_POST['hargaJual'];
    $stokBeras = $_POST['stokBeras'];
    $idPemasok = $_POST['idPemasok'];

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
                        header("Location: ../../../pages/pemasok/riceManagement.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Image size is too large (Max 5MB))";
                    header("Location: ../../../pages/pemasok/riceManagement.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Unsupported file formats (JPG, JPEG, PNG, WEBP only)";
                header("Location: ../../../pages/pemasok/riceManagement.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "An error occurred while uploading the image";
            header("Location: ../../../pages/pemasok/riceManagement.php");
            exit();
        }
    }

    // 3. Query insert ke database
    $query = "INSERT INTO stokberaspemasok  (
        idBeras, 
        namaBeras, 
        jenisBeras, 
        beratBeras, 
        hargaJual,
        stokBeras, 
        idPemasok,
        gambarBeras
    ) VALUES (
        '',
        '$namaBeras',
        '$tipeBeras',
        '$beratBeras',
        '$hargaJual',
        '$stokBeras',
        '$idPemasok',
        '$gambarBeras'
    )";

    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Rice data successfully added";
    } else {
        $_SESSION['error'] = "Failed to add data: " . mysqli_error($conn);
    }
    header("Location: ../../../pages/pemasok/riceManagement.php");
    exit();
}

//edit data beras
if(isset($_POST['editBeras'])) {
    $idBeras = $_POST['idBeras'];
    $namaBeras = $_POST['namaBeras'];
    $tipeBeras = $_POST['jenisBeras'];
    $beratBeras = $_POST['beratBeras'];
    $hargaJual = $_POST['hargaJual'];
    $stokBeras = $_POST['stokBeras'];
    $idPemasok = $_POST['supplierBeras'];

    $queryGetGambar = "SELECT gambarBeras FROM stokberaspemasok WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $queryGetGambar);
    $row = mysqli_fetch_assoc($result);
    $gambarLama = $row['gambarBeras'];
    
    $gambarBaru = $gambarLama;
    if (!empty($_FILES['gambarBeras']['name'])) {
    $targetDir = "../../gambar/beras/";
    $ext = strtolower(pathinfo($_FILES['gambarBeras']['name'], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '_' . preg_replace('/\s+/', '_', $_FILES['gambarBeras']['name']);
    $newFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $newFileName); 
    $uploadPath = $targetDir . $newFileName;
    $check     = getimagesize($_FILES['gambarBeras']['tmp_name']);

    if ($check === false) {
        $_SESSION['error'] = "File is not an image!";
        header("Location: ../../../pages/pemasok/riceManagement.php");
        exit();
    }
    if ($_FILES['gambarBeras']['size'] > 5000000) {
        $_SESSION['error'] = "Ukuran file terlalu besar (max 5MB)!";
        header("Location: ../../../pages/pemasok/riceManagement.php");
        exit();
    }
    $allowed = ['jpg','jpeg','png','webp'];
    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG,WEBP formats are allowed!";
        header("Location: ../../../pages/pemasok/riceManagement.php");
        exit();
    }

    if (move_uploaded_file($_FILES['gambarBeras']['tmp_name'], $uploadPath)) {
        $oldPath = $targetDir . $gambarLama;
        if (file_exists($oldPath) && is_file($oldPath)) {
            unlink($oldPath);
        }
        $gambarBaru = $newFileName;
    } else {
        $_SESSION['error'] = "Failed to upload image!";
        header("Location: ../../../pages/pemasok/riceManagement.php");
        exit();
    }
}
    $q = "UPDATE stokberaspemasok SET
          namaBeras='$namaBeras',
          jenisBeras='$tipeBeras',
          beratBeras='$beratBeras',
          hargaJual='$hargaJual', 
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
//delete beras
if(isset($_POST['deleteBeras'])) {
    $idBeras = $_POST['idBeras'];
    $query = "SELECT gambarBeras FROM stokberaspemasok WHERE idBeras = '$idBeras'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $gambarBeras = $row['gambarBeras'];
        $deleteQuery = "DELETE FROM stokberaspemasok WHERE idBeras = '$idBeras'";
        if(mysqli_query($conn, $deleteQuery)) {
            $checkQuery = "SELECT COUNT(*) AS total FROM stokberaspemasok WHERE gambarBeras = '$gambarBeras'";
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
    header("Location: ../../../pages/pemasok/riceManagement.php");
    exit();
}


//buat nerima atau nolak pesanan pemilikusaha
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
                      FROM pesananpemasok 
                      WHERE idPesanan = $idPesanan";
            $result = mysqli_query($conn, $query);
            $pesanan = mysqli_fetch_assoc($result);
            
            if (!$pesanan) {
                throw new Exception('Order not found');
            }
            
            $idBeras = $pesanan['idBeras'];
            $jumlahPesanan = $pesanan['jumlahPesanan'];

            // Update status pesanan
            $query = "UPDATE pesananpemasok 
                      SET status = 'approved', waktu_approve = NOW() 
                      WHERE idPesanan = $idPesanan";
            
            if (!mysqli_query($conn, $query)) {
                throw new Exception('Failed to approve order: ' . mysqli_error($conn));
            }

            // Kurangi stok
            $query = "UPDATE stokberaspemasok 
                      SET stokBeras = stokBeras - $jumlahPesanan 
                      WHERE idBeras = $idBeras";
            
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
            $query = "DELETE FROM pesananpemasok 
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
        $allowedStatus = ['Order Placed', 'Packaging', 'On The Road', 'Delivered','Completed'];
        if (!in_array($status, $allowedStatus)) {
            echo "error: Invalid status value";
            exit();
        }

        // Update database
        $query = "UPDATE pesananpemasok SET status_pengiriman = ? WHERE idPesanan = ?";
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