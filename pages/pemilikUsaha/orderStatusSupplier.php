<?php
session_start();
if (!isset($_SESSION['namaPemilik']) || !isset($_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}
$nama = $_SESSION['namaPemilik'];
$idPemilik = $_SESSION['idPemilik'];
$currentPage = 'orderStatusSupplier.php';
include '../../assets/mysql/connect.php';
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '$idPemilik'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);

// Ambil data pesanan yang sudah di-approve untuk pemilik usaha ini
$query = mysqli_query($conn, "
    SELECT 
        pp.*,
        b.namaBeras,
        b.gambarBeras,
        b.beratBeras,
        p.namaPemasok AS supplierName,
        p.nomorHPPemasok AS supplierContact,  
        po.namaPemilik AS customerName,
        po.alamatPemilik AS alamatCustomer,
        COALESCE(LOWER(pp.status_pengiriman), 'order placed') AS status_normalized,
        DATE_FORMAT(pp.waktu_approve, '%h:%i %p') AS waktu_approve_formatted
    FROM pesananpemasok pp
    JOIN stokberaspemasok b ON pp.idBeras = b.idBeras
    JOIN pemasok p ON pp.idPemasok = p.idPemasok
    JOIN pemilikusaha po ON pp.idPemilik = po.idPemilik
    WHERE pp.status = 'approved' AND pp.idPemilik = '$idPemilik'
    ORDER BY pp.tanggalPesanan DESC
");

$dataPesanan = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataPesanan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
    <style>
    .status-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 40px 0;
    }

    .status-stepper::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #E5E7EB;
        z-index: 1;
    }

    .status-step {
        position: relative;
        z-index: 2;
        text-align: center;
        width: 25%;
    }

    .status-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #EFE9E2;
        border: 2px solid #E5E7EB;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        transition: all 0.3s ease;
    }

    .status-step.active .status-dot {
        background: #A2845E;
        border-color: #A2845E;
        color: white;
    }

    .status-label {
        font-size: 14px;
        color: #6B7280;
    }

    .status-step.active .status-label {
        color: #16151C;
        font-weight: 600;
    }

    .status-time {
        font-size: 12px;
        color: #6B7280;
        margin-top: 4px;
    }

    .order-item.selected {
        background-color: #f3f4f6;
        border-color: #A2845E;
        border-width: 2px;
    }

    .supplier-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }

    .supplier-icon {
        background-color: #EFE9E2;
        border-radius: 8px;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    </style>
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarBusinessOwner.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Order Status</p>
                <p class="text-l text-[#5D5C61] font-regular">Order Status Supplier</p>
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
                        <a href="settingsBusinessOwner.php"
                            class="block font-semibold px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-center">Settings</a>
                        <a href="../../assets/mysql/pemilikUsaha/proses.php?logout=true"
                            class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log
                            Out</a>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        if (isset($_GET['search']) && $_GET['search'] === 'notfound') :?>
        <div class="mt-4 mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <p>Keyword not found</p>
        </div>
        <?php endif; ?>

        <!-- Tampilan Utama -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-[#16151C] mb-4">Approved Orders</h3>
                <div class="space-y-4">
                    <?php if (empty($dataPesanan)): ?>
                    <p class="text-gray-500">No approved orders found</p>
                    <?php else: ?>
                    <?php foreach ($dataPesanan as $index => $pesanan): ?>
                    <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer order-item
                                <?= $index === 0 ? 'selected' : '' ?>" data-id="<?= $pesanan['idPesanan'] ?>"
                        data-beras="<?= htmlspecialchars($pesanan['namaBeras']) ?>"
                        data-berat="<?= htmlspecialchars($pesanan['beratBeras']) ?>"
                        data-jumlah="<?= htmlspecialchars($pesanan['jumlahPesanan']) ?>"
                        data-supplier="<?= htmlspecialchars($pesanan['supplierName']) ?>"
                        data-contact="<?= htmlspecialchars($pesanan['supplierContact']) ?>"
                        data-tanggal="<?= $pesanan['waktu_approve_formatted'] ?: date('h:i A', strtotime($pesanan['tanggalPesanan'])) ?>"
                        data-status="<?= $pesanan['status_normalized'] ?>">
                        <div class="flex justify-between">
                            <div>
                                <h4 class="font-semibold"><?= htmlspecialchars($pesanan['namaBeras']) ?></h4>
                                <p class="text-sm text-gray-600">
                                    <?= date('M d, Y', strtotime($pesanan['tanggalPesanan'])) ?></p>
                            </div>
                            <span
                                class="p-2 mt-2 text-xs font-semibold rounded-full bg-green-200 text-green-900 capitalize">
                                <?= $pesanan['status_normalized'] ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Pesanan -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-[#16151C] mb-6">Order Tracking</h3>
                <div class="status-stepper">
                    <div class="status-step" data-step="order placed">
                        <div class="status-dot">1</div>
                        <div class="status-label">Order Placed</div>
                        <div class="status-time" id="time-order-placed">
                            <?= $dataPesanan[0]['waktu_approve_formatted'] ?? '10:30 AM' ?></div>
                    </div>
                    <div class="status-step" data-step="packaging">
                        <div class="status-dot">2</div>
                        <div class="status-label">Packaging</div>
                        <div class="status-time" id="time-packaging">On Process</div>
                    </div>
                    <div class="status-step" data-step="on the road">
                        <div class="status-dot">3</div>
                        <div class="status-label">On The Road</div>
                        <div class="status-time" id="time-on-the-road">Pending</div>
                    </div>
                    <div class="status-step" data-step="delivered">
                        <div class="status-dot">4</div>
                        <div class="status-label">Delivered</div>
                        <div class="status-time" id="time-delivered">Pending</div>
                    </div>
                </div>

                <!-- Order Detail -->
                <div class="mt-8 border-t pt-6">
                    <h4 class="text-lg font-bold text-[#16151C] mb-4">Order Detail</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="font-semibold text-gray-700">Item Name</p>
                            <p class="text-[#16151C] font-medium" id="detail-beras">-</p>

                            <div class="mt-4 grid grid-cols-1 gap-2">
                                <div>
                                    <p class="font-semibold text-gray-700">Weight</p>
                                    <p class="text-[#16151C]" id="detail-berat">-</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Much</p>
                                    <p class="text-[#16151C]" id="detail-jumlah">-</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="font-semibold text-gray-700">Shipping From</p>

                            <div class="supplier-info">
                                <div class="supplier-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#A2845E]" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-[#16151C] mt-1" id="detail-supplier">-</p>
                                    <p class="text-gray-600 mt-1" id="detail-contact">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderItems = document.querySelectorAll('.order-item');
        const statusSteps = document.querySelectorAll('.status-step');

        function initStatusColors() {
            orderItems.forEach(item => {
                const status = item.dataset.status || 'order placed';
                const statusSpan = item.querySelector('span');
                if (statusSpan) {
                    statusSpan.className = getStatusClass(status);
                }
            });
        }

        // Fungsi untuk mengupdate tampilan detail pesanan dan status
        function updateOrderDetail(data) {
            document.getElementById('detail-beras').textContent = data.beras;
            document.getElementById('detail-berat').textContent = data.berat;
            document.getElementById('detail-jumlah').textContent = data.jumlah;
            document.getElementById('detail-supplier').textContent = data.supplier;
            document.getElementById('detail-contact').textContent = "Contact: " + data.contact;
            document.getElementById('time-order-placed').textContent = data.tanggal || '10:30 AM';

            // Reset semua status
            statusSteps.forEach(step => {
                step.classList.remove('active');
                step.querySelector('.status-dot').style.backgroundColor = '#EFE9E2';
                step.querySelector('.status-dot').style.borderColor = '#E5E7EB';
                step.querySelector('.status-label').style.color = '#6B7280';
                step.querySelector('.status-label').style.fontWeight = 'normal';
            });

            // Set status aktif berdasarkan data
            const currentStatus = data.status || 'order placed';
            const activeStep = document.querySelector(`.status-step[data-step="${currentStatus}"]`);
            if (activeStep) {
                activeStep.classList.add('active');
                activeStep.querySelector('.status-dot').style.backgroundColor = '#A2845E';
                activeStep.querySelector('.status-dot').style.borderColor = '#A2845E';
                activeStep.querySelector('.status-dot').style.color = 'white';
                activeStep.querySelector('.status-label').style.color = '#16151C';
                activeStep.querySelector('.status-label').style.fontWeight = '600';
            }
        }

        // Event listener untuk klik pesanan
        orderItems.forEach(item => {
            item.addEventListener('click', function() {
                orderItems.forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');

                // Update detail pesanan
                updateOrderDetail({
                    id: this.dataset.id,
                    beras: this.dataset.beras,
                    berat: this.dataset.berat,
                    jumlah: this.dataset.jumlah,
                    supplier: this.dataset.supplier,
                    contact: this.dataset.contact,
                    tanggal: this.dataset.tanggal,
                    status: this.dataset.status
                });
            });
        });

        function getStatusClass(status) {
            const base = "p-2 mt-2 text-xs font-semibold rounded-full capitalize ";
            const normalizedStatus = status.toLowerCase();

            switch (normalizedStatus) {
                case 'order placed':
                    return base + "bg-yellow-100 text-yellow-800 border border-yellow-300";
                case 'packaging':
                    return base + "bg-yellow-100 text-yellow-800 border border-yellow-300";
                case 'on the road':
                    return base + "bg-blue-100 text-blue-800 border border-blue-300";
                case 'delivered':
                    return base + "bg-blue-100 text-blue-800 border border-blue-300";
                case 'completed':
                    return base + "bg-green-100 text-green-800 border border-green-300";
                default:
                    return base + "bg-gray-100 text-gray-800 border border-gray-300";
            }
        }
        

        if (orderItems.length > 0) {
            orderItems[0].click();
        }
        initStatusColors();
    });

    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownProfile');
        dropdown.classList.toggle('hidden');
    }
    </script>
</body>

</html>