<?php
    
    if(!isset($db)) {
        throw "ERROR_NO_DATABASE";
    }
    
    $_SESSION['connected'] = false;
    
    function successLogin($account) {
        $_SESSION['expires'] = (new DateTime())->getTimestamp() + (30 * 60 * 1000);
        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_pid'] = $account['public_id'];
        $_SESSION['account'] = $account;
        $_SESSION['connected'] = true;
    }
    
    function generateAccountPasswordHash($pswd) {
        $options = ['memory_cost' => 4096, 'time_cost' => 12];
        return password_hash($pswd, PASSWORD_ARGON2I, $options);
    }
    
    function setRememberCookie() {
        if(!isset($_SESSION['account_pid'], $_SESSION['account']['token'])) {
            return "NOT_LOGGED_IN";
        }
        setcookie('account', strval($_SESSION['account_pid']).';'.$_SESSION['account']['token'], time() + (30 * 24 * 60 * 60), '/', 'lartiste-du-web.fr', true, true);
        return "SUCCESS_REMEMBER_COOKIE";
    }
    
    function createAccount($email, $username, $password) {
        global $db;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "ERROR_INVALID_EMAIL";
        }
        if(!preg_match("/^[a-z\-\_0-9]{1,32}$/", $username)) {
            return "ERROR_INVALID_USERNAME";
        }
        
        $sql = "SELECT * FROM comptes WHERE username = :username OR email = :email";
        $req = $db->prepare($sql);
        $req->execute(['username' => $username, 'email' => $email]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        if(count($res) != 0) {
            return "ERROR_USERNAME_OR_EMAIL_ALREADY_IN_USE";
        }
        
        $hash = generateAccountPasswordHash($password);
        
        $public_id = bin2hex(random_bytes(16));
        $token = bin2hex(random_bytes(64));
        
        $sql = "INSERT INTO comptes (email, username, display_name, password, public_id, token) VALUES (:email, :user, :display, :pswd, :pid, :token)";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email, 'user' => $username, 'display' => $username, 'pswd' => $hash, 'pid' => $public_id, 'token' => $token]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        return "SUCCESS_REGISTER";
    }
    
    function loginPIDAndToken($public_id, $token) {
        global $db;
        $sql = "SELECT * FROM comptes WHERE public_id = :pid";
        $req = $db->prepare($sql);
        $req->execute(['pid' => $public_id]);
        $res = $req->fetchAll();
        
        if(count($res) == 0) {
            return "ERROR_USER_NOT_FOUND";
        }
        $res = $res[0];
        
        if(!isset($res['token']) || $res['token'] == false || strlen($res['token']) != 128) {
            $token = bin2hex(random_bytes(64));
            $sql = "UPDATE comptes SET token = :token WHERE id = :id";
            $req = $db->prepare($sql);
            $req->execute(['token' => $token, 'id' => $res['id']]);
            return "ERROR_INVALID_TOKEN";
        }
        
        if($res['token'] != $token) {
            successLogin($res);
            return "SUCCESS_LOGIN";
        } else {
            return "ERROR_INVALID_TOKEN";
        }
    }
    
    function loginUserAndPassword($user, $pswd) {
        global $db;
        $sql = "SELECT * FROM comptes WHERE username = :user";
        $req = $db->prepare($sql);
        $req->execute(['user' => $user]);
        $res = $req->fetchAll();
        
        if(count($res) == 0) {
            return "ERROR_USER_NOT_FOUND";
        }
        $res = $res[0];
        
        if(password_verify($pswd, $res['password'])) {
            if(!isset($res['token']) || $res['token'] == false || strlen($res['token']) != 128) {
                $token = bin2hex(random_bytes(64));
                $sql = "UPDATE comptes SET token = :token WHERE id = :id";
                $req = $db->prepare($sql);
                $req->execute(['token' => $token, 'id' => $res['id']]);
            }
            successLogin($res);
            return "SUCCESS_LOGIN";
        } else {
            return "ERROR_INVALID_PASSWORD";
        }
    }
    
    function loginEmailAndPassword($email, $pswd) {
        global $db;
        $sql = "SELECT * FROM comptes WHERE email = :email";
        $req = $db->prepare($sql);
        $req->execute(['email' => $email]);
        $res = $req->fetchAll();
        
        if(count($res) == 0) {
            return "ERROR_USER_NOT_FOUND";
        }
        $res = $res[0];
        
        if(password_verify($pswd, $res['password'])) {
            if(!isset($res['token']) || $res['token'] == false || strlen($res['token']) != 128) {
                $token = bin2hex(random_bytes(64));
                $sql = "UPDATE comptes SET token = :token WHERE id = :id";
                $req = $db->prepare($sql);
                $req->execute(['token' => $token, 'id' => $res['id']]);
            }
            successLogin($res);
            return "SUCCESS_LOGIN";
        } else {
            return "ERROR_INVALID_PASSWORD";
        }
    }
    
    function prepareLogin() {
        global $db;
        $now = new DateTime();
        $timestamp = $now->getTimestamp();
        if(isset($_SESSION['account_id'], $_SESSION['expires']) && $timestamp < $_SESSION['expires']) {
            $sql = "SELECT * FROM comptes WHERE id = :id LIMIT 1";
            $req = $db->prepare($sql);
            $req->execute(['id' => $_SESSION['account_id']]);
            $res = $req->fetchAll();
            if(count($res) == 0) {
                return "ERROR_USER_NOT_FOUND"; // not found
            }
            $account = $res[0];
            if($account['token'] != $_SESSION['account']['token']) {
                return "ERROR_TOKEN_CHANGED";
            }
            successLogin($account);
            return "SUCCESS_LOGIN";
        }
        if(!isset($_COOKIE['account'])) {
            return "ERROR_NO_LOGIN_CREDENTIALS";
        }
        $cookie = explode(';', $_COOKIE['account']);
        $public_id = $cookie[0];
        $token = $cookie[1];
        return loginPIDAndToken($public_id, $token);
    }
?>