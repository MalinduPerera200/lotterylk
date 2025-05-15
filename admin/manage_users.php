<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Include admin header
include 'includes_admin/admin_header.php';

// Initialize variables
$users = [];
$total_users = 0;

// Process form submission for adding new user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $new_username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
    $new_password = isset($_POST['password']) ? $_POST['password'] : '';
    $new_email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    
    // Basic validation
    $error_message = '';
    
    if (empty($new_username)) {
        $error_message = 'පරිශීලක නාමය අවශ්‍යයි.';
    } elseif (empty($new_password)) {
        $error_message = 'මුරපදය අවශ්‍යයි.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'මුරපදය අවම වශයෙන් අක්ෂර 6 ක් විය යුතුය.';
    } else {
        try {
            // Check if username already exists
            $check_stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = :username');
            $check_stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = 'පරිශීලක නාමය දැනටමත් භාවිතා කරයි.';
            } else {
                // Hash the password
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insert_stmt = $pdo->prepare('
                    INSERT INTO admin_users (username, password_hash, email) 
                    VALUES (:username, :password_hash, :email)
                ');
                $insert_stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
                $insert_stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
                $insert_stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
                
                if ($insert_stmt->execute()) {
                    $_SESSION['flash_message'] = 'පරිශීලකයා සාර්ථකව එකතු කරන ලදී.';
                    $_SESSION['flash_message_type'] = 'success';
                    header('Location: manage_users.php');
                    exit;
                } else {
                    $error_message = 'පරිශීලකයා එකතු කිරීමේදී දෝෂයක් ඇති විය.';
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in manage_users.php (add): " . $e->getMessage(), 0);
            $error_message = 'දත්ත සමුදාය දෝෂයක් ඇති විය.';
        }
    }
    
    // If error occurred, show message
    if (!empty($error_message)) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p>' . $error_message . '</p>
        </div>';
    }
}

// Get all admin users
if (isset($pdo) && $db_connected) {
    try {
        // Get total users count
        $count_stmt = $pdo->query('SELECT COUNT(*) FROM admin_users');
        $total_users = $count_stmt->fetchColumn();
        
        // Get all users
        $stmt = $pdo->query('
            SELECT id, username, email, last_login, created_at
            FROM admin_users
            ORDER BY username
        ');
        $users = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Database error in manage_users.php: " . $e->getMessage(), 0);
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p>දත්ත සමුදායෙන් තොරතුරු ලබා ගැනීමේදී දෝෂයක් ඇති විය.</p>
        </div>';
    }
}
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">පරිශීලකයින් කළමනාකරණය</h2>
    <button id="open-add-user-modal" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-user-plus mr-2"></i> නව පරිශීලකයකු එකතු කරන්න
    </button>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($users)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-users text-3xl mb-3"></i>
        <p>පරිශීලකයින් හමු නොවීය.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">පරිශීලක නාමය</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ඊමේල්</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">අවසන් පිවිසුම</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">සාදන ලද දිනය</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ක්‍රියා</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primaryBlue text-white flex items-center justify-center">
                                <span class="font-bold"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></div>
                                <div class="text-sm text-gray-500 md:hidden"><?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : '<span class="text-gray-400">ඊමේල් නැත</span>'; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                        <div class="text-sm text-gray-500"><?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : '<span class="text-gray-400">ඊමේල් නැත</span>'; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden lg:table-cell">
                        <?php echo !empty($user['last_login']) ? date('Y-m-d H:i', strtotime($user['last_login'])) : '<span class="text-gray-400">පිවිසී නැත</span>'; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden lg:table-cell">
                        <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <button data-userid="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>" class="reset-password-btn text-indigo-600 hover:text-indigo-900 mx-1" title="මුරපදය යළි පිහිටුවන්න">
                            <i class="fas fa-key"></i>
                        </button>
                        <?php if ($user['id'] != $_SESSION['admin_id']): // Prevent deleting own account ?>
                        <button data-userid="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>" class="delete-user-btn text-red-600 hover:text-red-900 mx-1" title="පරිශීලකයා මකා දමන්න">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg relative z-10 w-full max-w-md p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-primaryBlue">නව පරිශීලකයකු එකතු කරන්න</h3>
            <button id="close-add-user-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="manage_users.php" method="post">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-bold mb-2">පරිශීලක නාමය *</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">මුරපදය *</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                <p class="text-sm text-gray-500 mt-1">අවම වශයෙන් අක්ෂර 6 ක් විය යුතුය.</p>
            </div>
            
            <div class="mb-6">
                <label for="email" class="block text-gray-700 font-bold mb-2">ඊමේල් (අවශ්‍ය නැත)</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-add-user" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    අවලංගු කරන්න
                </button>
                <button type="submit" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    එකතු කරන්න
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="reset-password-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg relative z-10 w-full max-w-md p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-primaryBlue">මුරපදය යළි පිහිටුවන්න</h3>
            <button id="close-reset-password-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="reset_password.php" method="post" id="reset-password-form">
            <input type="hidden" id="reset-user-id" name="user_id" value="">
            
            <p class="mb-4">ඔබට <span id="reset-username" class="font-bold"></span> සඳහා නව මුරපදයක් යළි පිහිටුවීමට අවශ්‍යද?</p>
            
            <div class="mb-6">
                <label for="new-password" class="block text-gray-700 font-bold mb-2">නව මුරපදය *</label>
                <input type="password" id="new-password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                <p class="text-sm text-gray-500 mt-1">අවම වශයෙන් අක්ෂර 6 ක් විය යුතුය.</p>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-reset-password" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    අවලංගු කරන්න
                </button>
                <button type="submit" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    යළි පිහිටුවන්න
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div id="delete-user-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg relative z-10 w-full max-w-md p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-red-600">පරිශීලකයා මකා දමන්න</h3>
            <button id="close-delete-user-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="delete_user.php" method="post" id="delete-user-form">
            <input type="hidden" id="delete-user-id" name="user_id" value="">
            
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            ඔබට <span id="delete-username" class="font-bold"></span> පරිශීලකයා මැකීමට අවශ්‍ය බව විශ්වාසද? මෙම ක්‍රියාව ප්‍රතිවර්ත කළ නොහැක.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-delete-user" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    අවලංගු කරන්න
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    මකා දමන්න
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Modals -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add User Modal
        const addUserModal = document.getElementById('add-user-modal');
        const openAddUserModalBtn = document.getElementById('open-add-user-modal');
        const closeAddUserModalBtn = document.getElementById('close-add-user-modal');
        const cancelAddUserBtn = document.getElementById('cancel-add-user');
        
        openAddUserModalBtn.addEventListener('click', function() {
            addUserModal.classList.remove('hidden');
        });
        
        function closeAddUserModal() {
            addUserModal.classList.add('hidden');
        }
        
        closeAddUserModalBtn.addEventListener('click', closeAddUserModal);
        cancelAddUserBtn.addEventListener('click', closeAddUserModal);
        
        // Reset Password Modal
        const resetPasswordModal = document.getElementById('reset-password-modal');
        const resetPasswordBtns = document.querySelectorAll('.reset-password-btn');
        const closeResetPasswordModalBtn = document.getElementById('close-reset-password-modal');
        const cancelResetPasswordBtn = document.getElementById('cancel-reset-password');
        const resetUserIdInput = document.getElementById('reset-user-id');
        const resetUsernameSpan = document.getElementById('reset-username');
        
        resetPasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userid;
                const username = this.dataset.username;
                
                resetUserIdInput.value = userId;
                resetUsernameSpan.textContent = username;
                
                resetPasswordModal.classList.remove('hidden');
            });
        });
        
        function closeResetPasswordModal() {
            resetPasswordModal.classList.add('hidden');
        }
        
        closeResetPasswordModalBtn.addEventListener('click', closeResetPasswordModal);
        cancelResetPasswordBtn.addEventListener('click', closeResetPasswordModal);
        
        // Delete User Modal
        const deleteUserModal = document.getElementById('delete-user-modal');
        const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
        const closeDeleteUserModalBtn = document.getElementById('close-delete-user-modal');
        const cancelDeleteUserBtn = document.getElementById('cancel-delete-user');
        const deleteUserIdInput = document.getElementById('delete-user-id');
        const deleteUsernameSpan = document.getElementById('delete-username');
        
        deleteUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userid;
                const username = this.dataset.username;
                
                deleteUserIdInput.value = userId;
                deleteUsernameSpan.textContent = username;
                
                deleteUserModal.classList.remove('hidden');
            });
        });
        
        function closeDeleteUserModal() {
            deleteUserModal.classList.add('hidden');
        }
        
        closeDeleteUserModalBtn.addEventListener('click', closeDeleteUserModal);
        cancelDeleteUserBtn.addEventListener('click', closeDeleteUserModal);
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === addUserModal) {
                closeAddUserModal();
            }
            if (event.target === resetPasswordModal) {
                closeResetPasswordModal();
            }
            if (event.target === deleteUserModal) {
                closeDeleteUserModal();
            }
        });
    });
</script>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
