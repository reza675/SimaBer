<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Customer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png" />
</head>

<body class="bg-[#EFE9E2] min-h-screen flex flex-col overflow-y-auto">
    <?php
        if (isset($_GET['register']) && $_GET['register'] == "berhasil") {
            echo "<div id='successPopup' class='fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50'>
                <div class='bg-white rounded-2xl p-6 max-w-sm text-center shadow-xl'>
                    <img src='../../assets/gambar/register/success.webp' alt='success' class='w-20 h-20 mx-auto mb-4 mix-blend-multiply' />
                    <h3 class='text-xl font-bold mb-2'>Account Created Successfully</h3>
                    <p class='text-gray-600 mb-4'>Welcome aboard! Your account has been successfully created.</p>
                    <a href='../login/loginCustomer.php' class='inline-block px-5 py-2 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg'>Back to Login</a>
                </div>
            </div>";
        }
        if (isset($_GET['register']) && $_GET['register'] == "gagal_daftar") {
            echo "<div id='failedPopup' class='fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50'>
                <div class='bg-white rounded-2xl p-6 max-w-sm text-center shadow-xl'>
                    <img src='../../assets/gambar/register/failed.png' alt='failed' class='w-20 h-20 mx-auto mb-4 mix-blend-multiply' />
                    <h3 class='text-xl font-bold mb-2'>Account Create Failed</h3>
                    <p class='text-gray-600 mb-4'>Name already used. Try again.</p>
                    <a href='register.php' class='inline-block px-5 py-2 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg'>Back to Register</a>
                </div>
            </div>";
        }
    ?>

    <div class="flex flex-col md:flex-row w-full">
        <!-- Gambar -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 bg-[#EFE9E2]">
            <img src="../../assets/gambar/register/register.png" alt="Register Image"
                 class="w-full h-auto object-contain mix-blend-multiply max-h-[800px]" />
        </div>

        <!-- Form Register -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md space-y-4">
                <a href="../login/loginCustomer.php" class="text-gray-600 hover:text-gray-900 text-base">
                    &#8592; Back
                </a>
                <h2 class="text-3xl font-bold text-gray-800 mt-2">Register Customer</h2>
                <p class="text-gray-500 mb-4">Please fill in your personal data correctly.</p>
                <form action="../../assets/mysql/register_login/proses.php" method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block mb-1 text-sm font-semibold text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Email Address"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                    </div>
                    <div>
                        <label for="password" class="block mb-1 text-sm font-semibold text-gray-700">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Password"
                                   class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 pr-10 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-500">
                                <svg id="eyeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label for="fullname" class="block mb-1 text-sm font-semibold text-gray-700">Full Name</label>
                        <input type="text" name="fullname" id="fullname" placeholder="Full Name"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                    </div>
                    <div>
                        <label for="telephone" class="block mb-1 text-sm font-semibold text-gray-700">Telephone Number</label>
                         <input type="tel" name="telephone" id="telephone" placeholder="Telephone Number"
                               minlength="12" maxlength="13" pattern="\d{12,13}"
                               oninvalid="this.setCustomValidity('Number must be valid length 12-13')"
                               oninput="this.setCustomValidity('')"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                        <p id="telError" class="text-red-500 text-sm mt-1 hidden">Telephone number must be 12–13 digits.</p>
                    </div>
                    <div>
                        <label for="address" class="block mb-1 text-sm font-semibold text-gray-700">Home Address</label>
                        <input type="text" name="address" id="address" placeholder="Home Address"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                    </div>
                    <div>
                        <label for="zipcode" class="block mb-1 text-sm font-semibold text-gray-700">Zipcode</label>
                        <input type="text" name="zipcode" id="zipcode" placeholder="Zipcode"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080]" required />
                    </div>
                    <button type="submit" name="registerCustomer" value="registerCustomer"
                            class="w-full py-3 px-4 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg text-lg transition">
                        Register
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/cdn/flowbite.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M2 2L22 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6.71277 6.7226C3.66479 8.79527 2 12 2 12C2 12 5.63636 19 12 19C14.0503 19 15.8174 18.2734 17.2711 17.2884M11 5.05822C11.3254 5.02013 11.6588 5 12 5C18.3636 5 22 12 22 12C22 12 21.3082 13.3317 20 14.8335" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M14 14.2362C13.4692 14.7112 12.7684 15.0001 12 15.0001C10.3431 15.0001 9 13.657 9 12.0001C9 11.1764 9.33193 10.4303 9.86932 9.88818" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />`;
            } else {
                password.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
         document.getElementById('registerForm').addEventListener('submit', function(e) {
            const tel = document.getElementById('telephone');
            const err = document.getElementById('telError');
            if (!tel.checkValidity()) {
                e.preventDefault();
                err.classList.remove('hidden');
                tel.focus();
            }
        });
        window.onload = function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('register') === 'berhasil') document.getElementById('successPopup').classList.remove('hidden');
            if (params.get('register') === 'gagal_daftar') document.getElementById('failedPopup').classList.remove('hidden');
        };
    </script>
</body>

</html>
