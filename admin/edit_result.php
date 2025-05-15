<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Initialize variables
$result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lotteries = [];
$form_data = [
    'id' => 0,
    'lottery_id' => 0,
    'draw_date' => '',
    'winning_numbers' => '',
    'jackpot_amount' => 0,
    'other_prize_details' => '',
    'published_at' => ''
];
$error_message = '';
$result_found = false;

// Get all lotteries for the dropdown
if (isset($pdo) && $db_connected) {
    try {
        $lotteries = get_all_lotteries($pdo);
    } catch (PDOException $e) {
        error_log("Database error in edit_result.php (lotteries): " . $e->getMessage(), 0);
    }
}

// Get result data
if ($result_id > 0 && isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM results WHERE id = :id');
        $stmt->bindParam(':id', $result_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if ($result) {
            $result_found = true;
            $form_data = [
                'id' => $result['id'],
                'lottery_id' => $result['lottery_id'],
                'draw_date' => $result['draw_date'],
                'winning_numbers' => $result['winning_numbers'],
                'jackpot_amount' => $result['jackpot_amount'],
                'other_prize_details' => $result['other_prize_details'],
                'published_at' => $result['published_at']
            ];
        } else {
            $error_message = 'ප්‍රතිඵලය හමු නොවීය.';
        }
    } catch (PDOException $e) {
        error_log("Database error in edit_result.php (fetch): " . $e->getMessage(), 0);
        $error_message = 'ප්‍රතිඵලය ලබා ගැනීමේදී දෝෂයක් ඇති විය.';
    }
} else {
    $error_message = 'වලංගු ප්‍රතිඵල ID එකක් සපයන්න.';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $result_found) {
    // Get form data
    $form_data = [
        'id' => $result_id,
        'lottery_id' => isset($_POST['lottery_id']) ? (int)$_POST['lottery_id'] : 0,
        'draw_date' => isset($_POST['draw_date']) ? sanitize_input($_POST['draw_date']) : '',
        'winning_numbers' => isset($_POST['winning_numbers']) ? sanitize_input($_POST['winning_numbers']) : '',
        'jackpot_amount' => isset($_POST['jackpot_amount']) ? (float)$_POST['jackpot_amount'] : 0,
        'other_prize_details' => isset($_POST['other_prize_details']) ? sanitize_input($_POST['other_prize_details']) : '',
        'published_at' => isset($_POST['published_at']) ? sanitize_input($_POST['published_at']) : ''
    ];
    
    // Basic validation
    if ($form_data['lottery_id'] <= 0) {
        $error_message = 'කරුණාකර ලොතරැයිය තෝරන්න.';
    } elseif (empty($form_data['draw_date'])) {
        $error_message = 'කරුණාකර දිනුම් ඇදීම් දිනය ඇතුළත් කරන්න.';
    } elseif (empty($form_data['winning_numbers'])) {
        $error_message = 'කරුණාකර ජයග්‍රාහී අංක ඇතුළත් කරන්න.';
    } elseif ($form_data['jackpot_amount'] <= 0) {
        $error_message = 'කරුණාකර වලංගු ජැක්පොට් මුදලක් ඇතුළත් කරන්න.';
    } else {
        // All validation passed, update database
        try {
            $sql = "
                UPDATE results 
                SET lottery_id = :lottery_id, 
                    draw_date = :draw_date, 
                    winning_numbers = :winning_numbers, 
                    jackpot_amount = :jackpot_amount, 
                    other_prize_details = :other_prize_details
                WHERE id = :id
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $form_data['id'], PDO::PARAM_INT);
            $stmt->bindParam(':lottery_id', $form_data['lottery_id'], PDO::PARAM_INT);
            $stmt->bindParam(':draw_date', $form_data['draw_date']);
            $stmt->bindParam(':winning_numbers', $form_data['winning_numbers']);
            $stmt->bindParam(':jackpot_amount', $form_data['jackpot_amount']);
            $stmt->bindParam(':other_prize_details', $form_data['other_prize_details']);
            
            if ($stmt->execute()) {
                // Success
                $_SESSION['flash_message'] = 'ප්‍රතිඵලය සාර්ථකව යාවත්කාලීන කරන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
                header('Location: manage_results.php');
                exit;
            } else {
                $error_message = 'ප්‍රතිඵලය යාවත්කාලීන කිරීමේදී දෝෂයක් ඇති විය.';
            }
        } catch (PDOException $e) {
            error_log("Database error in edit_result.php (update): " . $e->getMessage(), 0);
            $error_message = 'ප්‍රතිඵලය යාවත්කාලීන කිරීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
        }
    }
}

// Include admin header
include 'includes_admin/admin_header.php';
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">ප්‍රතිඵලය සංස්කරණය කරන්න</h2>
    <a href="manage_results.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> ආපසු යන්න
    </a>
</div>

<!-- Error Message -->
<?php if (!empty($error_message)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
    <p><?php echo $error_message; ?></p>
    <?php if (!$result_found): ?>
    <div class="mt-2">
        <a href="manage_results.php" class="text-red-700 underline">ප්‍රතිඵල ලැයිස්තුවට ආපසු යන්න</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($result_found): ?>
<!-- Edit Result Form -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="edit_result.php?id=<?php echo $result_id; ?>" method="post" class="p-6">
        <input type="hidden" name="id" value="<?php echo $result_id; ?>">
        
        <!-- Lottery Selection -->
        <div class="mb-6">
            <label for="lottery_id" class="block text-gray-700 font-bold mb-2">ලොතරැයිය *</label>
            <select id="lottery_id" name="lottery_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                <option value="">ලොතරැයිය තෝරන්න</option>
                <?php foreach ($lotteries as $lottery): ?>
                <option value="<?php echo $lottery['id']; ?>" <?php echo ($form_data['lottery_id'] == $lottery['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($lottery['name']) . ' (' . $lottery['type'] . ')'; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Draw Date -->
        <div class="mb-6">
            <label for="draw_date" class="block text-gray-700 font-bold mb-2">දිනුම් ඇදීම් දිනය *</label>
            <input type="date" id="draw_date" name="draw_date" value="<?php echo htmlspecialchars($form_data['draw_date']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
        </div>
        
        <!-- Winning Numbers -->
        <div class="mb-6">
            <label for="winning_numbers" class="block text-gray-700 font-bold mb-2">ජයග්‍රාහී අංක * (කාඩි වලින් වෙන් කරන්න: උදා: 01-12-23-34-45-66)</label>
            <input type="text" id="winning_numbers" name="winning_numbers" value="<?php echo htmlspecialchars($form_data['winning_numbers']); ?>" placeholder="උදා: 01-12-23-34-45-66" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
            <p class="text-sm text-gray-500 mt-1">ජයග්‍රාහී අංක කැඩි ඉරි (-) මගින් වෙන් කරන්න.</p>
        </div>
        
        <!-- Jackpot Amount -->
        <div class="mb-6">
            <label for="jackpot_amount" class="block text-gray-700 font-bold mb-2">ජැක්පොට් මුදල (රුපියල්) *</label>
            <input type="number" id="jackpot_amount" name="jackpot_amount" value="<?php echo htmlspecialchars($form_data['jackpot_amount']); ?>" step="0.01" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
        </div>
        
        <!-- Other Prize Details -->
        <div class="mb-6">
            <label for="other_prize_details" class="block text-gray-700 font-bold mb-2">වෙනත් ත්‍යාග විස්තර (JSON ආකෘතිය)</label>
            <textarea id="other_prize_details" name="other_prize_details" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue"><?php echo htmlspecialchars($form_data['other_prize_details']); ?></textarea>
            <p class="text-sm text-gray-500 mt-1">JSON ආකෘතියෙන් - උදා: {"second": 1000000, "third": 500000}</p>
        </div>
        
        <!-- Published At (Read Only) -->
        <div class="mb-6">
            <label for="published_at" class="block text-gray-700 font-bold mb-2">ප්‍රකාශිත දිනය (සංස්කරණය කළ නොහැක)</label>
            <input type="text" id="published_at" value="<?php echo date('Y-m-d H:i:s', strtotime($form_data['published_at'])); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                <i class="fas fa-save mr-2"></i> යාවත්කාලීන කරන්න
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
