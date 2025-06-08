<?php

session_start();
if (!isset($_SESSION['namaPemilik'], $_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}

require_once('../../assets/php/fpdf/fpdf.php');
include '../../assets/mysql/connect.php';

$namaPemilik = $_SESSION['namaPemilik'];
$idPemilik   = $_SESSION['idPemilik'];

// Ambil periode dari query string, default awal dan akhir bulan ini
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate   = $_GET['end_date']   ?? date('Y-m-t');

class PDF extends FPDF {
    // Header dokumen
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'Laporan Usaha Beras SimaBer',0,1,'C');
        $this->Ln(5);
    }

    // Footer dokumen
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Inisialisasi PDF
$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Tampilkan metadata periode dan pemilik
$pdf->Cell(0,8,"Pemilik: $namaPemilik",0,1);
$pdf->Cell(0,8,"Periode: $startDate s.d. $endDate",0,1);
$pdf->Ln(5);

// 1) Pendapatan per SKU
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'1. Pendapatan per SKU',0,1);
$pdf->SetFont('Arial','',11);

$stmt = $conn->prepare("
    SELECT sb.namaBeras, sb.beratBeras, SUM(pp.jumlahPesanan*pp.hargaBeli) AS total_pendapatan
    FROM pesananpemilik pp
    JOIN stokberaspemilik sb USING(idBeras)
    WHERE pp.idPemilik = ? 
      AND pp.tanggalPesanan BETWEEN ? AND ?
      AND pp.status_pengiriman = 'Completed'
    GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
");
$stmt->bind_param('iss', $idPemilik, $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();

$totalPendapatan = 0;
while ($row = $res->fetch_assoc()) {
    $nama = "{$row['namaBeras']} {$row['beratBeras']} kg";
    $val  = number_format($row['total_pendapatan'],0,',','.');
    $pdf->Cell(120,6, "- $nama",0,0);
    $pdf->Cell(0,6, "Rp $val",0,1,'R');
    $totalPendapatan += $row['total_pendapatan'];
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,6,'Total Pendapatan',0,0);
$pdf->Cell(0,6,"Rp ".number_format($totalPendapatan,0,',','.'),0,1,'R');
$pdf->Ln(8);

// 2) Pengeluaran: Pembelian Beras
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'2. Pengeluaran - Pembelian Beras',0,1);
$pdf->SetFont('Arial','',11);

$stmt = $conn->prepare("
    SELECT sb.namaBeras, sb.beratBeras, SUM(pp.jumlahPesanan*pp.hargaBeli) AS total_pengeluaran
    FROM pesananpemasok pp
    JOIN stokberaspemasok sb USING(idBeras)
    WHERE pp.idPemilik = ?
      AND pp.tanggalPesanan BETWEEN ? AND ?
      AND pp.status_pengiriman = 'Completed'
    GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
");
$stmt->bind_param('iss', $idPemilik, $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();

$totalPengeluaran = 0;
while ($row = $res->fetch_assoc()) {
    $nama = "{$row['namaBeras']} {$row['beratBeras']} kg";
    $val  = number_format($row['total_pengeluaran'],0,',','.');
    $pdf->Cell(120,6, "- $nama",0,0);
    $pdf->Cell(0,6, "Rp $val",0,1,'R');
    $totalPengeluaran += $row['total_pengeluaran'];
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,6,'Total Pembelian Beras',0,0);
$pdf->Cell(0,6,"Rp ".number_format($totalPengeluaran,0,',','.'),0,1,'R');
$pdf->Ln(8);

// 3) Pengeluaran: Biaya Lain-lain
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'3. Pengeluaran - Biaya Lain-lain',0,1);
$pdf->SetFont('Arial','',11);

$stmt = $conn->prepare("
    SELECT namaBiaya, SUM(jumlahBiaya) AS total
    FROM biaya_lain
    WHERE idPemilik = ?
      AND tanggalBiaya BETWEEN ? AND ?
    GROUP BY idBiaya, namaBiaya
");
$stmt->bind_param('iss', $idPemilik, $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $nama = $row['namaBiaya'];
    $val  = number_format($row['total'],0,',','.');
    $pdf->Cell(120,6, "- $nama",0,0);
    $pdf->Cell(0,6, "Rp $val",0,1,'R');
    $totalPengeluaran += $row['total'];
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,6,'Total Pengeluaran',0,0);
$pdf->Cell(0,6,"Rp ".number_format($totalPengeluaran,0,',','.'),0,1,'R');
$pdf->Ln(8);

// 4) Pendapatan Bersih
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'4. Pendapatan Bersih',0,1);
$pdf->SetFont('Arial','',11);
$net = $totalPendapatan - $totalPengeluaran;
$pdf->Cell(120,6,'Pendapatan Bersih',0,0);
$pdf->Cell(0,6,"Rp ".number_format($net,0,',','.'),0,1,'R');
$pdf->Ln(8);

// 5) Stok Masuk
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'5. Rincian Stok Masuk',0,1);
$pdf->SetFont('Arial','',11);

$stmt = $conn->prepare("
    SELECT namaBeras, beratBeras, SUM(stokBeras) AS jumlah_masuk
    FROM stokberaspemilik
    WHERE idPemilik = ?
      AND tanggalMasuk BETWEEN ? AND ?
    GROUP BY idBeras, namaBeras, beratBeras
");
$stmt->bind_param('iss', $idPemilik, $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();

$totalMasuk = 0;
while ($row = $res->fetch_assoc()) {
    $nama = "{$row['namaBeras']} {$row['beratBeras']} kg";
    $qty  = $row['jumlah_masuk'];
    $pdf->Cell(120,6, "- $nama",0,0);
    $pdf->Cell(0,6, "$qty",0,1,'R');
    $totalMasuk += $qty;
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,6,'Total Stok Masuk',0,0);
$pdf->Cell(0,6,"$totalMasuk",0,1,'R');
$pdf->Ln(8);

// 6) Stok Keluar
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'6. Rincian Stok Keluar',0,1);
$pdf->SetFont('Arial','',11);

$stmt = $conn->prepare("
    SELECT sb.namaBeras, sb.beratBeras, SUM(pp.jumlahPesanan) AS jumlah_keluar
    FROM pesananpemilik pp
    JOIN stokberaspemilik sb USING(idBeras)
    WHERE pp.idPemilik = ?
      AND pp.tanggalPesanan BETWEEN ? AND ?
      AND pp.status_pengiriman = 'Completed'
    GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
");
$stmt->bind_param('iss', $idPemilik, $startDate, $endDate);
$stmt->execute();
$res = $stmt->get_result();

$totalKeluar = 0;
while ($row = $res->fetch_assoc()) {
    $nama = "{$row['namaBeras']} {$row['beratBeras']} kg";
    $qty  = $row['jumlah_keluar'];
    $pdf->Cell(120,6, "- $nama",0,0);
    $pdf->Cell(0,6, "$qty",0,1,'R');
    $totalKeluar += $qty;
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,6,'Total Stok Keluar',0,0);
$pdf->Cell(0,6,"$totalKeluar",0,1,'R');

// Output PDF ke browser untuk di-download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Laporan_SimaBer_'.$startDate.'_'.$endDate.'.pdf"');
$pdf->Output('I','Laporan_SimaBer.pdf');

exit;
