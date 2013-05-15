<?php
require_once 'ConfigFileManagerJSON.php';
require_once 'NavTools.php';

/**
 * Default editor fuer Bereiche.
 *
 * @uses config.php some global variables
 * @uses NavTools.php some functions
 * @author Dmitry Gorelenkov
 *
 */
class BereichsEditor {

    private $_fpath; //file path
    private $_bereichname; //bereichtsname
    private $_content_splitted; //splitted (start, middle, end) array. By markers
    private $_conf_array; //array mit daten aus config datei
    private $_start_marker; //startmarker
    private $_end_marker; //endmarker
    private $_content_start; //content inhalt bevor start marker
    private $_content_middle; //content inhalt zwischen start und end markers
    private $_content_end; //content inhalt nach dem end marker

    /**
     * Constructor
     *
     * @global array $ne2_config_info
     * @param string $bereichname Name des Bereichs

     */
    public function __construct($bereichname) {
        global $ne2_config_info;
        $this->_bereichname = $bereichname;
        $config_file_path = $ne2_config_info['config_file_path_bereiche'];
        $confMngr = new ConfigFileManagerJSON($config_file_path);
        $this->_conf_array = $confMngr->getSetting($bereichname);
        $this->_start_marker = \NavTools::ifsetor($this->_conf_array[$ne2_config_info['content_marker_start_setting']]);
        $this->_end_marker = \NavTools::ifsetor($this->_conf_array[$ne2_config_info['content_marker_end_setting']]);
        $filename_setting = $ne2_config_info['bereich_filename_setting'];
        $filename = \NavTools::ifsetor($this->_conf_array[$filename_setting]);
        if (strlen($filename) == 0) {
            throw new Exception('No filename setting: "'.$filename_setting.'" in config: "'.$config_file_path.'" found');
        }
        $this->_fpath = $ne2_config_info['ssi_folder_path'] . $filename;

        if (!is_file($this->_fpath)) {
//            throw new Exception('File ('.$this->_fpath.') not found');
            file_put_contents($this->_fpath, '');
        }
        $this->_loadContents();
    }


    /**
     * Loads content from file or $data to local variables
     * @param String $data [Optional] String to parse. Otherwise will be loaded from file
     */
    private function _loadContents($data = null){
        $content_full = (is_null($data))?file_get_contents($this->_fpath):$data;
        $this->_content_splitted = $this->_get_splitted_content($content_full, $this->_start_marker, $this->_end_marker);
        $this->_content_start = $this->_content_splitted['start'];
        $this->_content_middle = $this->_content_splitted['middle'];
        $this->_content_end = $this->_content_splitted['end'];
    }


    /**
     * Gets content.
     * @return String Content from file.
     */
    public function get_content() {
        //_loadContents() ? +stabilitaet -performance
        return $this->_content_middle;
    }

    /**
     * Updates content
     * @param String $newContent Content to put
     * @return String message about succes
     */
    public function update_content($newContent) {
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($newContent);
        } else {
            $data = $newContent;
        }

        // just add start_content, start marker, end_content, end marker...
        $data = $this->_content_start . $this->_start_marker . "\n"
                        . $data . "\n"
                        . $this->_end_marker . $this->_content_end;

        file_put_contents($this->_fpath, $data);

        //refresh local variables, for the case you call get_content() after update_content(), have to get updated data.
        $this->_loadContents($data);


        return ucfirst($this->_bereichname . ' wurde aktualisiert');
    }

    /**
     * Gibt gesplitterte nach markers daten zurueck.
     * <p>eigentliche content_markers sind nicht enthalten!</p>
     * @param String $content content zu bearbeiten
     * @param String $start_mark start content marker
     * @param String $end_mark end content marker
     * @return Array Array with 3 elements: 'start' -> start content, 'middle' -> middle content, 'end' -> end content
     *
     */
    private function _get_splitted_content($content, $start_mark, $end_mark) {
        $returnArray = array();
        $start_content = "";
        $end_content = "";
        //falls startmarker definiert bzw. not empty
        if (strlen($start_mark) > 0) {

            $start_pos = strpos($content, $start_mark);

            //TEMP. falls nicht gefunden, mit fallBack versuchen
            if ($start_pos === FALSE) {
                $start_pos = $this->_tryFallBack_start_pos($content, $this->_bereichname);
            }//TODO remove this code later..

            //falls position bestimmt, trennen, start_content speichern
            if ($start_pos !== FALSE) {
                $start_content = (string) substr($content, 0, $start_pos);
                //content weiter abgeschnitten benutzen
                $content = substr($content, $start_pos + strlen($start_mark));
            }
        }

        //end position bestimmen
        if (strlen($end_mark) > 0) {
            $end_pos = strpos($content, $end_mark);
            if ($end_pos === FALSE) {
                $end_pos = $this->_tryFallBack_end_pos($content, $this->_bereichname);
            }

            //falls position bestimmt, trennen, end_content speichern
            if ($end_pos !== FALSE) {
                $end_content = (string) substr($content, $end_pos + strlen($end_mark));
                //content endlich bestimmen
                $content = substr($content, 0, $end_pos);
            }
        }




        //alle contents gesplittert speichern und zurueckgeben
        $returnArray['start'] = $start_content;
        $returnArray['middle'] = $content;
        $returnArray['end'] = $end_content;

        return $returnArray;
    }


    //temp fallBack
    private function _tryFallBack_start_pos($content, $bereich) {
        //TODO
        switch ($bereich) {
            case 'kurzinfo':


                break;
            default:
                break;
        }
        return false;
    }

    //temp fallBack
    private function _tryFallBack_end_pos($content, $bereich) {
        //TODO
        return false;
    }

}

?>
