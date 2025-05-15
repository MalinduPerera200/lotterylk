<?php
// Include database connection and functions
include 'includes/db_connect.php';
include 'includes/functions.php';

// Include header
include 'includes/header.php';
?>

<!-- Page Title Section -->
<section class="bg-primaryBlue text-white py-8 mb-8 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold">අප ගැන</h1>
        <p class="mt-2">lotteryLK ගැන සහ අපගේ ලොතරැයි තොරතුරු සහ ඇණවුම් සේවාව ගැන තවත් දැනගන්න.</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-6 md:mb-0 md:pr-8">
                    <h2 class="text-2xl font-bold text-primaryBlue mb-4">අපගේ කතාව</h2>
                    <p class="text-gray-600 mb-4">
                        "lotteryLK" 2020 වසරේදී ආරම්භ කරන ලද ශ්‍රී ලාංකික ලොතරැයි භාවිතා කරන්නන් සඳහා තොරතුරු සැපයීම සහ ලොතරැයි ඇණවුම් කිරීම පහසු කරවීම අරමුණු කරගත් වෙබ් අඩවියකි.
                    </p>
                    <p class="text-gray-600 mb-4">
                        අපගේ වෙබ් අඩවිය මුලදී ලොතරැයි ප්‍රතිඵල පළ කිරීම සඳහා පමණක් ආරම්භ කරන ලද අතර, පසුව එය ලොතරැයි පුවත්, දිනුම් රටා විශ්ලේෂණය සහ WhatsApp හරහා ලොතරැයි ඇණවුම් කිරීමේ පහසුකම් ඇතුළු පරිපූර්ණ සේවාවක් බවට වර්ධනය විය.
                    </p>
                    <p class="text-gray-600">
                        අප ශ්‍රී ලංකාවේ ජාතික ලොතරැයි මණ්ඩලය (NLB) සහ සංවර්ධන ලොතරැයි මණ්ඩලය (DLB) විසින් නිකුත් කරන සියලුම නිල ලොතරැයි ගැන තොරතුරු සපයන අතර, පාරිභෝගිකයින්ට නවතම ප්‍රතිඵල පහසුවෙන් පරීක්ෂා කිරීමට සහ ඔවුන්ට අවශ්‍ය ලොතරැයි ඇණවුම් කිරීමට පහසුකම් සපයන්නෙමු.
                    </p>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="bg-gray-200 rounded-lg overflow-hidden shadow-lg w-full max-w-md">
                        <img src="/lotterylk/assets/images/about-story.jpg" alt="lotteryLK Story" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission Section -->
<section class="mb-12 bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-12 rounded-lg">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-6">අපගේ මෙහෙවර</h2>
        <p class="text-lg mb-10 max-w-3xl mx-auto">
            ශ්‍රී ලංකාවේ ලොතරැයි පාරිභෝගිකයින්ට ඉහළම තත්ත්වයේ තොරතුරු සැපයීම සහ සරල, සුරක්ෂිත අත්දැකීමක් හරහා ලොතරැයි ඇණවුම් කිරීමේ ක්‍රියාවලිය පහසු කිරීම.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white text-primaryBlue rounded-lg p-6 shadow-lg">
                <div class="w-16 h-16 bg-primaryBlue text-white rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">යාවත්කාලීන ප්‍රතිඵල</h3>
                <p>නවතම ලොතරැයි ප්‍රතිඵල විශ්වාසනීය ලෙස සහ ඉක්මනින් පළ කරන්නෙමු.</p>
            </div>
            
            <div class="bg-white text-primaryBlue rounded-lg p-6 shadow-lg">
                <div class="w-16 h-16 bg-primaryGreen text-white rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">අදාළ පුවත්</h3>
                <p>ශ්‍රී ලංකාවේ ලොතරැයි කර්මාන්තය පිළිබඳ නවතම තොරතුරු ලබා දෙන්නෙමු.</p>
            </div>
            
            <div class="bg-white text-primaryBlue rounded-lg p-6 shadow-lg">
                <div class="w-16 h-16 bg-accentYellow text-primaryBlue rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">පහසු ඇණවුම් කිරීම</h3>
                <p>WhatsApp හරහා ලොතරැයිපත් ඇණවුම් කිරීමේ පහසු සහ ආරක්ෂිත ක්‍රමයක් ලබා දෙන්නෙමු.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">අපගේ කණ්ඩායම</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="text-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 bg-gray-200">
                        <img src="/lotterylk/assets/images/team-1.jpg" alt="Saman Perera" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-bold text-primaryBlue">සමන් පෙරේරා</h3>
                    <p class="text-primaryGreen mb-2">ප්‍රධාන විධායක නිලධාරී</p>
                    <p class="text-gray-600 mb-4">
                        ලොතරැයි කර්මාන්තයේ වසර 15+ අත්දැකීම් සහිතව, සමන් lotteryLK වෙබ් අඩවිය ආරම්භ කළේ ලොතරැයි ලබා ගැනීම සියලු ශ්‍රී ලාංකිකයින්ට පහසු කිරීමට ය.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Team Member 2 -->
                <div class="text-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 bg-gray-200">
                        <img src="/lotterylk/assets/images/team-2.jpg" alt="Kumari Silva" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-bold text-primaryBlue">කුමාරි සිල්වා</h3>
                    <p class="text-primaryGreen mb-2">ප්‍රතිඵල සහ පුවත් කළමනාකරු</p>
                    <p class="text-gray-600 mb-4">
                        කුමාරි සියලුම ලොතරැයි ප්‍රතිඵල හා පුවත් යාවත්කාලීන කිරීම් භාරව සිටින අතර, එමඟින් අපගේ තොරතුරු සැමවිටම නිවැරදි බව සහතික කරයි.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Team Member 3 -->
                <div class="text-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4 bg-gray-200">
                        <img src="/lotterylk/assets/images/team-3.jpg" alt="Nuwan Bandara" class="w-full h-full object-cover" />
                    </div>
                    <h3 class="text-lg font-bold text-primaryBlue">නුවන් බණ්ඩාර</h3>
                    <p class="text-primaryGreen mb-2">ඇණවුම් සහ බෙදාහැරීම් කළමනාකරු</p>
                    <p class="text-gray-600 mb-4">
                        නුවන් අපගේ ලොතරැයි ඇණවුම් සහ බෙදාහැරීම් ක්‍රියාවලිය භාරව සිටින අතර, සියලුම පාරිභෝගිකයින් තම ලොතරැයිපත් නියමිත වේලාවට ලබා ගන්නා බව සහතික කරයි.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-primaryBlue">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">ඇයි අප තෝරා ගත යුත්තේ?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Feature 1 -->
                <div class="flex items-start">
                    <div class="bg-primaryBlue text-white rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">විශ්වාසනීයත්වය</h3>
                        <p class="text-gray-600">
                            අපි නිවැරදි සහ යාවත්කාලීන ලොතරැයි ප්‍රතිඵල සැපයීමට කැපවී සිටින්නෙමු. අපගේ ප්‍රතිඵල සැමවිටම නිල ලොතරැයි මණ්ඩලවල තොරතුරු සමඟ පරීක්ෂා කර තහවුරු කරනු ලැබේ.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="flex items-start">
                    <div class="bg-primaryGreen text-white rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">ඉක්මන් බෙදාහැරීම</h3>
                        <p class="text-gray-600">
                            ඔබගේ ඇණවුම් කළ ලොතරැයිපත් ඉක්මනින් බෙදා හැරීමට අපි කැපවී සිටින්නෙමු. ශ්‍රී ලංකාවේ බොහෝ ප්‍රදේශවල දින 1-3 ක් ඇතුළත බෙදාහැරීම් අපි සිදු කරන්නෙමු.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="flex items-start">
                    <div class="bg-accentYellow text-primaryBlue rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">භාවිතයට පහසු සේවාව</h3>
                        <p class="text-gray-600">
                            අපගේ WhatsApp ඇණවුම් ක්‍රමය සරල සහ පහසු වන අතර, ඕනෑම කෙනෙකුට ස්මාර්ට් ජංගම දුරකථනයක් භාවිතා කර ලොතරැයිපත් ඇණවුම් කිරීමට ඉඩ සලසයි.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 4 -->
                <div class="flex items-start">
                    <div class="bg-[#6c5ce7] text-white rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">ආරක්ෂාව සහ පෞද්ගලිකත්වය</h3>
                        <p class="text-gray-600">
                            ඔබගේ පෞද්ගලික තොරතුරු ආරක්ෂා කිරීමට අපි කැපවී සිටින්නෙමු. ඔබගේ දත්ත ආරක්ෂිතව තබා ගැනීමට සහ ඒවා තෙවන පාර්ශවයන් සමඟ බෙදා නොගැනීමට අපි ඇප කැප වී සිටින්නෙමු.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div class="flex items-start">
                    <div class="bg-[#e17055] text-white rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">මුදල් ආපසු ගෙවීමේ ප්‍රතිපත්තිය</h3>
                        <p class="text-gray-600">
                            ඔබට ලැබෙන ලොතරැයිපත් පිළිබඳව අතෘප්තිමත් නම්, අපි මුදල් ආපසු ගෙවීමේ ප්‍රතිපත්තියක් ලබා දෙන්නෙමු. ඔබගේ තෘප්තිය අපගේ ප්‍රමුඛතාවයයි.
                        </p>
                    </div>
                </div>
                
                <!-- Feature 6 -->
                <div class="flex items-start">
                    <div class="bg-[#00b894] text-white rounded-full p-3 mr-4 shrink-0">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue mb-2">උසස් පාරිභෝගික සේවාව</h3>
                        <p class="text-gray-600">
                            අපගේ කාර්යය මණ්ඩලය ඔබගේ ඕනෑම ප්‍රශ්නයකට හෝ ගැටළුවකට උදව් කිරීමට සූදානම්. WhatsApp, දුරකථන, හෝ විද්‍යුත් තැපෑල හරහා අපව සම්බන්ධ කරගත හැකිය.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Partners Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-primaryBlue mb-6 text-center">අපගේ හවුල්කරුවන්</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Partner 1 -->
                <div class="flex items-center p-4 border rounded-lg">
                    <div class="w-24 h-24 mr-6 flex items-center justify-center bg-gray-100 rounded-lg">
                        <img src="/lotterylk/assets/images/nlb-logo.png" alt="National Lotteries Board" class="max-w-full max-h-full p-2" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue">ජාතික ලොතරැයි මණ්ඩලය (NLB)</h3>
                        <p class="text-gray-600">
                            ශ්‍රී ලංකාවේ ප්‍රධාන ලොතරැයි අලෙවිකරු වන ජාතික ලොතරැයි මණ්ඩලය (NLB) මගින් නිකුත් කරන සියලුම ලොතරැයි අපි අලෙවි කරන්නෙමු.
                        </p>
                    </div>
                </div>
                
                <!-- Partner 2 -->
                <div class="flex items-center p-4 border rounded-lg">
                    <div class="w-24 h-24 mr-6 flex items-center justify-center bg-gray-100 rounded-lg">
                        <img src="/lotterylk/assets/images/dlb-logo.png" alt="Development Lotteries Board" class="max-w-full max-h-full p-2" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-primaryBlue">සංවර්ධන ලොතරැයි මණ්ඩලය (DLB)</h3>
                        <p class="text-gray-600">
                            සංවර්ධන ලොතරැයි මණ්ඩලය (DLB) මගින් නිකුත් කරන සියලුම ලොතරැයි අපි අලෙවි කරන්නෙමු, ඔබට එක් තැනකින් සියලුම ලොතරැයි ඇණවුම් කළ හැකිය.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-primaryBlue to-primaryGreen text-white py-10 rounded-lg">
            <h2 class="text-2xl font-bold mb-8 text-center">අපගේ පාරිභෝගිකයින් පවසන දේ</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4">
                <!-- Testimonial 1 -->
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                            <img src="/lotterylk/assets/images/testimonial-1.jpg" alt="Customer" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="font-bold text-primaryBlue">අනූෂා ගුණවර්ධන</h3>
                            <p class="text-sm text-gray-500">කොළඹ</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "lotteryLK මඟින් මම ලොතරැයිපත් ඇණවුම් කිරීම ඉතා පහසුයි. එය කාලය ඉතිරි කරන අතර, ඔවුන්ගේ WhatsApp ඇණවුම් ක්‍රමය ඉතා සරල හා විශ්වාසදායකයි. මම වසර 2ක් තිස්සේ ඔවුන්ගේ සේවාව භාවිතා කරන අතර අවසන් නොවන තෘප්තියක් ලබා ඇත."
                    </p>
                    <div class="mt-3 text-accentYellow">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                            <img src="/lotterylk/assets/images/testimonial-2.jpg" alt="Customer" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="font-bold text-primaryBlue">රවින්ද්‍ර පෙරේරා</h3>
                            <p class="text-sm text-gray-500">මහනුවර</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "මම මහනුවර ජීවත් වන අතර, නිතිපතා ලොතරැයිපත් ගැනීමට කොළඹට යා නොහැක. lotteryLK මගේ ජීවිතය ලෙහෙසි කර ඇත, ඔවුන් මගේ දොරකඩටම ලොතරැයිපත් ගෙනැවිත් දෙනවා. ඔවුන්ගේ ඉක්මන් බෙදාහැරීම් සේවාව ඉතා ප්‍රශංසනීයයි."
                    </p>
                    <div class="mt-3 text-accentYellow">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gray-200 overflow-hidden mr-4">
                            <img src="/lotterylk/assets/images/testimonial-3.jpg" alt="Customer" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <h3 class="font-bold text-primaryBlue">ෆාතිමා ජසීම්</h3>
                            <p class="text-sm text-gray-500">ගාල්ල</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "lotteryLK වෙබ් අඩවිය හරහා මම මගේ ලොතරැයි ප්‍රතිඵල නිතිපතා පරීක්ෂා කරනවා. ඔවුන්ගේ ප්‍රතිඵල යාවත්කාලීන කිරීම් ඉතා ඉක්මන් සහ නිවැරදියි. පසුගිය මාසයේ මම ලොතරැයියකින් දිනුම් ලැබූ අතර, ඔවුන් ඒ සම්බන්ධයෙන් මට මුදල් ලබා ගැනීමට උපදෙස් ලබා දුන්නා!"
                    </p>
                    <div class="mt-3 text-accentYellow">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="mb-12">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <h2 class="text-2xl font-bold text-primaryBlue mb-4">අද දිනයේ ඔබගේ ලොතරැයිපත ඇණවුම් කරන්න</h2>
            <p class="text-gray-600 mb-6 max-w-3xl mx-auto">
                WhatsApp හරහා ඔබට අවශ්‍ය ඕනෑම ලොතරැයියක් ඉතා පහසුවෙන් ඇණවුම් කළ හැකිය. අපි ඔබේ දොරකඩටම ලොතරැයිපත ගෙනවිත් දෙන්නෙමු. ඔබගේ වාසනාව අද දින අත්හදා බලන්න!
            </p>
            <a href="order_page.php" class="bg-accentYellow hover:bg-yellow-500 text-primaryBlue font-bold py-3 px-8 rounded-full inline-block whatsapp-btn">
                <i class="fab fa-whatsapp mr-2"></i> WhatsApp ඔස්සේ ඇණවුම් කරන්න
            </a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
