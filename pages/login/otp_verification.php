<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi OTP</title>
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
                    <a href="forgotPassword.php" class="mt-6 text-gray-600 hover:text-gray-900 text-lg block">
                        <p class="text-base">&#8592; Back</p>
                    </a>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-1">Enter OTP Code</h2>
                <p class="text-gray-500 mb-4">Make sure your OTP code is valid</p>

                <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                    <p class="text-red-500 text-sm mb-4">Kode OTP salah atau sudah kedaluwarsa.</p>
                <?php endif; ?>

                <form action="../../assets/mysql/register_login/proses.php" method="POST" class="space-y-4">
                    <div class="flex justify-between max-w-md mx-auto space-x-3">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <input type="text" name="otp[]" maxlength="1" pattern="\d{1}" inputmode="numeric"
                                class="w-10 text-center text-2xl border-0 border-b-2 border-black bg-transparent focus:outline-none focus:border-[#c0a080]"
                                required>
                        <?php endfor; ?>
                    </div>

                    <button type="submit" value="verifyOTP" name="verifyOTP"
                        class="w-full py-3 px-4 bg-[#8b5c2c] hover:bg-[#6f451e] text-white rounded-lg font-sm text-lg transition">
                        Send OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
  const inputs = document.querySelectorAll('input[name="otp[]"]');
  inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
      if (input.value && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });
</script>

</html>
