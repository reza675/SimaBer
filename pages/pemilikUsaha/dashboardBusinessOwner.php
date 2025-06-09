<?php
session_start();
if (!isset($_SESSION['namaPemilik']) || !isset($_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}
$nama = $_SESSION['namaPemilik'];
$idPemilik = $_SESSION['idPemilik'];
$currentPage = 'dashboardBusinessOwner.php';
include '../../assets/mysql/connect.php';

// Get profile photo
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '$idPemilik'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);

$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM stokberaspemilik WHERE idPemilik = '$idPemilik') as totalStokBeras,
        (SELECT COUNT(*) FROM pesananpemilik WHERE status_pengiriman != 'Completed' AND status = 'approved' AND idPemilik = '$idPemilik') as totalStatusCustomer,
        (SELECT COUNT(DISTINCT idPemasok) FROM stokberaspemilik WHERE idPemilik = '$idPemilik') as totalSupplier,
        (SELECT COUNT(*) FROM pelanggan) as totalPelanggan
";
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

// Get recent rice stock data
$recentStockQuery = "
    SELECT sb.namaBeras, sb.jenisBeras, sb.beratBeras, sb.stokBeras, sb.hargaBeliBeras, sb.hargaJualBeras, 
           p.namaPemasok, sb.tanggalMasuk
    FROM stokberaspemilik sb 
    LEFT JOIN pemasok p ON sb.idPemasok = p.idPemasok 
    WHERE sb.idPemilik = '$idPemilik' 
    ORDER BY sb.tanggalMasuk DESC 
    LIMIT 5
";
$recentStock = mysqli_query($conn, $recentStockQuery);

// Get low stock items (less than 50kg)
$lowStockQuery = "
    SELECT namaBeras, jenisBeras, stokBeras,  beratBeras
    FROM stokberaspemilik 
    WHERE idPemilik = '$idPemilik' AND stokBeras < 10
    ORDER BY stokBeras ASC 
    LIMIT 5
";
$lowStock = mysqli_query($conn, $lowStockQuery);

//chart
$topSellingQuery = "
    SELECT 
        sb.namaBeras,
        sb.beratBeras,
        SUM(pp.jumlahPesanan) AS totalJumlah,
        SUM(pp.jumlahPesanan * sb.hargaJualBeras) AS totalRevenue
    FROM pesananpemilik pp
    JOIN stokberaspemilik sb 
      ON pp.idBeras = sb.idBeras
    WHERE pp.idPemilik    = '$idPemilik'
      AND pp.status_pengiriman NOT IN ('Cancelled','Failed')
    GROUP BY sb.namaBeras, sb.beratBeras
    ORDER BY totalJumlah DESC
    LIMIT 5
";
$topSelling = mysqli_query($conn, $topSellingQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarBusinessOwner.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <!-- Header Section -->
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Dashboard</p>
                <p class="text-l text-[#5D5C61] font-regular">Dashboard Business Owner</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                        <path fill-rule="evenodd"
                            d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <form action="search.php" method="get">
                        <input name="inputSearch"
                            class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                            placeholder="Search" />
                    </form>
                </div>

                <svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="50" height="50" rx="10" fill="#EFE9E2" />
                    <path
                        d="M18.6796 21.794C19.0538 18.4909 21.7709 16 25 16C28.2291 16 30.9462 18.4909 31.3204 21.794L31.6652 24.8385C31.7509 25.595 32.0575 26.3069 32.5445 26.88C33.5779 28.0964 32.7392 30 31.1699 30H18.8301C17.2608 30 16.4221 28.0964 17.4555 26.88C17.9425 26.3069 18.2491 25.595 18.3348 24.8385L18.6796 21.794Z"
                        stroke="#16151C" stroke-width="1.5" stroke-linejoin="round" />
                    <path d="M28 32C27.5633 33.1652 26.385 34 25 34C23.615 34 22.4367 33.1652 22 32" stroke="#16151C"
                        stroke-width="1.5" stroke-linecap="round" />
                </svg>

                <div class="relative inline-block text-left">
                    <button onclick="toggleDropdown()"
                        class="flex border-2 border-solid items-center bg-none rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-500 transition space-x-4">
                        <img src="../../assets/gambar/pemilikUsaha/photoProfile/<?= $dataPemilikUsaha['fotoProfil'] ?? 'profil.jpeg' ?>"
                            alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
                        <div class="text-left hidden sm:block">
                            <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                            <span class="block font-semibold text-sm text-[#A2A1A8] leading-4">Business Owner</span>
                        </div>
                        <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownProfile"
                        class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-md z-50 w-48">
                        <a href="settingsBusinessOwner.php"
                            class="block font-semibold px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                        <a href="../../assets/mysql/pemilikUsaha/proses.php?logout=true"
                            class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log
                            Out</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['search']) && $_GET['search'] === 'notfound') : ?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <p>Keyword not found</p>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <a href="riceStock.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Total Type of Rice</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalStokBeras'] ?? 0 ?></p>
                            <p class="text-xs text-green-600 font-medium">Types Available</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            <a href="orderStatusCustomer.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Status Order Customer</p>
                            <p class="text-3xl font-bold text-[#16151C]">
                                <?= number_format($stats['totalStatusCustomer'] ?? 0) ?>
                            </p>
                            <p class="text-xs text-blue-600 font-medium">Order in Process</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.5 22H10.889C6.699 22 4.604 22 3.302 20.745C2 19.489 2 17.469 2 13.429V8H22V10.5"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M18 16H18.009M2 8L2.962 5.692C3.707 3.902 4.08 3.008 4.836 2.504C5.592 2 6.56 2 8.5 2H15.5C17.439 2 18.408 2 19.164 2.504C19.92 3.008 20.293 3.903 21.038 5.692L22 8M12 8V2M10 12H12M18 12C15.79 12 14 13.809 14 16.04C14 17.316 14.5 18.308 15.5 19.195C16.205 19.819 17.059 20.857 17.571 21.698C17.817 22.101 18.165 22.101 18.429 21.698C18.967 20.873 19.795 19.819 20.5 19.195C21.5 18.308 22 17.316 22 16.04C22 13.81 20.21 12 18 12Z"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            <a href="supplier.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Total Supplier</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalSupplier'] ?? 0 ?></p>
                            <p class="text-xs text-purple-600 font-medium">Active</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-purple-600" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.5 8C5.5 7.33696 5.76339 6.70107 6.23223 6.23223C6.70107 5.76339 7.33696 5.5 8 5.5C8.66304 5.5 9.29893 5.76339 9.76777 6.23223C10.2366 6.70107 10.5 7.33696 10.5 8C10.5 8.66304 10.2366 9.29893 9.76777 9.76777C9.29893 10.2366 8.66304 10.5 8 10.5C7.33696 10.5 6.70107 10.2366 6.23223 9.76777C5.76339 9.29893 5.5 8.66304 5.5 8ZM8 4C6.93913 4 5.92172 4.42143 5.17157 5.17157C4.42143 5.92172 4 6.93913 4 8C4 9.06087 4.42143 10.0783 5.17157 10.8284C5.92172 11.5786 6.93913 12 8 12C9.06087 12 10.0783 11.5786 10.8284 10.8284C11.5786 10.0783 12 9.06087 12 8C12 6.93913 11.5786 5.92172 10.8284 5.17157C10.0783 4.42143 9.06087 4 8 4ZM15.5 9C15.5 8.60218 15.658 8.22064 15.9393 7.93934C16.2206 7.65804 16.6022 7.5 17 7.5C17.3978 7.5 17.7794 7.65804 18.0607 7.93934C18.342 8.22064 18.5 8.60218 18.5 9C18.5 9.39782 18.342 9.77936 18.0607 10.0607C17.7794 10.342 17.3978 10.5 17 10.5C16.6022 10.5 16.2206 10.342 15.9393 10.0607C15.658 9.77936 15.5 9.39782 15.5 9ZM17 6C16.2044 6 15.4413 6.31607 14.8787 6.87868C14.3161 7.44129 14 8.20435 14 9C14 9.79565 14.3161 10.5587 14.8787 11.1213C15.4413 11.6839 16.2044 12 17 12C17.7956 12 18.5587 11.6839 19.1213 11.1213C19.6839 10.5587 20 9.79565 20 9C20 8.20435 19.6839 7.44129 19.1213 6.87868C18.5587 6.31607 17.7956 6 17 6ZM14.248 19.038C14.951 19.323 15.852 19.5 17.001 19.5C19.283 19.5 20.587 18.803 21.298 17.942C21.643 17.524 21.818 17.102 21.908 16.779C21.9595 16.5919 21.9907 16.3998 22.001 16.206V16.179C22.0007 15.6012 21.7711 15.0471 21.3625 14.6385C20.9539 14.2299 20.3998 14.0003 19.822 14H14.18C14.152 14 14.1247 14.0007 14.098 14.002C14.492 14.412 14.778 14.927 14.914 15.5H19.822C20.194 15.5 20.496 15.799 20.501 16.169L20.498 16.201C20.494 16.2397 20.4817 16.2997 20.461 16.381C20.3992 16.6034 20.2901 16.8098 20.141 16.986C19.791 17.412 18.969 18 17.001 18C16.021 18 15.325 17.854 14.831 17.655C14.723 18.055 14.545 18.538 14.248 19.038ZM4.25 14C3.65326 14 3.08097 14.2371 2.65901 14.659C2.23705 15.081 2 15.6533 2 16.25V16.528C2.00105 16.5975 2.00572 16.667 2.014 16.736C2.09712 17.48 2.36453 18.1915 2.792 18.806C3.61 19.974 5.172 21 8 21C10.828 21 12.39 19.975 13.208 18.805C13.6355 18.1905 13.9029 17.479 13.986 16.735C13.9931 16.6662 13.9977 16.5971 14 16.528V16.25C14 15.6533 13.7629 15.081 13.341 14.659C12.919 14.2371 12.3467 14 11.75 14H4.25ZM3.5 16.507V16.25C3.5 16.0511 3.57902 15.8603 3.71967 15.7197C3.86032 15.579 4.05109 15.5 4.25 15.5H11.75C11.9489 15.5 12.1397 15.579 12.2803 15.7197C12.421 15.8603 12.5 16.0511 12.5 16.25V16.507L12.493 16.587C12.4355 17.0749 12.2589 17.5413 11.979 17.945C11.486 18.65 10.422 19.5 8 19.5C5.578 19.5 4.514 18.65 4.02 17.945C3.74041 17.5412 3.56425 17.0748 3.507 16.587L3.5 16.507Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            <a href="customer.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Total Customer</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalPelanggan'] ?? 0 ?></p>
                            <p class="text-xs text-orange-600 font-medium">Registered</p>
                        </div>
                        <div class="bg-orange-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <div class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-[#16151C]">New Stock</h3>
                    <a href="riceStock.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                </div>
                <div class="space-y-3">
                    <?php while ($stock = mysqli_fetch_assoc($recentStock)) : ?>
                    <div class="flex items-center justify-between p-3 bg-[#F6F4F2] rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-[#16151C]"><?= htmlspecialchars($stock['namaBeras']) ?>
                                <?= htmlspecialchars($stock['beratBeras']) ?> kg</p>
                            <p class="text-sm text-[#5D5C61]"><?= htmlspecialchars($stock['jenisBeras']) ?> •
                                <?= htmlspecialchars($stock['namaPemasok'] ?? 'N/A') ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-[#16151C]"><?= number_format($stock['stokBeras']) ?> bag</p>
                            <p class="text-sm text-green-600">Rp <?= number_format($stock['hargaJualBeras']) ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-[#16151C]">Low Stock</h3>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Alert</span>
                </div>
                <div class="space-y-3">
                    <?php 
                    $lowStockCount = 0;
                    mysqli_data_seek($lowStock, 0);
                    while ($stock = mysqli_fetch_assoc($lowStock)) : 
                        $lowStockCount++;
                    ?>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border-l-4 border-red-400">
                        <div class="flex-1">
                            <p class="font-medium text-[#16151C]"><?= htmlspecialchars($stock['namaBeras']) ?>
                                <?= htmlspecialchars($stock['beratBeras']) ?> kg</p>
                            <p class="text-sm text-[#5D5C61]"><?= htmlspecialchars($stock['jenisBeras']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-red-600"><?= number_format($stock['stokBeras']) ?></p>
                            <p class="text-xs text-red-500">Stock running low</p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php if ($lowStockCount == 0) : ?>
                    <div class="text-center py-4 text-[#5D5C61]">
                        <p class="text-sm">All stock is in good condition</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Selling Products Chart -->
        <div class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 mt-8 mb-4">
            <h3 class="text-xl font-semibold text-[#16151C] mb-4"> Popular Product</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <canvas id="topSellingChart" width="400" height="200"></canvas>
                </div>
                <div class="space-y-3">
                    <?php 
                        $chartLabels = [];
                        $chartData   = [];
                        $chartColors = ['#10B981','#3B82F6','#F59E0B','#EF4444','#8B5CF6'];
                        $colorIndex  = 0;

                        while ($prod = mysqli_fetch_assoc($topSelling)) :
                            $chartLabels[] = $prod['namaBeras'] . ' (' . $prod['beratBeras'] . ')';
                            // Pakai totalJumlah untuk chart
                            $chartData[]   = $prod['totalJumlah']; 
                        ?>
                    <div class="flex items-center justify-between p-3 bg-[#F6F4F2] rounded-lg">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full mr-3"
                                style="background-color: <?= $chartColors[$colorIndex] ?>"></div>
                            <div>
                                <p class="font-medium text-[#16151C]"><?= htmlspecialchars($prod['namaBeras']) ?></p>
                                <p class="text-sm text-[#5D5C61]"><?= htmlspecialchars($prod['beratBeras']) ?> kg</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-[#16151C]"><?= number_format($prod['totalJumlah']) ?> bag </p>
                            <p class="text-sm text-green-600">Rp <?= number_format($prod['totalRevenue']) ?></p>
                        </div>
                    </div>
                    <?php 
    $colorIndex = ($colorIndex + 1) % count($chartColors);
endwhile; 
?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdownProfile");
        dropdown.classList.toggle("hidden");
    }

    document.addEventListener("click", function(event) {
        const dropdown = document.getElementById("dropdownProfile");
        const button = event.target.closest("button[onclick='toggleDropdown()']");
        if (!button && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });

    // Chart for top selling products
    const ctx = document.getElementById('topSellingChart').getContext('2d');
    const topSellingChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartData) ?>,
                backgroundColor: <?= json_encode($chartColors) ?>,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    </script>

    <script src="../../assets/cdn/flowbite.min.js"></script>
    <script src="../../assets/cdn/flowbite.bundle.js"></script>
</body>

</html>