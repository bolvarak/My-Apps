<?php
// Load our test class
require_once('TestClass.php');
// Load our Singleton generator
require_once('Singleton.php');
// Add something
print_r(Singleton::Get('TestClass')->add(5));
