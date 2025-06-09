<?php
session_start();
if (!isset($_SESSION['namaPemilik']) || !isset($_SESSION['idPemilik'])) {
    header("Location:../login/loginBusinessOwner.php?login=error");
    exit();
}
$nama = $_SESSION['namaPemilik'];
$idPemilik = $_SESSION['idPemilik'];
$currentPage = 'orderSupplier.php';
include '../../assets/mysql/connect.php';


//paginasi
$itemsPerPage = isset($_GET['show']) ? (int)$_GET['show'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

//parameter search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$whereClauses = [];
$whereClauses[] = "sb.beratBeras = 25";

if ($search !== '') {
    $escSearch = mysqli_real_escape_string($conn, $search);
    $whereClauses[] = "sb.namaBeras LIKE '%{$escSearch}%'";
}
$where = '';
if (count($whereClauses) > 0) {
    $where = 'WHERE ' . implode(' AND ', $whereClauses);
}

//Hitung total data & total halaman
$totalQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM stokberaspemasok sb {$where}");
$totalData = mysqli_fetch_assoc($totalQ)['total'];
$totalPages = ceil($totalData / $itemsPerPage);

//Ambil data stok dengan filter & paginasi
$sql = "
  SELECT sb.*,
         p.namaPemasok
  FROM stokberaspemasok sb
  LEFT JOIN pemasok p
    ON sb.idPemasok = p.idPemasok
  {$where}
  LIMIT {$itemsPerPage}
  OFFSET {$offset}
";
$query = mysqli_query($conn, $sql);

$dataBeras = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataBeras[] = $row;
}

//Hitung range tampilan
$start = $offset + 1;
$end   = min($offset + $itemsPerPage, $totalData);

//Ambil foto profil pemilik
$q = mysqli_query($conn, "SELECT fotoProfil FROM pemilikusaha WHERE idPemilik = '{$idPemilik}'");
$dataPemilikUsaha = mysqli_fetch_assoc($q);
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarBusinessOwner.php'; ?>
    <div class="main-container ml-[300px] mt-4 mr-12">
        <div class="flex justify-between items-center gap-6">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Order Supplier</p>
                <p class="text-l text-[#5D5C61] font-regular">Order Supplier Rice Stock</p>
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
        <div class="rounded-2xl border border-[#A2A1A8] shadow-lg p-8 mt-4">
            <div class="relative flex justify-between items-center gap-4">
                <div class="relative flex-grow">
                    <form action="orderSupplier.php" method="get" class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="absolute w-5 h-5 top-2.5 left-2.5 text-slate-600">
                            <path fill-rule="evenodd"
                                d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                clip-rule="evenodd" />
                        </svg>
                        <input type="text" name="search" value="<?= $search ?>" placeholder="Search Item"
                            class="w-64 bg-transparent placeholder:text-[#16151C] text-[#16151C] text-sm border border-slate-400 rounded-md pl-10 pr-3 py-2 transition focus:outline-none focus:border-slate-400" />
                    </form>

                </div>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="min-w-full bg-white rounded-lg shadow">
                    <thead class="bg-[#A2845E] text-black">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Item Name</th>
                            <th class="px-4 py-3 text-left">Image</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Weight(kg)</th>
                            <th class="px-4 py-3 text-left">Stock</th>
                            <th class="px-4 py-3 text-left">Selling Price </th>
                            <th class="px-4 py-3 text-left">Supplier Name</th>
                            <th class="px-4 py-3 text-center">Buy</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                            if (empty($dataBeras)):
                        ?>
                        <tr>
                            <td colspan="9" class="px-4 py-3 text-center text-gray-500">No data found</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($dataBeras as $index => $beras): ?>
                        <tr class="<?= $index % 2 === 0 
                          ? 'bg-[#FFEEDB]'    
                          : 'bg-[#E7DDD3]'    ?>
                     hover:bg-[#D1BEAB]">
                            <td class="px-4 py-3"><?= $start + $index ?></td>



                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['namaBeras']) ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="w-16 h-16 mx-auto overflow-hidden rounded-lg border border-gray-200">
                                    <img src="../../assets/gambar/beras/<?= htmlspecialchars($beras['gambarBeras']) ?>"
                                        alt="<?= htmlspecialchars($beras['namaBeras']) ?>"
                                        class="w-full h-full object-cover" />
                                </div>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['jenisBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['beratBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['stokBeras']) ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                Rp <?= number_format($beras['hargaJual'], 2, ',', '.') ?>
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                <?= htmlspecialchars($beras['namaPemasok']) ?>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                    class="buy-button bg-[#A2845E] text-white px-4 py-2 rounded-full hover:bg-[#5C4E3F] transition-colors duration-200"
                                    onclick="openModal(this)" data-idberas="<?= htmlspecialchars($beras['idBeras']) ?>"
                                    data-pemasok="<?= htmlspecialchars($beras['idPemasok']) ?>"
                                    data-namaberas="<?= htmlspecialchars($beras['namaBeras']) ?>"
                                    data-supplier="<?= htmlspecialchars($beras['namaPemasok']) ?>"
                                    data-price="<?= htmlspecialchars($beras['hargaJual']) ?>"
                                    data-stok="<?= htmlspecialchars($beras['stokBeras']) ?>">
                                    Buy
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
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
                <a href="?page=<?= $page-1 ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.4685 17.5856C14.7919 17.3269 14.8444 16.8549 14.5856 16.5315L10.9604 12L14.5856 7.46849C14.8444 7.14505 14.7919 6.67308 14.4685 6.41432C14.145 6.15556 13.6731 6.208 13.4143 6.53145L9.41432 11.5315C9.19519 11.8054 9.19519 12.1946 9.41432 12.4685L13.4143 17.4685C13.6731 17.7919 14.145 17.8444 14.4685 17.5856Z"
                            fill="#16151C" />
                    </svg>


                </a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md <?= $i == $page ? 'bg-none border-[#7C4D16] border-2 text-[#7C4D16] font-semibold' : 'bg-none hover:bg-[#D1BEAB]' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                <a href="?page=<?= $page+1 ?>&show=<?= $itemsPerPage ?>&search=<?= $search ?>"
                    class="px-4 py-2 border rounded-md bg-none hover:bg-[#D1BEAB]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.53151 17.5856C9.20806 17.3269 9.15562 16.8549 9.41438 16.5315L13.0396 12L9.41438 7.46849C9.15562 7.14505 9.20806 6.67308 9.53151 6.41432C9.85495 6.15556 10.3269 6.208 10.5857 6.53145L14.5857 11.5315C14.8048 11.8054 14.8048 12.1946 14.5857 12.4685L10.5857 17.4685C10.3269 17.7919 9.85495 17.8444 9.53151 17.5856Z"
                            fill="#16151C" />
                    </svg>

                </a>
                <?php endif; ?>
            </div>
            <!-- modal buy -->
            <div id="buyModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white rounded-lg w-96 max-w-full shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-xl font-semibold">Buy Item</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item Name:</label>
                            <span id="modalNamaBeras" class="mt-1 block text-gray-900 font-medium"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier Name:</label>
                            <span id="modalSupplierName" class="mt-1 block text-gray-900"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selling Price:</label>
                            <span id="modalSellingPrice" class="mt-1 block text-gray-900"></span>
                        </div>
                        <div>
                            <label for="modalJumlah" class="block text-sm font-medium text-gray-700">Jumlah
                                (karung):</label>
                            <input type="number" id="modalJumlah" name="jumlah" min="1" value="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                            <p id="modalStokInfo" class="mt-1 text-xs text-gray-500"></p>
                        </div>
                        <input type="hidden" id="modalIdPemilik" value="<?= $_SESSION['idPemilik'] ?>" />
                        <input type="hidden" id="modalIdBeras" />
                        <input type="hidden" id="modalIdPemasok" />
                        <input type="hidden" id="modalHargaBeli" />
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end space-x-2">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            onclick="closeModal()">
                            Cancel
                        </button>
                        <button type="button" id="confirmBuyButton"
                            class="px-4 py-2 bg-[#A2845E] text-white rounded-md hover:bg-[#5C4E3F]">
                            Buy
                        </button>
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

function openModal(btn) {
    const modal = document.getElementById('buyModal');
    const idBeras = btn.getAttribute('data-idberas');
    const idPemasok = btn.getAttribute('data-pemasok');
    const namaBeras = btn.getAttribute('data-namaberas');
    const supplierName = btn.getAttribute('data-supplier');
    const priceRaw = btn.getAttribute('data-price');
    const stokAvailable = btn.getAttribute('data-stok');

    document.getElementById('modalIdBeras').value = idBeras;
    document.getElementById('modalIdPemasok').value = idPemasok;
    document.getElementById('modalNamaBeras').innerText = namaBeras;
    document.getElementById('modalSupplierName').innerText = supplierName;

    document.getElementById('modalHargaBeli').value = priceRaw;

    const priceFormatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2
    }).format(parseFloat(priceRaw));
    document.getElementById('modalSellingPrice').innerText = priceFormatted + ' / karung';

    document.getElementById('modalStokInfo').innerText =
        'Stock tersedia: ' + stokAvailable + ' karung.';

    const jumlahInput = document.getElementById('modalJumlah');
    jumlahInput.value = 1;

    // Fungsi untuk update total harga
    function updateTotalPrice() {
        const jumlah = parseInt(jumlahInput.value) || 1;
        const totalHarga = jumlah * parseFloat(priceRaw);
        const totalFormatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 2
        }).format(totalHarga);

        // Update display total harga
        let totalPriceElement = document.getElementById('modalTotalPrice');
        if (!totalPriceElement) {
            // Jika element belum ada, buat baru
            const priceContainer = document.getElementById('modalSellingPrice').parentNode;
            const totalContainer = document.createElement('div');
            totalContainer.innerHTML = `
                <label class="block text-sm font-medium text-gray-700">Total Harga:</label>
                <span id="modalTotalPrice" class="mt-1 block text-gray-900 font-bold text-lg text-green-600"></span>
            `;
            priceContainer.insertAdjacentElement('afterend', totalContainer);
            totalPriceElement = document.getElementById('modalTotalPrice');
        }
        totalPriceElement.innerText = totalFormatted;
    }

    // Update total harga saat modal dibuka
    updateTotalPrice();

    // Event listener untuk update total harga saat jumlah berubah
    jumlahInput.addEventListener('input', updateTotalPrice);

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    const confirmBtn = document.getElementById('confirmBuyButton');
    confirmBtn.onclick = function() {
        const jumlah = parseInt(document.getElementById('modalJumlah').value);
        if (isNaN(jumlah) || jumlah < 1) {
            alert('Jumlah harus bernilai minimal 1.');
            return;
        }
        if (jumlah > parseInt(stokAvailable)) {
            alert('Jumlah melebihi stok tersedia!');
            return;
        }

        // Hitung total harga untuk dikirim ke backend
        const totalHargaBeli = jumlah * parseFloat(priceRaw);

        // Buat form dan submit via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= "../../assets/mysql/pemilikUsaha/proses.php" ?>';

        const idPemilik = "<?= $_SESSION['idPemilik'] ?>"; 

        // Di dalam confirmBtn.onclick
        const inputs = {
            'submitOrder': '1',
            'idBeras': idBeras,
            'idPemasok': idPemasok,
            'jumlahPesanan': jumlah,
            'hargaBeli': totalHargaBeli,
            'idPemilik': idPemilik 
        };

        for (const [name, value] of Object.entries(inputs)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    };
}

function closeModal() {
    const modal = document.getElementById('buyModal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    // Remove event listener untuk mencegah multiple listeners
    const jumlahInput = document.getElementById('modalJumlah');
    jumlahInput.removeEventListener('input', updateTotalPrice);
}
</script>

</html>