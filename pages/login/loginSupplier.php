<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Supplier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">
</head>

<body class="bg-[#EFE9E2] min-h-screen flex flex-col overflow-y-auto">
    <div class="flex flex-col md:flex-row w-full">
        <!-- Kiri: Gambar Desain -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 bg-[#EFE9E2]">
            <img src="../../assets/gambar/login/designlogin.jpg" alt="login Image"
                 class="rounded-xl w-full h-auto object-cover max-h-[500px]" />
        </div>

        <!-- Kanan: Form Login Supplier -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <a href="../login/loginCustomer.php" class="text-gray-600 hover:text-gray-900 text-base mb-4 inline-block">
                    &#8592; Back
                </a>
                <div class="flex flex-col items-start mb-6">
                    <img src="../../assets/gambar/logo.png" alt="SimaBer Logo"
                         class="w-32 h-32 mix-blend-multiply" />
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Welcome Supplier 👋</h2>
                <p class="text-gray-500 mb-6">Please login here</p>

                <form action="../../assets/mysql/register_login/proses.php" method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block mb-1 text-sm font-semibold text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email address"
                               class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080] text-gray-800"
                               value="<?php echo isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : ''; ?>" required />
                    </div>

                    <div>
                        <label for="password" class="block mb-1 text-sm font-semibold text-gray-700">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Enter your password"
                                   class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 pr-10 focus:outline-none focus:ring-2 focus:ring-[#c0a080] text-gray-800" required />
                            <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-3 text-gray-500 focus:outline-none">
                               <svg id="eyeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Pesan Error -->
                    <?php if (isset($_GET['login']) && $_GET['login'] == 'gagal'): ?>
                    <p class="text-red-500 text-sm">Email or Password is wrong</p>
                    <?php elseif (isset($_GET['login']) && $_GET['login'] == 'error'): ?>
                    <p class="text-red-500 text-sm">Please login first</p>
                    <?php endif; ?>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm text-gray-700">
                            <input type="checkbox" name="remember" class="mr-2 accent-[#8b5c2c]"
                                   <?php echo isset($_COOKIE['remember_email']) ? 'checked' : ''; ?> />
                            Remember Me
                        </label>
                        <a href="forgotPassword.php" class="text-sm text-[#c0a080] hover:underline">Forgot Password?</a>
                    </div>

                    <button type="submit" name="loginSupplier" value="loginSupplier"
                            class="w-full py-3 px-4 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg text-lg transition">
                        Login
                    </button>
                </form>

                <div class="mt-4 space-y-1">
                    <a href="../login/loginCustomer.php">
                        <button
                            class="w-full py-3 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">

                            Login as Customer

                        </button>
                    </a>
                    <a href="../login/loginBusinessOwner.php">
                        <button
                            class="w-full py-3 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                            Login as Business Owner
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/cdn/flowbite.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");
             if (password.type === "password") {
            password.type = "text";
            eyeIcon.innerHTML =
                `
          <path d="M2 2L22 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M6.71277 6.7226C3.66479 8.79527 2 12 2 12C2 12 5.63636 19 12 19C14.0503 19 15.8174 18.2734 17.2711 17.2884M11 5.05822C11.3254 5.02013 11.6588 5 12 5C18.3636 5 22 12 22 12C22 12 21.3082 13.3317 20 14.8335" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M14 14.2362C13.4692 14.7112 12.7684 15.0001 12 15.0001C10.3431 15.0001 9 13.657 9 12.0001C9 11.1764 9.33193 10.4303 9.86932 9.88818" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>`;
        } else {
            password.type = "password";
            eyeIcon.innerHTML =
                `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
        }
    </script>
</body>

</html>
