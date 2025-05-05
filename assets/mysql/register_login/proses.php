<?php
session_start();
include "../connect.php";

// Register customer
if (isset($_POST["registerCustomer"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $telephone = $_POST['telephone'];
    $address = $_POST['address'];
    $zipcode = $_POST['zipcode'];

    $query1 = mysqli_query($conn, "SELECT * FROM pelanggan");
    while ($data = mysqli_fetch_array($query1)) {
        if ($fullname == $data['namaPelanggan']) {
            header("Location:../../../pages/register/register.php?register=gagal_daftar");
            exit();
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = mysqli_query($conn, "INSERT INTO pelanggan VALUES ('','$email','$hashedPassword','$fullname','$telephone','$address','$zipcode','profil.jpeg')");
    header("Location:../../../pages/register/register.php?register=berhasil");
    exit();
}

// Forgot password
require '../../phpmailer/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['forgotPassword'])) {
    $email = $_POST['email'];
    $check = mysqli_query($conn, "SELECT * FROM pelanggan WHERE emailPelanggan='$email'");

    if ($check->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        $_SESSION['changePass'] = true;

        $otp = rand(100000, 999999);
        $expired = date("Y-m-d H:i:s", strtotime("+10 minutes"));
        $conn->query("INSERT INTO otp_codes (email, code, expired_at) VALUES ('$email', '$otp', '$expired')");

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'simaber.ep@gmail.com';
            $mail->Password = 'gavb aqty faac uyhj';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('simaber.ep@gmail.com', 'SimaBer');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "Your OTP code is <b>$otp</b>. It will expire in 10 minutes.";

            $mail->send();
            header("Location:../../../pages/login/otp_verification.php?verifikasi_otp=true");
            exit();
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        header("Location:../../../pages/login/forgotPassword.php?error=errorEmail");
    }
}

// Verify OTP
if (isset($_POST["verifyOTP"])) {
    $otp_input = implode("", $_POST['otp']);

    if (!isset($_SESSION['reset_email'])) {
        header("Location: forgotPassword.php");
        exit;
    }

    $email = $_SESSION['reset_email'];

    $query = mysqli_query($conn, "SELECT * FROM otp_codes WHERE email = '$email' ORDER BY id DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $stored_otp = $data['code'];
        $expired = strtotime($data['expired_at']);
        $now = time();

        if ($otp_input === $stored_otp && $now < $expired) {
            $_SESSION['otp_verified'] = true;
            header("Location:../../../pages/login/newPassword.php");
            exit;
        }
    }

    header("Location:../../../pages/login/otp_verification.php?error=invalid");
    exit;
}

// New Password
if (isset($_POST["newPassword"])) {
    if (!isset($_SESSION['reset_email'])) {
        header("Location: forgotPassword.php");
        exit();
    }

    $email = $_SESSION['reset_email'];
    $newPassword = $_POST['newPasswordUser'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateQuery = mysqli_query($conn, "UPDATE pelanggan SET passwordPelanggan = '$hashedPassword' WHERE emailPelanggan = '$email'");

    if ($updateQuery) {
        header("Location: ../../../pages/login/newPassword.php?newPassword=berhasil");
        exit();
    } else {
        echo "Error updating password.";
    }
}
//login customer
if (isset($_POST['loginCustomer'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE emailPelanggan = '$email'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $hashedPassword = $data['passwordPelanggan'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['idPelanggan'] = $data['idPelanggan'];
            $_SESSION['namaPelanggan'] = $data['namaPelanggan'];
            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (7 * 24 * 60 * 60), "/");
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }
            header("Location:../../../pages/pelanggan/dashboardCustomer.php?login=berhasil");
            exit();
        }
    }
    header("Location:../../../pages/login/loginCustomer.php?login=gagal");
    exit();
}
//login business owner
if (isset($_POST['loginBusinessOwner'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM pemilikusaha WHERE emailPemilik = '$email'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $hashedPassword = $data['passwordPemilik'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['idPemilik'] = $data['idPemilik'];
            $_SESSION['namaPemilik'] = $data['namaPemilik'];
            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (7 * 24 * 60 * 60), "/");
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }
            header("Location:../../../pages/pemilikUsaha/dashboardBusinessOwner.php?login=berhasil");
            exit();
        }
    }
    header("Location:../../../pages/login/loginBusinessOwner.php?login=gagal");
    exit();
}

//login Supplier
if (isset($_POST['loginSupplier'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM pemasok WHERE emailPemasok = '$email'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data) {
        $hashedPassword = $data['passwordPemilik'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['idPemasok'] = $data['idPemasok'];
            $_SESSION['namaPemasok'] = $data['namaPemasok'];
            if (isset($_POST['remember'])) {
                setcookie("remember_email", $email, time() + (7 * 24 * 60 * 60), "/");
            } else {
                setcookie("remember_email", "", time() - 3600, "/");
            }
            header("Location:../../../pages/pemasok/dashboardSupplier.php?login=berhasil");
            exit();
        }
    }
    header("Location:../../../pages/login/loginSupplier.php?login=gagal");
    exit();
}
?>