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
$q = mysqli_query($conn, "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Shipping SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarCustomer.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Checkout</p>
                <p class="text-l text-[#5D5C61] font-regular">Checkout Product</p>
            </div>

            <div class="flex items-center gap-4">
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
        <div class="mt-6">
            <a href="detailProduct.php?from=checkout" class="flex items-center text-gray-600 hover:text-gray-900">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path d="M21 12L3.5 12" stroke="black" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M10 5L3 12L10 19" stroke="black" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span class="ml-2 text-semibold text-2xl font-semibold text-black">Checkout Product</span>
            </a>
            <div class="flex mt-2 mx-9">
                <p class="text-lg font-bold text-black">Shipping</p>
                <svg class = "mt-4 ml-2 mr-2" width="69" height="1" viewBox="0 0 69 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="0.5" y1="0.5" x2="68.5" y2="0.5" stroke="black" />
                </svg>
                <p class="text-lg font-regular text-black">Address</p>
                <svg class = "mt-4 ml-2 mr-2" width="69" height="1" viewBox="0 0 69 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="0.5" y1="0.5" x2="68.5" y2="0.5" stroke="black" />
                </svg>
                <p class="text-lg font-regular text-black">Payment</p>

            </div>
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