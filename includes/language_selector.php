<?php
$languages = [
    'en' => ['name' => 'English', 'flag' => '🇺🇸'],
    'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
    'es' => ['name' => 'Español', 'flag' => '🇪🇸']
];
?>

<div class="language-selector">
    <button class="lang-toggle" type="button">
        <?php echo $languages[$current_language]['flag']; ?>
        <span class="lang-name"><?php echo $languages[$current_language]['name']; ?></span>
        <span class="lang-arrow">▼</span>
    </button>
    <div class="lang-menu">
        <?php foreach($languages as $code => $lang): ?>
            <?php if($code !== $current_language): ?>
                <a href="<?php echo langUrl($code); ?>" class="lang-option">
                    <?php echo $lang['flag']; ?>
                    <span><?php echo $lang['name']; ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<style>
.language-selector {
    position: relative;
    display: inline-block;
}

.lang-toggle {
    background: none;
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.lang-toggle:hover {
    background: #f5f5f5;
    border-color: #4CAF50;
}

.lang-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 140px;
    z-index: 1000;
    display: none;
}

.language-selector:hover .lang-menu {
    display: block;
}

.lang-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    transition: background 0.2s ease;
}

.lang-option:hover {
    background: #f5f5f5;
}

.lang-name {
    display: none;
}

@media (min-width: 768px) {
    .lang-name {
        display: inline;
    }
}



/* Mobile sidebar language selector */
@media (max-width: 768px) {
    .sidebar-language .lang-name {
        display: inline;
    }
    
    .sidebar-language .language-selector:hover .lang-menu {
        display: none;
    }
    
    .sidebar-language .language-selector.active .lang-menu {
        display: block;
    }
}
</style>

