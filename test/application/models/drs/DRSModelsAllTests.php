<?php
require_once dirname(__FILE__) . '/BatchBuilderModelTest.php';

class DRSModelsAllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('SAVE Application - Search Models');
        $suite->addTestSuite('BatchBuilderModelTest');
        return $suite;
    }
}
?>