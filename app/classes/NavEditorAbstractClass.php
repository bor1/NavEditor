<?php
/**
 * Class, to extend for NavEditor Classes<br />
 * has some useful for NE methods
 *
 * @uses auth.php for variables and constants
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */
class NavEditorAbstractClass {
    const NE_DIR_ROOT = NE_DIR_ROOT;

    protected static function getNESetting($sSettingName, $sDefaultValue = NULL) {
        global $ne_config_info;
        return NavTools::ifsetor($ne_config_info[$sSettingName],$sDefaultValue);
    }

    protected static function getCurrentUser() {
        global $g_current_user_name;
        return ($g_current_user_name)?:'';
    }
}