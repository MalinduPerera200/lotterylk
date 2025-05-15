<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Initialize variables
$selected_lottery_id = isset($_GET['lottery_id']) ? (int)$_GET['lottery_id'] : 0;
$analysis_range = isset($_GET['range']) ? (int)$_GET['range'] : 10; // Default to last 10 results
$lotteries = [];
$selected_lottery = null;
$number_frequency = [];
$jackpot_history = [];
$winning_numbers_history = [];

// Get all lotteries for the filter dropdown
if (isset($pdo) && $db_connected) {
    $lotteries = get_all_lotteries($pdo);
    
    // If no lottery is selected, use the first one
    if ($selected_lottery_id === 0 && !empty($lotteries)) {
        $selected_lottery_id = $lotteries[0]['id'];
    }
}

// Get selected lottery info
if ($selected_lottery_id > 0 && isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM lotteries WHERE id = :id');
        $stmt->bindParam(':id', $selected_lottery_id, PDO::PARAM_INT);
        $stmt->execute();
        $selected_lottery = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database error in patterns.php (get lottery): " . $e->getMessage(), 0);
    }
}

// Get number frequency for the selected lottery
if ($selected_lottery_id > 0 && isset($pdo) && $db_connected) {
    $number_frequency = get_number_frequency($pdo, $selected_lottery_id, $analysis_range);
}

// Get jackpot history for the selected lottery
if ($selected_lottery_id > 0 && isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('
            SELECT draw_date, jackpot_amount
            FROM results
            WHERE lottery_id = :lottery_id
            ORDER BY draw_date DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':lottery_id', $selected_lottery_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $analysis_range, PDO::PARAM_INT);
        $stmt->execute();
        $jackpot_history = $stmt->fetchAll();
        // Reverse to show oldest to newest for charting
        $jackpot_history = array_reverse($jackpot_history);
    } catch (PDOException $e) {
        error_log("Database error in patterns.php (jackpot history): " . $e->getMessage(), 0);
    }
}

// Get winning numbers history for the selected lottery
if ($selected_lottery_id > 0 && isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('
            SELECT draw_date, winning_numbers
            FROM results
            WHERE lottery_id = :lottery_id
            ORDER BY draw_date DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':lottery_id', $selected_lottery_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $analysis_range, PDO::PARAM_INT);
        $stmt->execute();
        $winning_numbers_history = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in patterns.php (winning numbers): " . $e->getMessage(), 0);
    }
}

// Prepare data for charts
$frequency_labels = [];
$frequency_data = [];
foreach ($number_frequency as $number => $count) {
    $frequency_labels[] = $number;
    $frequency_data[] = $count;
}

$jackpot_labels = [];
$jackpot_data = [];
foreach ($jackpot_history as $item) {
    $jackpot_labels[] = date('m/d', strtotime($item['draw_date']));
    $jackpot_data[] = $item['jackpot_amount'];
}

// Include header
include 'includes/header.php';
?>

<!-- Page Title Section -->
<section class="bg-primaryBlue text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">ලොතරැයි දිනුම් රටා</h1>
        <p class="mt-2">අතීත ලොතරැයි ප්‍රතිඵල විශ්ලේෂණය සහ දිනුම් රටා. මෙහි ඇති සියලුම තොරතුරු අතීත දත්ත මත පදනම් වේ - අනාගත ප්‍රතිඵල පුරෝකථනය කිරීමක් ලෙස සැලකිය නොහැක.</p>
        
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mt-4 text-sm">
            <p class="flex items-start">
                <i class="fas fa-exclamation-triangle mt-1 mr-2"></i>
                <span>මෙම විශ්ලේෂණය අතීත දත්ත මත පදනම් වේ. එය අනාගත ප්‍රතිඵල පුරෝකථනය කිරීමක් නොවේ. ලොතරැයි පූර්ණ වශයෙන් අවස්ථාව/වාසනාව මත පදනම් වේ.</span>
            </p>
        </div>
    </div>
</section>

<!-- Lottery Selection Form -->
<section class="mb-8">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="patterns.php" method="get" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Lottery Selection -->
                <div>
                    <label for="lottery_id" class="block text-gray-700 mb-2">ලොතරැයිය තෝරන්න</label>
                    <select id="lottery_id" name="lottery_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                        <?php foreach ($lotteries as $lottery): ?>
                        <option value="<?php echo $lottery['id']; ?>" <?php echo ($selected_lottery_id == $lottery['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lottery['name']) . ' (' . htmlspecialchars($lottery['type']) . ')'; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Analysis Range -->
                <div>
                    <label for="range" class="block text-gray-700 mb-2">විශ්ලේෂණය කළ යුතු ප්‍රතිඵල ගණන</label>
                    <select id="range" name="range" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                        <option value="5" <?php echo ($analysis_range == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?php echo ($analysis_range == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo ($analysis_range == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="30" <?php echo ($analysis_range == 30) ? 'selected' : ''; ?>>30</option>
                        <option value="50" <?php echo ($analysis_range == 50) ? 'selected' : ''; ?>>50</option>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit" class="bg-primaryGreen hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg w-full">
                        විශ්ලේෂණය පෙන්වන්න
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php if ($selected_lottery): ?>
<!-- Analysis Results Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6">
                <?php echo htmlspecialchars($selected_lottery['name']); ?> - දිනුම් රටා විශ්ලේෂණය
                <span class="text-sm font-normal text-gray-600 ml-2">(අවසන් <?php echo $analysis_range; ?> ප්‍රතිඵල)</span>
            </h2>
            
            <div class="bg-gray-100 rounded-lg p-4 mb-8">
                <p class="text-gray-700">
                    <i class="fas fa-info-circle text-primaryBlue mr-2"></i>
                    මෙම විශ්ලේෂණය <?php echo htmlspecialchars($selected_lottery['name']); ?> ලොතරැයියේ අවසන් <?php echo $analysis_range; ?> ප්‍රතිඵල මත පදනම් වේ. මෙම දත්ත ඔබගේ උපකාරය සඳහා පමණක් වන අතර, අනාගත ප්‍රතිඵල පිළිබඳ පුරෝකථනයක් ලෙස නොසැලකිය යුතුය.
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Number Frequency Chart -->
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold text-primaryBlue mb-4">අංක වාරගණන (Number Frequency)</h3>
                    <div class="h-80">
                        <canvas id="number-frequency-chart" data-numbers="<?php echo implode(',', $frequency_labels); ?>" data-frequencies="<?php echo implode(',', $frequency_data); ?>"></canvas>
                    </div>
                </div>
                
                <!-- Jackpot History Chart -->
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold text-primaryBlue mb-4">ජැක්පොට් ඉතිහාසය</h3>
                    <div class="h-80">
                        <canvas id="jackpot-history-chart" data-dates="<?php echo implode(',', $jackpot_labels); ?>" data-amounts="<?php echo implode(',', $jackpot_data); ?>"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Winning Numbers Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6">මෑත ජයග්‍රාහී අංක</h2>
            
            <?php if (empty($winning_numbers_history)): ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">ජයග්‍රාහී අංක ඉතිහාසය ලබා ගත නොහැක.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">දිනය</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ජයග්‍රාහී අංක</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($winning_numbers_history as $index => $history): ?>
                        <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?> hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo format_date_sinhala($history['draw_date']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    <?php
                                    $numbers = explode('-', $history['winning_numbers']);
                                    foreach ($numbers as $number) {
                                        echo '<span class="inline-block bg-primaryBlue text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mx-1">' . $number . '</span>';
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Analysis Notes Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-primaryBlue mb-4">විශ්ලේෂණ සටහන්</h2>
            
            <div class="text-gray-700 space-y-4">
                <p>
                    <i class="fas fa-chart-bar text-primaryGreen mr-2"></i>
                    <strong>අංක වාරගණන (Number Frequency):</strong> මෙම සටහන ඔබට පෙන්වන්නේ තෝරාගත් කාල සීමාව තුළ එක් එක් අංකය කොපමණ වාර ගණනක් ජයග්‍රාහී අංකයක් ලෙස තේරී ඇත්ද යන්නයි.
                </p>
                <p>
                    <i class="fas fa-money-bill-wave text-primaryGreen mr-2"></i>
                    <strong>ජැක්පොට් ඉතිහාසය:</strong> මෙම සටහන ඔබට පෙන්වන්නේ පසුගිය දිනුම් ඇදීම් වල ජැක්පොට් ප්‍රමාණයන් කෙසේ වෙනස් වී ඇත්ද යන්නයි.
                </p>
                <p>
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <strong>වැදගත් සටහන:</strong> අතීත දත්ත මත පදනම්ව අනාගත ප්‍රතිඵල පුරෝකථනය කිරීමට උත්සාහ කිරීම නිවැරදි ක්‍රමවේදයක් නොවේ. ලොතරැයි ප්‍රතිඵල අහඹු ලෙස ලබාගන්නා දත්ත වේ, එනම් සම්පූර්ණයෙන්ම අවස්ථාව/වාසනාව මත තීරණය වේ.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- WhatsApp Order CTA -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-8 rounded-lg text-center">
            <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($selected_lottery['name']); ?> ලොතරැයිය ඇණවුම් කිරීමට කැමතිද?</h2>
            <p class="mb-6 max-w-3xl mx-auto">WhatsApp හරහා ඔබට අවශ්‍ය <?php echo htmlspecialchars($selected_lottery['name']); ?> ලොතරැයිය ඉතා පහසුවෙන් ඇණවුම් කළ හැකිය. අපි ඔබේ දොරකඩටම ලොතරැයිපත ගෙනවිත් දෙන්නෙමු.</p>
            
            <a href="<?php echo generate_whatsapp_order_link($selected_lottery['name']); ?>" class="whatsapp-order-btn bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-8 rounded-full inline-block" data-lottery="<?php echo htmlspecialchars($selected_lottery['name']); ?>">
                <i class="fab fa-whatsapp mr-2"></i> WhatsApp ඔස්සේ ඇණවුම් කරන්න
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Chart Initialization JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Number Frequency Chart
    const numberFrequencyChart = document.getElementById('number-frequency-chart');
    if (numberFrequencyChart) {
        const numbers = numberFrequencyChart.dataset.numbers.split(',');
        const frequencies = numberFrequencyChart.dataset.frequencies.split(',').map(Number);
        
        new Chart(numberFrequencyChart, {
            type: 'bar',
            data: {
                labels: numbers,
                datasets: [{
                    label: 'වාර ගණන',
                    data: frequencies,
                    backgroundColor: '#0A3D62',
                    borderColor: '#1E8449',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    // Jackpot History Chart
    const jackpotHistoryChart = document.getElementById('jackpot-history-chart');
    if (jackpotHistoryChart) {
        const dates = jackpotHistoryChart.dataset.dates.split(',');
        const amounts = jackpotHistoryChart.dataset.amounts.split(',').map(Number);
        
        new Chart(jackpotHistoryChart, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'ජැක්පොට් ප්‍රමාණය (රුපියල්)',
                    data: amounts,
                    backgroundColor: 'rgba(30, 132, 73, 0.2)',
                    borderColor: '#1E8449',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'රු. ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'ජැක්පොට්: රු. ' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php
// Include footer
include 'includes/footer.php';
?>
