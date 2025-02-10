<?php
function myAutoloader($class) {
    include 'classes/' . $class . '.php';
}
include 'excute.php';
spl_autoload_register('myAutoloader');
?>