<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Get all lotteries
$lotteries = [];
if (isset($pdo) && $db_connected) {
    try {
        // Get NLB lotteries
        $nlb_lotteries = get_all_lotteries($pdo, 'NLB');
        
        // Get DLB lotteries
        $dlb_lotteries = get_all_lotteries($pdo, 'DLB');
        
        // Combined all lotteries but keeping them separated by type
        $lotteries = [
            'NLB' => $nlb_lotteries,
            'DLB' => $dlb_lotteries
        ];
    } catch (PDOException $e) {
        error_log("Database error in order_page.php: " . $e->getMessage(), 0);
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Page Title Section -->
<section class="bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">ලොතරැයි ඇණවුම් කරන්න</h1>
        <p class="mt-2">WhatsApp ඔස්සේ ඔබට අවශ්‍ය ලොතරැයි පහසුවෙන් ඇණවුම් කරන්න. අපි ඔබේ දොරකඩටම ලොතරැයිපත ගෙනවිත් දෙන්නෙමු.</p>
    </div>
</section>

<!-- How to Order Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">ලොතරැයි ඇණවුම් කරන්නේ කෙසේද?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-primaryBlue rounded-full flex items-center justify-center text-white text-2xl mb-4">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <h3 class="text-lg font-bold text-primaryBlue mb-2">1. ඔබට අවශ්‍ය ලොතරැයිය තෝරන්න</h3>
                    <p class="text-gray-600">පහත ලැයිස්තුවෙන් ඔබට අවශ්‍ය ලොතරැයිය තෝරාගෙන "ඇණවුම් කරන්න" ක්ලික් කරන්න.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-primaryGreen rounded-full flex items-center justify-center text-white text-2xl mb-4">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3 class="text-lg font-bold text-primaryGreen mb-2">2. WhatsApp හරහා සම්බන්ධ වන්න</h3>
                    <p class="text-gray-600">ඔබ ක්ලික් කළ විට, ඔබව අපගේ WhatsApp චැට් එකට යොමු කෙරේ. විස්තර ස්වයංක්‍රීයව පිරවෙනු ඇත.</p>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                    <div class="w-16 h-16 mx-auto bg-accentYellow rounded-full flex items-center justify-center text-primaryBlue text-2xl mb-4">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="text-lg font-bold text-primaryBlue mb-2">3. ඔබේ ඇණවුම තහවුරු කරන්න</h3>
                    <p class="text-gray-600">ඔබේ ලිපිනය සහ ගෙවීම් විස්තර ලබා දෙන්න. අපි ඔබේ ලොතරැයිපත ඔබ වෙත ලබා දෙන්නෙමු.</p>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-gray-100 rounded-lg">
                <p class="text-gray-700 text-sm">
                    <i class="fas fa-info-circle text-primaryBlue mr-2"></i>
                    <strong>සැලකිය යුතුයි:</strong> ලොතරැයි ඇණවුම් කිරීමේ අවම ප්‍රමාණයක් නොමැත. කොපමණ ප්‍රමාණයක් අවශ්‍යද යන්න WhatsApp ඔස්සේ සාකච්ඡා කරගත හැකිය. ගෙවීම් භාණ්ඩ ලැබීමේදී කළ හැකිය.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Order Lotteries Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <!-- NLB Lotteries -->
        <?php if (!empty($lotteries['NLB'])): ?>
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 flex items-center">
                <span class="bg-primaryBlue text-white rounded-lg px-3 py-1 text-sm mr-3">NLB</span>
                ජාතික ලොතරැයි මණ්ඩලය
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($lotteries['NLB'] as $lottery): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                    <div class="bg-primaryBlue text-white py-4 px-6">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($lottery['name']); ?></h3>
                        <p class="text-sm opacity-75"><?php echo htmlspecialchars($lottery['draw_schedule']); ?></p>
                    </div>
                    
                    <div class="p-6 flex-grow flex flex-col">
                        <p class="text-gray-600 mb-6 flex-grow"><?php echo htmlspecialchars($lottery['description']); ?></p>
                        
                        <a href="<?php echo generate_whatsapp_order_link($lottery['name']); ?>" class="whatsapp-order-btn bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-4 rounded-full text-center flex items-center justify-center" data-lottery="<?php echo htmlspecialchars($lottery['name']); ?>">
                            <i class="fab fa-whatsapp mr-2"></i> ඇණවුම් කරන්න
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- DLB Lotteries -->
        <?php if (!empty($lotteries['DLB'])): ?>
        <div>
            <h2 class="text-2xl font-bold text-primaryGreen mb-6 flex items-center">
                <span class="bg-primaryGreen text-white rounded-lg px-3 py-1 text-sm mr-3">DLB</span>
                සංවර්ධන ලොතරැයි මණ්ඩලය
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($lotteries['DLB'] as $lottery): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden h-full flex flex-col">
                    <div class="bg-primaryGreen text-white py-4 px-6">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($lottery['name']); ?></h3>
                        <p class="text-sm opacity-75"><?php echo htmlspecialchars($lottery['draw_schedule']); ?></p>
                    </div>
                    
                    <div class="p-6 flex-grow flex flex-col">
                        <p class="text-gray-600 mb-6 flex-grow"><?php echo htmlspecialchars($lottery['description']); ?></p>
                        
                        <a href="<?php echo generate_whatsapp_order_link($lottery['name']); ?>" class="whatsapp-order-btn bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-4 rounded-full text-center flex items-center justify-center" data-lottery="<?php echo htmlspecialchars($lottery['name']); ?>">
                            <i class="fab fa-whatsapp mr-2"></i> ඇණවුම් කරන්න
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (empty($lotteries['NLB']) && empty($lotteries['DLB'])): ?>
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-600 mb-4">ලොතරැයි ලැයිස්තුව ලබා ගැනීමට අපොහොසත් විය. කරුණාකර පසුව නැවත උත්සාහ කරන්න.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Custom Order Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-2/3 mb-6 md:mb-0 md:pr-8">
                    <h2 class="text-2xl font-bold text-primaryBlue mb-4">විශේෂ ඇණවුමක්?</h2>
                    <p class="text-gray-600 mb-6">
                        ඔබට විශේෂ අංකයක් සහිත ලොතරැයියක් අවශ්‍යද? ඔබට ලොතරැයි කිහිපයක් එකවර අවශ්‍යද? ඕනෑම අවශ්‍යතාවයක් සඳහා අප හා සම්බන්ධ වන්න. අපි ඔබට උදව් කරන්නට සූදානම්!
                    </p>
                    <a href="https://wa.me/+94771234567" class="bg-primaryGreen hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full inline-block">
                        <i class="fab fa-whatsapp mr-2"></i> අප හා සම්බන්ධ වන්න
                    </a>
                </div>
                <div class="md:w-1/3 flex justify-center">
                    <div class="relative">
                        <div class="w-48 h-48 rounded-full bg-primaryBlue opacity-10 absolute top-0 left-0"></div>
                        <div class="w-48 h-48 rounded-full bg-primaryGreen opacity-10 absolute top-4 left-4"></div>
                        <div class="w-48 h-48 rounded-full bg-accentYellow opacity-10 absolute top-8 left-8"></div>
                        <div class="w-48 h-48 rounded-full flex items-center justify-center bg-white shadow-lg relative z-10">
                            <i class="fas fa-ticket-alt text-8xl text-primaryBlue opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQs Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">නිතර අසන ප්‍රශ්න</h2>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="space-y-4" x-data="{selected: null}">
                <!-- FAQ Item 1 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 1 ? selected = 1 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ලොතරැයිපත් ලබා දීම සඳහා ගාස්තුවක් අය කරනවාද?</h3>
                        <i class="fas" :class="selected == 1 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 1" x-transition>
                        <p class="text-gray-600">
                            නැත, අප ඔබගෙන් කිසිදු අමතර ගාස්තුවක් අය නොකරන්නෙමු. ඔබ ගෙවන්නේ ලොතරැයිපතේ සාමාන්‍ය මිල පමණි. ඔබේ දොරකඩට ලොතරැයිපත් ගෙන ඒම සඳහා කිසිදු අමතර ගාස්තුවක් නැත.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 2 ? selected = 2 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ලොතරැයි ඇණවුම් කිරීමේ අවම ප්‍රමාණයක් තිබේද?</h3>
                        <i class="fas" :class="selected == 2 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 2" x-transition>
                        <p class="text-gray-600">
                            නැත, ඔබට එක් ලොතරැයිපතක් පමණක් ඇණවුම් කිරීමටද හැකිය. කෙසේ වෙතත්, ප්‍රවාහන පිරිවැය සලකා බැලීමේදී, ලොතරැයිපත් කිහිපයක් ඇණවුම් කිරීම වඩාත් කාර්යක්ෂම වනු ඇත.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 3 ? selected = 3 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ඇණවුම් සඳහා ගෙවීම් කරන්නේ කෙසේද?</h3>
                        <i class="fas" :class="selected == 3 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 3" x-transition>
                        <p class="text-gray-600">
                            ඔබට ලොතරැයිපත ලැබුණු පසු මුදල් ගෙවීමේ විකල්පය ඇත (භාණ්ඩ ලැබීමේදී ගෙවීම - COD). නැතහොත්, ඔබට මුදල් ඇතුළු විවිධ විද්‍යුත් ගෙවීම් ක්‍රම හරහා කලින් ගෙවීම් කළ හැකිය. විස්තර WhatsApp හරහා සාකච්ඡා කළ හැකිය.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 4 ? selected = 4 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">මගේ ඇණවුම ලැබෙන්නේ කවදාද?</h3>
                        <i class="fas" :class="selected == 4 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 4" x-transition>
                        <p class="text-gray-600">
                            ඔබගේ භූගෝලීය පිහිටීම මත පදනම්ව, ඔබගේ ඇණවුම සාමාන්‍යයෙන් දින 1-3 ක් ඇතුළත ලැබෙනු ඇත. කොළඹ සහ ප්‍රධාන නගර ආසන්නයේ ස්ථාන සඳහා, අපට බොහෝ විට එදිනම බෙදාහැරීම් සැපයිය හැකිය.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div>
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 5 ? selected = 5 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">මම ලොතරැයියෙන් දිනුම් ලැබුවහොත් කුමක් වේද?</h3>
                        <i class="fas" :class="selected == 5 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 5" x-transition>
                        <p class="text-gray-600">
                            ඔබ දිනුම් ලැබුවහොත්, ඔබට එය සෘජුවම ලොතරැයි මණ්ඩලයේ කාර්යාලයට ගෙන ගොස් ඔබගේ ත්‍යාගය ලබා ගත හැකිය. රුපියල් 100,000 ට වැඩි ත්‍යාග සඳහා, අවශ්‍ය ලේඛන (NIC ආදිය) සමඟ ප්‍රධාන ලොතරැයි මණ්ඩල කාර්යාලයට යා යුතුය. අපගේ කණ්ඩායම ද ඔබට ක්‍රියාවලිය සම්බන්ධයෙන් උපදෙස් ලබා දීමට කැමැත්තෙන් සිටිනු ඇත.
                        </p>
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
