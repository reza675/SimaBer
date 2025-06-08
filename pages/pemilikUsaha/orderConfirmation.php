
<?php
session_start();
if (!isset($_SESSION['namaPemilik']) || !isset($_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}

$nama       = $_SESSION['namaPemilik'];
$idPemilik  = $_SESSION['idPemilik'];
$currentPage = 'orderConfirmation.php';

include '../../assets/mysql/connect.php';

// Ambil foto profil
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '$idPemilik'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);

// Pagination
$itemsPerPage = isset($_GET['show']) ? (int) $_GET['show'] : 10;
$page         = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset       = ($page - 1) * $itemsPerPage;

// Search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Perbaikan bagian WHERE clause - pastikan alias tabel konsisten
$whereClauses = [];
$whereClauses[] = "sbp.idPemilik = '$idPemilik'";  // Menggunakan alias yang konsisten

if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $whereClauses[] = "(sbp.namaBeras LIKE '%{$esc}%' OR p.namaPelanggan LIKE '%{$esc}%')";
}   

$where = count($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Hitung total - perbaikan alias tabel
$totalQ = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM pesananpemilik pp
    JOIN stokberaspemilik sbp ON pp.idBeras = sbp.idBeras
    JOIN pelanggan p ON pp.idPelanggan = p.idPelanggan
    $where
");

if (!$totalQ) {
    die("Error in count query: " . mysqli_error($conn));
}

$totalRow   = mysqli_fetch_assoc($totalQ);
$totalData  = (int) $totalRow['total'];
$totalPages = $totalData > 0 ? ceil($totalData / $itemsPerPage) : 1;

// Ambil data pesanan - perbaikan alias tabel dan konsistensi
$sql = "
    SELECT
        pp.idPesanan,
        pp.jumlahPesanan,
        pp.hargaBeli,
        pp.status,
        pp.tanggalPesanan,
        pp.deliverNotes,
        pp.isDeliver,
        pp.metode_pembayaran,
        sbp.namaBeras,
        sbp.beratBeras,
        p.alamatPelanggan,
        p.namaPelanggan AS customerName
    FROM pesananpemilik pp
    JOIN stokberaspemilik sbp ON pp.idBeras = sbp.idBeras
    JOIN pelanggan p ON pp.idPelanggan = p.idPelanggan
    $where
    ORDER BY pp.tanggalPesanan DESC
    LIMIT $itemsPerPage OFFSET $offset
";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Error in main query: " . mysqli_error($conn));
}

$dataPesanan = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataPesanan[] = $row;
}

// Hitung range tampilan
$start = $offset + 1;
$end   = min($offset + $itemsPerPage, $totalData);

// Pesan sukses/error
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error']   ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
    <style>
    .dropdown:hover .dropdown-menu {
        display: block;
    }
    </style>
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarBusinessOwner.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Order Confirmation</p>
                <p class="text-l text-[#5D5C61] font-regular">Order Confirmation</p>
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
                        <a href="settingsSupplier.php"
                            class="block font-semibold px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                        <a href="../../assets/mysql/pemilikUsaha/proses.php?logout=true"
                            class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log
                            Out</a>
                    </div>
                </div>

            </div>
        </div>

        <?php if ($success): ?>
        <div class="mt-4 mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            <p><?= $success ?></p>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <p><?= $error ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['search']) && $_GET['search'] === 'notfound') :?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <p>Keyword not found</p>
        </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-[#A2A1A8] shadow-lg p-8 mt-4 mb-4">
            <div class="relative flex justify-between items-center gap-4">
                <div class="relative flex-grow">
                    <form action="orderConfirmation.php" method="get" class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                            <path fill-rule="evenodd"
                                d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Search Item"
                            class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition focus:outline-none focus:border-slate-400" />
                    </form>
                </div>
            </div>

            <!-- Tabel Utama -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-[#A2845E] text-black">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Item Name</th>
                            <th class="px-4 py-3 text-left">Much</th>
                            <th class="px-4 py-3 text-left">Customer Name</th>
                            <th class="px-4 py-3 text-left">Address</th>
                            <th class="px-4 py-3 text-left">Weight</th>
                            <th class="px-4 py-3 text-left">Buying Price</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Order Date</th>
                            <th class="px-4 py-3 text-left">Delivery Note</th>
                            <th class="px-4 py-3 text-left">is Deliver</th>
                            <th class="px-4 py-3 text-left">Payment Method</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($dataPesanan)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-center text-gray-500">No orders found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($dataPesanan as $index => $pesanan): ?>
                        <tr class="<?= $index % 2 === 0 ? 'bg-[#FFEEDB]' : 'bg-[#E7DDD3]' ?> hover:bg-[#D1BEAB]">
                            <td class="px-4 py-3"><?= $start + $index ?></td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($pesanan['namaBeras']) ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?= htmlspecialchars($pesanan['jumlahPesanan']) ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($pesanan['customerName']) ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($pesanan['alamatPelanggan']) ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($pesanan['beratBeras']) ?> kg
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                Rp <?= number_format($pesanan['hargaBeli'], 0, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                <?php 
                                switch($pesanan['status']) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'approved':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'rejected':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                    <?= ucfirst($pesanan['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= $pesanan['tanggalPesanan'] ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap"><?= htmlspecialchars($pesanan['deliverNotes']) ?></td>
                            <td class="px-4 py-3 whitespace-nowrap"><?php if ($pesanan['isDeliver'] == 1) { echo 'Delivery'; } else {echo 'Self Pickup'; } ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($pesanan['metode_pembayaran']) ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex space-x-2 justify-center">
                                    <?php if ($pesanan['status'] === 'pending'): ?>
                                    <button onclick="updateOrderStatus(<?= $pesanan['idPesanan'] ?>, 'approved')"
                                        class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-md transition">
                                        Approve
                                    </button>
                                    <button onclick="updateOrderStatus(<?= $pesanan['idPesanan'] ?>, 'rejected')"
                                        class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded-md transition">
                                        Reject
                                    </button>
                                    <?php else: ?>
                                    <span class="text-gray-500 text-xs">No action available</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-between items-center mb-4 mt-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <span>Showing</span>
                    <select id="showEntries" class="border bg-[#EFE9E2] rounded-md px-2 py-1"
                        onchange="window.location.href = '?show='+this.value+'&page=1&search=<?= urlencode($search) ?>'">
                        <option value="5" <?= $itemsPerPage == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
                    </select>
                </div>

                <div class="text-sm text-gray-600">
                    Showing <?= $start ?> to <?= $end ?> of <?= $totalData ?> records
                </div>
            </div>
            <div class="flex justify-center mt-6 gap-2 border[#7C4D16]">
                <?php if($page > 1): ?>
                <a href="?page=<?= $page-1 ?>&show=<?= $itemsPerPage ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.4685 17.5856C14.7919 17.3269 14.8444 16.8549 14.5856 16.5315L10.9604 12L14.5856 7.46849C14.8444 7.14505 14.7919 6.67308 14.4685 6.41432C14.145 6.15556 13.6731 6.208 13.4143 6.53145L9.41432 11.5315C9.19519 11.8054 9.19519 12.1946 9.41432 12.4685L13.4143 17.4685C13.6731 17.7919 14.145 17.8444 14.4685 17.5856Z"
                            fill="#16151C" />
                    </svg>
                </a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&show=<?= $itemsPerPage ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 border rounded-md <?= $i == $page ? 'bg-none border-[#7C4D16] border-2 text-[#7C4D16] font-semibold' : 'bg-none hover:bg-[#D1BEAB]' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&show=<?= $itemsPerPage ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.53151 17.5856C9.20806 17.3269 9.15562 16.8549 9.41438 16.5315L13.0396 12L9.41438 7.46849C9.15562 7.14505 9.20806 6.67308 9.53151 6.41432C9.85495 6.15556 10.3269 6.208 10.5857 6.53145L14.5857 11.5315C14.8048 11.8054 14.8048 12.1946 14.5857 12.4685L10.5857 17.4685C10.3269 17.7919 9.85495 17.8444 9.53151 17.5856Z"
                            fill="#16151C" />
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../../assets/cdn/flowbite.min.js"></script>
    <script src="../../assets/cdn/flowbite.bundle.js"></script>
    <script>
    function updateOrderStatus(orderId, status) {
        if (status === 'rejected') {
            if (!confirm('Are you sure you want to reject this order?')) {
                return;
            }
        }

        fetch('../../assets/mysql/pemilikUsaha/proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=confirm_order&idPesanan=${orderId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
    }

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
    </script>
</body>

</html>