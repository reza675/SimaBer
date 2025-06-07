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
        $_SESSION['error'] = "Only JPG, JPEG, PNG files are allowed.";
    } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $q = "UPDATE pelanggan SET fotoProfil='$fotoProfil' WHERE idPelanggan='$id'";
        if (mysqli_query($conn, $q)) {
            $_SESSION['success'] = "Photo profile successfully changed!";
            $_SESSION['fotoProfil'] = $fotoProfil;
        } else {
            $_SESSION['error'] = "Error DB: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Failed to upload file.";
    }
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}
//edit data customer
if (isset($_POST['submitEdit'])) {
    $id = $_POST['idPelanggan'];
    $email = $_POST['email'];
    $nama = $_POST['nama'];
    $telepon= $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $kodepos= $_POST['kodepos'];
    $q = "UPDATE pelanggan SET
          emailPelanggan='$email',
          namaPelanggan='$nama',
          teleponPelanggan='$telepon',
          alamatPelanggan='$alamat',
          kodeposPelanggan='$kodepos'
          WHERE idPelanggan='$id'";
    if (mysqli_query($conn, $q)) {
        $_SESSION['success'] = "Profile data successfully updated!";
        $_SESSION['namaPelanggan'] = $nama;
    } else {
        $_SESSION['error'] = "Error: ".mysqli_error($conn);
    }
    header("Location:../../../pages/pelanggan/settingsCustomer.php");
    exit();
}

//route dan condition beli beras
if (isset($_POST['beliBeras'])) {
    $idPelanggan  = $_POST['idPelanggan'];
    $idBeras      = $_POST['idBeras'];
    $jumlahBeras  = $_POST['quantity'];
    $hargaBeras   = $_POST['harga'];
    $from         = isset($_POST['from']) ? $_POST['from'] : 'orderCustomer.php';

    // Gunakan prepared statement untuk keamanan
    $sqlStok = "SELECT beratBeras, stokBeras, namaBeras, gambarBeras FROM stokberasPemilik WHERE idBeras = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sqlStok);
    mysqli_stmt_bind_param($stmt, "i", $idBeras);
    mysqli_stmt_execute($stmt);
    $resStok = mysqli_stmt_get_result($stmt);
    
    if (!$resStok || mysqli_num_rows($resStok) === 0) {
        $_SESSION['error'] = "Product not found.";
        header("Location: ../../../pages/pelanggan/detailProduct.php?id=$idBeras&from=$from");
        exit();
    }
    
    $row = mysqli_fetch_assoc($resStok);
    $stok = $row['stokBeras'];

    // Validasi jumlah
    if ($jumlahBeras < 1) {
        $_SESSION['error'] = "Quantity must be at least 1 kg.";
        header("Location: ../../../pages/pelanggan/detailProduct.php?id=$idBeras&from=$from");
        exit();
    }
    
    if ($jumlahBeras > $stok) {
        $_SESSION['error'] = "Sorry, we only have $stok kg of this product.";
        header("Location: ../../../pages/pelanggan/detailProduct.php?id=$idBeras&from=$from");
        exit();
    }

    // simpan data sebelum redirect ke checkout1
    $_SESSION['checkout_data'] = [
        'idPelanggan' => $idPelanggan,
        'idBeras' => $idBeras,
        'namaBeras' => $row['namaBeras'],
        'beratBeras' => $row['beratBeras'],
        'gambarBeras' => $row['gambarBeras'],
        'quantity' => $jumlahBeras,
        'hargaSatuan' => $hargaBeras,
        'totalHarga' => $hargaBeras * $jumlahBeras,
        'stokTersedia' => $stok
    ];

    // Redirect ke checkout dengan data yang sudah disimpan di session
    header("Location: ../../../pages/pelanggan/checkout1.php");
    exit();
}

//validasi ke 2
if (isset($_POST['checkout_action']) && $_POST['checkout_action'] === 'continue_to_payment') {
    if (!isset($_SESSION['checkout_data'])) {
        $_SESSION['error'] = "No checkout data found. Please start over.";
        header("Location: ../../../pages/pelanggan/orderCustomer.php");
        exit();
    }
    $selectedShipping = $_POST['selected_shipping'];
    $shippingCost = $_POST['shipping_cost'];
    $finalTotal = $_POST['final_total'];
    $_SESSION['checkout_data']['shippingMethod'] = $selectedShipping;
    $_SESSION['checkout_data']['shippingCost'] = $shippingCost;
    $_SESSION['checkout_data']['finalTotal'] = $finalTotal;
    $expectedTotal = $_SESSION['checkout_data']['totalHarga'] + $shippingCost;
    if ($finalTotal != $expectedTotal) {
        $_SESSION['error'] = "Invalid total calculation. Please try again.";
        header("Location: ../../../pages/pelanggan/checkout1.php");
        exit();
    }

    header("Location: ../../../pages/pelanggan/checkout2.php");
    exit();
}

//validasi ke 3 - Complete Order
if (isset($_POST['checkout_action']) && $_POST['checkout_action'] === 'complete_order') {
    if (!isset($_SESSION['checkout_data'])) {
        $_SESSION['error'] = "No checkout data found. Please start over.";
        header("Location: ../../../pages/pelanggan/orderCustomer.php");
        exit();
    }

    $checkoutData = $_SESSION['checkout_data'];
    $paymentMethod = $_POST['payment_method'];
    $recipientName = mysqli_real_escape_string($conn, $_POST['recipient_name']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $deliveryNotes = isset($_POST['delivery_notes']) ? mysqli_real_escape_string($conn, $_POST['delivery_notes']) : '';
    $tanggalPesanan = date('Y-m-d');
    $status = ($paymentMethod === 'qris') ? 'Menunggu Pembayaran' : 'Dikonfirmasi';
    
    // Set delivery flag
    $isDeliver = ($checkoutData['shippingMethod'] === 'delivery') ? 1 : 0;
    
    // Prepare delivery notes with recipient info
    $fullDeliveryNotes = "Penerima: $recipientName, Telp: $phoneNumber";
    if (!empty($deliveryNotes)) {
        $fullDeliveryNotes .= ", Catatan: $deliveryNotes";
    }
    
    // Insert order into database
    $insertQuery = "INSERT INTO pesananpemilik (
        tanggalPesanan, 
        status, 
        idPelanggan, 
        idBeras, 
        jumlahPesanan, 
        hargaBeli, 
        isDeliver, 
        deliverNotes
    ) VALUES (
        '$tanggalPesanan',
        '$status',
        '{$_SESSION['idPelanggan']}',
        '{$checkoutData['idBeras']}',
        '{$checkoutData['quantity']}',
        '{$checkoutData['finalTotal']}',
        '$isDeliver',
        '$fullDeliveryNotes'
    )";
    
    if (mysqli_query($conn, $insertQuery)) {
        $orderID = mysqli_insert_id($conn);
        
        // Update stock beras (reduce quantity)
        $updateStockQuery = "UPDATE stokberasPemilik 
                           SET stokBeras = stokBeras - {$checkoutData['quantity']} 
                           WHERE idBeras = '{$checkoutData['idBeras']}'";
        mysqli_query($conn, $updateStockQuery);
        
        // Clear checkout data from session
        unset($_SESSION['checkout_data']);
        
        // Set success message with order details
        $_SESSION['success'] = "Pesanan berhasil dibuat! ID Pesanan: #$orderID. Status: $status";
        
        // Redirect based on payment method
        if ($paymentMethod === 'qris') {
            // Redirect to payment page or show QRIS code
            header("Location: ../../../pages/pelanggan/paymentQRIS.php?order_id=$orderID");
        } else {
            // For COD, redirect back to checkout2 with success parameter
            header("Location: ../../../pages/pelanggan/checkout2.php?success=1&order_id=$orderID");
        }
        exit();
    } else {
        $_SESSION['error'] = "Gagal membuat pesanan. Silakan coba lagi.";
        header("Location: ../../../pages/pelanggan/checkout2.php");
        exit();
    }
}

?>