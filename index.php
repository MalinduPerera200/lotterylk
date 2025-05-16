<?php
// Start session if not already started (good practice, though not strictly needed for index if no session vars used directly)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php'; // This will include the updated format_winning_numbers() and Lottery ID constants

// Initialize variables to hold data
$latest_results = [];
$latest_news = [];
$all_lotteries = [];
$db_error = false;

// Check if database connection is established
if (!isset($pdo) || !$db_connected) {
    $db_error = true;
    // Optional: Log this error more formally or display a user-friendly general error message
    error_log("Database connection failed on index.php");
} else {
    // Get latest lottery results (ensure get_latest_results selects lottery_id)
    $latest_results = get_latest_results($pdo, 6); // Get 6 results to show in 2 rows of 3

    // Get latest news
    $latest_news = get_latest_news($pdo, 3);

    // Get all lotteries for the featured section
    $all_lotteries = get_all_lotteries($pdo);
}

// Include header
include 'includes/header.php';
?>

<section class="bg-gradient-to-br from-primaryBlue to-primaryGreen text-white py-12 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-8 md:mb-0 text-center md:text-left">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">ලොතරැයි තොරතුරු සහ ඇණවුම් සේවාව</h1>
                <p class="text-lg mb-6">අපේ වෙබ් අඩවිය හරහා ලොතරැයි ප්‍රතිඵල, පුවත්, දිනුම් රටා බලන්න සහ WhatsApp ඔස්සේ පහසුවෙන් ලොතරැයි ඇණවුම් කරන්න.</p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center md:justify-start">
                    <a href="results.php" class="bg-white text-primaryBlue hover:bg-gray-100 font-bold py-3 px-6 rounded-full inline-block text-center transition-transform transform hover:scale-105">
                        <i class="fas fa-trophy mr-2"></i> ප්‍රතිඵල බලන්න
                    </a>
                    <a href="order_page.php" class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-6 rounded-full inline-block text-center whatsapp-btn transition-transform transform hover:scale-105">
                        <i class="fab fa-whatsapp mr-2"></i> ලොතරැයි ඇණවුම්
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center mt-8 md:mt-0">
                <div id="lottery-animation" class="w-full h-64 md:h-80">
                    <img src="/lotterylk/assets/images/hero-lottery-balls.png" alt="Lottery Balls" class="w-full h-full object-contain animate-float">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-primaryBlue">නවතම ලොතරැයි ප්‍රතිඵල</h2>
            <a href="results.php" class="text-primaryGreen hover:text-primaryBlue font-medium flex items-center text-sm md:text-base">
                සියලුම ප්‍රතිඵල <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <?php if ($db_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
                <p class="font-bold">දෝෂයකි!</p>
                <p>දැනට ප්‍රතිඵල පෙන්වීමට නොහැක. කරුණාකර මඳ වේලාවකින් නැවත උත්සාහ කරන්න.</p>
            </div>
        <?php elseif (!empty($latest_results)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($latest_results as $result): ?>
                    <div class="bg-white rounded-lg shadow-md p-5 hover:shadow-xl transition-shadow duration-300 ease-in-out flex flex-col result-card">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-primaryBlue"><?php echo htmlspecialchars($result['lottery_name']); ?></h3>
                                <p class="text-gray-500 text-xs"><?php echo format_date_sinhala($result['draw_date']); ?></p>
                            </div>

                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-white <?php echo (strtoupper($result['type']) == 'NLB') ? 'bg-primaryBlue' : 'bg-primaryGreen'; ?>">
                                <?php echo htmlspecialchars($result['type']); ?>
                            </span>
                        </div>

                        <div class="mb-3 winning-numbers-display min-h-[50px] flex items-center justify-center"> <?php
                                                                                                                    // Pass lottery_id and the winning_numbers JSON string
                                                                                                                    echo format_winning_numbers($result['lottery_id'], $result['winning_numbers']);
                                                                                                                    ?>
                        </div>

                        <div class="border-t pt-3 mt-auto">
                            <p class="text-primaryBlue font-bold text-sm mb-2">
                                ජැක්පොට්:
                                <span class="<?php echo ($result['jackpot_amount'] > 0) ? 'text-green-600' : 'text-gray-500'; ?>">
                                    <?php echo ($result['jackpot_amount'] > 0) ? format_currency($result['jackpot_amount']) : 'N/A'; ?>
                                </span>
                            </p>
                            <a href="<?php echo generate_whatsapp_order_link($result['lottery_name'], $result['draw_date']); ?>"
                                class="whatsapp-order-btn inline-block bg-accentYellow hover:bg-yellow-500 text-primaryBlue text-xs font-bold py-2 px-3 rounded-full w-full text-center transition-colors"
                                data-lottery="<?php echo htmlspecialchars($result['lottery_name']); ?>"
                                data-date="<?php echo htmlspecialchars($result['draw_date']); ?>">
                                <i class="fab fa-whatsapp mr-1"></i> ඊළඟ ඇදීම ඇණවුම් කරන්න
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">දැනට පෙන්වීමට නවතම ප්‍රතිඵල නොමැත.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="mb-12 bg-gray-100 py-10 rounded-lg">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-primaryBlue mb-8 text-center">ජනප්‍රිය ලොතරැයි</h2>

        <?php if ($db_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-center" role="alert">
                <p>ලොතරැයි ලැයිස්තුව ලබා ගැනීමට නොහැකි විය.</p>
            </div>
        <?php elseif (!empty($all_lotteries)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach (array_slice($all_lotteries, 0, 8) as $lottery): // Show up to 8 featured lotteries 
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden featured-lottery h-full flex flex-col">
                        <?php if (!empty($lottery['image_path'])): ?>
                            <div class="h-32 flex items-center justify-center p-4 text-center overflow-hidden">
                                <img src="<?php echo htmlspecialchars($lottery['image_path']); ?>" alt="<?php echo htmlspecialchars($lottery['name']); ?>" class="w-full h-full object-contain">
                            </div>
                        <?php else: ?>
                            <div class="h-32 bg-gradient-to-r <?php echo (strtoupper($lottery['type']) == 'NLB') ? 'from-primaryBlue to-blue-700' : 'from-primaryGreen to-green-700'; ?> flex items-center justify-center p-4 text-center">
                                <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($lottery['name']); ?></h3>
                            </div>
                        <?php endif; ?>
                        <div class="p-5 flex flex-col flex-grow">
                            <p class="text-gray-600 text-sm mb-3 flex-grow"><?php echo htmlspecialchars(shorten_text($lottery['description'], 70)); ?></p>
                            <p class="text-xs text-primaryBlue mb-4">
                                <i class="far fa-calendar-alt mr-1"></i> <?php echo htmlspecialchars($lottery['draw_schedule']); ?>
                            </p>
                            <a href="<?php echo generate_whatsapp_order_link($lottery['name']); ?>"
                                class="whatsapp-order-btn block bg-accentYellow hover:bg-yellow-500 text-primaryBlue text-center font-bold py-2 px-4 rounded-full mt-auto text-sm"
                                data-lottery="<?php echo htmlspecialchars($lottery['name']); ?>">
                                <i class="fab fa-whatsapp mr-1"></i> ඇණවුම් කරන්න
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg p-6 text-center">
                <p class="text-gray-600">දැනට පෙන්වීමට ජනප්‍රිය ලොතරැයි නොමැත.</p>
            </div>
        <?php endif; ?>
        <div class="text-center mt-8">
            <a href="order_page.php" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full inline-block transition-transform transform hover:scale-105">
                සියලුම ලොතරැයි බලන්න
            </a>
        </div>
    </div>
</section>

<section class="mb-12 py-10">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-primaryBlue mb-8 text-center">ලොතරැයි ඇණවුම් කරන්නේ කෙසේද?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <div class="w-16 h-16 mx-auto bg-primaryBlue rounded-full flex items-center justify-center text-white text-3xl mb-4">
                    <i class="fas fa-mouse-pointer"></i>
                </div>
                <h3 class="text-lg font-bold text-primaryBlue mb-2">1. ලොතරැයිය තෝරන්න</h3>
                <p class="text-gray-600 text-sm">අපගේ වෙබ් අඩවියේ ඇති ලොතරැයි වලින් ඔබට අවශ්‍ය එක තෝරා "ඇණවුම් කරන්න" ක්ලික් කරන්න.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <div class="w-16 h-16 mx-auto bg-primaryGreen rounded-full flex items-center justify-center text-white text-3xl mb-4">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h3 class="text-lg font-bold text-primaryGreen mb-2">2. WhatsApp හරහා දන්වන්න</h3>
                <p class="text-gray-600 text-sm">ඔබව අපගේ WhatsApp වෙත යොමු කෙරේ. ඔබට අවශ්‍ය ලොතරැයිපත් ගණන සහ විස්තර එහිදී ලබා දෙන්න.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <div class="w-16 h-16 mx-auto bg-accentYellow rounded-full flex items-center justify-center text-primaryBlue text-3xl mb-4">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="text-lg font-bold text-primaryBlue mb-2">3. නිවසටම ගෙන්වාගන්න</h3>
                <p class="text-gray-600 text-sm">ඔබේ ඇණවුම තහවුරු කළ පසු, අපි ඔබේ ලොතරැයිපත ඔබගේ නිවසටම ගෙනැවිත් දෙන්නෙමු.</p>
            </div>
        </div>
    </div>
</section>

<section class="mb-12 bg-gray-50 py-10 rounded-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-primaryBlue">නවතම ලොතරැයි පුවත්</h2>
            <a href="news.php" class="text-primaryGreen hover:text-primaryBlue font-medium flex items-center text-sm md:text-base">
                සියලුම පුවත් <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <?php if ($db_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md text-center" role="alert">
                <p>පුවත් ලබා ගැනීමට නොහැකි විය.</p>
            </div>
        <?php elseif (!empty($latest_news)): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($latest_news as $news_item): // Renamed $news to $news_item to avoid conflict if $news was used elsewhere 
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden news-card h-full flex flex-col">
                        <a href="news_article.php?id=<?php echo $news_item['id']; ?>" class="block">
                            <?php if (!empty($news_item['image_path'])): ?>
                                <div class="h-48 overflow-hidden">
                                    <img data-src="<?php echo htmlspecialchars($news_item['image_path']); ?>" src="/lotterylk/assets/images/placeholder-news.png" alt="<?php echo htmlspecialchars($news_item['title']); ?>" class="w-full h-full object-cover lazy">
                                </div>
                            <?php else: ?>
                                <div class="h-48 bg-gradient-to-r from-primaryBlue to-primaryGreen flex items-center justify-center">
                                    <i class="fas fa-newspaper text-5xl text-white opacity-25"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-2 text-xs text-gray-500">
                                <span><?php echo date('Y-m-d', strtotime($news_item['published_date'])); ?></span>
                                <?php if (!empty($news_item['author_name'])): ?>
                                    <span class="text-primaryGreen"><?php echo htmlspecialchars($news_item['author_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-lg font-bold text-primaryBlue mb-2">
                                <a href="news_article.php?id=<?php echo $news_item['id']; ?>" class="hover:underline">
                                    <?php echo htmlspecialchars($news_item['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 flex-grow"><?php echo shorten_text(htmlspecialchars(strip_tags($news_item['content'])), 100); ?></p>
                            <a href="news_article.php?id=<?php echo $news_item['id']; ?>" class="text-primaryGreen hover:text-primaryBlue font-medium text-sm mt-auto self-start">
                                තවත් කියවන්න <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">දැනට පෙන්වීමට පුවත් නොමැත.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="mb-12 bg-primaryBlue text-white py-12 rounded-lg shadow-xl">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-2/3 mb-8 md:mb-0 md:pr-10 text-center md:text-left">
                <h2 class="text-3xl font-bold mb-4 text-accentYellow">අප ගැන</h2>
                <p class="mb-4 leading-relaxed">
                    "lotteryLK" යනු ශ්‍රී ලාංකික ලොතරැයි භාවිතා කරන්නන් සඳහා තොරතුරු සැපයීම සහ ලොතරැයි ඇණවුම් කිරීම පහසු කරවීම අරමුණු කරගත් විශ්වාසනීය වෙබ් අඩවියකි.
                </p>
                <p class="mb-6 leading-relaxed">
                    අපි ජාතික ලොතරැයි මණ්ඩලය (NLB) සහ සංවර්ධන ලොතරැයි මණ්ඩලය (DLB) විසින් නිකුත් කරන සියලුම ලොතරැයි පිළිබඳ යාවත්කාලීන තොරතුරු සපයන අතර, WhatsApp හරහා ලොතරැයිපත් ඇණවුම් කිරීමේ පහසුකම ද සලසන්නෙමු.
                </p>
                <a href="about.php" class="bg-white text-primaryBlue hover:bg-gray-200 font-bold py-3 px-6 rounded-full inline-block transition-transform transform hover:scale-105">
                    වැඩි විස්තර දැනගන්න
                </a>
            </div>
            <div class="md:w-1/3 flex justify-center">
                <img src="/lotterylk/assets/images/logo.png" alt="lotteryLK Trust" class="w-40 h-40 rounded-full shadow-2xl border-4 border-accentYellow">
            </div>
        </div>
    </div>
</section>

<section class="mb-12 py-10">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-primaryBlue mb-2">අපව සම්බන්ධ කර ගන්න</h2>
                <p class="text-gray-600">ඔබට යම් ප්‍රශ්නයක් හෝ යෝජනාවක් තිබේ නම්, අප හා සම්බන්ධ වන්න!</p>
            </div>
            <form action="contact.php" method="post" class="max-w-2xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="name_contact" class="block text-gray-700 mb-1 text-sm font-medium">නම</label>
                        <input type="text" id="name_contact" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                    </div>
                    <div>
                        <label for="email_contact" class="block text-gray-700 mb-1 text-sm font-medium">ඊමේල්</label>
                        <input type="email" id="email_contact" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label for="message_contact" class="block text-gray-700 mb-1 text-sm font-medium">පණිවිඩය</label>
                    <textarea id="message_contact" name="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-transform transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i> පණිවිඩය යවන්න
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>