<?php
define('TESTS_BASE_DIR', __DIR__);
$autoloadFile = __DIR__. '/../vendor/autoload.php';
if (! is_readable($autoloadFile)) {
    echo <<<EOT
You must run `composer install` to install the dependencies
before running the test suite.
EOT;
    exit(1);
}
// Include the Composer generated autoloader
require_once $autoloadFile;
spl_autoload_register(function ($class)
{
    if (0 === strpos($class, 'OnlineSid\\DataTabulator\\Tests')) {
        $classFile = str_replace('\\', '/', $class) . '.php';
        require __DIR__ . '/' . $classFile;
    }
});