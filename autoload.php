<?php
function myAutoloader($class) {
    include 'classes/' . $class . '.php';
}
include 'read_csv.php';
include 'db.php';
spl_autoload_register('myAutoloader');
?>