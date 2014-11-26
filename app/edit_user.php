<?php
/**
 * php file to handle ajax requests, to manage Users.
 * ajax from user_manager.php
 */


require_once('../auth.php');



$oper = $_REQUEST['json_oper'];

if($oper == 'get_users') {
    $users = $g_UserMgmt->GetUsers('array');
            for($i = 0; $i< count($users); $i++){
                $users[$i]['password_hash'] = "";
            }
            echo(json_encode($users));
}

if($oper == 'create_user') {
    $new_user_name = NavTools::filterSymbols(Input::get_post('user'));//nicht erlaubte symbole filtern
    $params = json_decode(stripslashes(Input::get_post('params')), true);
    if(!$g_UserMgmt->AddUser($new_user_name, $params)) {
        echo('Fehlgeschlagen, vielleicht existiert der Benutzername bereits!');
    } else {
        echo('Benutzer ' . $new_user_name . ' erfolgreich hinzugefügt!');
    }
}

if($oper == 'update_user') {
            $user = Input::get_post('user');
            $paramArray = json_decode(stripslashes(Input::get_post('params')), true);
            $paramArray['user_name'] = NavTools::filterSymbols($paramArray['user_name']); //nicht erlaubte symbole in user_name filtern
            //remove pw from params if not set, or hash of empty string ""
            if(!isset($paramArray['password_hash']) || $paramArray['password_hash'] == "d41d8cd98f00b204e9800998ecf8427e"){
                unset($paramArray['password_hash']);
            }
            //remove not editable values
            $userArray = array_diff_key($paramArray, get_ne_user_params_not_editable());
            if(!$g_UserMgmt->UpdateUser($user, $userArray)) {
        echo('Aktualisierung fehlgeschlagen!');
    } else {
        echo('Benutzer: ' . $user . ' erfolgreich aktualisiert!');
    }
}

if($oper == 'remove_user') {
    $user_name = Input::get_post('user_name');
            if(NavTools::getServerAdmin() == $user_name){
                echo('Darf nicht den Admin löschen!'); //theoretisch sicherheitsluecke...
                return;
            }
    if(!$g_UserMgmt->RemoveUser($user_name)) {
        echo('Fehlgeschlagen!');
    } else {
        echo('Benutzer: ' . $user_name . ' erfolgreich gelöscht!');
    }
}


?>
