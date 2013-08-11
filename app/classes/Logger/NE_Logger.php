<?php
if(defined('NE_DIR_CLASSES'))
require_once(NE_DIR_CLASSES.'/NavEditorAbstractClass.php');

/**
 * Abstract Logger Class for NavEditor Loggers
 *
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */
abstract class NE_Logger extends NavEditorAbstractClass{

    /**
     * activate or deactivate the logger
     * @var bool
     */
    protected $_activated;

    /**
     * Mask for error levels
     * @var int
     */
    protected $_errors_mask;

    /**
     * Full path to log file
     * @var string
     */
    protected $_logFilePath;

    /**
     * Maximum older or logger entries in seconds
     * @var int 
     */
    protected $_maxLogHistory;

    /**
     * maximum size of log file in bytes
     * @var int
     */
    protected $_maxFileSize;

    //errorlevel names
    const INFO      = 'INFO';
    const WARNING   = 'WARNING';
    const ERROR     = 'ERROR';
    const DEBUG     = 'DEBUG';

    //standard errorlevel
    const DEFAULT_ERROR_LEVEL = self::INFO;

    //errorlevel masks
    const MASK_DEBUG    = 0x1;
    const MASK_INFO     = 0x2;
    const MASK_WARNING  = 0x4;
    const MASK_ERROR    = 0x8;

    /**
     * All masks in the associative array
     * @var array
     */
    protected static $_errorlvlMaskFlags = array(
        self::DEBUG     => self::MASK_DEBUG,
        self::INFO      => self::MASK_INFO,
        self::WARNING   => self::MASK_WARNING,
        self::ERROR     => self::MASK_ERROR
    );

    /**
     * must log the message
     */
    abstract public function log($message = '', $errorLevel = self::DEFAULT_ERROR_LEVEL);

    /**
     * must return associative array of log data
     */
    abstract public function getLogArray();

    /**
     * Set if logger will log anything
     * @param bool $bActivated TRUE for activation, FALSE for deactivation
     */
    public function setActivated($bActivated) {
        $this->_activated = NavTools::ifsetor($bActivated,
                parent::getNESetting('log_activated', TRUE));
    }

    /**
     * Set log file path
     * @param string $sLogFilePath
     */
    public function setLogFilePath($sLogFilePath) {
        $this->_logFilePath = NavTools::ifsetor($sLogFilePath,
                parent::getNESetting('log_file', parent::NE_DIR_ROOT . 'log/log.csv'));
    }

    /**
     * Set $_maxFileSize
     * @param int $iMaxFileSizeInBytes Max allowed log-filesize in bytes
     */
    public function setMaxFileSize($iMaxFileSizeInBytes) {
        $this->_maxFileSize = NavTools::ifsetor($iMaxFileSizeInBytes,
                parent::getNESetting('log_max_file_size', 1048576));
    }

    /**
     * Set $_maxLogHistory
     * @param int $iMaxLogHistoryInSeconds time in seconds how old can be log entries
     */
    public function setMaxLogHistory($iMaxLogHistoryInSeconds) {
        $this->_maxLogHistory = NavTools::ifsetor($iMaxLogHistoryInSeconds,
                parent::getNESetting('log_max_history', 3600*24));
    }

    /**
     * Set bitmask of errortypes
     * @param int(bitmask) $bitErrorsFlags
     */
    public function setErrorFlags($bitErrorsFlags) {
        $this->_errors_mask = NavTools::ifsetor($bitErrorsFlags,
                parent::getNESetting('log_errormask', self::getAllErrorsMask()));
    }

    /**
     * Return all errors-types mask
     * @return int(bitsmask)
     */
    public static function getAllErrorsMask() {
        $resultMask = 0x0;
        foreach (self::$_errorlvlMaskFlags as $option) {
            $resultMask |= $option;
        }
        return $resultMask;
    }

    /**
     * Convenience wrapper function for info log type
     * @param string $message
     */
    public function info($message){
        $this->log($message, self::INFO);
    }

    /**
     * Convenience wrapper function for warning log type
     * @param string $message
     */
    public function warning($message){
        $this->log($message, self::WARNING);
    }

    /**
     * Convenience wrapper function for error log type
     * @param string $message
     */
    public function error($message){
        $this->log($message, self::ERROR);
    }

    /**
     * Convenience wrapper function for debug log type
     * @param string $message
     */
    public function debug($message){
        $this->log($message, self::DEBUG);
    }


}

?>
