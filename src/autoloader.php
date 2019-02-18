<?php

/**
 * Defined MAINDIR and autoloader function
 */

spl_autoload_register(function ($className){
    $fullPath = __DIR__ . '/../src/' . str_replace('\\','/',$className) . '.php';
    if (file_exists($fullPath)) {
        require_once($fullPath);
    } else {
        throw new Exception("Undefined class ".$className);
    }
});
