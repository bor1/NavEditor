<?php

/**
 * Abstract manager for config files
 * To Implement: decode/encode functons of data in file
 *
 * @abstract
 * @author Dmitry Gorelenkov
 * @internal Purpose: learning PHP -> probably low quality code, sorry :/
 */
abstract class ConfigFileManager{
    /**
     * Config filepath
     * @var string
     */
    protected $_configFilePath;

    /**
     * Data array, got from json config file
     * @var array data
     */
    protected $_configDataCache;

    /**
     * Construct
     * @param string $configFilePath config file path
     * @throws Exception if no file exists
     */
    public function __construct($configFilePath = null) {
        //falls kein config datei vorhanden, erstellen oder fehler..
        if (!is_file($configFilePath)) {
//            file_put_contents($configFilePath, ''); //touch slower?
            throw new Exception('No config file: "' . $configFilePath . ' found');
        }

        $this->_configFilePath = $configFilePath;
        $this->reloadDataCache();
    }

    /**
     * Gets setting names
     * @return array setting names (keys)
     */
    public function getSettingNames() {
        return array_keys($this->_configDataCache);
    }

    /**
     * Sets values $settingValue to $settingName setting
     * @param string $settingName name of setting
     * @param mixed $settingValue value of setting
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function setSetting($settingName, $settingValue, $bMultipleCalls = FALSE) {
        $cache = &$this->_configDataCache;

        if (in_array($settingName, $cache)) {
            $cache[$settingName] = $settingValue;
        } else {
            //Code warning? TODO
        }

        $cache[$settingName] = $settingValue;

        $this->multipleCallsHandler($bMultipleCalls);

        return true;
    }

    /**
     * Get value of setting $sSettingName
     * @param string $sSettingName
     * @return mixed NULL if no sSettingName exists, setting value otherwise
     */
    public function getSetting($sSettingName) {
        if (!array_key_exists($sSettingName, $this->_configDataCache)) {
            return NULL;
        }
        return $this->_configDataCache[$sSettingName];
    }


    /**
     * add new setting $sSettingName
     * @param string $sSettingName
     * @param mixed $settingValue
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function addSetting($sSettingName, $settingValue, $bMultipleCalls = FALSE) {

        $this->_configDataCache[$sSettingName] = $settingValue;

        $this->multipleCallsHandler($bMultipleCalls);
        return true;
    }

    /**
     * get all settings
     * @return array all data, settings and values in array
     */
    public function getSettingsArray() {
        return $this->_configDataCache;
    }

    /**
     * set many settings by $arrayToSet
     * @param array $arrayToSet
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function setSettingsByArray($arrayToSet, $bMultipleCalls = FALSE) {
        $bReturn = true;
        foreach ($arrayToSet as $settingName => $sValue) {
            $bReturn &= $this->setSetting($settingName, $sValue, TRUE);
        }

        $this->multipleCallsHandler($bMultipleCalls);
        return $bReturn;
    }

    /**
     * add many settings by $arrayToAdd
     * @param array $arrayToAdd Associative array with setting names->values
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function addSettingsByArray($arrayToAdd, $bMultipleCalls = FALSE) {
        $bReturn = true;
        if(!\NavTools::is_assoc($arrayToAdd)){return false;}

        foreach ($arrayToAdd as $settingName => $sValue) {
            $bReturn &= $this->addSetting($settingName, $sValue, TRUE);
        }

        $this->multipleCallsHandler($bMultipleCalls);

        return $bReturn;

    }

    /**
     * Remove all settings, listed in $aSettingNames
     * @param array $aSettingNames Setting name
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function removeSettings($aSettingNames, $bMultipleCalls = FALSE) {
        $bReturn = TRUE;
        foreach ($aSettingNames as $settingName) {
            $bReturn &= $this->removeSetting($settingName, TRUE);
        }

        $this->multipleCallsHandler($bMultipleCalls);

        return $bReturn;
    }

    /**
     * Remove one setting $sSettingName
     * @param string $sSettingName Setting name
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function removeSetting($sSettingName, $bMultipleCalls = FALSE) {
        $bReturn = FALSE;
        if(in_array($sSettingName, $this->_configDataCache)){
            unset($this->_configDataCache[$sSettingName]);
            $bReturn = TRUE;
        }

        $this->multipleCallsHandler($bMultipleCalls);

        return $bReturn;
    }

    /**
     * Rename setting
     * @param string $sOldName Old name
     * @param string $sNewName New name
     * @param boolean $bMultipleCalls [Optional = FALSE] Set it true if calls more methods at once
     * @return boolean Success
     */
    public function renameSetting($sOldName, $sNewName, $bMultipleCalls = FALSE) {
        $bReturn = TRUE;

        if(empty($sOldName) || empty($sNewName)){
//            throw new Exception('Empty parameters: sOldName: '.$sOldName.', sNewName: '.$sNewName);
            return false;
        }

        if(!in_array($sOldName, $this->_configDataCache) || in_array($sNewName, $this->_configDataCache)){
//            throw new Exception('$sOldName not found, or $sNewName exists');
            return false;
        }

//        $this->_configDataCache[$sNewName] = $this->_configDataCache[$sOldName];
//        unset($this->_configDataCache[$sOldName]);

        if($this->addSetting($sNewName, $this->getSetting($sOldName), TRUE)){
            $bReturn &= $this->removeSetting($sOldName, TRUE);
        }

        $this->multipleCallsHandler($bMultipleCalls);

        return $bReturn;
    }

    /**
     * Tests if $newDataCache is consistent for the config
     * @param array $newDataCache
     * @return boolean Consistence
     */
    public function testConsistence($newDataCache) {
        //TODO?
        return NavTools::is_assoc($newDataCache) || (empty($newDataCache) && is_array($newDataCache));
    }

    /**
     * Save data to file
     * @return boolean Success
     * @throws Exception on fail
     */
    public function saveToFile() {
        if (file_put_contents($this->_configFilePath, $this->encodeData($this->_configDataCache)) === FALSE) {
            throw new Exception('Cannot save to file: "' . $this->configFilePath);
        }
        return TRUE;
    }

    /**
     * Loads/reloads data from config file or $newDataCache (if not null) to local cache
     * @param array $newDataCache [optional = NULL] if not null, will be setted to cache
     * @return boolean Success
     * @throws Exception If failed to save
     */
    protected function reloadDataCache($newDataCache = NULL) {
        if (is_null($newDataCache)) {
            $content = file_get_contents($this->_configFilePath);
            if($content === FALSE){
                throw new Exception('Cannot open file: "' . $this->_configFilePath);
            }
            $newDataCache = $this->decodeData($content);
        }

        return $this->setToCache($newDataCache);
    }

    /**
     * Used in funcs to define if need to save cached data to file after changes done.<br>
     * $bMultipleCalls = TRUE means, there will be more calls, so dont need to save to file now.<br>
     * FALSE means, save the changes to file now.<br><br>
     * Purpose: performance.
     * @param boolean $bMultipleCalls [Optional = FALSE] TRUE - dont save now, FALSE - save now
     */
    protected function multipleCallsHandler($bMultipleCalls) {
        if (!$bMultipleCalls) {
            $this->saveToFile();
        }
    }

    /**
     * Sets array $arrayToSet to cache of this Instance
     * @param array $arrayToSet
     * @return boolean Success
     * @throws Exception if not consistent data in var $arrayToSet
     */
    protected function setToCache($arrayToSet) {
        if (!$this->testConsistence($arrayToSet)) {
            throw new Exception('Inconsistent data');
//            return FALSE;
        }

        $this->_configDataCache = $arrayToSet;
        return true;
    }

    /**
     * To implement, decodes data from text to array
     * @abstract
     * @param string $data string to decode
     * @return array Decoded data
     */
    public abstract function decodeData($data);

    /**
     * To implement, encodes data from array to text format
     * @abstract
     * @param array $data array to encode
     * @return array Encoded data
     */
    public abstract function encodeData($data);

}

?>
