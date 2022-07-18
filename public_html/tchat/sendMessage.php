<?php
    
    session_start();
    
    try {
        require('../php/database.php'); // base de données = database = db
        require('../php/accounts.php'); // fonctions pour les comptes
        
        $connect_res = prepareLogin();
        
        $CONNECTED = false;
        if(isset($_SESSION['connected']) && $_SESSION['connected']) {
            $CONNECTED = true;
        }
        
        function sendMessage() {
            global $CONNECTED;
            global $db;
            global $connect_res;
            if(!$CONNECTED) {
                header('Content-Type: application/json');
                echo '{"success": false, "error": "'.$connect_res.'"}';
                return "ERROR_NO_AUTHENTICATION";
            }
            if(isset($_POST['message']) && !empty($_POST['message']) && $_POST['message'] != '' && !ctype_space($_POST['message'])) {
                $message = htmlspecialchars($_POST['message']);
                
                $len = strlen($message);
                
                if($len > 1024) {
                    header('Content-Type: application/json');
                    echo '{"success": false, "error": "ERROR_MESSAGE_TOO_LONG"}';
                    return "ERROR_MESSAGE_TOO_LONG";
                }
                if($len <= 0) {
                    header('Content-Type: application/json');
                    echo '{"success": false, "error": "ERROR_MESSAGE_EMPTY"}';
                    "ERROR_MESSAGE_EMPTY";
                }
                
                $sql = "INSERT INTO messages (message, authorid) VALUES (:message, :authorid)";
                $req = $db->prepare($sql);
                $req->execute(['message' => $message, 'authorid' => $_SESSION['account_id']]);
                
                header('Content-Type: application/json');
                echo '{"success": true, "error": null}';
                return "SUCCESS";
            } else {
                header('Content-Type: application/json');
                echo '{"success": false, "error": "ERROR_MESSAGE_EMPTY"}';
                return "ERROR_MESSAGE_EMPTY";
            }
        }
        sendMessage();
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo '{"success": false, "error": "ERROR_UNKNOWN"}';
        return "ERROR_UNKNOWN";
    }
    
?>