<?php
/**
* This file is part of the AMEE php calculator.
*
* The AMEE php calculator is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* The AMEE php calculator is free software is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
* @package AMEE
* @author Franck Cassedanne <kifranky [at] gmail [dot] com>
* @version $id: AllTests.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

#error_reporting(E_ALL | E_STRICT);

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'PHPUnit/Framework/TestSuite.php';
#require_once 'PHPUnit/Extensions/PhptTestSuite.php';

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'AMEE.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AMEE' . DIRECTORY_SEPARATOR . 'AllTests.php';

PHPUnit_Util_Filter::$filterPHPUnit = FALSE;

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('AMEE-php');
        $suite->addTest( AMEE_AllTests::suite() );
        #$suite->addTest(new PHPUnit_Extensions_PhptTestSuite(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TextUI'));
        return $suite;
    }
}