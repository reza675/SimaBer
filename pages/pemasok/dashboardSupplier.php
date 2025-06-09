<?php
session_start();
if (!isset($_SESSION['namaPemasok'])) {
    header("Location:../login/loginSupplier.php?login=error");
    exit();
}

$nama = $_SESSION['namaPemasok'];
$idPemasok = $_SESSION['idPemasok'];
$currentPage = 'dashboardSupplier.php';
include '../../assets/mysql/connect.php';
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'");
$dataPemasok = mysqli_fetch_assoc($q);  

//card
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM stokberaspemasok WHERE idPemasok = '$idPemasok') as totalJenisBeras,
        (SELECT COUNT(*) FROM pesananpemasok WHERE idPemasok = '$idPemasok' AND status_pengiriman != 'Completed' AND status = 'approved') as totalStatusBusinessOwner,
        (SELECT COUNT(*) FROM pesananpemasok WHERE idPemasok = '$idPemasok' AND status = 'pending') as totalConfirmation,
        (SELECT COUNT(*) FROM pesananpemasok WHERE status_pengiriman = 'Completed' AND idPemasok = '$idPemasok') as totalOrder
";
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

// Get recent rice stock data
$recentStockQuery = "
    SELECT sb.namaBeras, sb.jenisBeras, sb.beratBeras, sb.stokBeras, sb.hargaJual, 
           p.namaPemasok
    FROM stokberaspemasok sb 
    LEFT JOIN pemasok p ON sb.idPemasok = p.idPemasok 
    WHERE p.idPemasok = '$idPemasok' 
    ORDER BY sb.stokBeras DESC 
    LIMIT 5
";
$recentStock = mysqli_query($conn, $recentStockQuery);

// Get low stock items 
$lowStockQuery = "
    SELECT namaBeras, jenisBeras, stokBeras,  beratBeras
    FROM stokberaspemasok 
    WHERE idPemasok = '$idPemasok' AND stokBeras < 50
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
        SUM(pp.jumlahPesanan * sb.hargaJual) AS totalRevenue
    FROM pesananpemasok pp
    JOIN stokberaspemasok sb 
      ON pp.idBeras = sb.idBeras
    WHERE pp.idPemasok    = '$idPemasok'
      AND pp.status_pengiriman = 'Completed'
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
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarSupplier.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Dashboard</p>
                <p class="text-l text-[#5D5C61] font-regular">Dashboard Supplier</p>
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
                        <img src="../../assets/gambar/pemasok/photoProfile/<?= $dataPemasok['fotoProfil'] ?? 'profil.jpeg' ?>"
                            alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
                        <div class="text-left hidden sm:block">
                            <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                            <span class="block font-semibold text-sm text-[#A2A1A8] leading-4">Supplier</span>
                        </div>
                        <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownProfile"
                        class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-md z-50 w-48">
                        <a href="settingsSupplier.php"
                            class="block font-semibold px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                        <a href="../../assets/mysql/pemasok/proses.php?logout=true"
                            class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log
                            Out</a>
                    </div>
                </div>

            </div>
        </div>
        <?php 
    if (isset($_GET['search']) && $_GET['search'] === 'notfound') :?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <p>Keyword not found</p>
        </div>

        <?php endif; ?>

        <!-- Statistics Cards -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <a href="riceManagement.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Total Type of Rice</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalJenisBeras'] ?? 0 ?></p>
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
            <a href="orderConfirmation.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Order Confirmation</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalConfirmation'] ?? 0 ?></p>
                            <p class="text-xs text-blue-600 font-medium">Status pending</p>
                        </div>
                       
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            <a href="orderStatusSupplier.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Status Order</p>
                            <p class="text-3xl font-bold text-[#16151C]">
                                <?= number_format($stats['totalStatusBusinessOwner'] ?? 0) ?>
                            </p>
                            <p class="text-xs text-purple-600 font-medium">Order in Process</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-purple-600" viewBox="0 0 24 24" fill="none"
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
            <a href="orderHistorySupplier.php">
                <div
                    class="bg-white rounded-2xl border border-[#A2A1A8] shadow-lg p-6 hover:shadow-xl transition duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#5D5C61]">Order History</p>
                            <p class="text-3xl font-bold text-[#16151C]"><?= $stats['totalOrder'] ?? 0 ?></p>
                            <p class="text-xs text-orange-600 font-medium">Order Completed</p>
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
                            <p class="text-sm text-green-600">Rp <?= number_format($stock['hargaJual']) ?></p>
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

</body>

<script src="../../assets/cdn/flowbite.min.js"></script>
<script src="../../assets/cdn/flowbite.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

</html>