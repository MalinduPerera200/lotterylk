</main>

    <!-- Footer -->
    <footer class="bg-primaryBlue text-white">
        <!-- Footer Main -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About lotteryLK -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-accentYellow">lotteryLK</h3>
                    <p class="text-sm leading-relaxed mb-4">
                        "lotteryLK" යනු ශ්‍රී ලාංකික ලොතරැයි භාවිතා කරන්නන් සඳහා තොරතුරු සැපයීම සහ ලොතරැයි ඇණවුම් කිරීම පහසු කරවීම අරමුණු කරගත් වෙබ් අඩවියකි.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-accentYellow"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-accentYellow"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-accentYellow"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover:text-accentYellow"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-accentYellow">ඉක්මන් සබැඳි</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/lotterylk/index.php" class="hover:text-accentYellow">මුල් පිටුව</a></li>
                        <li><a href="/lotterylk/results.php" class="hover:text-accentYellow">ප්‍රතිඵල</a></li>
                        <li><a href="/lotterylk/news.php" class="hover:text-accentYellow">පුවත්</a></li>
                        <li><a href="/lotterylk/patterns.php" class="hover:text-accentYellow">දිනුම් රටා</a></li>
                        <li><a href="/lotterylk/order_page.php" class="hover:text-accentYellow">ලොතරැයි ඇණවුම්</a></li>
                        <li><a href="/lotterylk/about.php" class="hover:text-accentYellow">අප ගැන</a></li>
                        <li><a href="/lotterylk/contact.php" class="hover:text-accentYellow">සම්බන්ධ වන්න</a></li>
                    </ul>
                </div>
                
                <!-- Contact Us -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-accentYellow">අප අමතන්න</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-accentYellow"></i>
                            <span>123, අබේරත්න මාවත, කොළඹ 07, ශ්‍රී ලංකාව</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-accentYellow"></i>
                            <span>+94 77 123 4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-accentYellow"></i>
                            <span>info@lotterylk.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-whatsapp mr-3 text-accentYellow"></i>
                            <span>+94 77 123 4567</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="bg-[#072f4d] py-4">
            <div class="container mx-auto px-4 text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> lotteryLK. සියලුම හිමිකම් ඇවිරිණි.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="/lotterylk/assets/js/main.js"></script>
    
    <!-- Mobile menu toggle script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
    </script>
</body>
</html>
