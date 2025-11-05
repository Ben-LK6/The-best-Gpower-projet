</main>
    <footer class="professional-footer">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <!-- Logo et Description -->
                    <div class="footer-brand">
                        <div class="footer-logo">
                            <img src="images/logo-gpower.png" alt="GPower Logo" class="footer-logo-img">
                            <h3>GPower</h3>
                        </div>
                        <p class="footer-description">
                            <?php echo t('footer_description'); ?>
                        </p>
                        <div class="footer-contact-icons">
                            <?php 
                            require_once 'whatsapp_helper.php';
                            echo getWhatsAppButton('22940870199', t('whatsapp_professional'), 'large');
                            echo getGmailButton('generatorpower60@gmail.com', t('whatsapp_professional') . ' GPower', 'large');
                            ?>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="footer-nav">
                        <h4><?php echo t('footer_navigation'); ?></h4>
                        <ul>
                            <li><a href="index.php"><?php echo t('nav_home'); ?></a></li>
                            <li><a href="products.php"><?php echo t('nav_products'); ?></a></li>
                            <li><a href="about.php"><?php echo t('nav_about'); ?></a></li>
                            <li><a href="contact.php"><?php echo t('nav_contact'); ?></a></li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div class="footer-services">
                        <h4><?php echo t('footer_services'); ?></h4>
                        <ul>
                            <li><?php echo t('contact_service_desc'); ?></li>
                            <li><?php echo t('contact_global_desc'); ?></li>
                            <li><?php echo t('contact_logistics_desc'); ?></li>
                            <li><?php echo t('whatsapp_professional'); ?></li>
                        </ul>
                    </div>

                    <!-- Business Info -->
                    <div class="footer-business">
                        <h4><?php echo t('footer_business_hours'); ?></h4>
                        <div class="business-hours">
                            <p><strong><?php echo t('footer_monday_friday'); ?></strong></p>
                            <p>8:00 AM - 6:00 PM GMT</p>
                            <p><strong><?php echo t('footer_response_time'); ?></strong></p>
                            <p><?php echo t('footer_whatsapp_instant'); ?></p>
                            <p><?php echo t('footer_email_24h'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p><?php echo t('footer_copyright'); ?></p>
                    <div class="footer-links">
                        <a href="#"><?php echo t('footer_privacy'); ?></a>
                        <a href="#"><?php echo t('footer_terms'); ?></a>
                        <a href="admin/login.php" class="admin-link">Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Lien vers le fichier footer.css -->
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/footer-pro.css">
</body>
</html>