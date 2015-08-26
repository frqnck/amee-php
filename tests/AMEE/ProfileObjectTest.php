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
* @version $id: ProfileObjectTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'AMEE' . DIRECTORY_SEPARATOR . 'ProfileObject.php';

class ProfileObjectTest extends PHPUnit_Framework_TestCase
{

		public function testPathIsUnderProfiles()
		{
			$Profile = new AMEE_ProfileObject();
    	$this->assertSame('/profiles', $Profile->full_path);
		}

		public function testProfileUid()
		{
			$Profile = new AMEE_ProfileObject( array('profile_uid'=>'ABC123') );
    	$this->assertSame('ABC123', $Profile->profile_uid);
    	$this->assertSame('/profiles/ABC123', $Profile->full_path);
		}

		public function testProfileDate()
		{
			$Profile = new AMEE_ProfileObject( array('profile_date'=>'2009,04') );
    	$this->assertSame('2009,04', $Profile->profile_date);
		}

}