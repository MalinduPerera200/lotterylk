<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Initialize variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6; // Number of news articles per page
$offset = ($page - 1) * $per_page;
$total_news = 0;
$news_articles = [];

// Get total count of news articles for pagination
if (isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->query('SELECT COUNT(*) FROM news_articles');
        $total_news = $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in news.php (count): " . $e->getMessage(), 0);
    }
}

// Calculate total pages
$total_pages = ceil($total_news / $per_page);
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}

// Get news articles with pagination
if (isset($pdo) && $db_connected) {
    try {
        $stmt = $pdo->prepare('
            SELECT * FROM news_articles
            ORDER BY published_date DESC
            LIMIT :offset, :per_page
        ');
        
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        $news_articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in news.php: " . $e->getMessage(), 0);
        $error_message = "පුවත් ලබා ගැනීමේදී දෝෂයක් ඇති විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.";
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Page Title Section -->
<section class="bg-primaryBlue text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">ලොතරැයි පුවත්</h1>
        <p class="mt-2">ශ්‍රී ලංකාවේ ලොතරැයි කර්මාන්තය සම්බන්ධ නවතම තොරතුරු සහ පුවත්.</p>
    </div>
</section>

<!-- News Articles Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <?php if (isset($error_message)): ?>
        <!-- Error Message -->
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <p class="font-bold">දෝෂයක් ඇති විය!</p>
            <p><?php echo $error_message; ?></p>
        </div>
        <?php elseif (empty($news_articles)): ?>
        <!-- No News Found -->
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-600 mb-4">දැනට ප්‍රකාශිත පුවත් නොමැත.</p>
        </div>
        <?php else: ?>
        
        <!-- News Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($news_articles as $article): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden news-card h-full flex flex-col">
                <?php if (!empty($article['image_path'])): ?>
                <div class="h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-48 bg-gradient-to-r from-primaryBlue to-primaryGreen flex items-center justify-center">
                    <i class="fas fa-newspaper text-5xl text-white opacity-25"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-5 flex-grow flex flex-col">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-500"><?php echo date('Y-m-d', strtotime($article['published_date'])); ?></span>
                        <?php if (!empty($article['author_name'])): ?>
                        <span class="text-xs text-primaryGreen"><?php echo htmlspecialchars($article['author_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="text-xl font-bold text-primaryBlue mb-3"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="text-gray-600 mb-6 flex-grow"><?php echo shorten_text(htmlspecialchars($article['content']), 150); ?></p>
                    
                    <a href="news_article.php?id=<?php echo $article['id']; ?>" class="mt-auto text-primaryGreen hover:text-primaryBlue font-medium flex items-center">
                        සම්පූර්ණයෙන් කියවන්න <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <!-- Pagination -->
        <div class="mt-10 flex justify-center">
            <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                <?php if ($page > 1): ?>
                <a href="news.php?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php else: ?>
                <span class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-500 cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <?php endif; ?>
                
                <?php
                // Page links
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
                
                // Show page numbers
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <?php if ($i == $page): ?>
                    <span class="relative inline-flex items-center px-4 py-2 border border-primaryBlue bg-primaryBlue text-sm font-medium text-white cursor-default">
                        <?php echo $i; ?>
                    </span>
                    <?php else: ?>
                    <a href="news.php?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <?php echo $i; ?>
                    </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="news.php?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
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
        
        <?php endif; ?>
    </div>
</section>

<!-- Subscribe to Updates Section -->
<section class="mb-12 bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-10 rounded-lg">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold mb-4">නවතම ලොතරැයි පුවත් සඳහා දායක වන්න</h2>
        <p class="mb-6 max-w-3xl mx-auto">අපගේ විද්‍යුත් තැපැල් ලැයිස්තුවට එක්වීමෙන්, ඔබට නවතම ලොතරැයි ප්‍රතිඵල, නව ලොතරැයි හඳුන්වා දීම් සහ විශේෂ ප්‍රවර්ධන පිළිබඳ යාවත්කාලීන තොරතුරු ලබා ගත හැකිය.</p>
        
        <form action="#" method="post" class="max-w-lg mx-auto flex flex-col sm:flex-row">
            <input type="email" name="email" placeholder="ඔබගේ විද්‍යුත් තැපැල් ලිපිනය" required class="px-4 py-3 w-full rounded-full sm:rounded-l-full sm:rounded-r-none text-gray-700 mb-4 sm:mb-0">
            <button type="submit" class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-6 rounded-full sm:rounded-l-none sm:rounded-r-full">
                දායක වන්න
            </button>
        </form>
    </div>
</section>

<!-- Buy Lottery Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-2/3 mb-6 md:mb-0 md:pr-8">
                    <h2 class="text-2xl font-bold text-primaryBlue mb-4">ලොතරැයිපත් ඇණවුම් කරන්න</h2>
                    <p class="text-gray-600 mb-6">
                        WhatsApp හරහා ඔබට අවශ්‍ය ඕනෑම ලොතරැයියක් ඉතා පහසුවෙන් ඇණවුම් කළ හැකිය. අපි ඔබේ දොරකඩටම ලොතරැයිපත ගෙනවිත් දෙන්නෙමු. ලොතරැයිපත් ඇණවුම් කිරීමට පහත "ඇණවුම් කරන්න" බොත්තම ක්ලික් කරන්න.
                    </p>
                    <a href="order_page.php" class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-8 rounded-full inline-block whatsapp-btn">
                        <i class="fab fa-whatsapp mr-2"></i> ඇණවුම් කරන්න
                    </a>
                </div>
                <div class="md:w-1/3 flex justify-center">
                    <div class="w-64 h-64 rounded-full bg-primaryBlue flex items-center justify-center animate-float">
                        <div class="w-56 h-56 rounded-full bg-accentYellow flex items-center justify-center text-primaryBlue">
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-2">ඇණවුම්</div>
                                <div class="text-3xl font-bold">කරන්න</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
