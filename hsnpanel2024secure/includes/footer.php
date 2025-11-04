            </div>
        </div>
    </div>
    
    <script>
        // Mobile sidebar toggle
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebar = document.querySelector('.admin-sidebar');
        
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !mobileSidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            });
        }
        
        // Confirm delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Bu öğeyi silmek istediğinizden emin misiniz?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

