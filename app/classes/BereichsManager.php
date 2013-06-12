<?php
require_once 'ConfigFileManagerJSON.php';
require_once 'NavTools.php';
/**
 * Verwaltung fuer Bereiche.
 * requires config.php!
 * @uses ConfigFileManagerJSON um config datei zu verwalten
 * @uses NavTools functions
 * @author Dmitry Gorelenkov
 * @internal Purpose: learning PHP -> probably low quality code, sorry :/
 */
class BereichsManager {
    /**
     * Manager for config file
     * @var ConfigFileManager
     */
    private $_ConfigManager;

    /**
     * Array with all possible settings
     * @var array
     */
    private $_aPossibleSettings;

    /**
     * Array with all settings keys and empty values ''
     * @var array
     */
    private $_aEmptySettings;

    /**
     * Constructor
     * @global array $ne2_config_info
     * @global array $g_bereich_settings
     */
    public function __construct() {
        global $ne2_config_info, $g_bereich_settings;
        $configPath = $ne2_config_info['config_file_path_bereiche'];
        $bFirstTime = false;
        //falls kein config datei vorhanden, erstellen, danach mit default values fuellen
        if(!is_file($configPath)){
            file_put_contents($configPath, ''); //touch slower?
            $bFirstTime = true;
        }
        $this->_ConfigManager = new ConfigFileManagerJSON($configPath);
        $this->_aPossibleSettings = $g_bereich_settings['possible_bereich_settings'];
        $this->_aEmptySettings = array_fill_keys($this->_aPossibleSettings, '');

        //fall back
        if($bFirstTime){
            //falls neu erstellt, mit default values fuellen
            $this->addDefaultAreaSettings();
        }
    }

    /**
     * Search all saved Areas-names in config
     * @return array List of all Areas-names
     */
    public function getAreaList(){

        return $this->_ConfigManager->getSettingNames();

    }

    /**
     * Get array of all areas, with all settings for each area.
     * @return array associative 'area'=>'associative settings array'
     */
    public function getAllAreaSettings(){

        return $this->_ConfigManager->getSettingsArray();

    }

    /**
     * Get settings array for $sAreaName area
     * @param string $sAreaName - name of area
     * @return array associative 'setting-name'=>'setting-value'
     */
    public function getAreaSettings($sAreaName){

        return $this->_ConfigManager->getSetting($sAreaName);

    }

    /**
     * Add new area, creates empty file
     * @param string $sAreaName name of area
     * @param array $aSettings associative array of area settings
     * @return bool success
     */
    public function addAreaSettings($sAreaName, array $aSettings) {

        //falls dateiname existiert, abbrechen
        $aAllSettings = $this->getAllAreaSettings();
        $newFileName = $aSettings['file_name'];
        foreach ($aAllSettings as $aSetting) {
            if(strcmp($aSetting['file_name'],$newFileName)== 0 ){
                throw new Exception('Can add, file "'. $newFileName .'" already exists');
            }
        }

        //save settings
        $bResult = $this->_ConfigManager->addSetting($sAreaName, $aSettings);

        //falls erfolgreich neue Datei erstellen
        if ($bResult) {
            //datei mit start/end marker erstellen
            $newAreaData = $aSettings['content_marker_start'] . "\n" . $aSettings['content_marker_end'];
            $path = NavTools::root_filter($this->_getPathByAreaName($sAreaName));
            if (!file_exists($path) && strcmp($path, '') != 0) {
                if (!file_put_contents($path, $newAreaData)) {
                    NavTools::error_log('Can not create area file', __METHOD__);
                }
            }

        }

        return $bResult;
    }

    /**
     * Remove area
     * @param string $sAreaName name of area
     * @return success
     */
    public function deleteAreaSettings($sAreaName){

        return $this->_ConfigManager->removeSetting($sAreaName);

    }

    /**
     * Update area
     * @param string $sAreaName name of area
     * @param array $aSettings associative array of area settings
     * @return success
     * @throws Exception
     * @internal TODO falls datei sich auch aendert... vllt allgemein BereichFileHandler Klass erstellen.
     */
    public function updateAreaSettings($sAreaName, $aSettings){
        //vorherige settings temporaer speichern
        $aOldAreaSettings = $this->getAreaSettings($sAreaName);

        //nur erlaubte einstellungen aktualisieren
        $aSettings = array_intersect_key($aSettings, array_flip($this->_aPossibleSettings));

        //pruefen ob auch umbennen noetig ist //schlechte loesung fuer die Klasse? :/
        $newName = NavTools::ifsetor($aSettings['name']);

        //falls Name angegeben und nicht die Originalname ist
        if(strcmp($newName, $sAreaName)!=0){

            //namen zeichen filtrieren
            $newName = NavTools::filterSymbols($newName);
            $aSettings['name'] = $newName;

            //testen ob name schon vorhanden
            $allNames = $this->getAreaList();
            if(in_array($newName, $allNames)){
                throw new Exception('Can not update, name "'. $newName .'" already exists');
            }

            $this->_ConfigManager->renameSetting($sAreaName, $newName, TRUE);

            $sAreaName = $newName;
        }


        //setting speichern,
        $bResult = $this->_ConfigManager->setSetting($sAreaName, $aSettings);

        // falls erfolgreich und falls marker aktualisiert werden muessen
        // alte marker in der datei ersetzen
        $sNewStartMark = $aSettings['content_marker_start'];
        $sNewEndMark = $aSettings['content_marker_end'];
        $sOldStartMark = $aOldAreaSettings['content_marker_start'];
        $sOldEndMark = $aOldAreaSettings['content_marker_end'];
        $bMarkMustBeChanged = strcmp($sOldStartMark,$sNewStartMark) != 0 ||
                              strcmp($sOldEndMark,$sNewEndMark) != 0;

        if($bResult && $bMarkMustBeChanged){
            $bResult &= $this->_replaceMarks($sAreaName, $sOldStartMark, $sNewStartMark);
            $bResult &= $this->_replaceMarks($sAreaName, $sOldEndMark, $sNewEndMark);
        }

        return $bResult;
    }


    /**
     * Add default areas settings to file (fallback)
     * @global type $aBereicheditors
     */
    public function addDefaultAreaSettings(){
        global $aBereicheditors, $ne2_config_info;

        $aSettings = array();

        foreach ($aBereicheditors as $values) {
            $aSettings = $values;
            $sSettingName = $aSettings['name'];
            $aSettings['content_marker_start'] = NavTools::ifsetor($ne2_config_info[$sSettingName.'_content_marker_start'],'');
            $aSettings['content_marker_end'] = NavTools::ifsetor($ne2_config_info[$sSettingName.'_content_marker_end'],'');
            $aSettings['user_role_required'] = 'user';

            $aSettingsAdjused = $this->_adjustSettingsArray($aSettings);

            $this->addAreaSettings($sSettingName, $aSettingsAdjused);
        }


    }

    /**
     * Modify $aSettings to usual format. <br/>
     * fills new fields if missing, remove fields if not allowed
     * @param array $aSettings associative with settings 'setting-name'=>'setting-value'
     * @return array Modified array
     */
    private function _adjustSettingsArray($aSettings){

        $returnArray = array_replace($this->_aEmptySettings, array_intersect_key($aSettings, $this->_aEmptySettings));

         return $returnArray;
    }

    /**
     * Prueft ob $aSettings nur geeignete einstellungen enthaelt
     * @param array $aSettings assiziatives array, der geprueft werden muss
     * @param boolean $bAllSettings [optional = FALSE] true um zu pruefen <br/>
     * ob $aSettings array auch die entsprechende groesse hat
     * @return boolean true falls konsistent
     * @internal nicht benutzt?
     */
    private function _testAreaSettingsConsistence($aSettings, $bAllSettings=FALSE){
        //TODO pruefen einstellungen die sein MUESSEN? und die optional sind?
        $allPossibleSettings = &$this->_aPossibleSettings;

        //falls in arg array mehr als moeglich einstellungen enthalten sind, return false
        if(count(array_diff_key($aSettings, $allPossibleSettings)) > 0){
            return false;
        }
        //falls alle settings konsistent sein muessen($bAllSettings), muessen die
        //elementenanzahlen uebereinstimmmen.
        if($bAllSettings){
            return (count($aSettings) == count($allPossibleSettings));
        }
        //sonst kann auch weniger sein, also true,
        //da alle enthalten sind (vorher ueberprueft).
        return true;
    }

    /**

     */


    /**
     * get path of arean $sAreaName
     * @global array $ne2_config_info
     * @param string $sAreaName
     * @return string Path to $sAreaName file
     */
    public function _getPathByAreaName($sAreaName) {
        global $ne2_config_info;
        $aAreaArray = $this->getAreaSettings($sAreaName);

        return $ne2_config_info['ssi_folder_path'].$aAreaArray['file_name'];
    }



    /**
     * replace one text for another (marks) in file of $sAreaName
     * @param string $sAreaName
     * @param string $sOldMark
     * @param string $sNewMark
     * @return boolean Success
     */
    public function _replaceMarks($sAreaName, $sOldMark, $sNewMark) {
        $filepath = NavTools::root_filter($this->_getPathByAreaName($sAreaName));

        if(!is_file($filepath)){
            NavTools::error_log("File: '$filepath' not found");
            return FALSE;
        }

        $content = file_get_contents($filepath);
        if($content === FALSE){
            NavTools::error_log("Can not get content from '$filepath'", __METHOD__);
            return FALSE;
        }

        $pos = strpos($content,$sOldMark);
        if ($pos !== FALSE) {
            $content = substr_replace($content,$sNewMark,$pos,strlen($sOldMark));
        }

        if(file_put_contents($filepath, $content) === FALSE){
            NavTools::error_log("Can not save to '$filepath'", __METHOD__);
            return FALSE;
        }

        return TRUE;

    }




}

?>
