<?php

require_once('../auth.php');

if (!ini_get('safe_mode')) {
    set_time_limit(10);
}
ini_set("max_input_time", 2);

?>