<?php

/**
 * User Manager
 *
 * Requires config.php, auth.php!
 * @TODO work with exclusive file handle
 * @global array $ne_config_info
 *
 */
class UserMgmt {

    private $_user_data_file;
    private $_user_default_array;

    /**
     * Constructor <br/>
     * every time on construct tests settings file for new format
     * @global array $ne_config_info
     */
    public function __construct() {
        global $ne_config_info;
        $this->_user_data_file = $ne_config_info['user_data_file_path'];
        $this->_user_default_array = get_ne_user_params_simple();
        $this->checkForNewFormat();
    }

    /**
     * Get array of Users with all parameters
     * @param string $format [Optional = 'json'] what format, supports json
     * @return mixed array of users, json string, or NULL if no users found
     */
    public function GetUsers($format = 'json') {
        if (!file_exists($this->_user_data_file)) {
            touch($this->_user_data_file); // create file if not exists
            return null;
        }
        $jss = file_get_contents($this->_user_data_file);
        if (strlen($jss) == 0) {
            return null;
        }

        if (strtolower($format) == 'json') {
            return $jss;
        } else {
            return json_decode(stripslashes($jss), true);
        }

    }

    /**
     * Get data of only one user $user_name in $format
     * @param string $user_name username
     * @param string $format [Optional = 'json'] what format, supports json
     * @return mixed If user not found NULL, else user data in array format or json format
     */
    public function GetUser($user_name, $format = 'json') {
        $users = $this->GetUsers('array');
        if (!is_null($users)) {
            foreach ($users as $udata) {
                if (strcmp($udata['user_name'], $user_name) == 0) {
                    if (strtolower($format) == 'json') {
                        return json_encode($udata);
                    } else {
                        return $udata;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Add new user
     * @param string $user_name
     * @param array $user_params User settings array.
     * @return boolean success
     */
    public function AddUser($user_name, $user_params) {
        //if user exists, false
        if(!is_null($this->GetUser($user_name))){
            return false;
        }

        $users = $this->GetUsers('array');
        //if no users yet, create empty array
        if (is_null($users)) {
            $users = array();
        }

        $userArray = $this->_user_default_array;
        $this->fillByArray($userArray, $user_params);
        $userArray['user_name'] = $user_name;
        $userArray['erstellungsdatum'] = time();
        if (strcmp($user_name, NavTools::getServerAdmin()) == 0) { //admin name
            $userArray['rolle'] = "1000";
        }
        array_push($users, $userArray);
        $jss_to_write = json_encode($users);
        return file_put_contents($this->_user_data_file, $jss_to_write);
    }


    /**
     * Macht Loginprozedur fuer benutzer $user_name
     * @param string $user_name username
     * @param string $passwd user password, md5 hash!
     * @return int 0 login failed, 1 login OK, -1 abgelaufen
     */
    public function Login($user_name, $passwd) {
        /** @var array $udata */
        $udata = $this->GetUser($user_name, 'array');
        if (!is_null($udata)) {
            if (strcmp($udata['password_hash'], $passwd) == 0) {
                if (intval($udata['ablaufdatum']) > time() || strcmp($udata['ablaufdatum'], "") == 0 || $udata['ablaufdatum'] == 0) {
                    //temporaer? Einige fehlerhafte eintraege reparieren..
                    if ($udata['rolle'] == "") {
                        $this->UpdateUser($user_name, Array("rolle" => "100"));
                    }
                    //---------
                    return 1;
                } else { //faster
                    return -1; //abgelaufen
                }
            }
        }

        return 0;
    }

    /**
     * Updates information about user $user_name
     * @param string $user_name Username
     * @param mixed $arg2 Can be array of user settings.<br/>
     * Or password. If password then sets $new_permission as user permissions parameter
     * @param string $new_permission [Optional = null] new user permissions. Needed only if $arg2 is string password
     * @return bool FALSE if no user found, otherwise TRUE
     */
    public function UpdateUser($user_name, $arg2, $new_permission = null) {

        if(is_null($this->GetUser($user_name))){
            return false;
        }

        $users = $this->GetUsers('array');

        for ($i = 0; $i < count($users); $i++) {
            if (strcmp($users[$i]['user_name'], $user_name) == 0) {

                //overloading arg1 = username, arg2 = user data array
                if (is_array($arg2) && func_num_args() == 2) {

                    $this->fillByArray($users[$i], $arg2);

                //arg1 = user_name, arg2 pw, arg3 permissions, for deprecated funcs?
                } elseif (func_num_args() == 3 && is_string($arg2) && is_string($new_permission)) {

                    $new_pwd = $arg2;
                    if ($new_pwd != '') {
                        $users[$i]['password_hash'] = $new_pwd;
                    }
                    $users[$i]['permission'] = $new_permission;

                }

                break; //user index in array found
            }

        }

        return file_put_contents($this->_user_data_file, json_encode($users));

    }

    /**
     * Remove user $user_name
     * @param string $user_name
     * @return bool Success
     */
    public function RemoveUser($user_name) {
        if(is_null($this->GetUser($user_name))){
            return false;
        }

        $users = $this->GetUsers('array');
        for ($i = 0; $i < count($users); $i++) {
            if (strcmp($users[$i]['user_name'], $user_name) == 0) {
                array_splice($users, $i, 1);
                break;
            }
        }

        return file_put_contents($this->_user_data_file, json_encode($users));
    }

    /**
     * Test if user $user_name has permission to path $file_path
     * @param string $user_name username
     * @param string $file_path file path
     * @return bool TRUE if has permission
     */
    public function UserHasPermission($user_name, $file_path) {

        $user_permissions = $this->GetPermission($user_name);
        return $this->checkPermission($file_path, $user_permissions);
    }

    /**
     * Tests if $file_path is in $permissions
     * @param string $file_path file path to test
     * @param string $permissions string with permissions
     * @return bool True if in string
     */
    private function checkPermission($file_path, $permissions) {
        if ($permissions == '/') { // can do everything
            return true;
        }

        $permissionsArray = explode('|', $permissions);
        foreach ($permissionsArray as $perm) {
            if (substr($perm, -1) == '/') { // has permission of a dir

                //permission folder must be at beginning
                if (strpos($file_path, $perm) == 0) {
                    return true;
                } else {
                    continue;
                }

            } else { // has permission only of a certain file

                if ($perm == $file_path) {
                    return true;
                } else {
                    continue;
                }

            }
        }

        return false;

    }

    public function ChangePermissionPath($user_name, $old_path, $new_path) {
        // ...
    }

    /**
     * Get user permission string
     * @param string $user_name Username
     * @return string User permissions
     */
    public function GetPermission($user_name) {
        $user = $this->GetUser($user_name, "array");

        if (!is_null($user)) {
            if ($user['rolle']=="1000") {
                return "/";
            }
            return $user['permission'];
        } else {
            return 'NO_SUCH_USER';
        }
    }


    /**
     * pruefen ob dem benuzer $user erlaubt ist die $menuId von $menu mit vordefinierten $rollen zu zugreifen
     *
     * @param int $menuId <p>
     * menu id to check
     * </p>
     * @param string $user <p>
     * user name to check
     * </p>
     * @param string $menu_value_to_check <p> [optional = 'id']
     * what menu value (of array) will be checked, id? link?
     * </p>
     * @param array $menu <p> [optional]
     * menu values with id, role requirement, name etc..
     * </p>
     * @param array $rollen <p> [optional]
     * User roles values(e.g 'Admin' => 1000)
     * </p>
     * @return bool allowed or not
     */
    public function isAllowAccessMenu($menuId, $user, $menu_value_to_check = 'id', $menu=null, $rollen=null ) {

        //fast check if admin..
        if(strcmp($user, NavTools::getServerAdmin())==0){
            return true;
        }

        //falls keine menu oder rollen übergeben wurde, dann die aus dem config.php holen. <require config.php !>
        if($menu === null){$menu = NavTools::ifsetor($GLOBALS['ne_menu']);}
        if($rollen === null){$rollen = NavTools::ifsetor($GLOBALS['ne_user_roles']);}

        // die werte muessen bestimmt werden
        if($menu !== null && is_array($menu) && $rollen !== null && $menuId !== null){
            //erforderliche rolle herausfinden
            $need_role = null;
            foreach ($menu as $menuItem) {
                //pruefn ob array die werte hat
                if (!isset($menuItem[$menu_value_to_check])
                 || !isset($menuItem['role'])) {
                    continue;
                }
                //vergleichen, falls gleich, rollen beschreibung gefunden
                if ($menuItem[$menu_value_to_check] == $menuId) {
                    $need_role = $menuItem['role'];
                    break;
                }
            }

            if (is_null($need_role)){//falls nichts gefunden
                return false;
            }elseif ($need_role == 'public' || $need_role == '0') { //falls public immer erlauben
                return true;
            }elseif($user === null){ //falls kein user und kein public, nicht erlauben
                return false;
            }else{
                $need_value = NavTools::ifsetor($rollen[$need_role]['value']);
            }

            $user_data = $this->getUser($user, 'array');
            if (isset($user_data['rolle']) && $user_data['rolle'] >= $need_value) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }


    /**
     * Tests if current user has minimal access level $access
     *
     * @global array $ne_user_roles
     * @global string $g_current_user_name
     * @param mixed $access<p>
     * minimal rights level needed to have access.<br>
     * can be role name (string), or role level (numeric)<br>
     * in case $access is wrong argument, false will be returned
     * </p>
     * @param string $userName [optional]
     * @return bool
     */
    public function hasAccessLevel($access, $userName = null) {
        global $ne_user_roles, $g_current_user_name;

        //if no $useName passed, try to get it from global variable
        if (is_null($userName)) {
            if (!is_null($g_current_user_name)) {
                $userName = $g_current_user_name;
            } else {
                return false;
            }
        }

        if (!is_numeric($access)) {
            $needAccessLvl = NavTools::ifsetor($ne_user_roles[$access]['value']);
        } else {
            $needAccessLvl = intval($access);
        }

        //if nothing found return false
        if (is_null($needAccessLvl) || !is_numeric($needAccessLvl)) {
            return false;
        }

        $user_data = $this->GetUser($userName, 'array');
        if (isset($user_data['rolle']) && $user_data['rolle'] >= $needAccessLvl) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * Test if user has access to the php file
     * Requires config.php for $ne_user_public_php, $ne_config_info
     * @uses auth.php for $g_current_user_name
     *
     * @global string $g_current_user_name
     * @global array $ne_config_info
     *
     * @param string $phpPath Full or relative to NavEditor/ path to the php file
     * @param string $userName [Optional] Username to test. If null try to get global set variable $g_current_user_name
     * @return bool true if access allowed, false otherwise
     */
    public function isAllowAccessPHP($phpPath, $userName = null){
        //TODO ausnahmen bei jedem User, pruefen.
        global $g_current_user_name, $ne_config_info;

        //cut root path if found
        $phpPath = str_replace($ne_config_info['app_path_without_host'], '', $phpPath);
        //if public, ok
        if(in_array($phpPath, $ne_config_info['public_php_files'])){
            return true;
        }

        //try to get username
        if(is_null($userName)){
            $userName = $g_current_user_name;
            if(is_null($g_current_user_name)){
                return false;
            }
        }

        $needRole = NavTools::ifsetor($ne_config_info['php_file_permissions'][$phpPath]);
        //if no role found or not enough access-lvl of the current user, decline
        if (is_null($needRole) || !$this->hasAccessLevel($needRole, $userName)) {
            return false;
        }

        return true;
    }


    /**
     * Save login time to user info
     * @param string $user Username
     */
    public function saveLoginTime($user) {
        if (isset($user)) {
            $this->UpdateUser($user, Array('letzter_login' => time()));
        }
    }


    private function fillByArray(&$arrayOriginal, $arrayInfo) {
        foreach ($arrayOriginal as $key => $value) {
            if (isset($arrayInfo[$key])) {
                $arrayOriginal[$key] = $arrayInfo[$key];
            }
        }
        return $arrayOriginal;
    }

    /**
     * test if user data is in old format, and changes to new if needed
     */
    private function checkForNewFormat() {
        if (!file_exists($this->_user_data_file)) {
            touch($this->_user_data_file); // create file if not exists
        } else {
            $jss = file_get_contents($this->_user_data_file);
            $test_jss = json_decode(stripslashes($jss), true);
            if (!is_null($test_jss[0])) {
                //test only first user (admin) -> checkAll ? +stability, -performance
                if ($this->arrayDiff($this->_user_default_array, $test_jss[0])) {
                    $new_jss = $this->modifyToNewFormat($test_jss);
                    file_put_contents($this->_user_data_file, $new_jss) OR die('Could not write to file: ' . $this->_user_data_file); // rewrite!
                }
            }
        }
    }

    /**
     * Modify user settings to new format
     * @param array $paramsAll new parameters
     * @return string jss encoded new parameters
     */
    private function modifyToNewFormat($paramsAll) {
        if (!isset($paramsAll) || $paramsAll == null) {
            return null;
        }
        $copy_jss = array();
        foreach ($paramsAll as $params) {
            if (!$this->arrayDiff($this->_user_default_array, $params)) { //check if array keys are different
                $retVal = $params;
            } else {
                //only for update from old version where /index.shtml means directory acces
                if (!$this->arrayDiff($params, Array("user_name"=>Array(),"password_hash"=>Array(),"permission"=>Array()))){
                    $params['permission'] = $this->modifyPermissions($params['permission']);
                    //set rolle and mail for admin
                    if($params["user_name"] == NavTools::getServerAdmin()){
                        $params["rolle"] ="1000";
                        $params["email"] = $_SERVER['SERVER_ADMIN'];
                    }else{
                    //set other users as just "user"
                        $params["rolle"] ="100";
                    }
                }

                $arrayTemplate = $this->_user_default_array;
                //copy all possible from old array
                $this->fillByArray($arrayTemplate, $params);
                // add full permission, in case it is missing
                if (!isset($params['permission'])) {
                    $arrayTemplate['permission'] = '/';
                }


                $retVal = $arrayTemplate;
            }
            array_push($copy_jss, $retVal);
        }
        $new_jss = json_encode($copy_jss);
        return $new_jss;
    }


    /**
     * macht einige tests fuer die Benutzerdaten, und repariert was sicher fehlerhaft ist
     * @param String $userName <p>
     * Benutzername
     * </p>
     * @todo create it! :)
     */
    private function repairUser($userName){
        //TODO
    }


    //modify permissions to new format ('/index.shtml' => '/')
    private function modifyPermissions($permissions){
        if(!is_null($permissions)){
            return str_replace("/index.shtml", "/", $permissions);
        }
        return '';
    }

    private function arrayDiff($array1, $array2) {
        if (count(array_diff_assoc(array_keys($array1), array_keys($array2))) == 0 && count($array1) == count($array2)) {
            return false;
        } else {
            return true;
        }
    }

}