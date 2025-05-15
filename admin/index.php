<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Redirect to dashboard
    header('Location: dashboard.php');
    exit;
}

// Get login error message if exists
$error_message = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']); // Clear error after displaying

// Remember username if provided previously
$username = isset($_SESSION['login_username']) ? $_SESSION['login_username'] : '';
unset($_SESSION['login_username']); // Clear saved username after using
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - lotteryLK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primaryBlue: '#0A3D62',
                        primaryGreen: '#1E8449',
                        accentYellow: '#F1C40F',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-primaryBlue text-white p-6 text-center">
                <div class="flex justify-center mb-4">
                    <img src="../assets/images/logo.png" alt="lotteryLK Logo" class="h-16 rounded-lg">
                </div>
                <h1 class="text-2xl font-bold">lotteryLK Admin</h1>
                <p class="text-sm opacity-75">පරිපාලක පිවිසුම</p>
            </div>
            
            <!-- Form -->
            <div class="p-6">
                <h2 class="text-xl font-bold text-primaryBlue mb-6 text-center">පිවිසුම</h2>
                
                <?php if (!empty($error_message)): ?>
                <!-- Error Message -->
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p><?php echo $error_message; ?></p>
                </div>
                <?php endif; ?>
                
                <form action="auth/login_process.php" method="post">
                    <div class="mb-6">
                        <label for="username" class="block text-gray-700 mb-2">පරිශීලක නාමය</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" class="w-full pl-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 mb-2">මුරපදය</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" id="password" name="password" class="w-full pl-10 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-primaryBlue hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
                            පිවිසෙන්න
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 p-6 text-center">
                <a href="../index.php" class="text-primaryBlue hover:text-primaryGreen flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i> මුල් පිටුවට ආපසු යන්න
                </a>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="text-center mt-6 text-gray-500 text-sm">
            <p>&copy; <?php echo date('Y'); ?> lotteryLK. සියලුම හිමිකම් ඇවිරිණි.</p>
        </div>
    </div>
</body>
</html>
