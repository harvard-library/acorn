<?php
require_once dirname(__FILE__) . '/bootstrap.php';
require_once dirname(__FILE__) . '/application/models/drs/DRSModelsAllTests.php';

class AllTests
{
    public static function main()
    {
        $parameters = array();
        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('ACORN Application');
        $suite->addTest(DRSModelsAllTests::suite());
        return $suite;
    }
}

AllTests::main();
?>