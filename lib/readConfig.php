<?php

/**
 * Reads the configuration file
 *
 * @param string $file Path to the config file
 * @return array Config structure
 */
function readConfig($file) {
    $configLines = file($file);

    if ($configLines === FALSE) {
        return FALSE;
    }

    $config = array();

    foreach ($configLines as $line_num => $line) {
      if (!preg_match("/#.*/", $line)) {
        if (preg_match("/\S/", $line)) {
          list($key, $value) = explode("=", trim($line), 2);
          $config[$key] = $value;
        }
      }
    }

    return $config;
}

?>