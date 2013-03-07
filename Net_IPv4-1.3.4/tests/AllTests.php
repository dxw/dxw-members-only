<?php
if (!defined('PHPUNIT_MAIN_METHOD')) {
    define('PHPUNIT_MAIN_METHOD', 'Net_IPv4_AllTests::main');
}

require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'IPv4Test.php';

class Net_IPv4_AllTests {

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite();

        $suite->addTestSuite('IPv4Test');

        return $suite;
    }
}

if (PHPUNIT_MAIN_METHOD == 'Net_IPv4_AllTests::main') {
    Net_IPv4_AllTests::main();
}
