<?php
    try {
        require('../php/database.php'); // base de données = database = db
        
        if(isset($_GET['before'])) {
            if(!is_numeric($_GET['before'])) {
                throw new Exception('ERROR_INVALID_BEFORE');
            }
            $sql = "SELECT messages.message, messages.timestamp, messages.id, comptes.public_id, comptes.username, comptes.display_name, comptes.verified FROM messages INNER JOIN comptes ON messages.authorid = comptes.id WHERE messages.id < :before ORDER BY messages.id DESC LIMIT 150";
            $req = $db->prepare($sql);
            $req->execute(['before' => intval($_GET['before'])]);
        } else {
            $sql = "SELECT messages.message, messages.timestamp, messages.id, comptes.public_id, comptes.username, comptes.display_name, comptes.verified FROM messages INNER JOIN comptes ON messages.authorid = comptes.id ORDER BY messages.id DESC LIMIT 150";
            $req = $db->prepare($sql);
            $req->execute();
        }
        
        
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo '{"res": '.json_encode($res).', "error": null}';
    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo '{"res": null, "error": "'.$e->getMessage().'"}';
    }
?>