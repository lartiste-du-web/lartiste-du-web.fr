<?php

    session_start();
    
    try {
        require('../php/database.php'); // base de données = database = db
        require('../php/accounts.php'); // fonctions pour les comptes
        
        $login_status = prepareLogin();
        
        if(isset($_SESSION['connected']) && $_SESSION['connected']) {
            header('Location: /');
        }
        
        function register() {
            if(!isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['password2'])) {
                return null;
            }
            $pass1 = $_POST['password'];
            $pass2 = $_POST['password2'];
            if($pass1 != $pass2) {
                return "ERROR_PASSWORD_NO_MATCH";
            }
            $username = $_POST['username'];
            $email = $_POST['email'];
            
            $res = createAccount($email, $username, $pass2);
            if(preg_match("/ERROR.*/", $res)) {
                return $res;
            }
            
            header('Location: /login');
        }
        $result = register();
    } catch(PDOException $e) {
        echo 'Erreur: '.$e->getMessage();
        die("");
    }
    $CONNECTED = false;
    if(isset($_SESSION['connected']) && $_SESSION['connected']) {
        $CONNECTED = true;
    }

?>
<html>
    <head>
        <title>L'artiste Du Web | Inscription</title>
        <link rel="icon" href="/logo.png">
        <script src="../commun.js"></script>
        
        <link rel="stylesheet" href="/css/commun.css">
        <link rel="stylesheet" href="/css/menu.css">
        <link rel="stylesheet" href="index.css">
        
        <title>L'artiste Du Web | Connexion</title>
        <script src="commun.js"></script>
        <script data-host="https://webanalysis.dev" data-dnt="false" src="https://webanalysis.dev/js/script.js" id="ZwSg9rf6GA" async defer></script>
        <!-- Primary Meta Tags -->
        <meta name="title" content="L'artiste Du Web">
        <meta name="description" content="Découvre toutes les créations de L'artiste Du Web depuis le début ! Et parle avec des gens qui sont dans le meme pays que toi dans un tchat en direct !">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://lartiste-du-web.fr">
        <meta property="og:title" content="L'artiste Du Web">
        <meta property="og:description" content="Contact L'artiste Du Web pour avoir ta propre photo de profil ou logo ! Et parle avec des gens qui sont dans le meme pays que toi dans un tchat en direct !">
        <meta property="og:image" content="https://i.ibb.co/CJRFHFr/logo.png">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="https://lartiste-du-web.fr">
        <meta property="twitter:title" content="L'artiste Du Web">
        <meta property="twitter:description" content="Contact L'artiste Du Web pour avoir ta propre photo de profil ou logo ! Et parle avec des gens qui sont dans le meme pays que toi dans un tchat en direct !">
        <meta property="twitter:image" content="https://i.ibb.co/CJRFHFr/logo.png">
    </head>
    <header>
        <div class="align">
            <h1 class="titre">L'artiste Du Web</h1>
            <nav role='navigation'>
                <div id="menuToggle">
                    <input type="checkbox" />
                    <span></span>
                    <span></span>
                    <span></span>
                    <ul id="menu">
                        <a href="/">
                            <li>Accueil</li>
                        </a>
                        <a href="/tchat">
                            <li>Tchat en direct</li>
                        </a>
                        <a href="/creations">
                            <li>Mes créations</li>
                        </a>
                        <a href="/contact">
                            <li>Me contacter</li>
                        </a>
                        <a href="/commandes">
                            <li>Passer commande</li>
                        </a>
                        <a href="/don">
                            <li>Faire un don</li>
                        </a>
                        <hr style="color: black; border: none; background: black; height: 3px;">
                        <?php
                            if($CONNECTED) {
                                ?>
                                    <a href="../logout">
                                        <li>Déconnexion</li>
                                    </a>
                                    <a href="../me">
                                        <li>Mon profile</li>
                                    </a>
                                <?php
                            } else {
                                ?>
                                    <a href="../register">
                                        <li>S'inscrire</li>
                                    </a>
                                    <a href="../login">
                                        <li>Se connecter</li>
                                    </a>
                                <?php
                            }
                        ?>
                    </ul>
                </div>
            </nav>
            <div class="container">
            </div>
        </div>
    </header>
    <body>
        <div class="border-form">
            <h1 class="inscription">Inscription</h1>
            <form class="form" method="POST" action="">
                <input class="input" placeholder="Pseudo" name="username" autocomplete="username" required>
                <div class="username-info"></div>
                
                <input class="input" placeholder="Mail" name="email" type="email" required>
                
                <input class="input" placeholder="Mot de passe" name="password" type="password" autocomplete="new-password" required>
                <div class="password-info"><div></div></div>
                
                <input class="input" placeholder="Vérification mot de passe" name="password2" type="password" autocomplete="new-password" required>
                <div class="confirm-info"></div>
                
                <button type="submit" class="submit">S'inscrire</button>
            </form>
            <?= $result ?>
            <center><a href="/login" class="compte-deja-cree">Vous avez déjà un compte ?</a></center>
        </div>
        <script src="https://lartiste-du-web.fr/register/script.js"></script>
        <script>
            console.log('Login status: ', "<?= $login_status ?>")
        </script>
    </body>
    <footer>
        <div class="contact">
            <span>Contact :</span>
            <br>
            <a target="blank" href="mailto:contact@lartiste-du-web.fr">--> Mail</a>
        </div>
        <div class="liens-utiles">
            <span>Liens utiles :</span>
            <br>
            <a href="https://lartiste-du-web.fr">--> Accueil</a>
            <br>
            <a href="https://lartiste-du-web.fr/creations">--> Mes créations</a>
            <br>
            <a href="https://lartiste-du-web.fr/tchat">--> Tchat en direct</a>
            <br>
            <a href="https://lartiste-du-web.fr/contact">--> Me contacter</a>
            <br>
            <a href="https://lartiste-du-web.fr/commandes">--> Passer commande</a>
            <br>
            <a href="https://lartiste-du-web.fr/don">--> Faire un don</a>
        </div>
        <div class="copyright">
            <center>
                <span>© Copyright 2022 - Toutes reproductions interdites - Développé par L'artiste Du Web</span>
            </center>
        </div>
        <br>
        <br>
    </footer>
</html>