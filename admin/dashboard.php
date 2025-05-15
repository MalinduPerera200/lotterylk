<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Include admin header
include 'includes_admin/admin_header.php';

// Initialize variables
$total_results = 0;
$total_news = 0;
$total_lotteries = 0;
$latest_results = [];
$latest_news = [];

// Get statistics if database connection is established
if (isset($pdo) && $db_connected) {
    try {
        // Get total results count
        $stmt = $pdo->query('SELECT COUNT(*) FROM results');
        $total_results = $stmt->fetchColumn();
        
        // Get total news count
        $stmt = $pdo->query('SELECT COUNT(*) FROM news_articles');
        $total_news = $stmt->fetchColumn();
        
        // Get total lotteries count
        $stmt = $pdo->query('SELECT COUNT(*) FROM lotteries');
        $total_lotteries = $stmt->fetchColumn();
        
        // Get latest results
        $stmt = $pdo->query('
            SELECT r.*, l.name as lottery_name, l.type 
            FROM results r
            JOIN lotteries l ON r.lottery_id = l.id
            ORDER BY r.published_at DESC
            LIMIT 5
        ');
        $latest_results = $stmt->fetchAll();
        
        // Get latest news
        $stmt = $pdo->query('
            SELECT * FROM news_articles 
            ORDER BY published_date DESC 
            LIMIT 5
        ');
        $latest_news = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Database error in admin dashboard: " . $e->getMessage(), 0);
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p>දත්ත සමුදායෙන් තොරතුරු ලබා ගැනීමේදී දෝෂයක් ඇති විය.</p>
        </div>';
    }
}
?>

<!-- Dashboard Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Results Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="bg-primaryBlue rounded-full p-3 text-white mr-4">
                <i class="fas fa-trophy text-xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500 text-sm">මුළු ප්‍රතිඵල ගණන</h3>
                <p class="text-3xl font-bold text-primaryBlue"><?php echo $total_results; ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="manage_results.php" class="text-primaryBlue hover:text-blue-700 inline-flex items-center text-sm">
                ප්‍රතිඵල කළමනාකරණය <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Total News Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="bg-primaryGreen rounded-full p-3 text-white mr-4">
                <i class="fas fa-newspaper text-xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500 text-sm">මුළු පුවත් ගණන</h3>
                <p class="text-3xl font-bold text-primaryGreen"><?php echo $total_news; ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="manage_news.php" class="text-primaryGreen hover:text-green-700 inline-flex items-center text-sm">
                පුවත් කළමනාකරණය <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Total Lotteries Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="bg-accentYellow rounded-full p-3 text-primaryBlue mr-4">
                <i class="fas fa-ticket-alt text-xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500 text-sm">මුළු ලොතරැයි වර්ග ගණන</h3>
                <p class="text-3xl font-bold text-accentYellow"><?php echo $total_lotteries; ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="manage_lottery_types.php" class="text-accentYellow hover:text-yellow-600 inline-flex items-center text-sm">
                ලොතරැයි වර්ග කළමනාකරණය <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Latest Results -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-primaryBlue">නවතම ප්‍රතිඵල</h2>
            <a href="add_result.php" class="bg-primaryBlue hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                <i class="fas fa-plus mr-1"></i> නව ප්‍රතිඵලයක්
            </a>
        </div>
        
        <?php if (empty($latest_results)): ?>
        <div class="p-6 text-center text-gray-500">
            <p>ප්‍රතිඵල නොමැත.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ලොතරැයිය</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">දිනය</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ජයග්‍රාහී අංක</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ක්‍රියා</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($latest_results as $result): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded text-white bg-<?php echo ($result['type'] == 'NLB') ? 'primaryBlue' : 'primaryGreen'; ?> mr-2">
                                    <?php echo $result['type']; ?>
                                </span>
                                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($result['lottery_name']); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('Y-m-d', strtotime($result['draw_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="flex justify-center space-x-1">
                                <?php
                                $numbers = explode('-', $result['winning_numbers']);
                                foreach ($numbers as $number) {
                                    echo '<span class="inline-block bg-primaryBlue text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">' . $number . '</span>';
                                }
                                ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <a href="edit_result.php?id=<?php echo $result['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_result.php?id=<?php echo $result['id']; ?>" class="text-red-600 hover:text-red-900 delete-button">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="p-4 border-t text-center">
            <a href="manage_results.php" class="text-primaryBlue hover:text-blue-700 text-sm">
                සියලුම ප්‍රතිඵල බලන්න <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <!-- Latest News -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-primaryGreen">නවතම පුවත්</h2>
            <a href="add_news.php" class="bg-primaryGreen hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                <i class="fas fa-plus mr-1"></i> නව පුවතක්
            </a>
        </div>
        
        <?php if (empty($latest_news)): ?>
        <div class="p-6 text-center text-gray-500">
            <p>පුවත් නොමැත.</p>
        </div>
        <?php else: ?>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($latest_news as $news): ?>
            <li class="p-4 hover:bg-gray-50">
                <div class="flex justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900"><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <?php echo shorten_text(htmlspecialchars($news['content']), 100); ?>
                        </p>
                    </div>
                    <div class="flex items-start ml-4">
                        <a href="edit_news.php?id=<?php echo $news['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_news.php?id=<?php echo $news['id']; ?>" class="text-red-600 hover:text-red-900 delete-button">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span><?php echo date('Y-m-d', strtotime($news['published_date'])); ?></span>
                    <?php if (!empty($news['author_name'])): ?>
                    <span class="ml-2">ලියන්නා: <?php echo htmlspecialchars($news['author_name']); ?></span>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
        <div class="p-4 border-t text-center">
            <a href="manage_news.php" class="text-primaryGreen hover:text-green-700 text-sm">
                සියලුම පුවත් බලන්න <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">ඉක්මන් ක්‍රියා</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="add_result.php" class="p-4 bg-primaryBlue text-white rounded-lg flex items-center hover:bg-blue-700 transition-colors">
            <i class="fas fa-trophy text-2xl mr-4"></i>
            <span>නව ප්‍රතිඵලයක් එකතු කරන්න</span>
        </a>
        
        <a href="add_news.php" class="p-4 bg-primaryGreen text-white rounded-lg flex items-center hover:bg-green-700 transition-colors">
            <i class="fas fa-newspaper text-2xl mr-4"></i>
            <span>නව පුවතක් එකතු කරන්න</span>
        </a>
        
        <a href="manage_lottery_types.php" class="p-4 bg-accentYellow text-primaryBlue rounded-lg flex items-center hover:bg-yellow-500 transition-colors">
            <i class="fas fa-ticket-alt text-2xl mr-4"></i>
            <span>ලොතරැයි වර්ග කළමනාකරණය</span>
        </a>
    </div>
</div>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
