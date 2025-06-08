<?php
session_start();
if (!isset($_SESSION['namaPelanggan']) || !isset($_SESSION['idPelanggan'])) {
    header("Location:../login/loginCustomer.php?login=error");
    exit();
}

if (!isset($_SESSION['checkout_data'])) {
    $_SESSION['error'] = "No checkout data found. Please add items to cart first.";
    header("Location: orderCustomer.php");
    exit();
}

$currentPage = 'orderCustomer.php';
$nama = $_SESSION['namaPelanggan'];
$idPelanggan = $_SESSION['idPelanggan'];
$checkoutData = $_SESSION['checkout_data'];

include '../../assets/mysql/connect.php';
$q = mysqli_query($conn, "SELECT fotoProfil FROM pelanggan WHERE idPelanggan = '$idPelanggan'");
$dataPelanggan = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen">
    <?php include '../../layout/sidebarCustomer.php'; ?>
    
    <div class="main-container ml-[300px] mt-4 mr-12">
        <!-- Header -->
        <div class="flex justify-between items-center gap-6 mb-8">
            <div class="flex-shrink-0">
                <p class="text-2xl text-[#16151C] font-bold">Checkout</p>
                <p class="text-l text-[#5D5C61] font-regular">Checkout Product</p>
            </div>
            
            <!-- Profile dropdown sama seperti di detailProduct.php -->
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()"
                    class="flex border-2 border-solid items-center bg-none rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-500 transition space-x-4">
                    <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>" alt="User"
                        class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
                    <div class="text-left hidden sm:block">
                        <span class="block text-lg font-bold text-black leading-5"><?= $nama; ?></span>
                        <span class="block font-semibold text-sm text-[#A2A1A8] leading-4">Customer</span>
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
                        class="block px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 text-center rounded-b-lg">Log Out</a>
                </div>
            </div>
        </div>

        <!-- Checkout Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Shipping & Payment -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center mb-6">
                    <a href="orderCustomer.php" class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M21 12L3.5 12" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10 5L3 12L10 19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <span class="ml-4 text-2xl font-bold text-black">Checkout</span>
                </div>

                <!-- Progress Steps -->
                <div class="flex items-center mb-8">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-[#8C5E3C] text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Shipping</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-600">Payment</span>
                    </div>
                </div>

                <!-- Shipping Options -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Shipping Method</h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="shipping" value="self_pickup" class="mr-3" checked id="selfPickup">
                            <div class="flex-1">
                                <div class="font-medium">Self Pickup</div>
                                <div class="text-sm text-gray-600">Free - Pick up directly at our store</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="shipping" value="delivery" class="mr-3" id="delivery">
                            <div class="flex-1">
                                <div class="font-medium">Delivered by Store</div>
                                <div class="text-sm text-gray-600">Rp 10,000 - Delivered by our own courier (1-2 days)</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Continue Button -->
                <form action="../../assets/mysql/pelanggan/proses.php" method="post">
                    <input type="hidden" name="checkout_action" value="continue_to_payment">
                    <input type="hidden" name="selected_shipping" id="selectedShipping" value="self_pickup">
                    <input type="hidden" name="shipping_cost" id="shippingCostValue" value="0">
                    <input type="hidden" name="final_total" id="finalTotal" value="<?= $checkoutData['totalHarga'] ?>">
                    <button type="submit" class="w-full bg-[#8C5E3C] text-white py-3 px-6 rounded-lg font-medium hover:bg-[#79513a] transition">
                        Continue to payment
                    </button>
                </form>
            </div>

            <!-- Right Column - Cart Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Your cart</h3>
                
                <div class="flex items-center space-x-4 mb-6">
                    <img src="../../assets/gambar/beras/<?= $checkoutData['gambarBeras'] ?>" 
                         alt="<?= $checkoutData['namaBeras'] ?>"
                         class="w-20 h-20 object-cover rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900"><?= $checkoutData['namaBeras'] ?></h4>
                        <p class="text-sm text-gray-600">Shipping Method: <span id="shippingMethodText">self pickup</span></p>
                        <p class="text-sm text-gray-600">Weight: <?= $checkoutData['beratBeras'] ?> KG</p>
                        <p class="text-sm text-gray-600">Quantity: <?= $checkoutData['quantity'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg">Rp <?= number_format($checkoutData['totalHarga'], 0, ',', '.') ?></p>
                        <a href="orderCustomer.php" class="text-sm text-blue-600 hover:text-blue-800">Remove</a>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="border-t pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp <?= number_format($checkoutData['totalHarga'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Shipping</span>
                        <span id="shippingCost">Free</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg border-t pt-2">
                        <span>Total</span>
                        <span id="totalAmount">Rp <?= number_format($checkoutData['totalHarga'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Shipping method functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selfPickupRadio = document.getElementById('selfPickup');
            const deliveryRadio = document.getElementById('delivery');
            const shippingMethodText = document.getElementById('shippingMethodText');
            const shippingCost = document.getElementById('shippingCost');
            const totalAmount = document.getElementById('totalAmount');
            const selectedShipping = document.getElementById('selectedShipping');
            const shippingCostValue = document.getElementById('shippingCostValue');
            const finalTotal = document.getElementById('finalTotal');
            
            const subtotal = <?= $checkoutData['totalHarga'] ?>;
            const deliveryFee = 10000;

            function updateShipping() {
                if (deliveryRadio.checked) {
                    shippingMethodText.textContent = 'delivery';
                    shippingCost.textContent = 'Rp ' + deliveryFee.toLocaleString('id-ID');
                    const newTotal = subtotal + deliveryFee;
                    totalAmount.textContent = 'Rp ' + newTotal.toLocaleString('id-ID');
                    selectedShipping.value = 'delivery';
                    shippingCostValue.value = deliveryFee;
                    finalTotal.value = newTotal;
                } else {
                    shippingMethodText.textContent = 'self pickup';
                    shippingCost.textContent = 'Free';
                    totalAmount.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                    selectedShipping.value = 'self_pickup';
                    shippingCostValue.value = 0;
                    finalTotal.value = subtotal;
                }
            }

            // Add event listeners
            selfPickupRadio.addEventListener('change', updateShipping);
            deliveryRadio.addEventListener('change', updateShipping);
        });
    </script>
</body>
</html>