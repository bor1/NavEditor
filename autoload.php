<?php

/**
 * Autoload
 *
 * @author Dmitry Gorelenkov
 * @internal note: learning PHP -> probably low quality code, sorry :/
 */
if (strcmp(realpath(__FILE__), realpath($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'])) === 0) {
    die("Direct access forbidden");
}

spl_autoload_register(function($className) {
            //security?
            $className = str_replace('../', '', $className);

            //namespaces
            if ($className[0] === '\\') {
                $className = substr($className, 1);
            }

            $filePath = NE_DIR_CLASSES . str_replace("\\","/",$className) . '.php';

            if (is_file($filePath))
                require_once $filePath;
        });
?>
