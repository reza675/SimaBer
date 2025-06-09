<?php
session_start();
if (!isset($_SESSION['namaPemasok'])) {
    header("Location:../login/loginSupplier.php?login=error");
    exit();
}

$nama = $_SESSION['namaPemasok'];
$idPemasok = $_SESSION['idPemasok'];
$currentPage = 'riceManagement.php';
include '../../assets/mysql/connect.php';
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'");
$dataPemasok = mysqli_fetch_assoc($q);  

//paginasi
$itemsPerPage = isset($_GET['show']) ? (int)$_GET['show'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

//parameter search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "WHERE sb.idPemasok = '" . mysqli_real_escape_string($conn, $idPemasok) . "'";

if ($search !== '') {
    $escSearch = mysqli_real_escape_string($conn, $search);
    $where    .= " AND namaBeras LIKE '%{$escSearch}%'";
}
//Hitung total data & total halaman
$totalQ = mysqli_query($conn, " SELECT COUNT(*) AS total FROM stokberaspemasok sb JOIN pemasok p ON sb.idPemasok = p.idPemasok {$where}");
$totalRow  = mysqli_fetch_assoc($totalQ);
$totalData = (int) $totalRow['total'];
$totalPages= ($totalData > 0) ? ceil($totalData / $itemsPerPage) : 1;

//Ambil data stok dengan filter & paginasi
$sql = "
  SELECT 
    sb.*,
    p.namaPemasok AS supplierName
  FROM stokberaspemasok sb
  JOIN pemasok p 
    ON sb.idPemasok = p.idPemasok
  {$where}
  LIMIT {$itemsPerPage} OFFSET {$offset}
";
$query = mysqli_query($conn, $sql);
$dataBeras = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataBeras[] = $row;
}

//Hitung range tampilan
$start = $offset + 1;
$end   = min($offset + $itemsPerPage, $totalData);
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rice Management Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarSupplier.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Rice Management</p>
                <p class="text-l text-[#5D5C61] font-regular">Item Detail Information</p>
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
        <?php if ($success): ?>
        <div class="mt-4 mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
            <?= $success ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <?= $error ?>
        </div>
        <?php endif; ?>
        <div class="rounded-2xl border border-[#A2A1A8] shadow-lg p-8 mt-4 mb-4">
            <div class="relative flex justify-between items-center gap-4">
                <div class="relative flex-grow">
                    <form action="riceManagement.php" method="get" class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                            <path fill-rule="evenodd"
                                d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        <input type="text" name="search" value="<?= $search ?>" placeholder="Search Item"
                            class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition focus:outline-none focus:border-slate-400" />
                    </form>
                </div>
                <div>
                    <button type="button" onclick="openAddModal()"
                        class="flex items-center gap-2 px-4 py-2 bg-[#A2845E] rounded-md hover:bg-[#8C6B42] focus:outline-none transition">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 8V16M16 12H8M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                stroke="#EFE9E2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <span class="font-semibold text-sm text-white">Add Item</span>
                    </button>
                </div>

            </div>

            <!-- Tabel Utama -->
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-[#A2845E] text-black">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Item Name</th>
                            <th class="px-4 py-3 text-left">Image</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Weight(kg)</th>
                            <th class="px-4 py-3 text-left">Selling Price</th>
                            <th class="px-4 py-3 text-left">Stock</th>
                            <th class="px-4 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                            if (empty($dataBeras)):
                        ?>
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-center text-gray-500">No data found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($dataBeras as $index => $beras): ?>
                        <tr class="<?= $index % 2 === 0 
                          ? 'bg-[#FFEEDB]'    
                          : 'bg-[#E7DDD3]'    ?>
                     hover:bg-[#D1BEAB]">
                            <td class="px-4 py-3"><?= $start + $index ?></td>

                            <td class="px-4 py-3 whitespace-nowrap font-semibold">
                                <?= htmlspecialchars($beras['idBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['namaBeras']) ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="w-16 h-16 mx-auto overflow-hidden rounded-lg border border-gray-200">
                                    <img src="../../assets/gambar/beras/<?= htmlspecialchars($beras['gambarBeras']) ?>"
                                        alt="<?= htmlspecialchars($beras['namaBeras']) ?>"
                                        class="w-full h-full object-cover" />
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['jenisBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['beratBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                Rp <?= number_format($beras['hargaJual'], 2, ',', '.') ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['stokBeras']) ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex space-x-3 justify-center">
                                    <a href="#" onclick='showDetailModal(<?= json_encode($beras) ?>)'
                                        class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg flex items-center justify-center transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button onclick="openEditModal(
                            '<?= $beras['idBeras'] ?>',
                            '<?= $beras['namaBeras'] ?>',
                            '<?= $beras['jenisBeras'] ?>',
                            '<?= $beras['beratBeras'] ?>',
                            '<?= $beras['hargaJual'] ?>',
                            '<?= $beras['stokBeras'] ?>',
                            '<?= $beras['idPemasok'] ?>'
                        )" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center transition">
                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10.5 3.50006L14.5 7.50006M1 17.0001H5L15.5 6.50006C15.7626 6.23741 15.971 5.92561 16.1131 5.58245C16.2553 5.23929 16.3284 4.87149 16.3284 4.50006C16.3284 4.12862 16.2553 3.76083 16.1131 3.41767C15.971 3.07451 15.7626 2.7627 15.5 2.50006C15.2374 2.23741 14.9256 2.02907 14.5824 1.88693C14.2392 1.74479 13.8714 1.67163 13.5 1.67163C13.1286 1.67163 12.7608 1.74479 12.4176 1.88693C12.0744 2.02907 11.7626 2.23741 11.5 2.50006L1 13.0001V17.0001Z"
                                                stroke="white" stroke-width="1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button
                                        onclick="openDeleteModal('<?= $beras['idBeras'] ?>', '<?= $beras['namaBeras'] ?>')"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14 11V17M10 11V17M6 7V19C6 19.5304 6.21071 20.0391 6.58579 20.4142C6.96086 20.7893 7.46957 21 8 21H16C16.5304 21 17.0391 20.7893 17.4142 20.4142C17.7893 20.0391 18 19.5304 18 19V7M4 7H20M7 7L9 3H15L17 7"
                                                stroke="white" stroke-width="1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
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
                        onchange="window.location.href = '?show='+this.value+'&page=1'">
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
                <a href="?page=<?= $page-1 ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.4685 17.5856C14.7919 17.3269 14.8444 16.8549 14.5856 16.5315L10.9604 12L14.5856 7.46849C14.8444 7.14505 14.7919 6.67308 14.4685 6.41432C14.145 6.15556 13.6731 6.208 13.4143 6.53145L9.41432 11.5315C9.19519 11.8054 9.19519 12.1946 9.41432 12.4685L13.4143 17.4685C13.6731 17.7919 14.145 17.8444 14.4685 17.5856Z"
                            fill="#16151C" />
                    </svg>


                </a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md <?= $i == $page ? 'bg-none border-[#7C4D16] border-2 text-[#7C4D16] font-semibold' : 'bg-none hover:bg-[#D1BEAB]' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.53151 17.5856C9.20806 17.3269 9.15562 16.8549 9.41438 16.5315L13.0396 12L9.41438 7.46849C9.15562 7.14505 9.20806 6.67308 9.53151 6.41432C9.85495 6.15556 10.3269 6.208 10.5857 6.53145L14.5857 11.5315C14.8048 11.8054 14.8048 12.1946 14.5857 12.4685L10.5857 17.4685C10.3269 17.7919 9.85495 17.8444 9.53151 17.5856Z"
                            fill="#16151C" />
                    </svg>

                </a>
                <?php endif; ?>
            </div>
            <!-- Add Modal -->
            <div id="addModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-[600px] h-full">
                    <div class="flex justify-between items-center pb-1">
                        <h3 class="text-2xl font-bold mb-4 text-[#16151C]">Add Items</h3>
                        <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M27.0708 12.929L12.9287 27.0712M27.0708 27.0711L12.9287 12.929"
                                    stroke="#28303F" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <form action="../../assets/mysql/pemasok/proses.php" method="POST" enctype="multipart/form-data"
                        id="myForm">

                        <div class="grid grid-cols-2 gap-4">
                            <!-- <div>
                                <label class="block text-sm font-semibold mb-2">ID Item</label>
                                <input type="text" name="idBeras" placeholder="ID" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div> -->
                            <div>
                                <label class="block text-sm font-semibold mb-2">Item Name</label>
                                <input type="text" name="namaBeras" placeholder="Name" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Type</label>
                                <input type="text" name="jenisBeras" placeholder="Item Type" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Weight (kg)</label>
                                <input type="text" name="beratBeras" placeholder="Item Weight" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2"> Selling Price</label>
                                <input type="number" name="hargaJual" placeholder="Item Selling Price" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Stock</label>
                                <input type="number" name="stokBeras" placeholder="Item Stock" required
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold mb-2">ID Supplier</label>
                                <input type="text" name="idPemasok"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]"
                                    value="<?= $_SESSION['idPemasok'] ?>" readonly>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-semibold">Image</label>
                                <input type="file" name="gambarBeras" required
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-black focus:outline-none dark:border-gray-600 dark:placeholder-black">

                            </div>
                        </div>

                        <div class="flex justify-end gap-2 mt-2">
                            <button type="button" onclick="closeAddModal()"
                                class="px-4 py-2 border rounded-md hover:bg-gray-100">Cancel</button>
                            <button type="submit" name="addBeras"
                                class="flex items-center gap-2 px-4 py-2 bg-[#A2845E] rounded-md hover:bg-[#8C6B42] focus:outline-none transition">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 8V16M16 12H8M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                        stroke="#EFE9E2" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>

                                <span class="font-semibold text-sm text-white">Add Item</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Detail Modal -->
            <div id="detailModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl p-6 max-w-2xl w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Item Detail</h3>
                        <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mb-2 mt-2">
                        <svg width="620" height="1" viewBox="0 0 860 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="-4.37114e-08" y1="0.5" x2="860" y2="0.499925" stroke="#A2A1A8" />
                        </svg>
                    </div>
                    <div class="grid grid-cols-2">
                        <div class="col-span-1">
                            <img id="detail-gambar" src="" alt="rice" class="object-cover">
                        </div>
                        <div class="col-span-1">
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-32 font-medium">ID</span>
                                    <span id="detail-id"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Item Name</span>
                                    <span id="detail-nama"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Type</span>
                                    <span id="detail-jenis"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Weight (kg)</span>
                                    <span id="detail-berat"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Selling Price</span>
                                    <span id="detail-hargaJual"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Stock</span>
                                    <span id="detail-stok"></span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 font-medium">Supplier Name</span>
                                    <span id="detail-supplierName"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 mt-2">
                        <svg width="620" height="1" viewBox="0 0 860 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="-4.37114e-08" y1="0.5" x2="860" y2="0.499925" stroke="#A2A1A8" />
                        </svg>
                    </div>
                    <div class="flex justify-center mt-4 mx-auto">
                        <button onclick="closeDetailModal()"
                            class="px-4 py-2 bg-[#007AFF] hover:bg-[#0056B4] text-white rounded-lg">
                            OK
                        </button>
                    </div>

                </div>
            </div>
            <!-- Edit Modal -->
            <div id="editModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 w-full max-w-md">
                    <h3 class="text-2xl font-bold mb-4 text-[#16151C]">Edit Item</h3>
                    <form action="../../assets/mysql/pemasok/proses.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="idBeras" id="editId">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Item Name</label>
                                <input type="text" name="namaBeras" id="editNama"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Type</label>
                                <input type="text" name="jenisBeras" id="editJenis"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2">Weight (kg)</label>
                                <input type="text" name="beratBeras" id="editBerat"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2"> Selling Price</label>
                                <input type="number" name="hargaJual" id="editHargaJual"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Stock</label>
                                <input type="number" name="stokBeras" id="editStok"
                                    class="w-full border rounded-md p-2 focus:ring-2 focus:ring-[#A2845E]">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">ID Supplier</label>
                                <input type="text" name="supplierBeras" id="editPemasok" readonly
                                    class="w-full border rounded-md p-2">
                            </div>

                            <div class="col-span-2">
                                <label class="block mb-2 text-sm font-semibold">Image</label>
                                <input type="file" name="gambarBeras"
                                    class="block w-full text-sm text-gray-900 border rounded-md cursor-pointer bg-gray-50 focus:ring-2 focus:ring-[#A2845E]">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 border rounded-md hover:bg-gray-100">Cancel</button>
                            <button type="submit" name="editBeras"
                                class="px-4 py-2 bg-[#A2845E] text-white rounded-md hover:bg-[#8C6B42]">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Delete Modal -->
            <div id="deleteModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full ">
                    <h3 class="text-xl font-bold mb-4 text-center">Delete Confirmation</h3>
                    <p class="mb-4 text-center">Are you sure you want to delete<br> <span id="deleteItemSpan"
                            class="font-semibold"></span> <span id="deleteItemName" class="font-semibold"></span>?
                    </p>
                    <div class="flex justify-center gap-2">
                        <form id="deleteForm" action="../../assets/mysql/pemasok/proses.php" method="POST">
                            <input type="hidden" name="idBeras" id="deleteItemId">
                            <div class="mx-auto justify-center flex space-x-2">
                                <button type="button" onclick="closeDeleteModal()"
                                    class="px-4 py-2 border rounded-md hover:bg-gray-100">Cancel</button>
                                <button type="submit" name="deleteBeras"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>


    </div>

</body>

<script src="../../assets/cdn/flowbite.min.js"></script>
<script src="../../assets/cdn/flowbite.bundle.js"></script>
<script>
//add modal
const modal = document.getElementById('addModal');

function openAddModal() {
    modal.classList.remove('hidden');
}

function closeAddModal() {
    modal.classList.add('hidden');
}

window.onclick = function(event) {
    if (event.target === modal) {
        closeAddModal();
    }
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

//show detail
function showDetailModal(beras) {
    document.getElementById('detail-id').textContent = beras.idBeras;
    document.getElementById('detail-nama').textContent = beras.namaBeras;
    document.getElementById('detail-jenis').textContent = beras.jenisBeras;
    document.getElementById('detail-berat').textContent = beras.beratBeras;
    document.getElementById('detail-hargaJual').textContent = 'Rp ' +
        new Intl.NumberFormat('id-ID').format(beras.hargaJual);
    document.getElementById('detail-stok').textContent = beras.stokBeras;
    document.getElementById('detail-supplierName').textContent = beras.supplierName;
    document.getElementById('detail-gambar').src = '../../assets/gambar/beras/' + beras.gambarBeras;
    document.getElementById('detailModal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target === modal) {
        closeDetailModal();
    }
}

//show edit
function openEditModal(id, nama, jenis, berat, hargaJual, stok, idPemasok) {
    document.getElementById('editId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editJenis').value = jenis;
    document.getElementById('editBerat').value = berat;
    document.getElementById('editHargaJual').value = hargaJual;
    document.getElementById('editStok').value = stok;
    document.getElementById('editPemasok').value = idPemasok;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeEditModal();
    }
});
//delete modal
function openDeleteModal(id, name) {
    document.getElementById('deleteItemSpan').textContent = id;
    document.getElementById('deleteItemId').value = id;
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
});
</script>

</html>