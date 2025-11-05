<?php
function getWhatsAppButton($number, $message = '', $size = 'normal') {
    $cleanNumber = preg_replace('/[^0-9]/', '', $number);
    $whatsappUrl = "https://wa.me/{$cleanNumber}";
    if ($message) {
        $whatsappUrl .= "?text=" . urlencode($message);
    }
    
    $sizeClass = $size === 'large' ? 'whatsapp-large' : ($size === 'small' ? 'whatsapp-small' : '');
    
    return '<a href="' . $whatsappUrl . '" class="whatsapp-icon-btn ' . $sizeClass . '" target="_blank" title="Contacter via WhatsApp">
                <img src="images/Whatsap.avif" alt="WhatsApp" class="whatsapp-img">
            </a>';
}

function getGmailButton($email, $subject = '', $size = 'normal') {
    $gmailUrl = "mailto:{$email}";
    if ($subject) {
        $gmailUrl .= "?subject=" . urlencode($subject);
    }
    
    $sizeClass = $size === 'large' ? 'gmail-large' : ($size === 'small' ? 'gmail-small' : '');
    
    return '<a href="' . $gmailUrl . '" class="gmail-icon-btn ' . $sizeClass . '" title="Contacter par email">
                <svg viewBox="0 0 24 24">
                    <path fill="#EA4335" d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-.904.732-1.636 1.636-1.636h.91L12 10.09l9.455-6.269h.909c.904 0 1.636.732 1.636 1.636z"/>
                </svg>
            </a>';
}
?>