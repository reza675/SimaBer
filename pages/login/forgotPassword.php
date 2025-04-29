<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Pelanggan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />

</head>

<body class="bg-[#EFE9E2] min-h-screen flex">
    <div class="flex flex-col md:flex-row w-full h-screen overflow-hidden">
        <div class="flex items-center justify-center md:w-1/2 bg-[#EFE9E2] overflow-hidden">
            <img src="../../assets/gambar/login/lupapass.png" alt="login Image" class="p-6 mx-auto w-90 h-full" />
        </div>

        <div class="flex flex-col justify-center md:w-1/2 p-4">
            <div class="max-w-md w-full mx-auto">
                <div class="flex flex-col items-left">
                    <img src="../../assets/gambar/logo.png" alt="SimaBer Logo" class="mt-4 mix-blend-multiply mx-0"
                        style="width: 140px; height: 140px;" />
                    <a href="../login/loginCustomer.php" class="mt-6 text-gray-600 hover:text-gray-900 text-lg block">
                        <p class="text-base">&#8592; Back</p>
                    </a>

                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Forgot Password</h2>
                <p class="text-gray-500 mb-4">Enter your registered email address. we’ll send you a code to reset your
                    password.</p>



                <form action="../../assets/mysql/register_login/proses.php" method="POST" class="space-y-4">
                    <div>
                        <label for="email" class="block mb-1 text-sm font-semibold text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" placeholder="Enter your email address"
                            class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#c0a080] text-gray-800"
                            required />
                    </div>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'errorEmail'): ?>
                        <p class="text-red-500 text-sm mb-4">Email unregisted.</p>
                    <?php endif; ?>

                    <button type="submit" value="forgotPassword" name="forgotPassword"
                        class="w-full py-3 px-4 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg font-sm text-lg transition">
                        Send Email
                    </button>
                </form>
            </div>
        </div>
    </div>


</body>

</html>