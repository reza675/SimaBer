<?php
session_start();
if (!isset($_SESSION['namaPemasok']) || !isset($_SESSION['idPemasok'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}
$nama = $_SESSION['namaPemasok'];
$idPemasok = $_SESSION['idPemasok'];
$currentPage = 'settingsSupplier.php';

include '../../assets/mysql/connect.php';
$query = mysqli_query($conn, "SELECT * FROM pemasok WHERE idPemasok= '$idPemasok'");
$dataPemasok = mysqli_fetch_assoc($query);

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarSupplier.php'; ?>

    <div class="main-container ml-[300px] mt-4 mr-12 overflow-visible">
        <div class="flex justify-between items-center gap-6">
            <div>
                <p class="text-2xl font-bold text-[#16151C]">Settings</p>
                <p class="text-l text-[#5D5C61]">Manage your profile settings</p>
            </div>
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()"
                    class="flex items-center gap-4 border-2 rounded-xl px-4 py-2 hover:ring-2 hover:ring-gray-500 transition">
                    <img src="../../assets/gambar/pemasok/photoProfile/<?= $dataPemasok['fotoProfil'] ?? 'profil.jpeg' ?>"
                        alt="User" class="w-14 h-14 rounded-xl object-cover">
                    <div class="hidden sm:block text-left">
                        <span class="block text-lg font-bold"><?= $nama; ?></span>
                        <span class="block text-sm text-[#A2A1A8]">Supplier</span>
                    </div>
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div id="dropdownProfile"
                    class="hidden absolute right-0 mt-2 bg-white border rounded-t-lg rounded-b-lg shadow-md z-50 w-48">
                    <a href="settingsSupplier.php"
                        class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                    <a href="../../assets/mysql/pemasok/proses.php?logout=true"
                        class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-b-lg text-center">Log Out</a>
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
        <div class="rounded-2xl border border-[#A2845E] shadow-lg p-8 mt-4">
            <div class="bg-[#EFE9E2] p-8">
                <form id="photoForm" action="../../assets/mysql/pemasok/proses.php" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="idPemasok" value="<?= $idPemasok ?>">
                    <div class="flex items-center gap-6">
                        <label class="relative cursor-pointer">
                            <img src="../../assets/gambar/pemasok/photoProfile/<?= $dataPemasok['fotoProfil'] ?? 'profil.jpeg' ?>"
                                alt="Profile" class="w-32 h-32 rounded-full border-4 border-[#A2845E] object-cover">
                            <input type="file" name="photo" class="absolute inset-0 opacity-0" accept="image/*"
                                onchange="this.form.submit()">
                        </label>
                        <div>
                            <h2 class="text-2xl font-bold text-[#3D3D3D]">Change Profile Picture</h2>
                            <p class="text-[#666]">Click on the image to upload a new profile photo.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-[#EFE9E2] p-8">
                <form id="profileForm" action="../../assets/mysql/pemasok/proses.php" method="POST">
                    <input type="hidden" name="idPemasok" value="<?= $idPemasok ?>">
                    <input type="hidden" name="submitEdit" value="1">

                    <div class="grid gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#3D3D3D]">Email</label>
                            <input type="email" name="email" value="<?= $dataPemasok['emailPemasok'] ?>" disabled
                                class="w-full p-3 border rounded-lg bg-gray-100">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-[#3D3D3D]">Full Name</label>
                            <input type="text" name="nama" value="<?= $dataPemasok['namaPemasok'] ?>" disabled
                                class="w-full p-3 border rounded-lg bg-gray-100">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <button type="button" id="editButton" onclick="toggleEditMode()"
                            class="px-8 py-3 rounded-full bg-[#A2845E] text-white hover:bg-[#8a715b] transition">Edit
                            Data Profil</button>
                        <button type="submit" id="submitButton"
                            class="hidden px-8 py-3 rounded-full bg-green-600 text-white hover:bg-green-700 transition">Save
                            Changes</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="../../assets/cdn/flowbite.min.js"></script>
    <script>
    function toggleDropdown() {
        document.getElementById('dropdownProfile').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const btn = e.target.closest("button[onclick='toggleDropdown()']");
        const menu = document.getElementById('dropdownProfile');
        if (!btn && !menu.contains(e.target)) menu.classList.add('hidden');
    });

    function toggleEditMode() {
        const fields = document.querySelectorAll(
            '#profileForm input:not([type="hidden"]):not([type="file"]), #profileForm textarea');
        fields.forEach(el => {
            el.disabled = !el.disabled;
            el.classList.toggle('bg-gray-100');
            el.classList.toggle('bg-white');
        });
        document.getElementById('editButton').classList.toggle('hidden');
        document.getElementById('submitButton').classList.toggle('hidden');
    }
    </script>
</body>

</html>