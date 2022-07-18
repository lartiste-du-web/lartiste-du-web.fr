<?php
    session_start();
    if(isset($_COOKIE['account'])) {
        unset($_COOKIE['account']);
        setcookie('account', '', time() - 3600000, '/', 'lartiste-du-web.fr');
    }
    session_unset();
    session_destroy();
    header('Location: /login');
?>