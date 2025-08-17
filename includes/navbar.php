<?php
include_once dirname(__DIR__) . '/includes/session_manager.php';
?>
<nav class=" sm:px-[10%] px-[2%] sm:py-2 py-5 items-center  flex shadow-b shadow-sm">
    <h1 class="sm:text-4xl text-2xl title-font">
        LAREA DASHBOARD
        <?php if (isLoggedIn()): ?>
            <span class="text-lg text-gray-600 ml-2">(<?= getRoleName(getUserLevel()) ?>)</span>
        <?php endif; ?>
    </h1>

    <div class="ml-auto flex items-center space-x-4">
        <?php if (isLoggedIn()): ?>
            <span class="text-sm text-gray-600">
                Welcome, <?= htmlspecialchars(getUserProfile()['first_name'] ?? 'User') ?>
            </span>
        <?php endif; ?>
        <a href="/LAREA/includes/logout.php"
            class="border-2 rounded-full p-2 hover:bg-black hover:text-white hover:border-none transition ease-in-out">
            <i class="fa-solid fa-sign-out-alt"></i>
        </a>
    </div>
</nav>