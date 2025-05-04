<?php
session_start();
if (!isset($_SESSION['namaPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$currentPage = 'settingsCustomer.php';
$nama = $_SESSION['namaPelanggan'];
$idPelanggan = $_SESSION['idPelanggan'];

include '../../assets/mysql/connect.php';
$query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($query);

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Customer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarCustomer.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Settings</p>
                <p class="text-l text-[#5D5C61] font-regular">Settings Customer</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative inline-block text-left">
                    <button onclick="toggleDropdown()"
                        class="flex border-2 border-solid items-center bg-none rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-500 transition space-x-4">
                        <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>"
                            alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply">
                        <div class="text-left hidden sm:block">
                            <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                            <span class="block text-sm text-[#A2A1A8] font-semibold leading-4">Pelanggan</span>
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

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4">
            <?= $success ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="bg-[#EFE9E2] mt-8 rounded-2xl border border-[#A2845E] shadow-lg p-8">
            <form action="../../assets/mysql/pelanggan/proses.php" method="POST" enctype="multipart/form-data"
                id="profileForm">
                <input type="hidden" name="idPelanggan" value="<?= $idPelanggan ?>">

                <!-- Profile Picture Section -->
                <div class="flex items-center gap-6 mb-8">
                    <div class="relative">
                        <img src="../../assets/gambar/pelanggan/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>"
                            alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-[#A2845E]">
                        <label
                            class="absolute bottom-0 right-0 bg-[#A2845E] text-white p-2 rounded-full cursor-pointer hover:bg-[#8a715b] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <input type="file" name="photo" id="uploadPhoto" class="hidden" accept="image/*" disabled>
                        </label>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-[#3D3D3D]">Profile Picture</h2>
                        <p class="text-[#666666]">Click the icon to change your profile photo</p>
                    </div>
                </div>

                <!-- Profile Data Fields -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-[#3D3D3D]">Email</label>
                        <input type="email" name="email" value="<?= $dataPelanggan['emailPelanggan'] ?>" disabled
                            class="w-full p-3 border border-[#A2845E] rounded-lg bg-gray-100">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-[#3D3D3D]">Full Name</label>
                        <input type="text" name="nama" value="<?= $dataPelanggan['namaPelanggan'] ?>" disabled
                            class="w-full p-3 border border-[#A2845E] rounded-lg bg-gray-100">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-[#3D3D3D]">Zip Code</label>
                        <input type="text" name="kodepos" value="<?= $dataPelanggan['kodeposPelanggan'] ?>" disabled
                            class="w-full p-3 border border-[#A2845E] rounded-lg bg-gray-100">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-[#3D3D3D]">Telephone Number</label>
                        <input type="tel" name="telepon" value="<?= $dataPelanggan['teleponPelanggan'] ?>" disabled
                            class="w-full p-3 border border-[#A2845E] rounded-lg bg-gray-100">
                    </div>
                    <div class="space-y-2 col-span-2">
                        <label class="text-sm font-semibold text-[#3D3D3D]">Home Address</label>
                        <textarea name="alamat" disabled
                            class="w-full p-3 border border-[#A2845E] rounded-lg bg-gray-100 h-24"><?= $dataPelanggan['alamatPelanggan'] ?></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-span-2 mt-8 flex justify-end gap-4">
                    <button type="button" id="editButton" onclick="toggleEditMode()"
                        class="bg-[#A2845E] text-white px-8 py-3 rounded-full hover:bg-[#8a715b] transition">Edit Data
                        Profil</button>
                    <button type="submit" name="submitEdit" id="submitButton"
                        class="hidden bg-green-600 text-white px-8 py-3 rounded-full hover:bg-green-700 transition">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

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

    function toggleEditMode() {
        const inputs = document.querySelectorAll('#profileForm input:not([type="hidden"]), #profileForm textarea');
        const fileInput = document.getElementById('uploadPhoto');
        const editButton = document.getElementById('editButton');
        const submitButton = document.getElementById('submitButton');

        inputs.forEach(input => {
            input.disabled = !input.disabled;
            input.classList.toggle('bg-gray-100');
            input.classList.toggle('bg-white');
        });

        // Enable/disable file input
        fileInput.disabled = !fileInput.disabled;

        editButton.classList.toggle('hidden');
        submitButton.classList.toggle('hidden');
    }
    </script>
</body>

</html>