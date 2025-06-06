<?php
session_start();
if (!isset($_SESSION['namaPelanggan']) || !isset($_SESSION['idPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$nama = $_SESSION['namaPelanggan'];
$idPelanggan = $_SESSION['idPelanggan'];
$currentPage = 'orderCustomer.php';
$idBeras = $_GET['id'];
$from = $_GET['from'];
if ($from === 'dashboard') {
    $backURL = 'dashboardCustomer.php';
} else {
    $backURL = 'orderCustomer.php';
}
include '../../assets/mysql/connect.php';
$query = mysqli_query($conn, "SELECT * FROM stokberasPemilik WHERE idBeras = '$idBeras'");
$product = mysqli_fetch_assoc($query);
$namaBeras = $product['namaBeras'];
$queryWeights = mysqli_query($conn, "SELECT * FROM stokberasPemilik WHERE namaBeras = '$namaBeras'");
$q = mysqli_query($conn, "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Product SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
    <style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarCustomer.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Detail Product</p>
                <p class="text-l text-[#5D5C61] font-regular">Detail Product SimaBer</p>
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
                        <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>" alt="User"
                            class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
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
            <a href="<?= $backURL?>" class="flex items-center text-gray-600 hover:text-gray-900">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path d="M21 12L3.5 12" stroke="black" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M10 5L3 12L10 19" stroke="black" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <span class="ml-2 text-semibold text-2xl font-semibold text-black">Detail Product</span>
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="relative">
                <img src="../../assets/gambar/beras/<?= $product['gambarBeras'] ?>" alt="<?= ($product['namaBeras']) ?>"
                    class="w-[520px] h-[500px] object-cover  rounded-2xl shadow" />
            </div>

            <div>
                <h1 class="text-3xl font-bold text-[#16151C]"><?= ($product['namaBeras']) ?></h1>
                <p class="text-2xl text-black font-md mt-2">Rp
                    <?= number_format($product['hargaJualBeras'],0,',','.') ?></p>
                <p class="mt-4 text-black leading-relaxed"><?= $product['deskripsiBeras']?></p>

                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-800">Weight</p>
                    <div class="flex gap-3 mt-2">
                        <?php while ($weightProduct = mysqli_fetch_assoc($queryWeights)) : ?>
                        <a href="detailProduct.php?id=<?= $weightProduct['idBeras'] ?>&from=<?= $from ?>"
                            class="px-4 py-2 border border-black hover:border-[#A2845E] transition <?= ($idBeras == $weightProduct['idBeras'] ? 'bg-[#A2845E] text-white' : '' )?>">
                            <?= $weightProduct['beratBeras'] ?> kg
                        </a>
                        <?php endwhile; ?>
                    </div>
                    <p class="text-sm text-gray-black mt-4 underline">Weight Guide</p>
                    <p class="text-xs text-gray-800 mt-1">Weight in kg</p>
                </div>

                <div class="mt-8 flex items-center gap-6">
                    <form action="../../assets/mysql/pelanggan/proses.php" method="post" class="w-full">
                        <input type="hidden" name="idBeras" value="<?= $idBeras ?>">
                        <input type="hidden" name="quantity" id="formQuantity" value="1">
                        <input type="hidden" name="harga" value="<?= $product['hargaJualBeras'] ?>">
                        <button type="submit" name="beliBeras"
                            class="w-full px-6 py-3 mt-7 bg-[#8C5E3C] text-white font-medium rounded-lg shadow hover:bg-[#79513a] transition">
                            Add to Cart Rp <span
                                id="totalPrice"><?= number_format($product['hargaJualBeras'], 2, ',', '.') ?></span>
                        </button>
                    </form>
                    <div class="flex flex-col items-center">
                        <p class="text-sm font-medium text-gray-800 mb-2">Quantity</p>
                        <div class="flex items-center border border-black">
                            <button id="decrement" class="px-2 py-2 text-xl">-</button>
                            <input type="number" id="quantity" value="1" min="1"
                                class="w-12 text-center bg-[#EFE9E2] border-none focus:outline-none">
                            <button id="increment" class="px-2 py-2 text-xl">+</button>
                        </div>
                    </div>
                </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const decrementButton = document.getElementById('decrement');
        const incrementButton = document.getElementById('increment');
        const totalPriceSpan = document.getElementById('totalPrice');
        const formQuantity = document.getElementById('formQuantity');
        const pricePerUnit = <?= $product['hargaJualBeras'] ?>;

        function updateTotalPrice() {
            const quantity = parseInt(quantityInput.value);
            const totalPrice = pricePerUnit * quantity;
            totalPriceSpan.textContent = totalPrice.toLocaleString('id-ID');
            formQuantity.value = quantity;
        }

        decrementButton.addEventListener('click', function(e) {
            e.preventDefault();
            let currentVal = parseInt(quantityInput.value);
            if (currentVal > 1) {
                quantityInput.value = currentVal - 1;
                updateTotalPrice();
            }
        });

        incrementButton.addEventListener('click', function(e) {
            e.preventDefault();
            let currentVal = parseInt(quantityInput.value);
            quantityInput.value = currentVal + 1;
            updateTotalPrice();
        });

        quantityInput.addEventListener('change', function() {
            let currentVal = parseInt(this.value);
            if (currentVal < 1) {
                this.value = 1;
            }
            updateTotalPrice();
        });
    });
</script>


</html>