<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Initialize variables
$results = [];
$all_lotteries = [];
$filter_lottery_id = isset($_GET['lottery']) ? intval($_GET['lottery']) : 0;
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$db_error = false;

// Check if database connection is established
if (!isset($pdo) || !$db_connected) {
    $db_error = true;
    error_log("Database connection failed on results.php");
} else {
    // Get all lotteries for filter dropdown
    $all_lotteries = get_all_lotteries($pdo);
    
    // Build query based on filters
    $sql = "SELECT r.*, l.name AS lottery_name, l.type, l.image_path, l.draw_schedule, l.description 
            FROM results r 
            JOIN lotteries l ON r.lottery_id = l.id 
            WHERE 1=1";
    $params = [];
    
    if ($filter_lottery_id > 0) {
        $sql .= " AND r.lottery_id = ?";
        $params[] = $filter_lottery_id;
    }
    
    if (!empty($filter_type)) {
        $sql .= " AND l.type = ?";
        $params[] = $filter_type;
    }
    
    if (!empty($filter_date_from)) {
        $sql .= " AND r.draw_date >= ?";
        $params[] = $filter_date_from;
    }
    
    if (!empty($filter_date_to)) {
        $sql .= " AND r.draw_date <= ?";
        $params[] = $filter_date_to;
    }
    
    // Order by most recent first
    $sql .= " ORDER BY r.draw_date DESC LIMIT 50";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_error = true;
        error_log("Database query failed on results.php: " . $e->getMessage());
    }
}

// Include header
include 'includes/header.php';
?>

<section class="bg-gradient-to-br from-primaryBlue to-primaryGreen text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-2 text-center">ලොතරැයි ප්‍රතිඵල</h1>
        <p class="text-center mb-0">නවතම ලොතරැයි ප්‍රතිඵල සහ ඓතිහාසික ප්‍රතිඵල බලන්න</p>
    </div>
</section>

<section class="mb-12">
    <div class="container mx-auto px-4">
        <!-- Filter controls -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-primaryBlue mb-4">ප්‍රතිඵල පෙරන්න</h2>
            <form action="results.php" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="lottery" class="block text-gray-700 mb-1 text-sm font-medium">ලොතරැයිය</label>
                    <select id="lottery" name="lottery" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                        <option value="0">සියල්ල</option>
                        <?php if (!$db_error && !empty($all_lotteries)): ?>
                            <?php foreach ($all_lotteries as $lottery): ?>
                                <option value="<?php echo $lottery['id']; ?>" <?php echo ($filter_lottery_id == $lottery['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lottery['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-gray-700 mb-1 text-sm font-medium">ලොතරැයි ප්‍රකාරය</label>
                    <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                        <option value="">සියල්ල</option>
                        <option value="NLB" <?php echo ($filter_type == 'NLB') ? 'selected' : ''; ?>>NLB</option>
                        <option value="DLB" <?php echo ($filter_type == 'DLB') ? 'selected' : ''; ?>>DLB</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-gray-700 mb-1 text-sm font-medium">දින සිට</label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo $filter_date_from; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                </div>
                <div>
                    <label for="date_to" class="block text-gray-700 mb-1 text-sm font-medium">දින දක්වා</label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo $filter_date_to; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                </div>
                <div class="md:col-span-2 lg:col-span-4 flex justify-center mt-2">
                    <button type="submit" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full mr-4">
                        <i class="fas fa-filter mr-2"></i> පෙරන්න
                    </button>
                    <a href="results.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full">
                        <i class="fas fa-sync-alt mr-2"></i> යළි සකසන්න
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Results display -->
        <?php if ($db_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                <p class="font-bold">දෝෂයකි!</p>
                <p>ප්‍රතිඵල ලබා ගැනීමට නොහැකි විය. කරුණාකර මඳ වේලාවකින් නැවත උත්සාහ කරන්න.</p>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md" role="alert">
                <p class="font-bold">ප්‍රතිඵල හමු නොවීය!</p>
                <p>ඔබගේ පෙරීම් තේරීම්වලට ගැලපෙන ප්‍රතිඵල කිසිවක් හමු නොවීය. කරුණාකර වෙනත් පෙරීම් තේරීමක් භාවිතා කරන්න.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($results as $result): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                        <!-- Lottery Image/Header -->
                        <?php if (!empty($result['image_path'])): ?>
                            <div class="h-32 bg-white flex items-center justify-center p-2 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($result['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($result['lottery_name']); ?>" 
                                     class="max-h-28 max-w-full object-contain">
                            </div>
                        <?php else: ?>
                            <div class="h-32 bg-gradient-to-r <?php echo (strtoupper($result['type']) == 'NLB') ? 'from-primaryBlue to-blue-700' : 'from-primaryGreen to-green-700'; ?> flex items-center justify-center p-4">
                                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($result['lottery_name']); ?></h3>
                            </div>
                        <?php endif; ?>

                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-primaryBlue"><?php echo htmlspecialchars($result['lottery_name']); ?></h3>
                                    <p class="text-gray-500 text-xs">ඇදීම් දිනය: <?php echo format_date_sinhala($result['draw_date']); ?></p>
                                </div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-white <?php echo (strtoupper($result['type']) == 'NLB') ? 'bg-primaryBlue' : 'bg-primaryGreen'; ?>">
                                    <?php echo htmlspecialchars($result['type']); ?>
                                </span>
                            </div>
                            
                            <div class="mb-3 winning-numbers-display min-h-[60px] flex items-center justify-center">
                                <?php echo format_winning_numbers($result['lottery_id'], $result['winning_numbers']); ?>
                            </div>
                            
                            <div class="border-t pt-3 mt-auto">
                                <p class="text-primaryBlue font-bold text-sm mb-2">
                                    ජැක්පොට්:
                                    <span class="<?php echo ($result['jackpot_amount'] > 0) ? 'text-green-600' : 'text-gray-500'; ?>">
                                        <?php echo ($result['jackpot_amount'] > 0) ? format_currency($result['jackpot_amount']) : 'N/A'; ?>
                                    </span>
                                </p>
                                
                                <?php if (!empty($result['other_prize_details'])): ?>
                                    <div class="mb-3">
                                        <button class="text-xs text-primaryBlue hover:text-primaryGreen underline cursor-pointer prize-details-btn" data-result-id="<?php echo $result['id']; ?>">
                                            අනෙකුත් ත්‍යාග විස්තර බලන්න
                                        </button>
                                        <div id="prize-details-<?php echo $result['id']; ?>" class="hidden mt-2 text-xs bg-gray-50 p-2 rounded">
                                            <?php
                                            $prize_details = json_decode($result['other_prize_details'], true);
                                            if (is_array($prize_details)) {
                                                echo '<ul class="space-y-1">';
                                                foreach ($prize_details as $level => $amount) {
                                                    echo '<li><span class="font-medium">' . ucfirst($level) . ':</span> ' . format_currency($amount) . '</li>';
                                                }
                                                echo '</ul>';
                                            } else {
                                                echo htmlspecialchars($result['other_prize_details']);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php echo generate_whatsapp_order_link($result['lottery_name'], $result['draw_date']); ?>"
                                   class="whatsapp-order-btn inline-block bg-accentYellow hover:bg-yellow-500 text-primaryBlue text-xs font-bold py-2 px-3 rounded-full w-full text-center transition-colors"
                                   data-lottery="<?php echo htmlspecialchars($result['lottery_name']); ?>"
                                   data-date="<?php echo date('Y-m-d', strtotime('+1 week', strtotime($result['draw_date']))); ?>">
                                    <i class="fab fa-whatsapp mr-1"></i> ඊළඟ ඇදීම ඇණවුම් කරන්න
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Link to patterns page -->
            <div class="text-center mt-10">
                <a href="patterns.php" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full inline-block transition-all hover:shadow-lg">
                    <i class="fas fa-chart-line mr-2"></i> ලොතරැයි දිනුම් රටා බලන්න
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- JavaScript for toggle prize details -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.prize-details-btn');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const resultId = this.getAttribute('data-result-id');
            const detailsElement = document.getElementById('prize-details-' + resultId);
            
            if (detailsElement.classList.contains('hidden')) {
                detailsElement.classList.remove('hidden');
                this.textContent = 'ත්‍යාග විස්තර සඟවන්න';
            } else {
                detailsElement.classList.add('hidden');
                this.textContent = 'අනෙකුත් ත්‍යාග විස්තර බලන්න';
            }
        });
    });
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>