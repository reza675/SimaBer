<?php
session_start();
if (!isset($_SESSION['namaPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$nama = $_SESSION['namaPelanggan'];
$currentPage = 'dashboardCustomer.php';

include '../../assets/mysql/connect.php';
$query = mysqli_query($conn, "SELECT * FROM stokberas WHERE id = '1M' or id = '2M' or id = '3M' or id = '4SP' AND stokBeras > 0 AND beratBeras = 5 or beratBeras = 10");
$dataBeras = [];
while ($data = mysqli_fetch_array($query)) {
    $dataBeras[] = $data;
}
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
    <div class="main-container">
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
                    class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-md z-50"
                    style="width: 210px;">
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="../../assets/mysql/pelanggan/proses.php?logout=true"
                        class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-b-lg text-center">Log
                        Out</a>
                </div>
            </div>
        </div>

        <div class="bg-[#A2845E] py-10">
            <h1 class="font-semibold text-3xl text-white text-center mb-2">
                Bringing Quality to Your Table, One Grain at a Time. <br>
                Fresh, Premium Rice – Only at SimaBer.
            </h1>
            <p class="font-reguler text-lg text-[#CECCCC] text-center">We’re committed to bringing you premium rice at
                fair prices.<br>Experience quality, freshness, and trust in every grain.</p>
            <div class="w-full max-w-sm min-w-[200px] mx-auto mt-6">
                <div class="relative">
                    <input type="text"
                        class="w-full bg-black bg-opacity-30 placeholder:text-[#CECCCC] text-[#CECCCC] text-sm border border-slate-200 rounded-full py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                        placeholder="Search an Item" />

                    <button
                        class="absolute right-1 top-1 rounded bg-slate-800 p-1.5 border border-transparent rounded-full text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd"
                                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-2 mx-12 mt-4">
            <div>
                <h1 class="font-semibold text-3xl text-[#3D3D3D]">Featured Product</h1>
                <p class="font-reguler text-reguler text-[#3D3D3D]">Check out our best-selling and most recommended rice
                    products, carefully selected <br>for your daily needs.</p>
            </div>
            <div class="relative inline-block text-left">
                <a href="orderCustomer.php"
                    class="w-full inline-flex justify-center bg-[#A2845E] items-center py-3 px-4 hover:bg-[#D2B48C] text-white rounded-full font-semibold transition">
                    View All
                    <svg class="ml-2" width="10" height="12" viewBox="0 0 10 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M0.72013 15.5455C0.28079 15.1062 0.28079 14.3938 0.72013 13.9545L6.67463 8L0.72013 2.04549C0.28079 1.60616 0.28079 0.893845 0.72013 0.454506C1.15947 0.0151653 1.87178 0.0151653 2.31112 0.454506L9.06112 7.2045C9.50046 7.64384 9.50046 8.35616 9.06112 8.7955L2.31112 15.5455C1.87178 15.9848 1.15947 15.9848 0.72013 15.5455Z"
                            fill="white" />
                    </svg>

                </a>
            </div>
        </div>
        <div class="mx-12 mt-4 grid grid-cols-4 gap-6">
            <?php foreach ($dataBeras as $beras) :
                $diskon = rand(1, 9);
                ?>
            <div>
                <div class="relative max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm">
                    <button class="absolute left-2 top-2 z-1000 bg-[#3D3D3D] w-12 h-8 rounded-full text-white"> -<?= $diskon ?>%

                    </button>
                    <a href="#">
                        <img class="rounded-t-lg" src="../../assets/gambar/beras/<?= $beras['gambarBeras']?>" alt="" />

                    </a>
                </div>
                <a href="#">
                    <h5 class="mb-2 text-2xl font-regular tracking-tight text-[#404040] text-center">
                        <?= $beras['namaBeras'] ?></h5>

                    <div class="flex justify-between mb-3 items-center">
                        <p class="font-normal text-black">Rp <?= number_format($beras['hargaJualBeras'], 2, ',', '.') ?></p>
                        <div class="flex items-center gap-2 font-normal text-black">
                            <span><?= $beras['beratBeras'] ?> kg</span>
                            <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.5 0.5C4.725 0.5 0 5.225 0 11C0 16.775 4.725 21.5 10.5 21.5C16.275 21.5 21 16.775 21 11C21 5.225 16.275 0.5 10.5 0.5ZM10.5 20.225C5.4 20.225 1.275 16.1 1.275 11C1.275 5.9 5.4 1.775 10.5 1.775C15.6 1.775 19.725 5.9 19.725 11C19.725 16.1 15.6 20.225 10.5 20.225Z"
                                    fill="black" />
                                <path
                                    d="M11.1 7.32495H9.9V10.4H7.125V11.6H9.9V14.825H11.1V11.6H13.95V10.4H11.1V7.32495Z"
                                    fill="black" />
                            </svg>
                        </div>
                    </div>

                </a>

            </div>
            <?php endforeach; ?>



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