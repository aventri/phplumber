<?php
spl_autoload_register(function ($class_name) {
    // Trim leading backslash
    if (substr($class_name, 0, 1) === '\\') {
        $class_name = substr($class_name, 1);
    }
    $src = __DIR__;
    if (strpos($class_name, 'Etouches\\Phplumber') !== false) {
        $src = realpath(__DIR__ . '/../../../src');
        $class_name = str_replace('Etouches\\Phplumber\\', '', $class_name);
    }
    include "$src/$class_name.php";
});
