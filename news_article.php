<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Initialize variables
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;
$related_articles = [];

// Get article details
if ($article_id > 0 && isset($pdo) && $db_connected) {
    try {
        // Get the article
        $article = get_news_by_id($pdo, $article_id);
        
        if ($article) {
            // Get related articles (other recent articles)
            $stmt = $pdo->prepare('
                SELECT * FROM news_articles
                WHERE id != :article_id
                ORDER BY published_date DESC
                LIMIT 3
            ');
            $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->execute();
            $related_articles = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        error_log("Database error in news_article.php: " . $e->getMessage(), 0);
        $error_message = "පුවත් ලිපිය ලබා ගැනීමේදී දෝෂයක් ඇති විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.";
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="container mx-auto px-4 py-4">
    <div class="flex items-center text-sm text-gray-600">
        <a href="index.php" class="hover:text-primaryBlue">මුල් පිටුව</a>
        <i class="fas fa-chevron-right mx-2 text-xs"></i>
        <a href="news.php" class="hover:text-primaryBlue">පුවත්</a>
        <?php if ($article): ?>
        <i class="fas fa-chevron-right mx-2 text-xs"></i>
        <span class="text-gray-500"><?php echo htmlspecialchars(shorten_text($article['title'], 30)); ?></span>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($error_message)): ?>
<!-- Error Message -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <p class="font-bold">දෝෂයක් ඇති විය!</p>
            <p><?php echo $error_message; ?></p>
            <div class="mt-4">
                <a href="news.php" class="text-primaryBlue hover:underline">← පුවත් ලැයිස්තුවට ආපසු යන්න</a>
            </div>
        </div>
    </div>
</section>
<?php elseif (!$article): ?>
<!-- Article Not Found -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <h2 class="text-2xl font-bold text-primaryBlue mb-4">පුවත් ලිපිය හමු නොවීය</h2>
            <p class="text-gray-600 mb-6">ඔබ සොයන පුවත් ලිපිය හමු නොවීය. ලිපිය ඉවත් කර ඇති විය හැක.</p>
            <a href="news.php" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg inline-block">
                පුවත් ලැයිස්තුවට ආපසු යන්න
            </a>
        </div>
    </div>
</section>
<?php else: ?>

<!-- Article Content Section -->
<section class="mb-10">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (!empty($article['image_path'])): ?>
            <div class="w-full h-80 md:h-96 overflow-hidden">
                <img src="<?php echo htmlspecialchars($article['image_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover">
            </div>
            <?php endif; ?>
            
            <div class="p-6 md:p-10">
                <!-- Article Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-primaryBlue mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                    
                    <div class="flex items-center text-gray-600 text-sm mb-4">
                        <span class="flex items-center mr-6">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <?php echo date('Y-m-d', strtotime($article['published_date'])); ?>
                        </span>
                        
                        <?php if (!empty($article['author_name'])): ?>
                        <span class="flex items-center">
                            <i class="far fa-user mr-2"></i>
                            <?php echo htmlspecialchars($article['author_name']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="border-b border-gray-200 mb-6"></div>
                </div>
                
                <!-- Article Content -->
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    <?php 
                    // Format the content with paragraphs
                    $paragraphs = explode("\n", $article['content']);
                    foreach ($paragraphs as $paragraph) {
                        if (trim($paragraph) !== '') {
                            echo '<p class="mb-4">' . htmlspecialchars($paragraph) . '</p>';
                        }
                    }
                    ?>
                </div>
                
                <!-- Share Article -->
                <div class="mt-10 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-primaryBlue mb-4">මෙම පුවත බෙදාගන්න:</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-[#3b5998] hover:bg-blue-700 text-white px-4 py-2 rounded-full flex items-center">
                            <i class="fab fa-facebook-f mr-2"></i> Facebook
                        </a>
                        <a href="#" class="bg-[#1da1f2] hover:bg-blue-500 text-white px-4 py-2 rounded-full flex items-center">
                            <i class="fab fa-twitter mr-2"></i> Twitter
                        </a>
                        <a href="#" class="bg-[#25D366] hover:bg-green-500 text-white px-4 py-2 rounded-full flex items-center">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles Section -->
<?php if (!empty($related_articles)): ?>
<section class="mb-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-primaryBlue mb-6">තවත් පුවත්</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($related_articles as $related): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden news-card h-full flex flex-col">
                <?php if (!empty($related['image_path'])): ?>
                <div class="h-40 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($related['image_path']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="h-40 bg-gradient-to-r from-primaryBlue to-primaryGreen flex items-center justify-center">
                    <i class="fas fa-newspaper text-4xl text-white opacity-25"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-5 flex-grow flex flex-col">
                    <h3 class="text-lg font-bold text-primaryBlue mb-3"><?php echo htmlspecialchars($related['title']); ?></h3>
                    <p class="text-gray-600 mb-4 flex-grow"><?php echo shorten_text(htmlspecialchars($related['content']), 100); ?></p>
                    
                    <a href="news_article.php?id=<?php echo $related['id']; ?>" class="mt-auto text-primaryGreen hover:text-primaryBlue font-medium">
                        සම්පූර්ණයෙන් කියවන්න <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Back to News Button -->
<section class="mb-12">
    <div class="container mx-auto px-4 text-center">
        <a href="news.php" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-block">
            <i class="fas fa-arrow-left mr-2"></i> සියලුම පුවත් වෙත ආපසු යන්න
        </a>
    </div>
</section>

<?php endif; ?>

<?php
// Include footer
include 'includes/footer.php';
?>
