<?php
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Include admin header
include 'includes_admin/admin_header.php';

// Initialize variables
$news_articles = [];
$total_pages = 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$filtered = !empty($search);

// Get total count of news articles (with search filter if applied)
if (isset($pdo) && $db_connected) {
    try {
        $count_sql = 'SELECT COUNT(*) FROM news_articles WHERE 1=1';
        $params = [];
        
        if (!empty($search)) {
            $count_sql .= ' AND (title LIKE :search OR content LIKE :search OR author_name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $pdo->prepare($count_sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total_news = $stmt->fetchColumn();
        $total_pages = ceil($total_news / $per_page);
        
        // Adjust page number if needed
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
            $offset = ($page - 1) * $per_page;
        }
        
    } catch (PDOException $e) {
        error_log("Database error in manage_news.php (count): " . $e->getMessage(), 0);
    }
}

// Get news articles with pagination and search filter
if (isset($pdo) && $db_connected) {
    try {
        $sql = 'SELECT * FROM news_articles WHERE 1=1';
        $params = [];
        
        if (!empty($search)) {
            $sql .= ' AND (title LIKE :search OR content LIKE :search OR author_name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= ' ORDER BY published_date DESC LIMIT :offset, :per_page';
        $params[':offset'] = $offset;
        $params[':per_page'] = $per_page;
        
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, ($key === ':offset' || $key === ':per_page') ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $news_articles = $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Database error in manage_news.php: " . $e->getMessage(), 0);
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p>දත්ත සමුදායෙන් තොරතුරු ලබා ගැනීමේදී දෝෂයක් ඇති විය.</p>
        </div>';
    }
}
?>

<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">පුවත් කළමනාකරණය</h2>
    <a href="add_news.php" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-plus mr-2"></i> නව පුවතක් එකතු කරන්න
    </a>
</div>

<!-- Search Box -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="manage_news.php" method="get" class="flex flex-col md:flex-row items-center">
        <div class="w-full md:w-2/3 mb-4 md:mb-0 md:mr-4">
            <label for="search" class="block text-gray-700 mb-2">පුවත් සොයන්න</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="මාතෘකාව, අන්තර්ගතය, හෝ ලියන්නා..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryGreen">
        </div>
        <div class="w-full md:w-1/3 flex items-end">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                <i class="fas fa-search mr-2"></i> සොයන්න
            </button>
        </div>
    </form>
    
    <?php if ($filtered): ?>
    <div class="mt-4">
        <a href="manage_news.php" class="text-primaryGreen hover:text-green-700 flex items-center">
            <i class="fas fa-times-circle mr-1"></i> සියලුම පුවත් පෙන්වන්න
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- News Articles Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($news_articles)): ?>
    <div class="p-6 text-center text-gray-500">
        <i class="fas fa-newspaper text-3xl mb-3"></i>
        <p>පුවත් හමු නොවීය. <?php echo $filtered ? 'කරුණාකර වෙනත් සෙවුම් පදයක් උත්සාහ කරන්න.' : 'නව පුවතක් එකතු කරන්න.'; ?></p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">මාතෘකාව</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ලියන්නා</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ප්‍රකාශිත දිනය</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">පින්තූරය</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ක්‍රියා</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($news_articles as $article): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars(shorten_text($article['title'], 50)); ?>
                        </div>
                        <div class="text-sm text-gray-500 md:hidden">
                            <?php echo !empty($article['author_name']) ? htmlspecialchars($article['author_name']) : '<span class="text-gray-400">ලියන්නා නැත</span>'; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="text-sm text-gray-500">
                            <?php echo !empty($article['author_name']) ? htmlspecialchars($article['author_name']) : '<span class="text-gray-400">ලියන්නා නැත</span>'; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        <?php echo date('Y-m-d', strtotime($article['published_date'])); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                        <?php if (!empty($article['image_path'])): ?>
                        <span class="text-primaryGreen"><i class="fas fa-check-circle"></i></span>
                        <?php else: ?>
                        <span class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <a href="../news_article.php?id=<?php echo $article['id']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mx-1" title="බලන්න">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="edit_news.php?id=<?php echo $article['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mx-1" title="සංස්කරණය කරන්න">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_news.php?id=<?php echo $article['id']; ?>" class="text-red-600 hover:text-red-900 mx-1 delete-button" title="මකා දමන්න">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<div class="flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
        <?php if ($page > 1): ?>
        <a href="manage_news.php?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-chevron-left"></i>
        </a>
        <?php else: ?>
        <span class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-500 cursor-not-allowed">
            <i class="fas fa-chevron-left"></i>
        </span>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        // Ensure we always show at least 5 page links if available
        if ($end_page - $start_page + 1 < 5 && $total_pages >= 5) {
            if ($start_page == 1) {
                $end_page = min($total_pages, 5);
            } elseif ($end_page == $total_pages) {
                $start_page = max(1, $total_pages - 4);
            }
        }
        
        for ($i = $start_page; $i <= $end_page; $i++): 
        ?>
            <?php if ($i == $page): ?>
            <span class="relative inline-flex items-center px-4 py-2 border border-primaryGreen bg-primaryGreen text-sm font-medium text-white cursor-default">
                <?php echo $i; ?>
            </span>
            <?php else: ?>
            <a href="manage_news.php?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <?php echo $i; ?>
            </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
        <a href="manage_news.php?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-chevron-right"></i>
        </a>
        <?php else: ?>
        <span class="relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-500 cursor-not-allowed">
            <i class="fas fa-chevron-right"></i>
        </span>
        <?php endif; ?>
    </nav>
</div>
<?php endif; ?>

<?php
// Include admin footer
include 'includes_admin/admin_footer.php';
?>
