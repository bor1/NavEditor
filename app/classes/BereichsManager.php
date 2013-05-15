<?php
require_once 'ConfigFileManagerJSON.php';
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
            $this->addDefaultBereichSettings();
        }
    }

    /**
     * Search all saved Areas-names in config
     * @return array List of all Areas-names
     */
    public function getBereichList(){

        return $this->_ConfigManager->getSettingNames();

    }

    /**
     * Get array of all areas, with all settings for each area.
     * @return array associative 'area'=>'associative settings array'
     */
    public function getAllBereichSettings(){

        return $this->_ConfigManager->getSettingsArray();

    }

    /**
     * Get settings array for $sBereichName area
     * @param string $sBereichName - name of area
     * @return array associative 'setting-name'=>'setting-value'
     */
    public function getBereichSettings($sBereichName){

        return $this->_ConfigManager->getSetting($sBereichName);

    }


    /**
     * Add new area
     * @param string $sBereichName name of area
     * @param array $aSettings associative array of area settings
     * @return bool success
     * @throws Exception
     */
    public function addBereichSettings($sBereichName, $aSettings){

        return $this->_ConfigManager->addSetting($sBereichName, $aSettings);

    }

    /**
     * Remove area
     * @param string $sBereichName name of area
     * @return success
     */
    public function deleteBereichSettings($sBereichName){

        return $this->_ConfigManager->removeSetting($sBereichName);

    }

    /**
     * Update area
     * @param string $sBereichName name of area
     * @param array $aSettings associative array of area settings
     * @return success
     * @throws Exception
     */
    public function updateBereichSettings($sBereichName, $aSettings){
        //nur erlaubte einstellungen aktualisieren
        $aSettings = array_intersect_key($aSettings, array_flip($this->_aPossibleSettings));

        //pruefen ob auch umbennen noetig ist //schlechte loesung fuer die Klasse? :/
        $newName = NavTools::ifsetor($aSettings['name']);

        //falls Name angegeben und nicht die Originalname ist
        if(isset($newName[0]) && strcmp($newName, $sBereichName)!=0){

            //namen zeichen filtrieren
            $newName = NavTools::filterSymbols($newName);
            $aSettings['name'] = $newName;

            //testen ob name schon vorhanden
            $allNames = $this->getBereichList();
            if(in_array($newName, $allNames)){
                throw new Exception('Can not update, name "'. $newName .'" already exists');
            }
            $this->deleteBereichSettings($sBereichName);
            $sBereichName = $aSettings['name'];
        }

        return $this->_ConfigManager->setSetting($sBereichName, $aSettings);
    }


    /**
     * Add default areas settings to file (fallback)
     * @global type $aBereicheditors
     */
    public function addDefaultBereichSettings(){
        global $aBereicheditors, $ne2_config_info;

        $aSettings = array();

        foreach ($aBereicheditors as $values) {
            $aSettings = $values;
            $sSettingName = $aSettings['name'];
            $aSettings['content_marker_start'] = NavTools::ifsetor($ne2_config_info[$sSettingName.'_content_marker_start'],'');
            $aSettings['content_marker_end'] = NavTools::ifsetor($ne2_config_info[$sSettingName.'_content_marker_end'],'');
            $aSettings['user_role_required'] = 'user';

            $aSettingsAdjused = $this->_adjustSettingsArray($aSettings);

            $this->addBereichSettings($sSettingName, $aSettingsAdjused);
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
    private function _testBereichSettingsConsistence($aSettings, $bAllSettings=FALSE){
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

}

?>
