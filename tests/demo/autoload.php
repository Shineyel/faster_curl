<?php
function_exists('auto_load') && spl_autoload_register('auto_load');

//$class_name，比如：Utilities\\BaseMailer
function auto_load($class_name) {
    $file_path = __DIR__.'/../../src/'.strtr($class_name,'\\','/').'.class.php';
    $path_info = pathinfo($file_path);
    $real_path = $path_info['dirname'].'/'.$path_info['basename'];

    file_exists($real_path) && require_once($real_path);
}

