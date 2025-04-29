<?php
include "../connect.php" ;
//register customer
 if(isset($_POST["registerCustomer"])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'];
    $telephone = $_POST['telephone'];
    $address = $_POST['address'];
    $zipcode = $_POST['zipcode'];
    $query1 = mysqli_query($conn, "SELECT * FROM pelanggan");
    while($data=mysqli_fetch_array($query1)){
        if($fullname == $data['namaPelanggan']){
            header("Location:../../../pages/register/register.php?register=gagal_daftar");
            exit();
        }
    }
    $query = mysqli_query($conn,"INSERT INTO pelanggan VALUES ('','$email','$password','$fullname','$telephone','$address','$zipcode')");
    header("Location:../../../pages/register/register.php?register=berhasil");
  
  }

?>