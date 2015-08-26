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
* @version $id: ObjectTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once 'PHPUnit/Framework/TestCase.php';

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'AMEE' . DIRECTORY_SEPARATOR . 'Object.php';

class ObjectTest extends PHPUnit_Framework_TestCase
{

		public function testArrayOfData()
		{
	    $data = array(
	    	'uid' => '6B0DD95CDF3D',
    		'created' => time()-10000,
    		'modified' => time()-10000,
    		'path' => '/transport/plane/generic/ABCD1234',
    		'name' => 'kgPerPassengerJourney'
			);
			$Obj = new AMEE_Object($data);
    	$this->assertSame($data['uid'], $Obj->uid);
			$this->assertSame($data['created'], $Obj->created);
    	$this->assertSame($data['modified'], $Obj->modified);
    	$this->assertSame($data['path'], $Obj->path);
    	$this->assertSame($data['name'], $Obj->name);
		}

		public function testObjectisCreatedWithoutData()
		{
			$Obj = new AMEE_Object();
    	$this->assertNull($Obj->uid);
			$this->assertSame($Obj->created, $Obj->modified);
    	$this->assertNull($Obj->path);
    	$this->assertNull($Obj->name);
		}

}