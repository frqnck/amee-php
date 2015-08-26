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

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'PHPUnit/Framework/TestSuite.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ObjectTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProfileObjectTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DataObjectTest.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DataItemTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DataItemValueTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DataCategoryTest.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ConnectionTest.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProfileTest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProfileCategoryTest.php';

// TODO
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProfileItemTest.php';
#require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ProfileItemValueTest.php';
#require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DrillDownTest.php';

PHPUnit_Util_Filter::$filterPHPUnit = FALSE;

class AMEE_AllTests
{
    public static function suite()
    {
			$suite = new PHPUnit_Framework_TestSuite('Class');

			$suite->addTestSuite('ObjectTest');
			$suite->addTestSuite('ProfileObjectTest');
			$suite->addTestSuite('DataObjectTest');
			$suite->addTestSuite('DataItemTest');
			$suite->addTestSuite('DataItemValueTest');
			$suite->addTestSuite('DataCategoryTest');
			$suite->addTestSuite('ConnectionTest');
			$suite->addTestSuite('ProfileTest');
			$suite->addTestSuite('ProfileCategoryTest');

			// TODO
			$suite->addTestSuite('ProfileItemTest');
			#$suite->addTestSuite('ProfileItemValue');
			#$suite->addTestSuite('DrillDown');

			return $suite;
    }
}
