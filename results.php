<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php'; // This will include the updated format_winning_numbers() and Lottery ID constants

$db_error_message = null; // Variable to store database related errors

// Check if database connection is established
if (!isset($pdo) || !$db_connected) {
    $db_error_message = "දත්ත සමුදාය සම්බන්ධතාවය අසාර්ථක විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.";
}

// Initialize variables
$lottery_id_filter = isset($_GET['lottery_id']) ? (int)$_GET['lottery_id'] : 0; // Renamed to avoid conflict
$draw_date_filter = isset($_GET['draw_date']) ? sanitize_input($_GET['draw_date']) : ''; // Renamed
$type_filter = isset($_GET['type']) ? sanitize_input($_GET['type']) : ''; // Renamed
$results = [];
$lotteries_for_dropdown = []; // Renamed
$filtered = false;
$total_results = 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15; // Number of results per page on public results page
$offset = ($page - 1) * $per_page;
$total_pages = 0;


// Get all lotteries for the filter dropdown
if (!$db_error_message && isset($pdo) && $db_connected) {
    $lotteries_for_dropdown = get_all_lotteries($pdo);
}

// Get lottery results based on filters
if (!$db_error_message && isset($pdo) && $db_connected) {
    try {
        // Build the base query
        $sql_select = 'SELECT r.id, r.lottery_id, r.draw_date, r.winning_numbers, r.jackpot_amount, l.name as lottery_name, l.type as lottery_type';
        $sql_from = ' FROM results r JOIN lotteries l ON r.lottery_id = l.id';
        $sql_where = ' WHERE 1=1';
        $params = [];

        // Apply filters
        if ($lottery_id_filter > 0) {
            $sql_where .= ' AND r.lottery_id = :lottery_id_filter';
            $params[':lottery_id_filter'] = $lottery_id_filter;
            $filtered = true;
        }
        if (!empty($draw_date_filter)) {
            $sql_where .= ' AND r.draw_date = :draw_date_filter';
            $params[':draw_date_filter'] = $draw_date_filter;
            $filtered = true;
        }
        if (!empty($type_filter)) {
            $sql_where .= ' AND l.type = :type_filter';
            $params[':type_filter'] = $type_filter;
            $filtered = true;
        }

        // Get total count for pagination
        $count_stmt = $pdo->prepare('SELECT COUNT(r.id)' . $sql_from . $sql_where);
        $count_stmt->execute($params);
        $total_results = $count_stmt->fetchColumn();
        $total_pages = ceil($total_results / $per_page);

        // Adjust page number if out of bounds
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
            $offset = ($page - 1) * $per_page;
        } elseif ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        // Get the actual results for the current page
        $sql_results = $sql_select . $sql_from . $sql_where . ' ORDER BY r.draw_date DESC, r.id DESC LIMIT :offset, :per_page';
        $stmt = $pdo->prepare($sql_results);

        // Bind common params first
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        // Bind pagination params
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);

        $stmt->execute();
        $results = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in results.php: " . $e->getMessage(), 0);
        $db_error_message = "ප්‍රතිඵල ලබා ගැනීමේදී දෝෂයක් ඇති විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.";
    }
}

// Include header
include 'includes/header.php';
?>

<section class="bg-primaryBlue text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">ලොතරැයි ප්‍රතිඵල</h1>
        <p class="mt-2">ජාතික ලොතරැයි මණ්ඩලය (NLB) සහ සංවර්ධන ලොතරැයි මණ්ඩලය (DLB) විසින් නිකුත් කරන ලොතරැයි වල නවතම ප්‍රතිඵල මෙතැනින් බලන්න.</p>
    </div>
</section>

<section class="mb-8 bg-white rounded-lg shadow-md p-6">
    <div class="container mx-auto">
        <h2 class="text-xl font-bold text-primaryBlue mb-4">ප්‍රතිඵල පෙරන්න</h2>

        <form action="results.php" method="get" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
            <div>
                <label for="lottery_id_filter_public" class="block text-gray-700 mb-2 text-sm font-medium">ලොතරැයිය</label>
                <select id="lottery_id_filter_public" name="lottery_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue text-sm">
                    <option value="0">සියල්ල තෝරන්න</option>
                    <?php if (!empty($lotteries_for_dropdown)): ?>
                        <?php foreach ($lotteries_for_dropdown as $lottery): ?>
                            <option value="<?php echo $lottery['id']; ?>" <?php echo ($lottery_id_filter == $lottery['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lottery['name']); ?> (<?php echo htmlspecialchars($lottery['type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div>
                <label for="draw_date_filter_public" class="block text-gray-700 mb-2 text-sm font-medium">දිනුම් ඇදුම් දිනය</label>
                <input type="date" id="draw_date_filter_public" name="draw_date" value="<?php echo htmlspecialchars($draw_date_filter); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue text-sm">
            </div>

            <div>
                <label for="type_filter_public" class="block text-gray-700 mb-2 text-sm font-medium">ලොතරැයි වර්ගය</label>
                <select id="type_filter_public" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue text-sm">
                    <option value="">සියල්ල තෝරන්න</option>
                    <option value="NLB" <?php echo (strtoupper($type_filter) == 'NLB') ? 'selected' : ''; ?>>NLB</option>
                    <option value="DLB" <?php echo (strtoupper($type_filter) == 'DLB') ? 'selected' : ''; ?>>DLB</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="bg-primaryGreen hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg w-full text-sm">
                    <i class="fas fa-search mr-1"></i> සොයන්න
                </button>
                <?php if ($filtered): ?>
                    <a href="results.php" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-lg w-auto text-sm flex items-center justify-center whitespace-nowrap" title="පෙරීම් ඉවත් කරන්න">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<section class="mb-12">
    <div class="container mx-auto">
        <?php if ($db_error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                <p class="font-bold">දෝෂයක් ඇති විය!</p>
                <p><?php echo $db_error_message; ?></p>
            </div>
        <?php elseif (empty($results) && $filtered): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-6 rounded-lg text-center shadow">
                <div class="flex justify-center mb-3">
                    <i class="fas fa-exclamation-circle fa-2x text-yellow-500"></i>
                </div>
                <p class="text-lg font-semibold mb-2">ප්‍රතිඵල හමු නොවීය</p>
                <p class="text-gray-600 mb-4">ඔබ තෝරාගත් පෙරීම් වලට අනුව ප්‍රතිඵල හමු නොවීය. කරුණාකර වෙනත් පෙරීම් උත්සාහ කරන්න හෝ සියලු ප්‍රතිඵල බලන්න.</p>
                <a href="results.php" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg inline-block text-sm">
                    සියලුම ප්‍රතිඵල පෙන්වන්න
                </a>
            </div>
        <?php elseif (empty($results) && !$filtered): ?>
            <div class="bg-gray-100 rounded-lg p-8 text-center">
                <p class="text-gray-600 mb-4">දැනට පෙන්වීමට ප්‍රතිඵල නොමැත.</p>
            </div>
        <?php else: ?>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-primaryBlue text-white">
                            <tr>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">ලොතරැයිය</th>
                                <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">දිනුම් ඇදුම් දිනය</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">ජයග්‍රාහී අංක / තොරතුරු</th>
                                <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">ජැක්පොට්</th>
                                <th scope="col" class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">ඇණවුම්</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($results as $result): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-xs font-semibold inline-block py-0.5 px-1.5 uppercase rounded text-white <?php echo (strtoupper($result['lottery_type']) == 'NLB') ? 'bg-primaryBlue' : 'bg-primaryGreen'; ?> mr-2">
                                                <?php echo htmlspecialchars($result['lottery_type']); ?>
                                            </span>
                                            <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['lottery_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo format_date_sinhala($result['draw_date']); ?>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo format_winning_numbers($result['lottery_id'], $result['winning_numbers']); ?>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 text-right font-semibold">
                                        <?php echo ($result['jackpot_amount'] > 0) ? format_currency($result['jackpot_amount']) : 'N/A'; ?>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <a href="<?php echo generate_whatsapp_order_link($result['lottery_name'], $result['draw_date']); ?>"
                                            class="whatsapp-order-btn inline-block bg-accentYellow hover:bg-yellow-500 text-primaryBlue text-xs font-bold py-1.5 px-3 rounded-full transition-colors"
                                            data-lottery="<?php echo htmlspecialchars($result['lottery_name']); ?>"
                                            data-date="<?php echo htmlspecialchars($result['draw_date']); // For JS if needed 
                                                        ?>">
                                            <i class="fab fa-whatsapp mr-1"></i> ඇණවුම් කරන්න
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($total_pages > 1):
                $pagination_params_url = [];
                if ($lottery_id_filter > 0) $pagination_params_url['lottery_id'] = $lottery_id_filter;
                if (!empty($draw_date_filter)) $pagination_params_url['draw_date'] = $draw_date_filter;
                if (!empty($type_filter)) $pagination_params_url['type'] = $type_filter;
                $query_string_for_pagination = http_build_query($pagination_params_url);
                $base_url_pagination = 'results.php?' . $query_string_for_pagination . (empty($query_string_for_pagination) ? '' : '&');
            ?>
                <div class="mt-8 flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                        <a href="<?php echo $base_url_pagination; ?>page=<?php echo max(1, $page - 1); ?>"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php if ($page <= 1) echo ' cursor-not-allowed opacity-50'; ?>">
                            <span class="sr-only">Previous</span><i class="fas fa-chevron-left h-5 w-5"></i>
                        </a>
                        <?php
                        $links_limit = 5; // Number of page links to show around current page
                        $start = max(1, $page - floor($links_limit / 2));
                        $end = min($total_pages, $start + $links_limit - 1);
                        if ($end - $start + 1 < $links_limit) {
                            $start = max(1, $end - $links_limit + 1);
                        }

                        if ($start > 1) {
                            echo '<a href="' . $base_url_pagination . 'page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                            if ($start > 2) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        }
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="<?php echo $base_url_pagination; ?>page=<?php echo $i; ?>"
                                class="relative inline-flex items-center px-4 py-2 border <?php echo $i == $page ? 'z-10 bg-primaryBlue border-primaryBlue text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'; ?> text-sm font-medium">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor;
                        if ($end < $total_pages) {
                            if ($end < $total_pages - 1) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                            echo '<a href="' . $base_url_pagination . 'page=' . $total_pages . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $total_pages . '</a>';
                        }
                        ?>
                        <a href="<?php echo $base_url_pagination; ?>page=<?php echo min($total_pages, $page + 1); ?>"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php if ($page >= $total_pages) echo ' cursor-not-allowed opacity-50'; ?>">
                            <span class="sr-only">Next</span><i class="fas fa-chevron-right h-5 w-5"></i>
                        </a>
                    </nav>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<section class="mb-12 bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-10 rounded-lg">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold mb-4">අද දිනයේ ලොතරැයිපත් ඇණවුම් කරන්න</h2>
        <p class="mb-6 max-w-3xl mx-auto">WhatsApp හරහා ඔබට අවශ්‍ය ඕනෑම ලොතරැයියක් ඉතා පහසුවෙන් ඇණවුම් කළ හැකිය. අපි ඔබේ දොරකඩටම ලොතරැයිපත ගෙනවිත් දෙන්නෙමු.</p>
        <a href="order_page.php" class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-8 rounded-full inline-block whatsapp-btn transition-transform transform hover:scale-105">
            <i class="fab fa-whatsapp mr-2"></i> WhatsApp ඔස්සේ ඇණවුම් කරන්න
        </a>
    </div>
</section>

<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-primaryBlue mb-3 md:mb-0">ලොතරැයි දිනුම් රටා</h2>
                <a href="patterns.php" class="text-primaryGreen hover:text-primaryBlue font-medium flex items-center text-sm">
                    සියලුම දිනුම් රටා <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <p class="text-gray-600 mb-6 text-sm">
                <i class="fas fa-info-circle text-primaryBlue mr-2"></i>
                අපගේ "දිනුම් රටා" පිටුවේ ඔබට පසුගිය ලොතරැයි ප්‍රතිඵල අනුව අංක වාප්තිය, ජැක්පොට් ඉතිහාසය, සහ තවත් විශ්ලේෂණ දත්ත බලාගත හැකිය. මෙය අතීත දත්ත විශ්ලේෂණයක් පමණක් වන අතර, අනාගත ප්‍රතිඵල සඳහා පුරෝකථනයක් නොවන බව කරුණාවෙන් සලකන්න.
            </p>
            <div class="text-center">
                <a href="patterns.php" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg inline-block text-sm transition-transform transform hover:scale-105">
                    දිනුම් රටා පිටුවට යන්න
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>