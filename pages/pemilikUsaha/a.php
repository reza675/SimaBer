<?php
//delete biaya lain
include '../../assets/mysql/connect.php';
echo "<script>console.log('Nama Biaya: ')</script>";
if (isset($_POST['hapusBiayaLain'])) {
    $nama = $_POST['namaBiaya'];
    
echo "<script>console.log('Nama')</script>";
    $startDate = $_POST['start_date'] ?? date('Y-m-01');
    $endDate   = $_POST['end_date']   ?? date('Y-m-t');
    $idPemilik = $_POST['idPemilik'];
    
    $sql = "DELETE FROM biaya_lain WHERE idPemilik = '$idPemilik' AND namaBiaya = '$nama' AND tanggalBiaya BETWEEN '$startDate' AND '$endDate'";
    mysqli_query($conn, $sql);
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['success'] = "Cost “{$nama}” successfully deleted!";
        header("Location:report.php?start_date={$startDate}&end_date={$endDate}");
    }
    else {
        $_SESSION['error'] = "Error to delete cost! ";
        header("Location:report.php?start_date={$startDate}&end_date={$endDate}");
    }
    
    exit();
}

?>