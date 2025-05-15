<?php
ob_start();
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Include admin header
include 'includes_admin/admin_header.php';

// Initialize variables
$lotteries = [];
$total_lotteries = 0;
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$filter_type = isset($_GET['type']) ? sanitize_input($_GET['type']) : '';
$filtered = !empty($search) || !empty($filter_type);

// Process form submission for adding/editing lottery
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action == 'add' || $action == 'edit') {
        // Get form data
        $lottery_id = isset($_POST['lottery_id']) ? (int)$_POST['lottery_id'] : 0;
        $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
        $type = isset($_POST['type']) ? sanitize_input($_POST['type']) : '';
        $draw_schedule = isset($_POST['draw_schedule']) ? sanitize_input($_POST['draw_schedule']) : '';
        $description = isset($_POST['description']) ? sanitize_input($_POST['description']) : '';
        
        // Basic validation
        $error_message = '';
        
        if (empty($name)) {
            $error_message = 'ලොතරැයි නම අවශ්‍යයි.';
        } elseif (empty($type)) {
            $error_message = 'ලොතරැයි වර්ගය අවශ්‍යයි.';
        } else {
            try {
                if ($action == 'add') {
                    // Check if lottery name already exists
                    $check_stmt = $pdo->prepare('SELECT id FROM lotteries WHERE name = :name');
                    $check_stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $check_stmt->execute();
                    
                    if ($check_stmt->rowCount() > 0) {
                        $error_message = 'ලොතරැයි නම දැනටමත් භාවිතා කරයි.';
                    } else {
                        // Insert new lottery
                        $insert_stmt = $pdo->prepare('
                            INSERT INTO lotteries (name, type, draw_schedule, description) 
                            VALUES (:name, :type, :draw_schedule, :description)
                        ');
                        $insert_stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $insert_stmt->bindParam(':type', $type, PDO::PARAM_STR);
                        $insert_stmt->bindParam(':draw_schedule', $draw_schedule, PDO::PARAM_STR);
                        $insert_stmt->bindParam(':description', $description, PDO::PARAM_STR);
                        
                        if ($insert_stmt->execute()) {
                            $_SESSION['flash_message'] = 'ලොතරැයිය සාර්ථකව එකතු කරන ලදී.';
                            $_SESSION['flash_message_type'] = 'success';
                            header('Location: manage_lottery_types.php');
                            exit;
                        } else {
                            $error_message = 'ලොතරැයිය එකතු කිරීමේදී දෝෂයක් ඇති විය.';
                        }
                    }
                } else {
                    // Check if lottery exists
                    $check_stmt = $pdo->prepare('SELECT id FROM lotteries WHERE id = :id');
                    $check_stmt->bindParam(':id', $lottery_id, PDO::PARAM_INT);
                    $check_stmt->execute();
                    
                    if ($check_stmt->rowCount() == 0) {
                        $error_message = 'ලොතරැයිය හමු නොවීය.';
                    } else {
                        // Check if name is unique (except for this lottery)
                        $check_name_stmt = $pdo->prepare('SELECT id FROM lotteries WHERE name = :name AND id != :id');
                        $check_name_stmt->bindParam(':name', $name, PDO::PARAM_STR);
                        $check_name_stmt->bindParam(':id', $lottery_id, PDO::PARAM_INT);
                        $check_name_stmt->execute();
                        
                        if ($check_name_stmt->rowCount() > 0) {
                            $error_message = 'ලොතරැයි නම දැනටමත් භාවිතා කරයි.';
                        } else {
                            // Update lottery
                            $update_stmt = $pdo->prepare('
                                UPDATE lotteries 
                                SET name = :name, type = :type, draw_schedule = :draw_schedule, description = :description
                                WHERE id = :id
                            ');
                            $update_stmt->bindParam(':id', $lottery_id, PDO::PARAM_INT);
                            $update_stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $update_stmt->bindParam(':type', $type, PDO::PARAM_STR);
                            $update_stmt->bindParam(':draw_schedule', $draw_schedule, PDO::PARAM_STR);
                            $update_stmt->bindParam(':description', $description, PDO::PARAM_STR);
                            
                            if ($update_stmt->execute()) {
                                $_SESSION['flash_message'] = 'ලොතරැයිය සාර්ථකව යාවත්කාලීන කරන ලදී.';
                                $_SESSION['flash_message_type'] = 'success';
                                header('Location: manage_lottery_types.php');
                                exit;
                            } else {
                                $error_message = 'ලොතරැයිය යාවත්කාලීන කිරීමේදී දෝෂයක් ඇති විය.';
                            }
                        }
                    }
                }
            } catch (PDOException $e) {
                error_log("Database error in manage_lottery_types.php (" . $action . "): " . $e->getMessage(), 0);
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
}

// Get all lotteries
if (isset($pdo) && $db_connected) {
    try {
        // Build query with filters
        $sql = 'SELECT * FROM lotteries WHERE 1=1';
        $params = [];
        
        if (!empty($search)) {
            $sql .= ' AND (name LIKE :search OR description LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        if (!empty($filter_type)) {
            $sql .= ' AND type = :type';
            $params[':type'] = $filter_type;
        }
        
        $sql .= ' ORDER BY name';
        
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $lotteries = $stmt->fetchAll();
        $total_lotteries = count($lotteries);
        
    } catch (PDOException $e) {
        error_log("Database error in manage_lottery_types.php (get): " . $e->getMessage(), 0);
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p>දත්ත සමුදායෙන් තොරතුරු ලබා ගැනීමේදී දෝෂයක් ඇති විය.</p>
        </div>';
    }
}
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">ලොතරැයි වර්ග කළමනාකරණය</h2>
    <button id="open-add-lottery-modal" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-plus mr-2"></i> නව ලොතරැයියක් එකතු කරන්න
    </button>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="manage_lottery_types.php" method="get" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Search Box -->
        <div>
            <label for="search" class="block text-gray-700 mb-2">සෙවීම</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ලොතරැයිය සොයන්න..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
        </div>
        
        <!-- Filter by Type -->
        <div>
            <label for="type" class="block text-gray-700 mb-2">වර්ගය අනුව පෙරන්න</label>
            <select id="type" name="type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                <option value="">සියල්ල පෙන්වන්න</option>
                <option value="NLB" <?php echo ($filter_type == 'NLB') ? 'selected' : ''; ?>>NLB</option>
                <option value="DLB" <?php echo ($filter_type == 'DLB') ? 'selected' : ''; ?>>DLB</option>
            </select>
        </div>
        
        <!-- Buttons -->
        <div class="flex items-end space-x-4">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex-1">
                <i class="fas fa-search mr-2"></i> සොයන්න
            </button>
            
            <?php if ($filtered): ?>
            <a href="manage_lottery_types.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded flex-1 text-center">
                <i class="fas fa-times mr-2"></i> පෙරීම් ඉවත් කරන්න
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Lotteries Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($lotteries)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-ticket-alt text-3xl mb-3"></i>
        <p>ලොතරැයි හමු නොවීය. <?php echo $filtered ? 'කරුණාකර වෙනත් පෙරීම් පරාමිතීන් උත්සාහ කරන්න.' : 'නව ලොතරැයියක් එකතු කරන්න.'; ?></p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ලොතරැයි නම</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">වර්ගය</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">දිනුම් ඇදීම් කාලසටහන</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">විස්තරය</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ක්‍රියා</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($lotteries as $lottery): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($lottery['name']); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium <?php echo ($lottery['type'] == 'NLB') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo htmlspecialchars($lottery['type']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($lottery['draw_schedule']); ?></div>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars(shorten_text($lottery['description'], 100)); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <button data-id="<?php echo $lottery['id']; ?>" class="edit-lottery-btn text-indigo-600 hover:text-indigo-900 mx-1" title="සංස්කරණය කරන්න">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button data-id="<?php echo $lottery['id']; ?>" data-name="<?php echo htmlspecialchars($lottery['name']); ?>" class="delete-lottery-btn text-red-600 hover:text-red-900 mx-1" title="මකා දමන්න">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Lottery Modal -->
<div id="lottery-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg relative z-10 w-full max-w-md p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-title" class="text-xl font-bold text-primaryBlue">නව ලොතරැයියක් එකතු කරන්න</h3>
            <button id="close-lottery-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="lottery-form" action="manage_lottery_types.php" method="post">
            <input type="hidden" id="action" name="action" value="add">
            <input type="hidden" id="lottery_id" name="lottery_id" value="0">
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">ලොතරැයි නම *</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
            </div>
            
            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-bold mb-2">වර්ගය *</label>
                <select id="lottery-type" name="type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                    <option value="">වර්ගය තෝරන්න</option>
                    <option value="NLB">NLB (ජාතික ලොතරැයි මණ්ඩලය)</option>
                    <option value="DLB">DLB (සංවර්ධන ලොතරැයි මණ්ඩලය)</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="draw_schedule" class="block text-gray-700 font-bold mb-2">දිනුම් ඇදීම් කාලසටහන</label>
                <input type="text" id="draw_schedule" name="draw_schedule" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" placeholder="උදා: සෑම අඟහරුවාදා දිනකම">
            </div>
            
            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-bold mb-2">විස්තරය</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue"></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-lottery" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    අවලංගු කරන්න
                </button>
                <button type="submit" id="submit-lottery" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    සුරකින්න
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Lottery Modal -->
<div id="delete-lottery-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg relative z-10 w-full max-w-md p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-red-600">ලොතරැයිය මකා දමන්න</h3>
            <button id="close-delete-lottery-modal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="delete_lottery.php" method="post" id="delete-lottery-form">
            <input type="hidden" id="delete-lottery-id" name="lottery_id" value="">
            
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            ඔබට <span id="delete-lottery-name" class="font-bold"></span> ලොතරැයිය මැකීමට අවශ්‍ය බව විශ්වාසද? මෙම ක්‍රියාව සහ මෙම ලොතරැයියට අදාළ සියලුම ප්‍රතිඵල ප්‍රතිවර්ත කළ නොහැක.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-delete-lottery" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    අවලංගු කරන්න
                </button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    මකා දමන්න
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Modals and Edit Operation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add/Edit Lottery Modal
        const lotteryModal = document.getElementById('lottery-modal');
        const openAddLotteryBtn = document.getElementById('open-add-lottery-modal');
        const closeLotteryModalBtn = document.getElementById('close-lottery-modal');
        const cancelLotteryBtn = document.getElementById('cancel-lottery');
        const modalTitle = document.getElementById('modal-title');
        const lotteryForm = document.getElementById('lottery-form');
        const actionInput = document.getElementById('action');
        const lotteryIdInput = document.getElementById('lottery_id');
        const nameInput = document.getElementById('name');
        const typeInput = document.getElementById('lottery-type');
        const drawScheduleInput = document.getElementById('draw_schedule');
        const descriptionInput = document.getElementById('description');
        const submitLotteryBtn = document.getElementById('submit-lottery');
        
        // Add new lottery
        openAddLotteryBtn.addEventListener('click', function() {
            // Set modal for adding
            modalTitle.textContent = 'නව ලොතරැයියක් එකතු කරන්න';
            actionInput.value = 'add';
            lotteryIdInput.value = '0';
            submitLotteryBtn.textContent = 'එකතු කරන්න';
            
            // Clear form
            lotteryForm.reset();
            
            // Show modal
            lotteryModal.classList.remove('hidden');
        });
        
        // Edit lottery
        const editLotteryBtns = document.querySelectorAll('.edit-lottery-btn');
        editLotteryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const lotteryId = this.dataset.id;
                
                // Fetch lottery data
                fetch('get_lottery.php?id=' + lotteryId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Set modal for editing
                            modalTitle.textContent = 'ලොතරැයිය සංස්කරණය කරන්න';
                            actionInput.value = 'edit';
                            lotteryIdInput.value = data.lottery.id;
                            nameInput.value = data.lottery.name;
                            typeInput.value = data.lottery.type;
                            drawScheduleInput.value = data.lottery.draw_schedule;
                            descriptionInput.value = data.lottery.description;
                            submitLotteryBtn.textContent = 'යාවත්කාලීන කරන්න';
                            
                            // Show modal
                            lotteryModal.classList.remove('hidden');
                        } else {
                            alert('ලොතරැයි දත්ත ලබා ගැනීමට අසමත් විය: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('ලොතරැයි දත්ත ලබා ගැනීමේදී දෝෂයක් ඇති විය.');
                    });
            });
        });
        
        // For demo purposes, handle edit click without AJAX
        editLotteryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const lotteryId = this.dataset.id;
                const row = this.closest('tr');
                
                // Get data from the table row
                const name = row.cells[0].querySelector('.text-sm').textContent;
                const type = row.cells[1].querySelector('span').textContent.trim();
                const drawSchedule = row.cells[2] ? row.cells[2].querySelector('.text-sm').textContent : '';
                const description = row.cells[3] ? row.cells[3].querySelector('.text-sm').textContent : '';
                
                // Set modal for editing
                modalTitle.textContent = 'ලොතරැයිය සංස්කරණය කරන්න';
                actionInput.value = 'edit';
                lotteryIdInput.value = lotteryId;
                nameInput.value = name;
                typeInput.value = type;
                drawScheduleInput.value = drawSchedule;
                descriptionInput.value = description;
                submitLotteryBtn.textContent = 'යාවත්කාලීන කරන්න';
                
                // Show modal
                lotteryModal.classList.remove('hidden');
            });
        });
        
        function closeLotteryModal() {
            lotteryModal.classList.add('hidden');
        }
        
        closeLotteryModalBtn.addEventListener('click', closeLotteryModal);
        cancelLotteryBtn.addEventListener('click', closeLotteryModal);
        
        // Delete Lottery Modal
        const deleteLotteryModal = document.getElementById('delete-lottery-modal');
        const deleteLotteryBtns = document.querySelectorAll('.delete-lottery-btn');
        const closeDeleteLotteryModalBtn = document.getElementById('close-delete-lottery-modal');
        const cancelDeleteLotteryBtn = document.getElementById('cancel-delete-lottery');
        const deleteLotteryIdInput = document.getElementById('delete-lottery-id');
        const deleteLotteryNameSpan = document.getElementById('delete-lottery-name');
        
        deleteLotteryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const lotteryId = this.dataset.id;
                const lotteryName = this.dataset.name;
                
                deleteLotteryIdInput.value = lotteryId;
                deleteLotteryNameSpan.textContent = lotteryName;
                
                deleteLotteryModal.classList.remove('hidden');
            });
        });
        
        function closeDeleteLotteryModal() {
            deleteLotteryModal.classList.add('hidden');
        }
        
        closeDeleteLotteryModalBtn.addEventListener('click', closeDeleteLotteryModal);
        cancelDeleteLotteryBtn.addEventListener('click', closeDeleteLotteryModal);
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === lotteryModal) {
                closeLotteryModal();
            }
            if (event.target === deleteLotteryModal) {
                closeDeleteLotteryModal();
            }
        });
    });
</script>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
ob_end_flush();
?>
