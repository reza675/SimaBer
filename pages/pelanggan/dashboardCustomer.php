<?php
session_start();
if (!isset($_SESSION['namaPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$nama = $_SESSION['namaPelanggan'];
$currentPage = 'dashboardCustomer.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png" />
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include 'sidebar.php'; ?>

    <div class="flex justify-between items-center p-4 bg-[#A2845E] text-white">
    <div class="ml-12">
        <h1 class="text-lg text-black font-semibold">Hello <?= $nama; ?> 👋</h1>
        <p class="text-sm">Welcome to SimaBer</p>
    </div>

    <div class="relative inline-block text-left">
        <button onclick="toggleDropdown()"
            class="flex border-2 border-solid items-center bg-[#A2845E] rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-300 transition space-x-4">
            <img src="../../assets/gambar/pelanggan/profil.jpeg" alt="User"
                class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
            <div class="text-left hidden sm:block">
                <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                <span class="block text-sm text-white leading-4">Pelanggan</span>
            </div>
            <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div id="dropdownProfile"
            class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-md z-50">
            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
            <a href="../../assets/mysql/pelanggan/proses.php?logout=true"
                class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-b-lg text-center">Log
                Out</a>
        </div>
    </div>
</div>

<div class="bg-[#A2845E] py-12">
    <h1 class="font-semibold text-3xl text-white text-center mb-2">
        Bringing Quality to Your Table, One Grain at a Time. <br>
        Fresh, Premium Rice – Only at SimaBer.
    </h1>
    <p class="font-reguler text-lg text-[#CECCCC] text-center">We’re committed to bringing you premium rice at fair prices.<br>Experience quality, freshness, and trust in every grain.</p>
</div>
</body>
<script src="../../assets/cdn/flowbite.min.js"></script>
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