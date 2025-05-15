<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lotteryLK - ශ්‍රී ලංකාවේ විශ්වාසනීයම ලොතරැයි තොරතුරු</title>
    <!-- Tailwind CSS -->
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/lotterylk/assets/css/style.css">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <!-- Top Bar -->
    <div class="bg-primaryGreen text-white text-sm py-1">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div>
                <i class="fas fa-phone-alt mr-1"></i> +94 77 123 4567
            </div>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-accentYellow"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-accentYellow"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-accentYellow"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-accentYellow"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-primaryBlue text-white py-4 shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="/lotterylk/assets/images/logo.png" alt="lotteryLK Logo" class="h-12 mr-3 rounded">
                    <div>
                        <h1 class="text-2xl font-bold text-accentYellow">lotteryLK</h1>
                        <p class="text-xs">ශ්‍රී ලංකාවේ විශ්වාසනීයම ලොතරැයි තොරතුරු</p>
                    </div>
                </div>
                <div class="hidden md:block">
                    <button class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-2 px-4 rounded-full flex items-center">
                        <i class="fab fa-whatsapp text-xl mr-2"></i> WhatsApp ඔස්සේ ඇණවුම් කරන්න
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between">
                <div class="hidden md:flex">
                    <a href="/lotterylk/index.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">මුල් පිටුව</a>
                    <a href="/lotterylk/results.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'results.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">ප්‍රතිඵල</a>
                    <a href="/lotterylk/news.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'news.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">පුවත්</a>
                    <a href="/lotterylk/patterns.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'patterns.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">දිනුම් රටා</a>
                    <a href="/lotterylk/order_page.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'order_page.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">ලොතරැයි ඇණවුම්</a>
                    <a href="/lotterylk/about.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">අප ගැන</a>
                    <a href="/lotterylk/contact.php" class="px-3 py-4 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'text-primaryGreen border-b-2 border-primaryGreen' : 'text-gray-700'; ?>">සම්බන්ධ වන්න</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="container mx-auto px-4 py-2">
                <a href="/lotterylk/index.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">මුල් පිටුව</a>
                <a href="/lotterylk/results.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'results.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">ප්‍රතිඵල</a>
                <a href="/lotterylk/news.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'news.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">පුවත්</a>
                <a href="/lotterylk/patterns.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'patterns.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">දිනුම් රටා</a>
                <a href="/lotterylk/order_page.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'order_page.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">ලොතරැයි ඇණවුම්</a>
                <a href="/lotterylk/about.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">අප ගැන</a>
                <a href="/lotterylk/contact.php" class="block py-2 text-sm font-medium hover:text-primaryGreen <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'text-primaryGreen' : 'text-gray-700'; ?>">සම්බන්ධ වන්න</a>
                <div class="py-2">
                    <button class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-2 px-4 rounded-full w-full flex items-center justify-center">
                        <i class="fab fa-whatsapp text-xl mr-2"></i> WhatsApp ඔස්සේ ඇණවුම් කරන්න
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <main class="container mx-auto px-4 py-6 flex-grow">
