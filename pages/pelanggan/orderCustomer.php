<?php
session_start();
if (!isset($_SESSION['namaPelanggan']) || !isset($_SESSION['idPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$nama = $_SESSION['namaPelanggan'];
$idPelanggan = $_SESSION['idPelanggan'];
$currentPage = 'orderCustomer.php';
include '../../assets/mysql/connect.php';

$itemsPerPage = isset($_GET['show']) ? (int)$_GET['show'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM stokberas");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalData / $itemsPerPage);
$query = mysqli_query($conn, "SELECT * FROM stokberas LIMIT $itemsPerPage OFFSET $offset");
$dataBeras = [];
while ($data = mysqli_fetch_array($query)) {
    $dataBeras[] = $data;
}
$start = $offset + 1;
$end = min($offset + $itemsPerPage, $totalData);
$q = mysqli_query($conn, "SELECT fotoProfil FROM pelanggan WHERE id = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarCustomer.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Order</p>
                <p class="text-l text-[#5D5C61] font-regular">Order SimaBer rice</p>
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
                        <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>"
                            alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
                        <div class="text-left hidden sm:block">
                            <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                            <span class="block font-semibold text-sm text-[#A2A1A8] leading-4">Pelanggan</span>
                        </div>
                        <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="dropdownProfile"
                        class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-md z-50 w-48">
                        <a href="settingsCustomer.php"
                            class="block font-semibold px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                        <a href="../../assets/mysql/pelanggan/proses.php?logout=true"
                            class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log
                            Out</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-[#A2A1A8] shadow-lg p-8 mt-4">
            <div class="relative flex justify-between items-center gap-4">
                <div class="relative flex-grow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                        <path fill-rule="evenodd"
                            d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <input name="inputSearch"
                        class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                        placeholder="Search Item" />
                </div>
                <div class="flex items-center gap-4">
                    <button type="button" class="flex items-center gap-2 px-4 py-2 border border-slate-400 rounded-md
                   hover:bg-[#A2845E] focus:outline-none transition">
                        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.5 6H10.5" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M3.5 12H12.5" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M19.5 12H21.5" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M14.5 6L21.5 6" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M13.5 18H20.5" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M3.5 18H6.5" stroke="#16151C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <circle cx="8.5" cy="18" r="2" stroke="#16151C" stroke-width="1.5" />
                            <circle cx="17.5" cy="12" r="2" stroke="#16151C" stroke-width="1.5" />
                            <circle cx="12.5" cy="6" r="2" stroke="#16151C" stroke-width="1.5" />
                        </svg>
                        <span>
                            Filter
                        </span></button>

                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#A2845E] text-black">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Item Name</th>
                            <th class="px-4 py-3">Image</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Weight(kg)</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($dataBeras as $index => $beras) : ?>
                        <tr class="<?= $index % 2 === 0 ? 'bg-[#FFEEDB]' : 'bg-[#E7DDD3]' ?> hover:bg-[#D1BEAB]">
                            <td class="px-4 py-3 text-center"><?= $index + 1 ?></td>
                            <td class="px-4 py-3 text-center"><?= $beras['id'] ?></td>
                            <td class="px-4 py-3"><?= $beras['namaBeras'] ?></td>
                            <td class="px-4 py-3 text-center">
                                <img src="../../assets/gambar/beras/<?= $beras['gambarBeras'] ?>"
                                    class="w-16 h-16 object-cover mx-auto">
                            </td>
                            <td class="px-4 py-3 text-center"><?= $beras['jenisBeras'] ?></td>
                            <td class="px-4 py-3 text-center"><?= $beras['beratBeras'] ?></td>
                            <td class="px-4 py-3 text-center">Rp
                                <?= number_format($beras['hargaJualBeras'], 2, ',', '.') ?>
                            </td>
                            <td class="px-4 py-3 text-center"><?= $beras['stokBeras'] ?></td>
                            <td class="px-4 py-3 text-center">
                                <a href="detailProduct.php?id=<?= $beras['id'] ?>&from=orderCustomer"
                                    class="bg-[#A2845E] text-white px-4 py-2 rounded-full hover:bg-[#5C4E3F] transition-colors duration-200">
                                    Buy
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
                <a href="?page=<?= $page-1 ?>&show=<?= $itemsPerPage ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.4685 17.5856C14.7919 17.3269 14.8444 16.8549 14.5856 16.5315L10.9604 12L14.5856 7.46849C14.8444 7.14505 14.7919 6.67308 14.4685 6.41432C14.145 6.15556 13.6731 6.208 13.4143 6.53145L9.41432 11.5315C9.19519 11.8054 9.19519 12.1946 9.41432 12.4685L13.4143 17.4685C13.6731 17.7919 14.145 17.8444 14.4685 17.5856Z"
                            fill="#16151C" />
                    </svg>


                </a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&show=<?= $itemsPerPage ?>"
                    class="px-4 py-2 border rounded-md <?= $i == $page ? 'bg-none border-[#7C4D16] border-2 text-[#7C4D16] font-semibold' : 'bg-none hover:bg-[#D1BEAB]' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&show=<?= $itemsPerPage ?>"
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

</body>

<script src="../../assets/cdn/flowbite.min.js"></script>
<script src="../../assets/cdn/flowbite.bundle.js"></script>
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
</script>

</html>