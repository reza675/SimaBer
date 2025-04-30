<?php
session_start();

if (!isset($_SESSION['otp_verified'])) {
    header("Location: forgotPassword.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../../assets/cdn/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="../../assets/gambar/icon.png">

</head>

<body class="bg-[#EFE9E2] min-h-screen flex">
    <?php
        if (isset($_GET['newPassword']) && $_GET['newPassword'] == "berhasil"){
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);
        echo "<div id='successPopup' class='fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden'>
            <div class='bg-white rounded-2xl p-8 max-w-md text-center shadow-xl'>
                <div class='mb-4'><img src='../../assets/gambar/register/success.webp' alt='success' class='w-24 h-24 mx-auto mix-blend-multiply'></div>
                <h3 class='text-xl font-bold mb-2'>Password Update Successfully</h3>
                <p class='text-gray-600 mb-6'>Your password has been update successfully</p>
                <a href='../login/loginCustomer.php'
                    class='inline-block px-6 py-2 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg font-medium'>
                    Back to Login
                </a>
            </div>
        </div>";
        
        }
    ?>
    <div class="flex flex-col md:flex-row w-full h-screen overflow-hidden">
        <div class="flex items-center justify-center md:w-1/2 bg-[#EFE9E2] overflow-hidden">
            <img src="../../assets/gambar/login/lupapass.png" alt="login Image" class="p-6 mx-auto w-90 h-full" />
        </div>

        <div class="flex flex-col justify-center md:w-1/2 p-4">
            <div class="max-w-md w-full mx-auto">
                <div class="flex flex-col items-left">
                    <img src="../../assets/gambar/logo.png" alt="SimaBer Logo" class="mt-4 mix-blend-multiply mx-0"
                        style="width: 140px; height: 140px;" />
                    <a href="otp_verification.php" class="mt-6 text-gray-600 hover:text-gray-900 text-lg block">
                        <p class="text-base">&#8592; Back</p>
                    </a>

                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Enter New Password</h2>
                <p class="text-gray-500 mb-4">Enter your new password</p>



                <form action="../../assets/mysql/register_login/proses.php" method="POST" class="space-y-4">
                    <div>
                        <label for="password" class="block mb-1 text-sm font-semibold text-gray-700">New Password</label>
                        <div class="relative">
                            <input type="password" name="newPasswordUser" id="password" placeholder="Enter your new password"
                                class="w-full bg-[#fef9f4] border border-[#c0a080] rounded-lg p-3 pr-10 focus:outline-none focus:ring-2 focus:ring-[#c0a080] text-gray-800"
                                required />
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

                    <button type="submit" value="newPassword" name="newPassword"
                        class="w-full py-3 px-4 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg font-sm text-lg transition">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>


</body>
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

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('newPassword') === 'berhasil') {
        document.getElementById("successPopup").classList.remove("hidden");

    };
}
</script>

</html>