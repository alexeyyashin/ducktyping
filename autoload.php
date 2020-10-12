<?php
spl_autoload_register(function($classname) {
    $filename = str_replace('\\', '/', trim(mb_substr($classname, 23), '\\'));
    if (file_exists($file = __DIR__ . '/src/' . $filename . '.php')) {
        include_once $file;
    }
});

require_once __DIR__ . '/src/functions.php';
