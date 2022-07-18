<?php
    session_start();
    
    try {
        require('../php/database.php'); // base de données = database = db
        require('../php/accounts.php'); // fonctions pour les comptes
        
        prepareLogin();
        
        $CONNECTED = false;
        if(isset($_SESSION['connected']) && $_SESSION['connected']) {
            $CONNECTED = true;
        }
        
        if(!$CONNECTED) {
            header('Location: /login');
        }
    } catch(PDOException $e) {
        echo 'Erreur: '.$e->getMessage();
        die("Une erreur est survenue ! Si vous obtenez cette erreur à plusieurs reprises, merci de nous la signaler. Nous sommes désolés pour la gêne occasionnée.");
    }
?>
<html>
    <head>
        <title>L'artiste Du Web | Profil</title>
        <meta charset="UTF-8">
        <script src="../commun.js"></script>
        <link rel="icon" href="../logo.png">
        
        <!-- CSS -->
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.1/themes/smoothness/jquery-ui.css">
        
        <!-- Script -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.1/jquery-ui.min.js"></script>
        
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/css/menu.css">
        <link rel="stylesheet" href="/css/profiles.css">
        <link rel="icon" href="logo.png">
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
                        <a href="https://lartiste-du-web.fr"><li>Accueil</li></a>
                        <a href="https://lartiste-du-web.fr/tchat"><li>Tchat en direct</li></a>
                        <a href="https://lartiste-du-web.fr/creations"><li>Mes créations</li></a>
                        <a href="https://lartiste-du-web.fr/contact"><li>Me contacter</li></a>
                        <a href="https://lartiste-du-web.fr/commandes"><li>Passer commande</li></a>
                        <a href="https://lartiste-du-web.fr/don"><li>Faire un don</li></a>
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
        <div id="main-info" class="little-profile" data-user-pid="<?= $_SESSION['account_pid'] ?>">
            <div class="round-image">
                <img src="/img/profiles/<?= $_SESSION['account_pid'] ?>.png" onerror="this.src = '/img/default_user.png'; this.onerror = null;" id="profile-picture" draggable="false" />
            </div>
            <div class="user-info">
                <div class="display-name" id="display-name-editable" contenteditable><?= $_SESSION['account']['display_name'] ?></div>
                <div class="username" id="username-editable" contenteditable><?= $_SESSION['account']['username'] ?></div>
            </div>
            <?php
                if($_SESSION['account']['verified'] == 1) {
                    ?>
                        <div class="verified">
                            <img src="/img/account_verified.svg" title="Compte officiel" draggable="false" />
                        </div>
                    <?php
                }
            ?>
        </div>
        <div id="image-crop-container">
            <h2>Modifier la photo de profil</h2>
            <div>
                <img draggable="false"></img>
                <div id="crop-section"></div>
            </div>
            <div id="crop-size-control">
                <button id="cancel-crop">Annuler</button>
                <div id="div-minus">
                    <div></div>
                </div>
                <div id="div-plus">
                    <div></div>
                    <div></div>
                </div>
                <button id="confirm-crop">Valider</button>
            </div>
        </div>
        <div id="unsaved-change-info">
            <span>Attention, vos modifications ne sont pas enregistrées !</span>
            <div>
                <input type="password" placeholder="Entrez votre mot de passe pour enregistrer les modifications" />
                <button>Enregistrer</button>
            </div>
        </div>
        <script src="/js/drag_elem.js"></script>
        <script src="/commun.js"></script>
        <script src="script.js"></script>
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