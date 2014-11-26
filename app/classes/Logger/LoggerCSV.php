<?php
namespace Logger;

/**
 * CSV Logger, to log something in csv format
 *
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */
class LoggerCSV extends NE_Logger {

    protected $_separator;
    protected $_format;
    protected $_headers;
    protected $_currentUserName;
    protected $_fileHandle;

    protected static $_unknownDataText = 'unknown';



    /**
     * Constructor<br />
     * all arguments have default values!
     *
     * @param string $sLogFilePath [Optional]
     * @param string $sSeparator [Optional]
     * @param string $sFormat [Optional]
     * @param int $bitErrorsFlags [Optional]
     * @param int $iMaxFileSizeInBytes [Optional]
     * @param int $iMaxLogHistoryInSeconds [Optional]
     * @param bool $bActivated [Optional]
     * @param string $sUserName [Optional]
     */
    public function __construct($sLogFilePath = NULL,
                                $sSeparator = NULL,
                                $sFormat = NULL,
                                $bitErrorsFlags = NULL,
                                $iMaxFileSizeInBytes = NULL,
                                $iMaxLogHistoryInSeconds = NULL,
                                $bActivated = NULL,
                                $sUserName = NULL) {
        $this->setSeparator($sSeparator);
        $this->setFormat($sFormat);
        parent::setErrorFlags($bitErrorsFlags);
        parent::setLogFilePath($sLogFilePath);
        parent::setMaxFileSize($iMaxFileSizeInBytes);
        parent::setMaxLogHistory($iMaxLogHistoryInSeconds);
        parent::setActivated($bActivated);
        $this->setCurrentUserName($sUserName);

    }

    public function __destruct() {
        if(is_resource($this->_fileHandle)){
            fclose($this->_fileHandle);
        }
    }

    /**
     *
     * @param string $message
     * @param string (Class Constant) $errorLevel
     * @return false if logging ignored
     */
    public function log($message = '', $errorLevel = self::DEFAULT_ERROR_LEVEL) {
        if(!$this->_activated){
            return false;
        }

        if(!(self::$_errorlvlMaskFlags[$errorLevel] & $this->_errors_mask)){
            return false;
        }

        if (!file_exists($this->_logFilePath)) {
            $this->createLogFile($this->_logFilePath);
        }

        $this->_fileHandle = @fopen($this->_logFilePath, 'a+');
        if(!$this->_fileHandle){return false;}

        $this->applyFilterOnLogFile();

        $rowDataToSave = $this->getRowDataArray($message,$errorLevel);

        fputcsv($this->_fileHandle, $rowDataToSave, $this->_separator);

        fclose($this->_fileHandle);

        return true;
    }



    /**
     * get associative array of log file
     * @return array
     */
    public function getLogArray() {
        if (!file_exists($this->_logFilePath)) {
            $this->createLogFile($this->_logFilePath);
        }
        return \NavTools::csv_to_array($this->_logFilePath, $this->_separator);
    }


    /**
     * Set log file separator
     * @param string $sSeparator
     */
    public function setSeparator($sSeparator) {
        $this->_separator = \NavTools::ifsetor($sSeparator,
                parent::getNESetting('log_csv_separator', ','));
    }

    /**
     * Set $_format and $_headers
     * @param string $sFormat
     */
    public function setFormat($sFormat) {
        $this->_format = \NavTools::ifsetor($sFormat,
                parent::getNESetting('log_csv_format', 'timestamp|date-time|errorlevel|username|ip|host|referrer|file|line|message'));
        //set headers
        $headersArray = explode('|',$this->_format);
        $this->_headers = implode($this->_separator, $headersArray);
    }

    /**
     * Set $_currentUserName
     * @param string $sCurrentUserName
     */
    public function setCurrentUserName($sCurrentUserName) {
        $this->_currentUserName = \NavTools::ifsetor($sCurrentUserName, parent::getCurrentUser());
    }


    /**
     * Return array for saving in csv file<br />
     * array depends on $_format
     *
     * @staticvar array $debugBacktrace debug_backtrace() will be save if needed
     * @param string $message
     * @param string $errorLevel
     * @return array filled associative array, depends on $_format
     */
    public function getRowDataArray($message, $errorLevel) {
        $formatArray = explode('|',$this->_format);
        $returnRowArray = array();
        $newValue = '';
        static $debugBacktrace = array();

        foreach ($formatArray as $field) {
            switch ($field) {
                case 'timestamp':
                    $newValue = time();
                    break;
                case 'date-time':
                    $newValue = date("d.m.Y - H:i:s");
                    break;
                case 'errorlevel':
                    $newValue = $errorLevel;
                    break;
                case 'message':
                    $newValue = $message;
                    break;
                case 'ip':
                    $newValue = \NavTools::ifsetor($_SERVER["REMOTE_ADDR"],  self::$_unknownDataText);
                    break;
                case 'host':
                    $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
                    $newValue =($host)?:self::$_unknownDataText;
                    break;
                case 'referrer':
                    $newValue = \NavTools::ifsetor($_SERVER["HTTP_REFERER"],self::$_unknownDataText);
                    break;
                case 'line':
                    $debugBacktrace = ($debugBacktrace)?:debug_backtrace();//performance
                    $newValue = \NavTools::ifsetor($debugBacktrace[1]['line'],self::$_unknownDataText);
                    break;
                case 'file':
                    $debugBacktrace = ($debugBacktrace)?:debug_backtrace();//performance
                    $newValue = \NavTools::ifsetor($debugBacktrace[1]['file'],self::$_unknownDataText);
                    break;
                case 'username':
                    $newValue = $this->_currentUserName;
                    break;
                default:
                    $newValue = 'Can not parse the format: '.$field;
            }

            $returnRowArray[$field] = \NavTools::ifsetor($newValue,self::$_unknownDataText);
        }
        unset($debugBacktrace);

        return $returnRowArray;
    }

    /**
     * creates or resets log file with current headers
     * @param string $sLogFilePath [Optional] path to log file
     * @return bool success
     */
    public function createLogFile($sLogFilePath = NULL) {
        $sLogFilePath = ($sLogFilePath)?:$this->_logFilePath;
        if(!($fd = @fopen($sLogFilePath, 'w+'))){return false;}

        if(!$this->resetLog($fd)){return false;}

        return fclose($fd);
    }

    /**
     * resets headers and content of file
     * @param resource $fdFileHandle
     * @return bool success
     */
    public function resetLog($fdFileHandle = NULL) {
        $fdFileHandle = ($fdFileHandle)?:$this->_fileHandle;
        //put headers and return
        return (ftruncate($fdFileHandle, 0) &&
               (fwrite($fdFileHandle, $this->_headers."\n") !== FALSE));
    }


    /**
     * Filters log file, depends on max history time (if timestamp set) or max file size<br />
     * With no arguments, class instance variables will be used
     * @param int $iMaxFileSize
     * @param int $iMaxHistory
     * @return int number of removed lines
     */
    public function applyFilterOnLogFile($iMaxFileSize = NULL, $iMaxHistory = NULL) {
        $iMaxFileSize   = ($iMaxFileSize)   ?   :$this->_maxFileSize;
        $iMaxHistory    = ($iMaxHistory)    ?   :$this->_maxLogHistory;

        $dataArray = $this->getLogArray();
        $countAtBegin = count($dataArray);
        $oldestLineTimestamp = time()-$iMaxHistory;

        if($countAtBegin == 0) return 0;

        //remove old values from array
        if(isset($dataArray[0]['timestamp'])){
            $arrayCount = $countAtBegin;
            while ($arrayCount > 0 && intval($dataArray[0]['timestamp']) <= intval($oldestLineTimestamp)) {
                array_shift($dataArray);
                $arrayCount--;
            }
        }

        //fit array size (approximately)
        while (strlen(serialize($dataArray). $this->_headers) > $iMaxFileSize){
            array_shift($dataArray);
        }

        //new array size
        $newArraySize = count($dataArray);

        //if something changed
        if($newArraySize != $countAtBegin){
            //empty log file
            if(!$this->resetLog()){return false;}

            //bad performance, todo array_to_csv ?
            foreach ($dataArray as $dataRow) {
                fputcsv($this->_fileHandle, $dataRow, $this->_separator);
            }
        }

        return $countAtBegin - $newArraySize;
    }

}

?>
