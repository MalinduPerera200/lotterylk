</div>
            
            <!-- Footer -->
            <footer class="bg-white p-4 text-center text-gray-600 text-sm mt-auto border-t">
                <p>&copy; <?php echo date('Y'); ?> lotteryLK Admin Panel. සියලුම හිමිකම් ඇවිරිණි.</p>
            </footer>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script>
        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 768 && !sidebar.contains(event.target) && event.target !== sidebarToggle) {
                    sidebar.classList.remove('open');
                }
            });
        }
        
        // User menu toggle (desktop)
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });
            
            // Close user menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
        
        // Mobile user menu toggle
        const mobileUserMenuButton = document.getElementById('mobile-user-menu-button');
        const mobileUserMenu = document.getElementById('mobile-user-menu');
        
        if (mobileUserMenuButton && mobileUserMenu) {
            mobileUserMenuButton.addEventListener('click', function() {
                mobileUserMenu.classList.toggle('hidden');
            });
            
            // Close mobile user menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileUserMenuButton.contains(event.target) && !mobileUserMenu.contains(event.target)) {
                    mobileUserMenu.classList.add('hidden');
                }
            });
        }
        
        // Flash message auto-hide
        const flashMessage = document.querySelector('.flash-message');
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.classList.add('opacity-0');
                setTimeout(function() {
                    flashMessage.remove();
                }, 300);
            }, 5000);
        }
        
        // Confirm delete
        const deleteButtons = document.querySelectorAll('.delete-button');
        if (deleteButtons) {
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('ඔබට මෙය මකා දැමීමට අවශ්‍ය බව විශ්වාසද?')) {
                        e.preventDefault();
                    }
                });
            });
        }
    </script>
</body>
</html>
