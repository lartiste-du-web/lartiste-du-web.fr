<?php
    session_start();
    
    try {
        require('php/database.php'); // base de données = database = db
        require('php/accounts.php'); // fonctions pour les comptes
        
        $login_status = prepareLogin();
        
        $CONNECTED = false;
        if(isset($_SESSION['connected']) && $_SESSION['connected']) {
            $CONNECTED = true;
        }
    } catch(PDOException $e) {
        echo 'Erreur: '.$e->getMessage();
        die("Une erreur est survenue ! Si vous obtenez cette erreur à plusieurs reprises, merci de nous la signaler. Nous sommes désolés pour la gêne occasionnée.");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/commun.css">
        <link rel="stylesheet" href="/css/menu.css">
        <link rel="stylesheet" href="index.css">
        <link rel="icon" href="logo.png">
        <link>
        <title>L'artiste Du Web | Accueil</title>
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
                                    <a href="/logout">
                                        <li>Déconnexion</li>
                                    </a>
                                    <a href="/me">
                                        <li>Mon profil</li>
                                    </a>
                                <?php
                            } else {
                                ?>
                                    <a href="/register">
                                        <li>S'inscrire</li>
                                    </a>
                                    <a href="/login">
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
        <h1 class="bienvenue">Bienvenue sur lartiste-du-web.fr</h1>
        <h5 class="sous-bienvenue">Graphiste gratuit | Web Designer</h5>
        <img class="img-1" draggable="false" src="bureau 1.png">
        
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