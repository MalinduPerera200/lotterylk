<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Initialize variables
$success_message = '';
$error_message = '';
$form_data = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'subject' => '',
    'message' => ''
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $form_data = [
        'name' => isset($_POST['name']) ? sanitize_input($_POST['name']) : '',
        'email' => isset($_POST['email']) ? sanitize_input($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '',
        'subject' => isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '',
        'message' => isset($_POST['message']) ? sanitize_input($_POST['message']) : ''
    ];
    
    // Simple validation
    $errors = [];
    
    if (empty($form_data['name'])) {
        $errors[] = 'නම අවශ්‍යයි.';
    }
    
    if (empty($form_data['email'])) {
        $errors[] = 'ඊමේල් ලිපිනය අවශ්‍යයි.';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'වලංගු ඊමේල් ලිපිනයක් ඇතුළත් කරන්න.';
    }
    
    if (empty($form_data['subject'])) {
        $errors[] = 'මාතෘකාව අවශ්‍යයි.';
    }
    
    if (empty($form_data['message'])) {
        $errors[] = 'පණිවිඩය අවශ්‍යයි.';
    }
    
    // If no errors, proceed with form submission
    if (empty($errors)) {
        // In a real application, you would save to database or send email
        // For now, we'll just simulate success
        $success_message = 'ඔබගේ පණිවිඩය සාර්ථකව යවන ලදී. අපි ඉක්මනින් ඔබව සම්බන්ධ කර ගන්නෙමු.';
        
        // Reset form data
        $form_data = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'subject' => '',
            'message' => ''
        ];
    } else {
        // Join errors with line breaks
        $error_message = implode('<br>', $errors);
    }
}

// Include header
include 'includes/header.php';
?>

<!-- Page Title Section -->
<section class="bg-primaryBlue text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">අප හා සම්බන්ධ වන්න</h1>
        <p class="mt-2">ඔබට යම් ප්‍රශ්නයක් හෝ යෝජනාවක් තිබේ නම්, අපි ඔබට උදව් කිරීමට කැමැත්තෙන් සිටිමු.</p>
    </div>
</section>

<!-- Contact Information Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-primaryBlue mb-6">පණිවිඩයක් යවන්න</h2>
                    
                    <?php if (!empty($success_message)): ?>
                    <!-- Success Message -->
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <p class="font-bold">සාර්ථකයි!</p>
                        <p><?php echo $success_message; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                    <!-- Error Message -->
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <p class="font-bold">දෝෂයක් ඇති විය!</p>
                        <p><?php echo $error_message; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <form action="contact.php" method="post">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-gray-700 mb-2">නම <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($form_data['name']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                            </div>
                            <div>
                                <label for="email" class="block text-gray-700 mb-2">ඊමේල් <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="phone" class="block text-gray-700 mb-2">දුරකථන අංකය</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                        </div>
                        
                        <div class="mb-6">
                            <label for="subject" class="block text-gray-700 mb-2">මාතෘකාව <span class="text-red-500">*</span></label>
                            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($form_data['subject']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                        </div>
                        
                        <div class="mb-6">
                            <label for="message" class="block text-gray-700 mb-2">පණිවිඩය <span class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="5" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required><?php echo htmlspecialchars($form_data['message']); ?></textarea>
                        </div>
                        
                        <div>
                            <button type="submit" class="bg-primaryGreen hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full">
                                <i class="fas fa-paper-plane mr-2"></i> යවන්න
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-primaryBlue mb-6">සම්බන්ධතා තොරතුරු</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-primaryBlue rounded-full p-3 text-white mr-4">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700 mb-1">ලිපිනය</h3>
                                <p class="text-gray-600">
                                    123, අබේරත්න මාවත,<br>
                                    කොළඹ 07,<br>
                                    ශ්‍රී ලංකාව
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-primaryGreen rounded-full p-3 text-white mr-4">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700 mb-1">දුරකථන</h3>
                                <p class="text-gray-600">
                                    +94 77 123 4567<br>
                                    +94 11 234 5678
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-accentYellow rounded-full p-3 text-primaryBlue mr-4">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700 mb-1">ඊමේල්</h3>
                                <p class="text-gray-600">
                                    info@lotterylk.com<br>
                                    support@lotterylk.com
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-[#25D366] rounded-full p-3 text-white mr-4">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-700 mb-1">WhatsApp</h3>
                                <p class="text-gray-600">
                                    +94 77 123 4567
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Links -->
                    <div class="mt-8">
                        <h3 class="font-bold text-gray-700 mb-3">අපව සොයන්න</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-[#3b5998] text-white p-3 rounded-full hover:opacity-90">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-[#1da1f2] text-white p-3 rounded-full hover:opacity-90">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="bg-[#ea4c89] text-white p-3 rounded-full hover:opacity-90">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="bg-[#25D366] text-white p-3 rounded-full hover:opacity-90">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Business Hours -->
                    <div class="mt-8">
                        <h3 class="font-bold text-gray-700 mb-3">කාර්යාල වේලාව</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex justify-between">
                                <span>සඳුදා - සිකුරාදා:</span>
                                <span>පෙ.ව. 9:00 - ප.ව. 5:00</span>
                            </li>
                            <li class="flex justify-between">
                                <span>සෙනසුරාදා:</span>
                                <span>පෙ.ව. 9:00 - ප.ව. 1:00</span>
                            </li>
                            <li class="flex justify-between">
                                <span>ඉරිදා & රජයේ නිවාඩු:</span>
                                <span>වසා ඇත</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h2 class="text-2xl font-bold text-primaryBlue mb-4 text-center">අපගේ පිහිටීම</h2>
            <div class="h-96 w-full rounded-lg overflow-hidden">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31686.89300559496!2d79.84857542424037!3d6.911624559479171!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259695b5a1741%3A0xe0ae06a65f36573a!2sColombo%2007%2C%20Colombo!5e0!3m2!1sen!2slk!4v1675427432294!5m2!1sen!2slk" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">නිතර අසන ප්‍රශ්න</h2>
            
            <div class="space-y-4" x-data="{selected: null}">
                <!-- FAQ Item 1 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 1 ? selected = 1 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ලොතරැයි ඇණවුම් ගැන ප්‍රශ්න සඳහා ඔබව සම්බන්ධ කරගත හැක්කේ කෙසේද?</h3>
                        <i class="fas" :class="selected == 1 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 1" x-transition>
                        <p class="text-gray-600">
                            ඔබට WhatsApp හරහා අපව සම්බන්ධ කරගත හැකිය. ඊට අමතරව, ඔබට ඉහත පෝරමය හරහා විමසීමක් යැවිය හැකිය හෝ අපගේ දුරකථන අංකයට ඇමතිය හැකිය. අපි ශ්‍රී ලංකාවේ ඕනෑම ස්ථානයකට ලොතරැයිපත් බෙදාහැරීම් සිදු කරමු.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="border-b border-gray-200 pb-4">
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 2 ? selected = 2 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ලොතරැයිපත් ලැබීමෙන් පසු ගෙවීම් කළ හැකිද?</h3>
                        <i class="fas" :class="selected == 2 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 2" x-transition>
                        <p class="text-gray-600">
                            ඔව්, අපි භාණ්ඩ ලැබීමේදී ගෙවීම (COD) සේවාවක් ලබා දෙන්නෙමු. ලොතරැයිපත් ලැබීමෙන් පසු ඔබට මුදල් ගෙවීමට හැකිය. මීට අමතරව, ඔබට මුදල් ඇතුළු විවිධ විද්‍යුත් ගෙවීම් ක්‍රම හරහා කලින් ගෙවීම් කළ හැකිය.
                        </p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div>
                    <button class="flex items-center justify-between w-full text-left" @click="selected !== 3 ? selected = 3 : selected = null">
                        <h3 class="text-lg font-medium text-primaryBlue">ඔබ නිල ලොතරැයි ආයතනයක්ද?</h3>
                        <i class="fas" :class="selected == 3 ? 'fa-chevron-up text-primaryGreen' : 'fa-chevron-down text-gray-500'"></i>
                    </button>
                    <div class="mt-3" x-show="selected == 3" x-transition>
                        <p class="text-gray-600">
                            අපි ජාතික ලොතරැයි මණ්ඩලය (NLB) සහ සංවර්ධන ලොතරැයි මණ්ඩලය (DLB) විසින් නිකුත් කරන නිල ලොතරැයි අලෙවි කරන්නෙමු. අපි ශ්‍රී ලංකාවේ ලොතරැයි අලෙවි කිරීමේ බලපත්‍රලාභී ආයතනයක් වන අතර, අපගේ සේවාව නම් ඔබට පහසුවෙන් ලොතරැයිපත් ඇණවුම් කර ඔබගේ නිවසට ලබා ගැනීමට අවස්ථාව ලබා දීමයි.
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
