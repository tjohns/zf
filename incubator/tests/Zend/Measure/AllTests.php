<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Measure_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Measure/Flow/MassTest.php';
require_once 'Zend/Measure/Flow/MoleTest.php';
require_once 'Zend/Measure/Flow/VolumeTest.php';

require_once 'Zend/Measure/Viscosity/DynamicTest.php';
require_once 'Zend/Measure/Viscosity/KinematicTest.php';

require_once 'Zend/Measure/AccelerationTest.php';
require_once 'Zend/Measure/AreaTest.php';
require_once 'Zend/Measure/CapacityTest.php';
require_once 'Zend/Measure/CurrentTest.php';
require_once 'Zend/Measure/EnergyTest.php';
require_once 'Zend/Measure/ForceTest.php';
require_once 'Zend/Measure/IlluminationTest.php';
require_once 'Zend/Measure/LengthTest.php';
require_once 'Zend/Measure/LightnessTest.php';
require_once 'Zend/Measure/PowerTest.php';
require_once 'Zend/Measure/SpeedTest.php';
require_once 'Zend/Measure/TemperatureTest.php';
require_once 'Zend/Measure/TorqueTest.php';
require_once 'Zend/Measure/VolumeTest.php';

class Zend_Measure_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Measure');

        $suite->addTestSuite('Zend_Measure_Flow_MassTest');
        $suite->addTestSuite('Zend_Measure_Flow_MoleTest');
        $suite->addTestSuite('Zend_Measure_Flow_VolumeTest');

        $suite->addTestSuite('Zend_Measure_Viscosity_DynamicTest');
        $suite->addTestSuite('Zend_Measure_Viscosity_KinematicTest');

        $suite->addTestSuite('Zend_Measure_AccelerationTest');
        $suite->addTestSuite('Zend_Measure_AreaTest');
        $suite->addTestSuite('Zend_Measure_CapacityTest');
        $suite->addTestSuite('Zend_Measure_CurrentTest');
        $suite->addTestSuite('Zend_Measure_EnergyTest');
        $suite->addTestSuite('Zend_Measure_ForceTest');
        $suite->addTestSuite('Zend_Measure_IlluminationTest');
        $suite->addTestSuite('Zend_Measure_LengthTest');
        $suite->addTestSuite('Zend_Measure_LightnessTest');
        $suite->addTestSuite('Zend_Measure_PowerTest');
        $suite->addTestSuite('Zend_Measure_SpeedTest');
        $suite->addTestSuite('Zend_Measure_TemperatureTest');
        $suite->addTestSuite('Zend_Measure_TorqueTest');
        $suite->addTestSuite('Zend_Measure_VolumeTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Measure_AllTests::main') {
    Zend_Measure_AllTests::main();
}
