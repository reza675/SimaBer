<?php
session_start();
if (!isset($_SESSION['namaPemilik'], $_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}
$nama      = $_SESSION['namaPemilik'];
$idPemilik = $_SESSION['idPemilik'];
$currentPage = 'report.php';
include '../../assets/mysql/connect.php';

// Get profile photo
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '$idPemilik'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);

// Get date range
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate   = $_GET['end_date']   ?? date('Y-m-t');

// —— 1) Revenue per SKU ——
$queryPendapatan = "
  SELECT
    sb.namaBeras,
    sb.beratBeras,
    SUM(pp.jumlahPesanan * pp.hargaBeli) AS total_pendapatan
  FROM pesananpemilik pp
  JOIN stokberaspemilik sb USING (idBeras)
  WHERE pp.idPemilik          = '$idPemilik'
    AND pp.tanggalPesanan    BETWEEN '$startDate' AND '$endDate'
    AND pp.status_pengiriman = 'Completed'
  GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
";
$resultPendapatan = mysqli_query($conn, $queryPendapatan);

// —— 2) Detail “Pembelian Beras” for expenses ——
$queryPembelianBeras = "
  SELECT
    sb.namaBeras,
    sb.beratBeras,
    SUM(pp.jumlahPesanan * pp.hargaBeli) AS total_pengeluaran
  FROM pesananpemilik pp
  JOIN stokberaspemilik sb USING (idBeras)
  WHERE pp.idPemilik          = '$idPemilik'
    AND pp.tanggalPesanan    BETWEEN '$startDate' AND '$endDate'
    AND pp.status_pengiriman = 'Completed'
  GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
";
$resultPembelianBeras = mysqli_query($conn, $queryPembelianBeras);

// —— 3) Fixed costs ——
$fixedCosts = [
  ['kategori'=>'Biaya Operasional', 'total'=>50000],
  ['kategori'=>'Label Beras',       'total'=>10000],
  ['kategori'=>'Dan lain-lain',     'total'=>20000],
];

// —— 4) Stock Masuk ——
$queryStokMasuk = "
  SELECT namaBeras, jenisBeras, stokBeras AS jumlah_masuk
    FROM stokberaspemilik
   WHERE idPemilik = '$idPemilik'
";
$resultStokMasuk = mysqli_query($conn, $queryStokMasuk);

// —— 5) Stock Keluar ——
$queryStokKeluar = "
  SELECT
    sb.namaBeras,
    sb.beratBeras,
    SUM(pp.jumlahPesanan) AS jumlah_keluar
  FROM pesananpemilik pp
  JOIN stokberaspemilik sb USING (idBeras)
  WHERE pp.idPemilik          = '$idPemilik'
    AND pp.tanggalPesanan    BETWEEN '$startDate' AND '$endDate'
    AND pp.status_pengiriman = 'Completed'
  GROUP BY sb.idBeras, sb.namaBeras, sb.beratBeras
";
$resultStokKeluar = mysqli_query($conn, $queryStokKeluar);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Report SimaBer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
  <link rel="icon" href="../../assets/gambar/icon.png">
</head>
<body class="bg-[#EFE9E2] min-h-screen">
  <?php include '../../layout/sidebarBusinessOwner.php'; ?>

  <div class="main-container ml-[300px] mt-4 mr-12">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold">Report</h1>
        <p class="text-[#5D5C61]">Report Information</p>
      </div>
      <div class="flex items-center space-x-4">
        <div class="relative flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
               class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
            <path fill-rule="evenodd"
                  d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                  clip-rule="evenodd"/>
          </svg>
          <form action="search.php" method="get">
            <input name="inputSearch" placeholder="Search"
                   class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition ease focus:outline-none"/>
          </form>
        </div>
        <div class="relative inline-block text-left">
          <button onclick="toggleDropdown()"
                  class="flex border-2 items-center rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-500 transition space-x-4">
            <img src="../../assets/gambar/pemilikUsaha/photoProfile/<?= $dataPemilikUsaha['fotoProfil'] ?? 'profil.jpeg' ?>"
                 alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply"/>
            <div class="hidden sm:block text-left">
              <span class="block text-lg font-bold text-black"><?= $nama; ?></span>
              <span class="block text-sm text-[#A2A1A8]">Business Owner</span>
            </div>
            <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div id="dropdownProfile" class="hidden absolute right-0 mt-2 bg-white border rounded-lg shadow-md z-50 w-48">
            <a href="settingsBusinessOwner.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
            <a href="../../assets/mysql/pemilikUsaha/proses.php?logout=true" class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log Out</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Period Time -->
    <div class="text-center mb-6">
      <h2 class="text-2xl font-bold mb-4">Period Time</h2>
      <form action="" method="GET" class="inline-flex items-center gap-2">
        <input type="date" name="start_date" value="<?= $startDate ?>" class="px-4 py-2 border rounded-lg text-sm"/>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
        <input type="date" name="end_date" value="<?= $endDate ?>" class="px-4 py-2 border rounded-lg text-sm"/>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </button>
      </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Revenue & Expenses -->
      <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-bold mb-2">Periode: <?= date('F Y', strtotime($startDate)) ?></h3>
        <!-- Pendapatan -->
        <p class="font-semibold mb-2">Rincian Pendapatan:</p>
        <?php
          $totalPendapatan = 0;
          $i = 1;
          mysqli_data_seek($resultPendapatan, 0);
          while($r = mysqli_fetch_assoc($resultPendapatan)):
            $totalPendapatan += $r['total_pendapatan'];
        ?>
          <div class="flex justify-between py-1 text-sm">
            <span><?= $i++ ?>. <?= "{$r['namaBeras']} {$r['beratBeras']}"; ?></span>
            <span>Rp <?= number_format($r['total_pendapatan'],0,',','.'); ?></span>
          </div>
        <?php endwhile; ?>
        <div class="border-t mt-2 pt-2 font-bold flex justify-between">
          <span>Total</span>
          <span>Rp <?= number_format($totalPendapatan,0,',','.'); ?></span>
        </div>

        <!-- Pengeluaran -->
        <div class="mt-6">
          <p class="font-semibold mb-2">Rincian Pengeluaran:</p>
          <!-- Pembelian Beras -->
          <p class="underline">1. Pembelian Beras:</p>
          <?php
            $i = 1; $totalPengeluaran = 0;
            mysqli_data_seek($resultPembelianBeras, 0);
            while($r = mysqli_fetch_assoc($resultPembelianBeras)):
              $totalPengeluaran += $r['total_pengeluaran'];
          ?>
            <div class="flex justify-between py-1 text-sm ml-4">
              <span><?= $i++ ?>. <?= "{$r['namaBeras']} {$r['beratBeras']}"; ?></span>
              <span>Rp <?= number_format($r['total_pengeluaran'],0,',','.'); ?></span>
            </div>
          <?php endwhile; ?>
          <!-- Biaya Lain-lain -->
          <p class="underline mt-3">2. Biaya Lain-lain:</p>
          <?php
            $j = 1;
            foreach($fixedCosts as $c):
              $totalPengeluaran += $c['total'];
          ?>
            <div class="flex justify-between py-1 text-sm ml-4">
              <span><?= $j++ ?>. <?= $c['kategori'] ?></span>
              <span>Rp <?= number_format($c['total'],0,',','.'); ?></span>
            </div>
          <?php endforeach; ?>
          <div class="border-t mt-2 pt-2 font-bold flex justify-between">
            <span>Total</span>
            <span>Rp <?= number_format($totalPengeluaran,0,',','.'); ?></span>
          </div>
        </div>

        <!-- Bersih -->
        <div class="mt-6 bg-blue-50 border-t-2 border-blue-200 rounded-lg p-3 flex justify-between font-bold text-blue-800">
          <span>Jumlah Pendapatan Bersih</span>
          <span>Rp <?= number_format($totalPendapatan - $totalPengeluaran,0,',','.'); ?></span>
        </div>
      </div>

      <!-- Stock -->
      <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-bold mb-2">Periode: <?= date('F Y', strtotime($startDate)) ?></h3>
        <p class="font-semibold mb-1">Rincian Stok Masuk:</p>
        <?php
          $sumIn = 0;
          while($r = mysqli_fetch_assoc($resultStokMasuk)):
            $sumIn += $r['jumlah_masuk'];
        ?>
          <div class="flex justify-between py-1 text-sm">
            <span><?= "{$r['namaBeras']} {$r['jenisBeras']}"; ?></span>
            <span><?= $r['jumlah_masuk'] ?></span>
          </div>
        <?php endwhile; ?>
        <div class="border-t mt-2 pt-2 font-bold flex justify-between">
          <span>Total</span><span><?= $sumIn ?></span>
        </div>

        <div class="mt-6">
          <p class="font-semibold mb-1">Rincian Stok Keluar:</p>
          <?php
            $sumOut = 0;
            while($r = mysqli_fetch_assoc($resultStokKeluar)):
              $sumOut += $r['jumlah_keluar'];
          ?>
            <div class="flex justify-between py-1 text-sm">
              <span><?= "{$r['namaBeras']} {$r['beratBeras']}"; ?></span>
              <span><?= $r['jumlah_keluar'] ?></span>
            </div>
          <?php endwhile; ?>
          <div class="border-t mt-2 pt-2 font-bold flex justify-between">
            <span>Total</span><span><?= $sumOut ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../../assets/cdn/flowbite.bundle.js"></script>
  <script>
    function toggleDropdown() {
      const dd = document.getElementById('dropdownProfile');
      dd.classList.toggle('hidden');
    }
    document.addEventListener('click', e => {
      const dd = document.getElementById('dropdownProfile');
      if (!e.target.closest('button[onclick="toggleDropdown()"]') && !dd.contains(e.target)) {
        dd.classList.add('hidden');
      }
    });
  </script>
</body>
</html>