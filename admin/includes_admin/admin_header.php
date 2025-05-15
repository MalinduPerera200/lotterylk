<?php
// Include the login check script
require_once 'auth/check_login.php';

// Get current page filename for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lotteryLK Admin</title>
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
    
    <!-- Custom Admin CSS -->
    <style>
        .sidebar-link {
            transition: all 0.3s ease;
        }
        
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #F1C40F;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Mobile Top Bar -->
    <div class="md:hidden bg-primaryBlue text-white p-4 flex justify-between items-center">
        <button id="sidebar-toggle" class="text-white focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <div class="flex items-center">
            <img src="../assets/images/logo.png" alt="lotteryLK Logo" class="h-8 rounded mr-2">
            <span class="font-bold">lotteryLK Admin</span>
        </div>
        <div class="relative">
            <button id="mobile-user-menu-button" class="text-white focus:outline-none">
                <i class="fas fa-user-circle text-xl"></i>
            </button>
            <div id="mobile-user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                <div class="py-1">
                    <span class="block px-4 py-2 text-sm text-gray-700 border-b">
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> පිටවීම
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-primaryBlue text-white w-64 flex-shrink-0 fixed md:sticky top-0 h-screen overflow-y-auto z-20">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-blue-800 flex items-center">
                <img src="../assets/images/logo.png" alt="lotteryLK Logo" class="h-10 rounded mr-3">
                <div>
                    <h1 class="font-bold text-lg">lotteryLK</h1>
                    <p class="text-xs opacity-75">පරිපාලක පැනලය</p>
                </div>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="mt-4">
                <ul>
                    <li>
                        <a href="dashboard.php" class="sidebar-link flex items-center px-4 py-3 <?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span>උපකරණ පුවරුව</span>
                        </a>
                    </li>
                    <li>
                        <a href="manage_results.php" class="sidebar-link flex items-center px-4 py-3 <?php echo ($current_page === 'manage_results.php' || $current_page === 'add_result.php' || $current_page === 'edit_result.php') ? 'active' : ''; ?>">
                            <i class="fas fa-trophy w-6"></i>
                            <span>ප්‍රතිඵල කළමනාකරණය</span>
                        </a>
                    </li>
                    <li>
                        <a href="manage_news.php" class="sidebar-link flex items-center px-4 py-3 <?php echo ($current_page === 'manage_news.php' || $current_page === 'add_news.php' || $current_page === 'edit_news.php') ? 'active' : ''; ?>">
                            <i class="fas fa-newspaper w-6"></i>
                            <span>පුවත් කළමනාකරණය</span>
                        </a>
                    </li>
                    <li>
                        <a href="manage_lottery_types.php" class="sidebar-link flex items-center px-4 py-3 <?php echo ($current_page === 'manage_lottery_types.php') ? 'active' : ''; ?>">
                            <i class="fas fa-ticket-alt w-6"></i>
                            <span>ලොතරැයි වර්ග</span>
                        </a>
                    </li>
                    <li>
                        <a href="manage_users.php" class="sidebar-link flex items-center px-4 py-3 <?php echo ($current_page === 'manage_users.php') ? 'active' : ''; ?>">
                            <i class="fas fa-users w-6"></i>
                            <span>පරිශීලකයින්</span>
                        </a>
                    </li>
                    <li class="border-t border-blue-800 mt-4 pt-4">
                        <a href="../index.php" target="_blank" class="sidebar-link flex items-center px-4 py-3">
                            <i class="fas fa-external-link-alt w-6"></i>
                            <span>වෙබ් අඩවිය බලන්න</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="sidebar-link flex items-center px-4 py-3 text-red-300 hover:text-red-100">
                            <i class="fas fa-sign-out-alt w-6"></i>
                            <span>පිටවීම</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm p-4 hidden md:flex justify-between items-center">
                <h1 class="text-xl font-bold text-gray-800">
                    <?php
                    // Set page title based on current page
                    switch ($current_page) {
                        case 'dashboard.php':
                            echo 'උපකරණ පුවරුව';
                            break;
                        case 'manage_results.php':
                            echo 'ප්‍රතිඵල කළමනාකරණය';
                            break;
                        case 'add_result.php':
                            echo 'නව ප්‍රතිඵලයක් එකතු කරන්න';
                            break;
                        case 'edit_result.php':
                            echo 'ප්‍රතිඵලය සංස්කරණය කරන්න';
                            break;
                        case 'manage_news.php':
                            echo 'පුවත් කළමනාකරණය';
                            break;
                        case 'add_news.php':
                            echo 'නව පුවතක් එකතු කරන්න';
                            break;
                        case 'edit_news.php':
                            echo 'පුවත සංස්කරණය කරන්න';
                            break;
                        case 'manage_lottery_types.php':
                            echo 'ලොතරැයි වර්ග කළමනාකරණය';
                            break;
                        case 'manage_users.php':
                            echo 'පරිශීලකයින් කළමනාකරණය';
                            break;
                        default:
                            echo 'lotteryLK Admin';
                            break;
                    }
                    ?>
                </h1>
                <div class="relative">
                    <button id="user-menu-button" class="flex items-center text-gray-700 focus:outline-none">
                        <span class="mr-2"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <i class="fas fa-user-circle text-2xl text-primaryBlue"></i>
                    </button>
                    <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                        <div class="py-1">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> පිටවීම
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="p-6">
                <?php if (isset($_SESSION['flash_message'])): ?>
                <!-- Flash Message -->
                <div class="mb-6 p-4 rounded-md <?php echo ($_SESSION['flash_message_type'] === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas <?php echo ($_SESSION['flash_message_type'] === 'success') ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        </div>
                        <div class="ml-3">
                            <p><?php echo $_SESSION['flash_message']; ?></p>
                        </div>
                    </div>
                </div>
                <?php
                // Clear flash message after displaying
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_message_type']);
                endif;
