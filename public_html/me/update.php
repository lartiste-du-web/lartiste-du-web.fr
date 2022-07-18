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
        
        function updateAccount() {
            global $CONNECTED;
            global $db;
            if(!$CONNECTED) {
                header('Content-Type: application/json');
                echo '{"res": null, "error": "ERROR_NOT_CONNECTED"}';
                return;
            }
            $changes = array();
            $reset_token = false;
            $username = $_SESSION['account']['username'];
            if(isset($_POST['username'])) {
                $username = $_POST['username'];
                if(!preg_match("/^[a-z\-\_0-9]{1,32}$/", $username)) {
                    header('Content-Type: application/json');
                    echo '{"res": null, "error": "ERROR_INVALID_USERNAME"}';
                    return;
                }
                array_push($changes, 'USERNAME');
                $reset_token = true;
            }
            $display_name = $_SESSION['account']['display_name'];
            if(isset($_POST['display_name'])) {
                $display_name = $_POST['display_name'];
                if(!preg_match("/^.{1,32}$/", $display_name)) {
                    header('Content-Type: application/json');
                    echo '{"res": null, "error": "ERROR_INVALID_DISPLAY_NAME"}';
                    return;
                }
                array_push($changes, 'DISPLAY_NAME');
            }
            $email = $_SESSION['account']['email'];
            if(isset($_POST['email'])) {
                $email = $_POST['email'];
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    header('Content-Type: application/json');
                    echo '{"res": null, "error": "ERROR_INVALID_EMAIL"}';
                    return;
                }
                $reset_token = true;
            }
            $password = $_SESSION['account']['password'];
            if(isset($_POST['new_password'])) {
                $password = generateAccountPasswordHash($_POST['new_password']);
                $reset_token = true;
                array_push($changes, 'PASSWORD');
            }
            
            if($reset_token) {
                if(!isset($_POST['last_password'])) {
                    header('Content-Type: application/json');
                    echo '{"res": null, "error": "ERROR_MISSING_AUTHENTICATION"}';
                    return;
                }
                if(!password_verify($_POST['last_password'], $_SESSION['account']['password'])) {
                    header('Content-Type: application/json');
                    echo '{"res": null, "error": "ERROR_WRONG_PASSWORD"}';
                    return;
                }
            }
            
            $ntoken = $_SESSION['account']['token'];
            if($reset_token) {
                $ntoken = bin2hex(random_bytes(64));
                array_push($changes, 'TOKEN');
            }
            
            if(isset($_POST['profile_picture'])) {
                $imgData = str_replace(' ','+',$_POST['profile_picture']);
                $imgData =  substr($imgData,strpos($imgData,",")+1);
                $imgData = base64_decode($imgData);
                // Path where the image is going to be saved
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/img/profiles/' . $_SESSION['account_pid'] . '.png';
                // Write $imgData into the image file
                $file = fopen($filePath, 'w');
                fwrite($file, $imgData);
                fclose($file);
                
                array_push($changes, 'PROFILE_PICTURE');
            }

            $sql = "UPDATE comptes SET username = :username, display_name = :display_name, email = :email, password = :password, token = :token WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['username' => $username, 'display_name' => $display_name, 'email' => $email, 'password' => $password, 'token' => $ntoken, 'id' => $_SESSION['account']['id']]);
            
            header('Content-Type: application/json');
            echo '{"res": '.json_encode($changes).', "error": null}';
        }
        updateAccount();
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        echo '{"res": null, "error": "'.$e->getMessage().'"}';
    }
?>