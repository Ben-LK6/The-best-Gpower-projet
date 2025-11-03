<?php
// footer-scripts.php
?>
            </div>
        </main>
    </div>

    <script>
    // Menu hamburger mobile - DOIT ÊTRE DANS CHAQUE PAGE
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileMenuToggle && adminSidebar && sidebarOverlay) {
        mobileMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('sidebar-open');
            sidebarOverlay.classList.toggle('active');
        });

        sidebarOverlay.addEventListener('click', function() {
            adminSidebar.classList.remove('sidebar-open');
            sidebarOverlay.classList.remove('active');
        });
    }
    </script>
</body>
</html>