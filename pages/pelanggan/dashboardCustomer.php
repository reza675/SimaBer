<?php
session_start();
if (!isset($_SESSION['namaPelanggan']) || !isset($_SESSION['idPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}
$nama = $_SESSION['namaPelanggan'];
$idPelanggan = $_SESSION['idPelanggan'];
$currentPage = 'dashboardCustomer.php';

include '../../assets/mysql/connect.php';
$query = mysqli_query($conn, "SELECT * FROM stokberaspemilik WHERE idBeras = '1M' or idBeras = '2M' or idBeras = '3M' or idBeras = '4M' AND stokBeras > 0 AND beratBeras = 5 or beratBeras = 10");
$dataBeras = [];
while ($data = mysqli_fetch_array($query)) {
    $dataBeras[] = $data;
}

$query2 = mysqli_query($conn, "SELECT * FROM stokberaspemilik");
$stockBeras = 0;
while ($data2 = mysqli_fetch_array($query2)) {
    $stockBeras += $data2['stokBeras'];
}

$query3 = mysqli_query($conn, "SELECT * FROM pesananpemilik WHERE idPelanggan = '$idPelanggan' AND status_pengiriman = 'Completed'");
$dataPesananBeras = 0;
$dataTransksi = 0;
while ($data3 = mysqli_fetch_array($query3)) {
    $dataPesananBeras += $data3['jumlahPesanan'];
    $dataTransksi += $data3['hargaBeli'];
}

$q = mysqli_query($conn, "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($q);
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
    <?php include '../../layout/sidebarCustomer.php'; ?>
    <div class="main-container">
        <div class="flex justify-between items-center p-4 bg-[#A2845E] text-white">
            <div class="ml-12">
                <h1 class="text-lg text-black font-semibold">Hello <?= $nama; ?> 👋</h1>
                <p class="text-sm">Welcome to SimaBer</p>
            </div>

            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()"
                    class="flex border-2 border-solid items-center bg-[#A2845E] rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-200 transition space-x-4">
                    <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>"
                        alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
                    <div class="text-left hidden sm:block">
                        <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                        <span class="block text-sm text-white leading-4">Customer</span>
                    </div>
                    <svg class="w-5 h-5 text-black ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="dropdownProfile"
                    class="hidden absolute right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-md z-50"
                    style="width: 210px;">
                    <a href="settingsCustomer.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-semibold text-center">Settings</a>
                    <a href="../../assets/mysql/pelanggan/proses.php?logout=true"
                        class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-b-lg text-center font-semibold">Log
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
                    <form action="search.php" method="get">
                        <input type="text" name="inputSearch"
                            class="w-full bg-black bg-opacity-30 placeholder:text-[#CECCCC] text-[#CECCCC] text-sm border border-slate-200 rounded-full py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                            placeholder="Search an Item">
                        <button
                            class="absolute right-1 top-1 rounded bg-slate-800 p-1.5 border border-transparent rounded-full text-center text-sm text-white transition-all shadow-sm hover:shadow focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                            type="submit" name="search">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd"
                                    d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-2 mx-12 mt-4">
            <div>
                <h1 class="font-semibold text-3xl text-[#3D3D3D]">Featured Product</h1>
                <p class="font-reguler text-sm text-[#3D3D3D]">Check out our best-selling and most recommended rice
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
            <?php foreach ($dataBeras as $beras) :?>
            <div>
                <div
                    class="relative max-w-sm bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                    
                    <a href="detailProduct.php?id=<?= $beras['idBeras']?>&from=dashboard">
                        <img class="w-full h-auto object-cover"
                            src="../../assets/gambar/beras/<?= $beras['gambarBeras']?>" alt="" />

                    </a>
                </div>
                <a href="detailProduct.php?id=<?= $beras['idBeras'] ?>">
                    <h5 class="mb-2 text-2xl font-regular tracking-tight text-[#404040] text-center">
                        <?= $beras['namaBeras'] ?></h5>

                    <div class="flex justify-between mb-3 items-center">
                        <p class="font-normal text-black">Rp <?= number_format($beras['hargaJualBeras'], 2, ',', '.') ?>
                        </p>
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
        <div class="flex justify-center items-center flex-col mt-12 py-4">
            <h1 class="font-semibold text-3xl text-[#3D3D3D] text-center">Find the Perfect Rice for Every Need</h1>
            <p class="font-reguler text- text-[#3D3D3D] text-center">Whether you're cooking for family or preparing
                something special, we've got the<br>perfect rice to match every moment.</p>
        </div>

        <div class="mx-[150px] mt-4 mb-12 grid grid-cols-3 auto-rows-[200px] gap-6 ">
            <div
                class="relative row-span-2 rounded-lg overflow-hidden transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <a href="detailProduct.php?id=1M&from=dashboard">
                    <img src="../../assets/gambar/pelanggan/UI4.avif" class="w-full h-full object-cover" />
                    <span class="absolute bottom-4 left-4 text-white text-lg font-semibold">Premium Rice</span>
                </a>
            </div>

            <div
                class="relative rounded-lg overflow-hidden transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <a href="detailProduct.php?id=2M&from=dashboard">
                    <img src="../../assets/gambar/pelanggan/UI1.jpg" class="w-full h-full object-cover" />
                    <span class="absolute bottom-4 left-4 text-white text-lg font-semibold">Daily Rice</span>
                </a>
            </div>

            <div
                class="relative row-span-2 rounded-lg overflow-hidden transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <a href="detailProduct.php?id=3M&from=dashboard">
                    <img src="../../assets/gambar/pelanggan/UI3.webp" class="w-full h-full object-cover" />
                    <span class="absolute bottom-4 left-4 text-white text-lg font-semibold">Specialty Rice</span>
                </a>
            </div>

            <div
                class="relative rounded-lg overflow-hidden transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <a href="detailProduct.php?id=4SP&from=dashboard">
                    <img src="../../assets/gambar/pelanggan/UI2.jpeg" class="w-full h-full object-cover" />
                    <span class="absolute bottom-4 left-4 text-white text-lg font-semibold">Organic Rice</span>
                </a>
            </div>
        </div>
        <div class="bg-[#A2845E] pt-6 mt-6 mb-6">
            <div class="container mx-auto px-6 lg:px-36 
              flex flex-col lg:flex-row items-start gap-[300px]">

                <div class="flex-shrink-0 space-y-4">
                    <h2 class="text-2xl font-semibold text-white mx-6">Why Choose SimaBer?</h2>
                    <a href="OrderCustomer.php" class="inline-flex items-center bg-black text-white font-semibold 
                py-3 px-6 rounded-full hover:bg-gray-800 transition mx-6">
                        Get Started
                        <svg class="ml-2" width="10" height="12" viewBox="0 0 10 16" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M0.72013 15.5455C0.28079 15.1062 0.28079 14.3938 0.72013 13.9545L6.67463 8L0.72013 2.04549C0.28079 1.60616 0.28079 0.893845 0.72013 0.454506C1.15947 0.0151653 1.87178 0.0151653 2.31112 0.454506L9.06112 7.2045C9.50046 7.64384 9.50046 8.35616 9.06112 8.7955L2.31112 15.5455C1.87178 15.9848 1.15947 15.9848 0.72013 15.5455Z"
                                fill="white" />
                        </svg>
                    </a>
                    <img src="../../assets/gambar/pelanggan/Dashboard1_pelanggan.png" alt="Thinking Person"
                        class="mt-4 mx-16" style="width: 200px; height: 200px;" />
                </div>

                <div class="flex-1 space-y-8 text-white">
                    <p class="text-sm lg:text-base">
                        SimaBer is committed to delivering the finest rice — fresh, clean, and full of natural taste.
                        Trusted by families and culinary businesses alike.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-5xl font-bold">99%</h3>
                            <p class="mt-2 text-sm">
                                Of our customers are satisfied with the freshness, texture, and aroma of our rice.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-5xl font-bold">100%</h3>
                            <p class="mt-2 text-sm">
                                Quality-checked and hygienically packaged rice, ready to be delivered to your doorstep.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-6 mt-6 mx-auto max-w-4xl grid grid-cols-1 sm:grid-cols-2 auto-rows-[100px] gap-6">
            <div
                class="relative row-span-2 bg-[#FFF1CB] rounded-lg overflow-hidden flex flex-col p-6 transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <p class="text-[#383E49] text-xl font-semibold mb-4">Total Transaction</p>
                <div class="flex-1 flex items-center justify-center">
                    <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M55.1511 47.2679C54.6384 45.815 53.6883 44.5571 52.4319 43.6675C51.1755 42.7779 49.6745 42.3003 48.1357 42.3005V40.438H44.416V42.3005C42.4429 42.3005 40.5507 43.0854 39.1555 44.4826C37.7604 45.8798 36.9766 47.7747 36.9766 49.7506C36.9766 51.7265 37.7604 53.6215 39.1555 55.0187C40.5507 56.4158 42.4429 57.2007 44.416 57.2007V64.6509C42.7979 64.6509 41.4198 63.6172 40.9065 62.1681C40.8303 61.931 40.7074 61.7117 40.5451 61.523C40.3828 61.3343 40.1844 61.1801 39.9616 61.0696C39.7387 60.959 39.496 60.8943 39.2478 60.8794C38.9996 60.8644 38.7508 60.8994 38.5164 60.9824C38.2819 61.0653 38.0664 61.1945 37.8827 61.3624C37.6989 61.5302 37.5507 61.7332 37.4466 61.9594C37.3426 62.1856 37.2849 62.4304 37.277 62.6793C37.2691 62.9282 37.3111 63.1762 37.4006 63.4086C37.9134 64.8614 38.8634 66.1193 40.1199 67.009C41.3763 67.8986 42.8773 68.3762 44.416 68.3759V70.2384H48.1357V68.3759C50.1088 68.3759 52.0011 67.591 53.3962 66.1938C54.7914 64.7967 55.5752 62.9017 55.5752 60.9258C55.5752 58.9499 54.7914 57.0549 53.3962 55.6578C52.0011 54.2606 50.1088 53.4757 48.1357 53.4757V46.0256C48.905 46.0254 49.6554 46.264 50.2836 46.7087C50.9118 47.1533 51.3869 47.7821 51.6434 48.5083C51.8077 48.9741 52.15 49.3555 52.5951 49.5686C52.8154 49.6741 53.0544 49.7351 53.2983 49.7481C53.5422 49.7611 53.7863 49.7259 54.0166 49.6445C54.2469 49.563 54.459 49.4369 54.6407 49.2734C54.8223 49.1099 54.97 48.9122 55.0754 48.6915C55.1807 48.4708 55.2416 48.2315 55.2546 47.9872C55.2676 47.743 55.2325 47.4985 55.1511 47.2679ZM44.416 46.0256C43.4295 46.0256 42.4834 46.418 41.7858 47.1166C41.0882 47.8152 40.6963 48.7627 40.6963 49.7506C40.6963 50.7386 41.0882 51.6861 41.7858 52.3846C42.4834 53.0832 43.4295 53.4757 44.416 53.4757V46.0256ZM48.1357 64.6509C49.1223 64.6509 50.0684 64.2584 50.766 63.5598C51.4636 62.8612 51.8555 61.9138 51.8555 60.9258C51.8555 59.9379 51.4636 58.9904 50.766 58.2918C50.0684 57.5932 49.1223 57.2007 48.1357 57.2007V64.6509Z"
                            fill="#DBA362" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M26.101 17.3817C30.6651 15.1448 38.1529 12.5 46.3475 12.5C54.3709 12.5 61.6616 15.0349 66.2015 17.242L66.4582 17.3668C67.827 18.0447 68.9318 18.6855 69.7092 19.2051L62.8389 29.2628C78.6775 45.4779 90.9117 79.5455 46.3475 79.5455C1.7833 79.5455 13.6883 46.0963 29.6645 29.2628L22.8388 19.2051C23.3652 18.8605 24.0328 18.4601 24.8289 18.0354C25.2232 17.8231 25.6472 17.604 26.101 17.3817ZM58.4236 29.1305L63.9232 21.0788C58.8086 21.4476 52.7231 22.6508 46.8645 24.3494C42.6798 25.5601 38.0302 25.3757 33.7246 24.5804C32.6396 24.3792 31.562 24.1394 30.494 23.8614L34.0649 29.1268C41.7183 31.8554 50.7684 31.8554 58.4236 29.1305ZM31.9168 32.2987C40.872 35.763 51.6351 35.763 60.5903 32.295C64.3274 36.2419 67.4543 40.7261 69.8673 45.599C72.3818 50.7359 73.7507 55.8708 73.5275 60.2962C73.3118 64.567 71.6342 68.2157 67.8029 70.9275C63.8097 73.753 57.0938 75.8204 46.3456 75.8204C35.5863 75.8204 28.8387 73.7884 24.8084 70.9946C20.9492 68.3181 19.2549 64.7179 19.0149 60.5048C18.7639 56.1279 20.103 51.019 22.6082 45.8467C24.9981 40.9147 28.3328 36.1672 31.9168 32.2987ZM29.778 19.7937C31.2659 20.2369 32.8207 20.6225 34.3979 20.9149C38.3966 21.6525 42.4064 21.7605 45.8286 20.7678C49.8164 19.6039 53.8725 18.6889 57.9735 18.028C54.5513 16.9961 50.5489 16.2251 46.3456 16.2251C39.9384 16.2251 33.9627 18.0149 29.778 19.7937Z"
                            fill="#DBA362" />
                    </svg>
                </div>
                <div>
                    <p class="text-[#383E49] text-lg font-bold mb-4 mt-5 text-center">Rp <?= number_format($dataTransksi, 2, ',', '.'); ?></p>
                </div>
            </div>
            <div
                class="relative row-span-2 bg-[#FFF1CB] rounded-lg overflow-hidden flex flex-col p-6 transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <p class="text-[#383E49] text-xl font-semibold mb-4">Purchased Rice</p>
                <div class="flex-1 flex items-center justify-center">
                    <svg width="65" height="61" viewBox="0 0 65 61" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M53.9873 0.900391L54.2461 0.908203C55.4477 0.982866 56.5694 1.57402 57.3564 2.52441L57.5205 2.7334V2.73438L63.9756 11.6963L64.0898 11.8545H64.0488C64.0622 11.8872 64.0764 11.92 64.085 11.9541C64.1057 12.0368 64.107 12.1267 64.0693 12.2188V56.1162L64.0645 56.3164C63.9586 58.3749 62.229 60.0996 60.0664 60.0996H4.90332C2.67175 60.0996 0.900391 58.3374 0.900391 56.1162V12.2031C0.900391 12.0253 0.98854 11.8533 1.06055 11.71L1.06348 11.7031L1.06836 11.6963L7.52344 2.73438C8.29449 1.58613 9.67645 0.900497 11.0566 0.900391H53.9873ZM2.60059 56.1162C2.60072 57.3325 3.60949 58.4062 4.90332 58.4062H60.1416C61.3652 58.4062 62.4432 57.4024 62.4434 56.1162V13.0488H41.6279L41.6289 25.7207L41.6143 25.9297C41.5464 26.4141 41.2499 26.8716 40.834 27.1475L40.8262 27.1523L40.8174 27.1562C40.2769 27.3865 39.7158 27.4714 39.1523 27.1514V27.1504L32.5225 23.5918L25.8984 27.1475L25.8037 27.2041C25.576 27.3276 25.304 27.3887 25.0918 27.3887C24.8167 27.3886 24.5366 27.329 24.3066 27.2051L24.2109 27.1475C23.7374 26.8337 23.4161 26.3569 23.416 25.7207V13.0488H2.60059V56.1162ZM25.1172 12.3662V25.7695H25.1406L32.4746 21.749L32.5225 21.7227L32.5693 21.748L39.9023 25.6943H39.9277V12.29L37.1748 2.59375H27.9453L25.1172 12.3662ZM41.4551 11.3555H61.6738L56.1602 3.6748V3.67578C55.6836 3.06656 54.9533 2.64845 54.1494 2.59863L53.9873 2.59375H38.96L41.4551 11.3555ZM11.0576 2.59375C10.185 2.59375 9.39231 2.95469 8.8877 3.67188H8.88672L3.37207 11.3555H23.666L26.1611 2.59375H11.0576Z"
                            fill="#DBA362" stroke="#DBA362" stroke-width="0.2" />
                        <path d="M38.626 11.356V13.0493H26.418V11.356H38.626Z" fill="#DBA362" stroke="#DBA362"
                            stroke-width="0.2" />
                    </svg>
                </div>
                <div>
                    <p class="text-[#383E49] text-lg font-bold mb-4 mt-5 text-center"><?= $dataPesananBeras; ?> Rice</p>
                </div>
            </div>
            <div
                class="relative row-span-2 bg-[#FFF1CB] rounded-lg overflow-hidden flex flex-col p-6 transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <p class="text-[#383E49] text-xl font-semibold mb-4">Total Product</p>
                <div class="flex-1 flex items-center justify-center">
                    <svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 54H42V60H24V54ZM24 66H54V72H24V66Z" fill="#DBA362" />
                        <path
                            d="M78 12H18C16.4094 12.0024 14.8847 12.6353 13.76 13.76C12.6353 14.8847 12.0024 16.4094 12 18V78C12.0024 79.5906 12.6353 81.1153 13.76 82.24C14.8847 83.3647 16.4094 83.9976 18 84H78C79.5906 83.9976 81.1153 83.3647 82.24 82.24C83.3647 81.1153 83.9976 79.5906 84 78V18C83.9976 16.4094 83.3647 14.8847 82.24 13.76C81.1153 12.6353 79.5906 12.0024 78 12ZM54 18V30H42V18H54ZM18 78V18H36V36H60V18H78L78.003 78H18Z"
                            fill="#DBA362" />
                    </svg>
                </div>
                <div>
                    <p class="text-[#383E49] text-lg font-bold mb-4 mt-5 text-center"><?= $stockBeras; ?> Rice</p>
                </div>
            </div>
            <div
                class="relative row-span-2 bg-[#FFF1CB] rounded-lg overflow-hidden flex flex-col p-6 transition duration-300 ease-in-out hover:scale-105 hover:shadow-lg">
                <p class="text-[#383E49] text-xl font-semibold mb-4">Status Account</p>
                <div class="flex-1 flex items-center justify-center">
                    <svg width="101" height="101" viewBox="0 0 101 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M33.6667 29.4583C33.6667 24.9939 35.4402 20.7122 38.597 17.5554C41.7539 14.3985 46.0355 12.625 50.5 12.625C54.9645 12.625 59.2461 14.3985 62.403 17.5554C65.5598 20.7122 67.3333 24.9939 67.3333 29.4583C67.3333 33.9228 65.5598 38.2044 62.403 41.3613C59.2461 44.5182 54.9645 46.2917 50.5 46.2917C46.0355 46.2917 41.7539 44.5182 38.597 41.3613C35.4402 38.2044 33.6667 33.9228 33.6667 29.4583ZM33.6667 54.7083C28.0861 54.7083 22.734 56.9252 18.788 60.8713C14.8419 64.8174 12.625 70.1694 12.625 75.75C12.625 79.0984 13.9551 82.3096 16.3228 84.6772C18.6904 87.0449 21.9016 88.375 25.25 88.375H75.75C79.0984 88.375 82.3096 87.0449 84.6772 84.6772C87.0449 82.3096 88.375 79.0984 88.375 75.75C88.375 70.1694 86.1581 64.8174 82.212 60.8713C78.266 56.9252 72.9139 54.7083 67.3333 54.7083H33.6667Z"
                            fill="#DBA362" />
                    </svg>

                </div>
                <div>
                    <p class="text-[#383E49] text-lg font-bold mb-4 mt-4 text-center">Customer</p>
                </div>


            </div>
        </div>

        <div class="relativ h-[600px] isolate overflow-hidden bg-custom">
            <div class="py-24 max-w-6xl mx-auto flex flex-col md:flex-row gap-12">
                <div class="flex flex-col text-left basis-1/2 relative h-[420px]">
                    <p class="sm:text-4xl text-3xl font-semibold text-[#3D3D3D]">Frequently Asked Questions</p>
                    <p class="mt-4 text-lg text-[#666666]">Find quick answers to common questions about our rice
                        products, delivery, and services.</p>

                    <img src="../../assets/gambar/pelanggan/Dashboard2_pelanggan.png" alt="faq"
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 object-cover w-[300px] h-[350px]">
                </div>


                <ul class="basis-1/2">
                    <li class='group relative'>
                        <button
                            class="relative  flex gap-2 items-center w-full py-5 text-base font-semibold text-left border-t md:text-lg border-base-content/10"
                            aria-expanded="false">
                            <span class="flex-1 text-md font-regular">What types of rice does SimaBer offer?</span>
                            <svg class="flex-shrink-0 w-4 h-4  ml-auto fill-current" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="16" height="2" rx="1"
                                    class="transform origin-center transition duration-200 ease-out false"></rect>
                                <rect y="7" width="16" height="2" rx="1"
                                    class="block group-hover:opacity-0 origin-center rotate-90 transition duration-200 ease-out false">
                                </rect>
                            </svg>
                        </button>
                        <div class="transition-all duration-300 ease-in-out group-hover:max-h-60 max-h-0 overflow-hidden"
                            style={{ transition: "max-height 0.3s ease-in-out 0s"}}>
                            <div class="pb-5 leading-relaxed">
                                <div class="space-y-2 leading-relaxed text-md font-regular text-[#666666]"> We offer a
                                    wide variety of rice including premium white rice, <br> organic rice, daily rice,
                                    organic rice, and specialty regional varieties <br> tailored to your needs.</div>
                            </div>
                        </div>
                    </li>
                    <li class='group'>
                        <button
                            class="relative flex gap-2 items-center w-full py-5 text-base font-semibold text-left border-t md:text-lg border-base-content/10"
                            aria-expanded="false">
                            <span class="flex-1 font-regular">Is the rice at SimaBer hygienically processed?</span>
                            <svg class="flex-shrink-0 w-4 h-4 ml-auto fill-current" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="16" height="2" rx="1"
                                    class="transform origin-center transition duration-200 ease-out false"></rect>
                                <rect y="7" width="16" height="2" rx="1"
                                    class="group-hover:opacity-0 transform origin-center rotate-90 transition-all duration-200 ease-out false">
                                </rect>
                            </svg>
                        </button>
                        <div class="transition-all duration-300 ease-in-out group-hover:max-h-60 max-h-0 overflow-hidden"
                            style={{ transition: "max-height 0.3s ease-in-out 0s" }}>
                            <div class="pb-5 leading-relaxed">
                                <div class="space-y-2 leading-relaxed text-md font-regular text-[#666666]"> Yes, the
                                    rice we sell is hygienically processed. We work directly with <br> trusted farmers
                                    and ensure the rice is cleaned and packaged properly <br> before reaching our
                                    customers.</div>
                            </div>
                        </div>
                    </li>
                    <li class='group'>
                        <button
                            class="relative flex gap-2 items-center w-full py-5 text-base font-semibold text-left border-t md:text-lg border-base-content/10"
                            aria-expanded="false">
                            <span class="flex-1 font-regular">How long does delivery usually take?</span>
                            <svg class="flex-shrink-0 w-4 h-4 ml-auto fill-current" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="16" height="2" rx="1"
                                    class="transform origin-center transition duration-200 ease-out false"></rect>
                                <rect y="7" width="16" height="2" rx="1"
                                    class="group-hover:opacity-0 transform origin-center rotate-90 transition duration-200 ease-out false">
                                </rect>
                            </svg>
                        </button>
                        <div class="transition-all duration-300 ease-in-out group-hover:max-h-60 max-h-0 overflow-hidden"
                            style={{ transition: "max-height 0.3s ease-in-out 0s" }}>
                            <div class="pb-5 leading-relaxed">
                                <div class="space-y-2 leading-relaxed text-md font-regular text-[#666666]"> Delivery
                                    usually takes 1–2 working days, depending on your location. <br> We’ll provide
                                    tracking info as soon as your order is shipped.</div>
                            </div>
                        </div>
                    </li>
                    <li class='group'>
                        <button
                            class="relative flex gap-2 items-center w-full py-5 text-base font-semibold text-left border-t md:text-lg border-base-content/10"
                            aria-expanded="false">
                            <span class="flex-1 font-regular">Can I order in bulk for business or events?</span>
                            <svg class="flex-shrink-0 w-4 h-4 ml-auto fill-current" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="16" height="2" rx="1"
                                    class="transform origin-center transition duration-200 ease-out false"></rect>
                                <rect y="7" width="16" height="2" rx="1"
                                    class="group-hover:opacity-0 transform origin-center rotate-90 transition duration-200 ease-out false">
                                </rect>
                            </svg>
                        </button>
                        <div class="transition-all duration-300 ease-in-out group-hover:max-h-60 max-h-0 overflow-hidden"
                            style={{ transition: "max-height 0.3s ease-in-out 0s" }}>
                            <div class="pb-5 leading-relaxed">
                                <div class="space-y-2 leading-relaxed text-md font-regular text-[#666666]"> Yes, we
                                    accept bulk orders for businesses, events, or community needs. <br> Contact our team
                                    for special pricing and delivery arrangements tailored <br>to large quantities.
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class='group'>
                        <button
                            class="relative flex gap-2 items-center w-full py-5 text-base font-semibold text-left border-t md:text-lg border-base-content/10"
                            aria-expanded="false">
                            <span class="flex-1 font-regular">Is SimaBer's rice tested for quality and safety?</span>
                            <svg class="flex-shrink-0 w-4 h-4 ml-auto fill-current" viewBox="0 0 16 16"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect y="7" width="16" height="2" rx="1"
                                    class="transform origin-center transition duration-200 ease-out false"></rect>
                                <rect y="7" width="16" height="2" rx="1"
                                    class="group-hover:opacity-0 transform origin-center rotate-90 transition duration-200 ease-out false">
                                </rect>
                            </svg>
                        </button>
                        <div class="transition-all duration-300 ease-in-out group-hover:max-h-60 max-h-0 overflow-hidden"
                            style={{ transition: "max-height 0.3s ease-in-out 0s" }}>
                            <div class="pb-5 leading-relaxed">
                                <div class="space-y-2 leading-relaxed text-md font-regular text-[#666666]"> Absolutely.
                                    Every batch of rice is carefully inspected and tested <br> to ensure it meets high
                                    standards of hygiene, moisture content, <br> and grain quality. We follow strict
                                    food safety protocols from <br>sourcing to packaging.</div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
<?php include '../../layout/footer.php'; ?>

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