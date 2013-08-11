<?php

require_once 'NE_Logger.php';

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

    protected static $_unknownDataText = 'unknown';



    /**
     * Constructor
     *
     * @param string $sLogFilePath
     * @param string $sSeparator
     * @param string $sFormat
     * @param int $bitErrorsFlags
     * @param int $iMaxFileSizeInBytes
     * @param int $iMaxLogHistoryInSeconds
     * @param bool $bActivated
     */
    public function __construct($sLogFilePath = NULL,
                                $sSeparator = NULL,
                                $sFormat = NULL,
                                $bitErrorsFlags = NULL,
                                $iMaxFileSizeInBytes = NULL,
                                $iMaxLogHistoryInSeconds = NULL,
                                $bActivated = NULL) {
        $this->setSeparator($sSeparator);
        $this->setFormat($sFormat);
        $this->setErrorFlags($bitErrorsFlags);
        $this->setLogFilePath($sLogFilePath);
        $this->setMaxFileSize($iMaxFileSizeInBytes);
        $this->setMaxLogHistory($iMaxLogHistoryInSeconds);
        $this->setActivated($bActivated);

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
		}else{
            $this->applyFilterOnLogFile();
        }

		$fd = fopen($this->_logFilePath, 'a');

        $rowDataToSave = $this->getRowDataArray($message,$errorLevel);

		fputcsv($fd, $rowDataToSave, $this->_separator);

		fclose($fd);

        return true;
    }



    /**
     * get associative array of log file
     * @return array
     */
    public function getLogArray() {
        return NavTools::csv_to_array($this->_logFilePath, $this->_separator);
    }


    /**
     * Set log file separator
     * @param string $sSeparator
     */
    public function setSeparator($sSeparator) {
        $this->_separator = NavTools::ifsetor($sSeparator,
                parent::getNESetting('log_csv_separator', ','));
    }

    /**
     * Set $_format and $_headers
     * @param string $sFormat
     */
    public function setFormat($sFormat) {
        $this->_format = NavTools::ifsetor($sFormat,
                parent::getNESetting('log_csv_format', 'timestamp|date-time|errorlevel|ip|host|referrer|file|line|message'));
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
                    $newValue = NavTools::ifsetor($_SERVER["REMOTE_ADDR"],  self::$_unknownDataText);
                    break;
                case 'host':
                    $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
                    $newValue =($host)?:self::$_unknownDataText;
                    break;
                case 'referrer':
                    $newValue = NavTools::ifsetor($_SERVER["HTTP_REFERER"],self::$_unknownDataText);
                    break;
                case 'line':
                    $debugBacktrace = ($debugBacktrace)?:debug_backtrace();//performance
                    $newValue = NavTools::ifsetor($debugBacktrace[1]['line'],self::$_unknownDataText);
                    break;
                case 'file':
                    $debugBacktrace = ($debugBacktrace)?:debug_backtrace();//performance
                    $newValue = NavTools::ifsetor($debugBacktrace[1]['file'],self::$_unknownDataText);
                    break;
                default:
                    $newValue = 'Can not parse the Format: '.$field;
            }

            $returnRowArray[$field] = $newValue;
        }
        unset($debugBacktrace);

        return $returnRowArray;
    }

    /**
     * creates or resets log file with current headers
     * @param string $sLogFilePath [Optional]
     */
    public function createLogFile($sLogFilePath = NULL) {
        $sLogFilePath = ($sLogFilePath)?:$this->_logFilePath;
        //set headers
        $headersArray = explode('|',$this->_format);
        $this->_headers = implode($this->_separator, $headersArray);
        //put them to file
        file_put_contents($sLogFilePath, $this->_headers."\n");
    }


    /**
     * Filters log file, depends on max history time (if timestamp set) or max file size<br />
     * With no argumets, class instance variables will be used
     * @param int $iMaxFileSize
     * @param int $iMaxHistory
     * @return int number of removed lines
     */
    public function applyFilterOnLogFile($iMaxFileSize = NULL, $iMaxHistory = NULL) {
        $iMaxFileSize = is_null($iMaxFileSize)?$this->_maxFileSize:$iMaxFileSize;
        $iMaxHistory = is_null($iMaxHistory)?$this->_maxLogHistory:$iMaxHistory;

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
            $this->createLogFile();

            $logfd = fopen($this->_logFilePath, 'a');

            //bad performance, todo array_to_csv ?
            foreach ($dataArray as $dataRow) {
                fputcsv($logfd, $dataRow, $this->_separator);
            }

            fclose($logfd);
        }

        return $countAtBegin - $newArraySize;
    }

}

?>
