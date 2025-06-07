    <?php
    session_start();
    if (!isset($_SESSION['namaPemasok'])) {
        header("Location:../login/loginSupplier.php?login=error");
        exit();
    }

    $nama = $_SESSION['namaPemasok'];
    $idPemasok = $_SESSION['idPemasok'];
    $currentPage = 'orderStatusSupplier.php';
    include '../../assets/mysql/connect.php';
    $q = mysqli_query($conn, "SELECT fotoProfil FROM pemasok WHERE idPemasok = '$idPemasok'");
    $dataPemasok = mysqli_fetch_assoc($q);  

    // Ambil data pesanan yang sudah di-approve
    $query = mysqli_query($conn, "
    SELECT 
        pp.*,
        b.namaBeras,
        b.gambarBeras,
        b.beratBeras,
        p.namaPemasok AS supplierName,
        po.namaPemilik AS customerName,
        po.alamatPemilik AS alamatCustomer,
        LOWER(COALESCE(pp.status_pengiriman, 'order placed')) AS status_normalized,
        DATE_FORMAT(pp.waktu_approve, '%h:%i %p') AS waktu_approve_formatted
    FROM pesananpemasok pp
    JOIN stokberaspemasok b ON pp.idBeras = b.idBeras
    JOIN pemasok p ON pp.idPemasok = p.idPemasok
    JOIN pemilikusaha po ON pp.idPemilik = po.idPemilik
    WHERE pp.status = 'approved'
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
        <title>Order Status</title>
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

        .status-action {
            margin-top: 10px;
        }

        .order-item.selected {
            background-color: #f3f4f6;
            border-color: #A2845E;
            border-width: 2px;
        }
        </style>
    </head>

    <body class="bg-[#EFE9E2] min-h-screen">
        <?php include '../../layout/sidebarSupplier.php'; ?>
        <div class="main-container ml-[300px] mt-4 mr-12">
            <div class="flex justify-between items-center gap-6">
                <div class="flex-shrink-0">
                    <p class="text-2xl text-[#16151C] font-bold">Order Status</p>
                    <p class="text-l text-[#5D5C61] font-regular">Order Status</p>
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
                            data-customer="<?= htmlspecialchars($pesanan['customerName']) ?>"
                            data-alamat="<?= htmlspecialchars($pesanan['alamatCustomer']) ?>"
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

                    <!-- Status Tracking -->
                    <div class="status-stepper">
                        <!-- STEP 1: Order Placed -->
                        <div class="status-step" data-step="order placed">
                            <div class="status-dot">1</div>
                            <div class="status-label">Order Placed</div>
                            <div class="status-time">10:30 AM</div>
                            <div class="status-action">
                                <button
                                    class="prev-btn hidden bg-gray-100 text-gray-600 hover:bg-gray-200 py-2 px-2 rounded">←
                                    Prev</button>
                                <button
                                    class="next-btn bg-[#8C5E3C] text-white hover:bg-[#A2845E] py-2 px-2 rounded md">Next
                                    →</button>
                            </div>
                        </div>

                        <!-- STEP 2: Packaging -->
                        <div class="status-step" data-step="packaging">
                            <div class="status-dot">2</div>
                            <div class="status-label">Packaging</div>
                            <div class="status-time">On Process</div>
                            <div class="status-action">
                                <button class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 py-2 px-2 rounded">←
                                    Prev</button>
                                <button
                                    class="next-btn bg-[#8C5E3C] text-white hover:bg-[#A2845E] py-2 px-2 rounded md">Next
                                    →</button>
                            </div>
                        </div>

                        <!-- STEP 3: On The Road -->
                        <div class="status-step" data-step="on the road">
                            <div class="status-dot">3</div>
                            <div class="status-label">On The Road</div>
                            <div class="status-time">Pending</div>
                            <div class="status-action">
                                <button class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 py-2 px-2 rounded">←
                                    Prev</button>
                                <button
                                    class="next-btn bg-[#8C5E3C] text-white hover:bg-[#A2845E] py-2 px-2 rounded md">Next
                                    →</button>
                            </div>
                        </div>

                        <!-- STEP 4: Delivered -->
                        <div class="status-step" data-step="delivered">
                            <div class="status-dot">4</div>
                            <div class="status-label">Delivered</div>
                            <div class="status-time">Pending</div>
                            <div class="status-action">
                                <button class="prev-btn bg-gray-100 text-gray-600 hover:bg-gray-200 py-2 px-2 rounded">←
                                    Prev</button>
                            </div>
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
                                <p class="font-semibold text-gray-700">Shipping To</p>
                                <p class="font-semibold text-[#16151C] mt-1" id="detail-customer">-</p>
                                <p class="text-gray-600 mt-1" id="detail-alamat">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    </body>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderItems = document.querySelectorAll('.order-item');
        const statusSteps = document.querySelectorAll('.status-step');
        let currentOrderId = null;

        // Mapping status UI ke database
        const statusMap = {
            'order placed': 'Order Placed',
            'packaging': 'Packaging',
            'on the road': 'On The Road',
            'delivered': 'Delivered'
        };

        // Fungsi untuk mengupdate tampilan detail pesanan
        function updateOrderDetail(data) {
            document.getElementById('detail-beras').textContent = data.beras;
            document.getElementById('detail-berat').textContent = data.berat;
            document.getElementById('detail-jumlah').textContent = data.jumlah;
            document.getElementById('detail-customer').textContent = data.customer;
            document.getElementById('detail-alamat').textContent = data.alamat;

            
            const orderPlacedTime = document.querySelector('.status-step[data-step="order placed"] .status-time');
    if (orderPlacedTime) {
        orderPlacedTime.textContent = data.tanggal || '12:00 AM';
    }

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

            // Sembunyikan semua tombol prev/next
            document.querySelectorAll('.prev-btn, .next-btn').forEach(btn => {
                btn.classList.add('hidden');
            });

            // Tampilkan tombol yang sesuai
            if (currentStatus === 'order placed') {
                document.querySelector('.status-step[data-step="order placed"] .next-btn')?.classList.remove(
                    'hidden');
            } else if (currentStatus === 'packaging') {
                document.querySelector('.status-step[data-step="packaging"] .prev-btn')?.classList.remove('hidden');
                document.querySelector('.status-step[data-step="packaging"] .next-btn')?.classList.remove('hidden');
            } else if (currentStatus === 'on the road') {
                document.querySelector('.status-step[data-step="on the road"] .prev-btn')?.classList.remove(
                    'hidden');
                document.querySelector('.status-step[data-step="on the road"] .next-btn')?.classList.remove(
                    'hidden');
            } else if (currentStatus === 'delivered') {
                document.querySelector('.status-step[data-step="delivered"] .prev-btn')?.classList.remove('hidden');
            }
        }

        // Event listener untuk klik pesanan
        orderItems.forEach(item => {
            item.addEventListener('click', function() {
                // Hapus seleksi sebelumnya
                orderItems.forEach(i => i.classList.remove('selected'));

                // Tambahkan seleksi
                this.classList.add('selected');

                // Simpan ID pesanan yang dipilih
                currentOrderId = this.dataset.id;

                // Update detail pesanan
                updateOrderDetail({
                    id: this.dataset.id,
                    beras: this.dataset.beras,
                    berat: this.dataset.berat,
                    jumlah: this.dataset.jumlah,
                    customer: this.dataset.customer,
                    alamat: this.dataset.alamat,
                    tanggal: this.dataset.tanggal,
                    status: this.dataset.status
                });
            });
        });

        // Event listener untuk tombol next
        document.querySelectorAll('.next-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!currentOrderId) {
                    alert('Please select an order first');
                    return;
                }

                const currentStep = this.closest('.status-step');
                const nextStep = currentStep.nextElementSibling;
                if (!nextStep) return;

                const newStatus = nextStep.dataset.step;
                const newStatusMapped = statusMap[newStatus] || newStatus; // Map ke format database

                // Tampilkan loading indicator
                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'Loading...';

                // Kirim permintaan update status
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('idPesanan', currentOrderId);
                formData.append('status', newStatusMapped);

                fetch('../../assets/mysql/pemasok/proses.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Kembalikan tombol ke keadaan semula
                        this.disabled = false;
                        this.textContent = originalText;

                        return response.text();
                    })
                    .then(text => {
                        console.log('Raw response:', text);

                        if (text.toLowerCase().includes('success')) {
                            // Update UI
                            const currentItem = document.querySelector(
                                `.order-item[data-id="${currentOrderId}"]`
                            );

                            if (currentItem) {
                                currentItem.dataset.status = newStatus;
                                const statusSpan = currentItem.querySelector('span');
                                if (statusSpan) {
                                    statusSpan.textContent = statusMap[newStatus] || newStatus;
                                }

                                // Update waktu jika perlu
                                const statusTimeElement = nextStep.querySelector(
                                    '.status-time');
                                if (statusTimeElement) {
                                    if (newStatus === 'packaging') {
                                        statusTimeElement.textContent = 'On Process';
                                    } else if (newStatus === 'on the road') {
                                        statusTimeElement.textContent = 'Retailing';
                                    } else if (newStatus === 'delivered') {
                                        statusTimeElement.textContent = 'Delivered at ' +
                                            new Date().toLocaleTimeString([], {
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            });
                                    }
                                }

                                updateOrderDetail({
                                    ...currentItem.dataset,
                                    status: newStatus
                                });

                                alert('Status updated successfully!');
                            } else {
                                alert('Order item not found in DOM');
                            }
                        } else {
                            alert('Failed to update status: ' + text);
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.textContent = originalText;
                        console.error('Fetch error:', error);
                        alert('Network error occurred while updating status');
                    });
            });
        });

        // Event listener untuk tombol prev
        document.querySelectorAll('.prev-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!currentOrderId) {
                    alert('Please select an order first');
                    return;
                }

                const currentStep = this.closest('.status-step');
                const prevStep = currentStep.previousElementSibling;
                if (!prevStep) return;

                const newStatus = prevStep.dataset.step;
                const newStatusMapped = statusMap[newStatus] || newStatus; // Map ke format database

                // Tampilkan loading indicator
                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'Loading...';

                // Kirim permintaan update status
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('idPesanan', currentOrderId);
                formData.append('status', newStatusMapped);

                fetch('../../assets/mysql/pemasok/proses.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        this.disabled = false;
                        this.textContent = originalText;
                        return response.text();
                    })
                    .then(text => {
                        if (text.toLowerCase().includes('success')) {
                            // Update UI
                            const currentItem = document.querySelector(
                                `.order-item[data-id="${currentOrderId}"]`
                            );

                            if (currentItem) {
                                currentItem.dataset.status = newStatus;
                                const statusSpan = currentItem.querySelector('span');
                                if (statusSpan) {
                                    statusSpan.textContent = statusMap[newStatus] || newStatus;
                                }

                                updateOrderDetail({
                                    ...currentItem.dataset,
                                    status: newStatus
                                });

                                alert('Status updated successfully!');
                            }
                        } else {
                            // Tampilkan error lebih informatif
                            alert(`Failed to update status: ${text}`);
                            console.error('Update failed:', text);

                            // Tampilkan data yang dikirim
                            console.log('Sent data:', {
                                idPesanan: currentOrderId,
                                status: newStatusMapped
                            });
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.textContent = originalText;
                        console.error('Error:', error);
                        alert('An error occurred while updating status');
                    });
            });
        });

        // Pilih pesanan pertama secara otomatis jika ada
        if (orderItems.length > 0) {
            orderItems[0].click();
        }
    });

    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownProfile');
        dropdown.classList.toggle('hidden');
    }
    </script>

    </html>