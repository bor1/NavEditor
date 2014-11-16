<?php

/**
 * NavEditor3 Tools Class
 * Functions collection
 * @uses config.php - global variables
 */
class NavTools {

    /**
     * Wrapes a <script> tag around input
     * @param String $filename
     * @return String
     * the tag to place between <head> tags
     */
    public static function wrapScriptInclude($filename){
        return '<script type="text/javascript" src="' . $filename . '"></script>' . "\n";
    }
    
    /**
     * Wrapes a <style> tag around input
     * @param String $filename
     * @return String
     * the tag to place between <head> tags
     */
    public static function wrapStyleInclude($filename){
        return '<link rel="stylesheet" type="text/css" href="' . $filename . '">' . "\n";
    }
    
    /**
     * include js und css files in html. from JS and CSS directory
     * there is an option to add default includes set like jQuery files etc..
     *
     * @global array $ne_config_info
     * @param String $Filenames<p>
     * Filenames, single filename or array of strings.
     * Additional html string (parameter?) after comma.<br />
     * example includeHtml("folder/file.css, version=2", "some.js")<br />
     * output:<br /> <link rel="stylesheet"... /folder/file.css?version=2...<br />
     *         <script type="text/javascript" src=".../some.js"...
     * </p>
     * @return String like <link rel="stylesheet" ... / <script src=" ...
     * for each argument
     * <p>with no params "default" files will be loaded  </p>
     * <p>*html source will be full url to the file  </p>
     */
    public static function includeHtml($Filenames = NULL /* ARGS */) {
        global $ne_config_info;
        $retString = '';
        //if no arguments, set to "default".
        if (func_num_args() == 0) {
            $arrayFiles = Array('default');
            //else if first argument is an array, then use args of this array
        } elseif (is_array(func_get_arg(0))) {
            $arrayFiles = func_get_arg(0);
            //else use each argument
        } else {
            $arrayFiles = func_get_args();
        }

        //for each argument..
        foreach ($arrayFiles as $file) {
            //check for default sets
            switch (strtolower($file)) {
                case "default":
                    $retString .= self::includeHtml($ne_config_info['default_includes_js_css']);
                    continue;
            }
            //split file - params
            $file_splitted_array = explode(",", $file, 2);
            if (sizeof($file_splitted_array) == 1) {
                $file_splitted_array[1] = "";
            }

            $file_only = $file_splitted_array[0];
            $file_params = $file_splitted_array[1];
            //get extension
            $ext = $file_only;
            $ext = parse_url($ext, PHP_URL_PATH);
            $ext = pathinfo($ext, PATHINFO_EXTENSION);

            switch (strtolower($ext)) {
                case "js":
                    $path = $ne_config_info['ne_url'] . $ne_config_info['js_folder_name'] . "/" . $file_only;
                    $retString .= NavTools::wrapScriptInclude($path . $file_params);
                    //$retString .= "<script type=\"text/javascript\" src=\"" . $path . $file_params . "\"></script>\n";
                    break;
                case "css":
                    $path = $ne_config_info['ne_url'] . $ne_config_info['css_folder_name'] . "/" . $file_only;
                    $retString .= NavTools::wrapStyleInclude($path . $file_params);
                    //$retString .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $path . $file_params . "\">\n";
            }
        }

        return $retString;
    }
    
    /**
     * Includes the the .css and .js files for the frontend
     * @param String $frontendClass
     * the class that should be loaded
     */
    public static function includeFE($frontendClass){
        global $ne_config_info;
        
        $retString = '';
        
        $path_css = $ne_config_info['ne_url'] . $ne_config_info['fe_css_folder_name'] . '/';
        $path_js  = $ne_config_info['ne_url'] . $ne_config_info['fe_js_folder_name'] . '/';
        
        switch ($frontendClass){
            case ("areas_manager"):
                $retString .= NavTools::wrapScriptInclude($path_js . "magic.js");
                $retString .= NavTools::wrapScriptInclude($path_js . "areas_manager.js");
                $retString .= NavTools::wrapStyleInclude($path_css . "areas_manager.css");
                $retString .= NavTools::includeHtml('default',
                        'jquery-ui-1.8.2.custom.min.js',
                        'jqueryui/ne2-theme/jquery-ui-1.8.17.custom.css',
                        'naveditor2.js',
                        'jquery.md5.js',
                        'livevalidation_standalone.compressed.js',
                        'live_validation.css',
                        'nav_tools.js'
                );
                break;
            
            case "nav_editor":
                $retString .= NavTools::includeHtml(
                        'default',
                        'json2.js',
                        'tinymce/tinymce.min.js',
                        'handlebars.js',
                        'naveditor2.js');
                $retString .= NavTools::wrapScriptInclude($path_js . "nav_editor.js");
            
            case "user_manager":
                $retString .= NavTools::includeHtml("default",
                            "jqueryui/ne2-theme/jquery-ui-1.8.17.custom.css",
                            "jquery-ui.min.js",
                            "json2.js",
                            "jquery.md5.js",
                            "livevalidation_standalone.compressed.js",
                            "jqueryFileTree.js",
                            "jqueryFileTree.css",
                            "live_validation.css"
                        );
                $retString .= NavTools::wrapScriptInclude($path_js . "user_manager.js");
            
            case "file_editor":
                $retString .= NavTools::includeHtml("default",
                        "jquery.MultiFile.js",
                        "jqueryFileTree.css",
                        "jqueryFileTree.js",
                        "queryFolderImgPreview.js",
                        "handlebars.js",
                        "jquery-ui-1.8.18.custom.min.js",
                        "upload/jquery.iframe-transport.js",
                        "upload/jquery.fileupload.js",
                        "upload/jquery.fileupload-ui.js",
                        "upload/jquery.tmpl.min.js",
                        "upload/jquery.image-gallery.js",
                        "upload/jquery.xdr-transport.js",
                        "jquery.ui.accordion.min.js",
                        "tinymce/tinymce.min.js",
                        "upload/jquery.fileupload.js",
                        "nav_tools.js"

                    );
                $retString .= NavTools::wrapScriptInclude($path_js . "file_editor.js");
                break;
                
            case ("not_found"):
                $retString .= NavTools::includeHtml('default');
                break;
            
            default:
                $retString .= "<!-- couldn\'t recognize the frontend class -->\n";
                break;
        }
        
        return $retString;
    }

    /**
     * get execution time in seconds at current point of call
     * @return float Execution time at this point of call
     */
    public static function get_execution_time() {
        static $microtime_start = null;
        if ($microtime_start === null) {
            $microtime_start = microtime(true);
            return 0.0;
        }
        return microtime(true) - $microtime_start;
    }

    /**
     * save execution time to logfile, in seconds at current point of call
     */
    public static function save_execution_time($message = "", $logfile = "", $debug = -1) {
        global $ne_config_info;
        static $last_saved_time = 0;

        if (strcmp($logfile, "") == 0) {
            $logfile = $ne_config_info['debug_execution_file'];
        }
        if ($debug == -1) {
            $debug = $ne_config_info['debug_time'];
        }


        if (isset($debug) && ($debug < 1 )) {
            return;
        }
        $nowtime = self::get_execution_time();
        $difference = $nowtime - $last_saved_time;
        $last_saved_time = $nowtime;
        if (isset($message)) {
            $nowtime = $nowtime . " (dif:".$difference.")\t" . $_SERVER['SCRIPT_FILENAME'] . "\t" . $message . "\n";
        }
        file_put_contents($logfile, $nowtime, FILE_APPEND | LOCK_EX);
        return;
    }

    /**
     * get server admin name
     * @return String
     */
    public static function getServerAdmin() {
        $server_admin = $_SERVER['SERVER_ADMIN'];
        $server_admin = explode('@', $server_admin);
        $server_admin = $server_admin[0];
        return $server_admin;
    }

    /**
     * Filters not allowed symbols by any string
     * @param String $string <p> Input String</p>
     * @return String <p>Returns filtered String</p>
     */
    public static function filterSymbols($string) {
        global $ne_config_info;
        $string = str_replace($ne_config_info['symbols_being_replaced'], $ne_config_info['symbols_replacement'], $string);
        $string = preg_replace($ne_config_info['regex_removed_symbols'] . 'u', '', $string); //u fuer UTF-8 symbole ersetzung
        return $string;
    }

    /**
     * Checks if $haystack starts with $needle
     * @param String $haystack string to check
     * @param String $needle starts with it
     * @param Boolean $case case sensive
     * @return Boolean True if $haystack starts with $needle
     */
    public static function startsWith($haystack, $needle, $case = TRUE) {

        if ($case) {
            return !strncmp($haystack, $needle, strlen($needle));
        }

        return !strncasecmp($haystack, $needle, strlen($needle));
    }

    /**
     * Checks if $haystack ends with $needle
     * @param String $haystack string to check
     * @param String $needle ends with it
     * @param Boolean $case case sensive
     * @return Boolean True if $haystack ends with $needle
     */
    public static function endsWith($haystack, $needle, $case = TRUE) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        if ($case) {
            return (substr($haystack, -$length) === $needle);
        }

        return (strcasecmp(substr($haystack, -$length), $needle) === 0);
    }

    /**
     * Filters path, in case it is not under DOCUMENT_ROOT path
     * @param String $path Path to test
     * @return String $path or empty string in case the path is over the root
     */
    public static function root_filter($path) {
        $ret_path = '';
        $root_path = $_SERVER['DOCUMENT_ROOT'];
        $path = self::simpleResolvePath($path);
        if (strlen($path) >= strlen($root_path)) {
            if (self::startsWith($path, $root_path)) {
                $ret_path = $path;
            }
        }
        return $ret_path;
    }

    /**
     * Simple resolve path func. /cgi-bin/feed/../dir  => /cgi-bin/dir
     * @param string $path
     * @return string
     */
    public static function simpleResolvePath($path) {
        return preg_replace('/\w+\/\.\.\//', '', $path);
    }

    /**
     * If $variable is not set, returns $default, otherwise $variable
     * @link https://wiki.php.net/rfc/ifsetor
     * @param mixed $variable variable to test
     * @param mixed $default value to return if $variable is null
     * @return mixed $default or $variable depends on isset($variable) test
     */
    public static function ifsetor(&$variable, $default = null) {
        if (isset($variable)) {
            $tmp = $variable;
        } else {
            $tmp = $default;
        }
        return $tmp;
    }

    /**
     * Tests if an Array is associative
     * http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric
     * @param array $array
     * @return bool
     */
    public static function is_assoc($array) {
        if (!is_array($array))
            return FALSE;

        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * log errors/warnings to specified file
     * @global array $ne_config_info
     * @param string $error_text
     * @param string $callerFunc [Optional] function that makes error_log call, usual simple __METHOD__
     */
    public static function error_log($error_text, $callerFunc = '') {
        global $ne_config_info;
        if (!empty($callerFunc)) {
            $callerFunc = $callerFunc . ': ';
        }
        error_log(date('Y-m-d H:m') . ' - ' . $callerFunc . $error_text . "\n", 3, $ne_config_info['log_file']);
    }


    /**
     * removes ALL or only given in $cookies_list cookies
     * @param array $cookies_list [Optional] explicit cookie-names to remove
     */
    public static function unsetAllCookies(array $cookies_list = NULL) {
        if (is_null($cookies_list)) {
            //load ALL cookies to unset
            if (isset($_SERVER['HTTP_COOKIE'])) {
                $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                foreach ($cookies as $cookie) {
                    $parts = explode('=', $cookie);
                    $name = trim($parts[0]);
                    $cookies_list[] = $name;
                }
            }
        }

        //remove routine
        foreach ($cookies_list as $cookieToRemove) {
            setcookie($cookieToRemove,'',1);
            unset($_COOKIE[$cookieToRemove]);
        }
    }


    /**
     * Convert a comma separated file into an associated array.
     * The first row should contain the array keys.
     *
     * @param string $filename Path to the CSV file
     * @param string $delimiter The separator used in the file
     * @return array
     * @link http://gist.github.com/385876
     * @author Jay Williams <http://myd3.com/>
     * @copyright Copyright (c) 2010, Jay Williams
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    static function csv_to_array($filename = '', $delimiter = ',') {
        if (!file_exists($filename) || !is_readable($filename))
            return FALSE;
        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

}
