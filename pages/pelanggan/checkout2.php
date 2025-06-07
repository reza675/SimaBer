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
    <title>Checkout Payment - SimaBer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            <!-- Profile dropdown -->
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()"
                    class="flex border-2 border-solid items-center bg-none rounded-xl px-4 py-2 shadow hover:ring-2 hover:ring-gray-500 transition space-x-4">
                    <img src="../../assets/gambar/pelanggan/photoProfile/<?= $dataPelanggan['fotoProfil'] ?? 'profil.jpeg' ?>"
                        alt="User" class="w-14 h-14 rounded-xl object-cover mix-blend-multiply" />
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

        <!-- Checkout Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Payment Methods -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center mb-6">
                    <a href="checkout1.php" class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path d="M21 12L3.5 12" stroke="black" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M10 5L3 12L10 19" stroke="black" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                    <span class="ml-4 text-2xl font-bold text-black">Checkout</span>
                </div>

                <!-- Progress Steps -->
                <div class="flex items-center mb-8">
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 bg-[#8C5E3C] text-white rounded-full flex items-center justify-center text-sm font-medium">
                            1</div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Shipping</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-[#8C5E3C] mx-4"></div>
                    <div class="flex items-center">
                        <div
                            class="w-8 h-8 bg-[#8C5E3C] text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2</div>
                        <span class="ml-2 text-sm font-medium text-gray-900">Payment</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-6">
                    <div class="flex gap-4 mb-6">
                        <button type="button" id="qrisBtn"
                            class="flex-1 flex items-center justify-center border-2 border-[#8C5E3C] bg-white rounded-lg font-medium transition hover:shadow">
                            <img src="../../assets/gambar/pelanggan/qris.png" alt="QRIS"
                                class="h-16 w-auto object-cover mix-blend-multiply" />
                        </button>

                        <button type="button" id="codBtn"
                            class="flex-1 flex items-center justify-center border-2 border-gray-300 bg-white rounded-lg font-medium transition hover:shadow">
                            <img src="../../assets/gambar/pelanggan/cod.png" alt="Cash on Delivery"
                                class="h-16 w-auto object-cover" />
                        </button>
                    </div>
                </div>

                <div id="paymentDetails">
                    <h3 class="text-lg font-semibold mb-4">
                        <?= $checkoutData['shippingMethod'] === 'self_pickup' ? 'Pickup at' : 'Payment Details' ?></h3>

                    <form id="checkoutForm" method="post">
                        <input type="hidden" name="checkout_action" value="complete_order">
                        <input type="hidden" name="payment_method" id="paymentMethod" value="qris">

                        <div class="space-y-4">
                            <div id="deliveryNotesDiv">
                                <label for="deliveryNotes" class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= $checkoutData['shippingMethod'] === 'delivery' ? 'Delivery Notes' : 'Pickup Notes' ?>
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea name="delivery_notes" id="deliveryNotes"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8C5E3C] focus:border-transparent"
                                    placeholder="<?= $checkoutData['shippingMethod'] === 'delivery' ? 'Please provide delivery instructions, address details, or special requests...' : 'Any notes for pickup' ?>"
                                    rows="3" required minlength="10" maxlength="500"></textarea>
                                <div class="mt-1 text-sm text-gray-500">
                                    Minimum 10 characters required
                                </div>
                                <div id="deliveryNotesError" class="mt-1 text-sm text-red-600 hidden">
                                    Please provide delivery/pickup notes (minimum 10 characters)
                                </div>
                            </div>
                        </div>

                        <!-- Save payment method toggle -->
                        <div class="flex items-center justify-between mt-6 mb-6">
                            <span class="text-sm text-gray-600">Save payment method data for future payments</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" value="" class="sr-only peer" name="save_payment_data">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#8C5E3C]/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8C5E3C]">
                                </div>
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-4">
                            <button type="button" id="cancelOrderBtn"
                                class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-400 transition">
                                Cancel Order
                            </button>
                            <button type="button" id="confirmOrderBtn"
                                class="flex-1 bg-[#8C5E3C] text-white py-3 px-6 rounded-lg font-medium hover:bg-[#79513a] transition">
                                <span id="confirmButtonText">Confirm QRIS Order</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Right Column - Cart Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold mb-4">Your cart</h3>

                <div class="flex items-center space-x-4 mb-6">
                    <img src="../../assets/gambar/beras/<?= $checkoutData['gambarBeras'] ?>"
                        alt="<?= $checkoutData['namaBeras'] ?>" class="w-20 h-20 object-cover rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900"><?= $checkoutData['namaBeras'] ?></h4>
                        <p class="text-sm text-gray-600">Shipping Method:
                            <?= $checkoutData['shippingMethod'] === 'delivery' ? 'Home Delivery' : 'Self Pickup' ?></p>
                        <p class="text-sm text-gray-600">Weight: <?= $checkoutData['beratBeras'] ?> KG</p>
                        <p class="text-sm text-gray-600">Quantity: <?= $checkoutData['quantity'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lg">Rp
                            <?= number_format($checkoutData['totalHarga'], 0, ',', '.') ?></p>
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
                        <span><?= $checkoutData['shippingMethod'] === 'delivery' ? 'Rp ' . number_format($checkoutData['shippingCost'], 0, ',', '.') : 'Rp 0' ?></span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg border-t pt-2">
                        <span>Total</span>
                        <span>Rp <?= number_format($checkoutData['finalTotal'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 text-center">
            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successfully</h2>
            <p class="text-gray-600 mb-8">The store has received your payment notification</p>
            <button onclick="redirectToDashboard()"
                class="w-full bg-[#8C5E3C] text-white py-3 px-6 rounded-lg font-medium hover:bg-[#79513a] transition">
                Back to Dashboard
            </button>
        </div>
    </div>

    <!-- Failed Payment Modal -->
    <div id="failedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 text-center">
            <div class="relative w-20 h-20 mx-auto mb-6">
                <div class="absolute inset-0 w-20 h-20 bg-pink-200 rounded-full opacity-50"></div>
                <div class="relative w-20 h-20 bg-red-500 rounded-full flex items-center justify-center">
                    <div class="text-white">
                        <div class="w-1 h-8 bg-white rounded-full mb-1"></div>
                        <div class="w-1 h-1 bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">We couldn't proceed your payment</h2>
            <p class="text-gray-500 mb-8">Process failed, please try again</p>
            
            <button onclick="closeFailedModal()"
                class="w-full bg-amber-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-amber-700 transition">
                Back to Dashboard
            </button>
        </div>
    </div>

    <!-- QRIS Modal -->
    <div id="qrisModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-2xl w-full mx-4 grid grid-cols-1 md:grid-cols-2 gap-8">

            <div class="flex items-center justify-center">
                <img src="../../assets/gambar/pelanggan/qrispayment.png" alt="QR Code"
                    class="w-full h-auto max-h-96 object-contain">
            </div>
            <div class="flex flex-col justify-center">
                <p class="font-semibold">Order ID: <span id="qrisOrderId"></span></p>
                <p class="text-3xl font-bold mt-2">Rp <span id="qrisTotal"></span></p>
                <p class="text-gray-600 mt-4 mb-8">
                    Please scan the QR code above to make your payment. Please screenshot payment proof for
                    confirmation
                </p>
                <div class="flex gap-4">
                    <button onclick="cancelQRISOrder()"
                        class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-400 transition">
                        Cancel Order
                    </button>
                    <button onclick="closeQRISModal()"
                        class="flex-1 bg-[#8C5E3C] text-white py-3 rounded-lg font-medium hover:bg-[#79513a] transition">
                        I've Paid
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
    let currentOrderId = null;

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

    // Payment method functionality
    document.addEventListener('DOMContentLoaded', function() {
        const qrisBtn = document.getElementById('qrisBtn');
        const codBtn = document.getElementById('codBtn');
        const paymentMethod = document.getElementById('paymentMethod');
        const confirmButtonText = document.getElementById('confirmButtonText');
        const shippingMethod = '<?= $checkoutData['shippingMethod'] ?>';

        function selectPaymentMethod(method) {
            if (method === 'qris') {
                qrisBtn.classList.add('border-[#8C5E3C]');
                qrisBtn.classList.remove('border-gray-300');
                codBtn.classList.remove('border-[#8C5E3C]');
                codBtn.classList.add('border-gray-300');
                paymentMethod.value = 'qris';
                confirmButtonText.textContent = 'Confirm QRIS Order';
            } else {
                codBtn.classList.add('border-[#8C5E3C]');
                codBtn.classList.remove('border-gray-300');
                qrisBtn.classList.remove('border-[#8C5E3C]');
                qrisBtn.classList.add('border-gray-300');
                paymentMethod.value = 'cod';
                confirmButtonText.textContent = 'Confirm COD Order';
            }
        }

        qrisBtn.addEventListener('click', function() {
            selectPaymentMethod('qris');
        });

        codBtn.addEventListener('click', function() {
            selectPaymentMethod('cod');
        });
        selectPaymentMethod('qris');
    });

    function validateDeliveryNotes() {
        const deliveryNotes = document.getElementById('deliveryNotes').value.trim();
        const shippingMethod = '<?= $checkoutData['shippingMethod'] ?>';
        const errorElement = document.getElementById('deliveryNotesError');

        if (deliveryNotes.length < 10) {
            errorElement.classList.remove('hidden');
            return false;
        }

        errorElement.classList.add('hidden');
        return true;
    }

    // Handle form submission
    document.getElementById('confirmOrderBtn').addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!validateDeliveryNotes()) {
            return;
        }

        const paymentMethod = document.getElementById('paymentMethod').value;
        const shippingMethod = '<?= $checkoutData['shippingMethod'] ?>';

        if (paymentMethod === 'cod') {
            const confirmationMessage = shippingMethod === 'delivery' ?
                'Are you sure you want to proceed with COD (Cash on Delivery)?' :
                'Are you sure you want to proceed with payment COD on pickup?';

            if (!confirm(confirmationMessage)) {
                return;
            }
        }

        const formData = new FormData(document.getElementById('checkoutForm'));
        
        // Disable button to prevent double submission
        const confirmBtn = document.getElementById('confirmOrderBtn');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span>Processing...</span>';

        $.ajax({
            url: '../../assets/mysql/pelanggan/proses.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentOrderId = response.order_id; // Store order ID globally
                    
                    if (response.payment_method === 'qris') {
                        document.getElementById('qrisOrderId').textContent = response.order_id;
                        document.getElementById('qrisTotal').textContent =
                            response.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        showQRISModal();
                    } else {
                        showSuccessModal();
                    }
                } else {
                    showFailedModal();
                }
                
                // Re-enable button
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span id="confirmButtonText">' + 
                    (paymentMethod === 'qris' ? 'Confirm QRIS Order' : 'Confirm COD Order') + '</span>';
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showFailedModal();
                
                // Re-enable button
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span id="confirmButtonText">' + 
                    (paymentMethod === 'qris' ? 'Confirm QRIS Order' : 'Confirm COD Order') + '</span>';
            }
        });
    });

    // Cancel order button
    document.getElementById('cancelOrderBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to cancel this order?')) {
            window.location.href = 'orderCustomer.php';
        }
    });

    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function showFailedModal() {
        const modal = document.getElementById('failedModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeFailedModal() {
        const modal = document.getElementById('failedModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function showQRISModal() {
        const modal = document.getElementById('qrisModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeQRISModal() {
        const modal = document.getElementById('qrisModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        showSuccessModal(); 
    }

    function cancelQRISOrder() {
        if (!confirm('Are you sure you want to cancel this QRIS order?')) {
            return;
        }

        if (currentOrderId) {
            $.ajax({
                url: '../../assets/mysql/pelanggan/proses.php',
                method: 'POST',
                data: {
                    cancel_action: 'cancel_order',
                    order_id: currentOrderId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const modal = document.getElementById('qrisModal');
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                        
                        alert('Order cancelled successfully');
                        window.location.href = 'orderCustomer.php';
                    } else {
                        const modal = document.getElementById('qrisModal');
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                        showFailedModal();
                    }
                },
                error: function() {
                    // Close QRIS modal first
                    const modal = document.getElementById('qrisModal');
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    
                    // Show failed modal instead of alert
                    showFailedModal();
                }
            });
        } else {
            // If no order ID, just redirect
            const modal = document.getElementById('qrisModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            window.location.href = 'orderCustomer.php';
        }
    }

    function redirectToDashboard() {
        window.location.href = 'dashboardCustomer.php';
    }

    document.getElementById('qrisModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRISModal();
        }
    });

    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) {
            redirectToDashboard();
        }
    });

    document.getElementById('failedModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFailedModal();
        }
    });
    </script>
</body>

</html>