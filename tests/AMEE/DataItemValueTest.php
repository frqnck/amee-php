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
* @version $id: DataItemValueTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class DataItemValueTest extends PHPUnit_Framework_TestCase
{

	public function testIsDataObject()
	{
		$ItemValue = new AMEE_DataItemValue();
		$this->assertTrue($ItemValue instanceof AMEE_DataObject, 'should have common AMEE object properties');
		$this->assertObjectHasAttribute('value', $ItemValue, 'value: should have a value');
    $this->assertObjectHasAttribute('type', $ItemValue, 'type: should have a type');
    $this->assertObjectHasAttribute('from_profile', $ItemValue, 'profile: can be from profile');
    $this->assertObjectHasAttribute('from_data', $ItemValue, 'data: can be from data');

    $data = array('uid' => 'ABCD1234');
		$ItemValue = new AMEE_DataItemValue($data);
		$this->assertSame($data['uid'], $ItemValue->uid, 'should initialize AMEE::Object data on creation');

		$data = array(
	    'value' => "test",
   		'type' => "TEXT",
    	'from_profile' => false,
    	'from_data' => true
    );
		$ItemValue = new AMEE_DataItemValue($data);
		$this->assertSame($data['value'], $ItemValue->value, 'value: can be created with an array of data');
    $this->assertSame($data['type'], $ItemValue->type, 'type: can be created with an array of data');
    $this->assertSame($data['from_profile'], $ItemValue->from_profile, 'from_profile: can be created with an array of data');
    $this->assertSame($data['from_data'], $ItemValue->from_data, 'from_data: can be created with an array of data');

		$ItemValue = new AMEE_DataItemValue( array('value'=>'1.5', 'type'=>'TEXT') );
    $this->assertSame('1.5', $ItemValue->value, 'should support TEXT data type');

		$data = array(
	    'value' => "test",
   		'type' => "TEXT",
    	'from_profile' => false,
    	'from_data' => true
    );
		$ItemValue = new AMEE_DataItemValue($data);
		$this->assertSame($data['value'], $ItemValue->value, 'value: can be created with an array of data');
    $ItemValue->value = 42;
		$this->assertSame(42, $ItemValue->value, 'value: allows value to be changed after creation');
	}

	public function mockConnection(array $data, $path=null, $method='request', Exception $Exception=null)
	{
		$Http = $this->getMock('AMEE_Http');
		$Http->expects( $this->once() )
			->method( $method )
			->will( $this->returnValue($data) );
		$controller = new AMEE_Connection($Http);
		if(!is_null($Exception)) {
			throw $Exception;
		}
		return $controller;
	}

	private function _helperCompareValuesOfArrays($data, $expected, $format)
	{
		if($format == 'XML') {
			// These are NOT set in JSON
			$this->assertSame($expected['from_profile'], $data->from_profile, 'from_profile: should be_false with ' . $format);
			$this->assertSame($expected['from_data'], $data->from_data, 'from_data: should be_true with ' . $format);
		}
		unset($expected['from_data']);
		unset($expected['from_profile']);
		foreach($expected as $k=>$v) {
			$this->assertSame($v, $data->$k, "$k: " . $format);
		}
	}

	public function testParsingXML_withAnAuthenticatedConnection()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?><Resources><DataItemValueResource><ItemValue Created="2007-08-01 09:00:41.0" Modified="2007-08-01 09:00:41.0" uid="127612FA4921"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><Value>0.1</Value><ItemValueDefinition uid="653828811D42"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="8CB8A1789CD6"><Name>kgCO2PerJourney</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition><DataItem uid="AD63A83B4D41"/></ItemValue><DataItem uid="AD63A83B4D41"/></DataItemValueResource></Resources>';
		$url = '/data/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney';
		$t = $this->mockConnection(array('body' => $xml), $url);
		$ItemValue = new AMEE_DataItemValue;
  	$data = $ItemValue->get($t, $url);
		$expected = array(
			'uid'=> '127612FA4921',
			'name'=> 'kgCO2 Per Passenger Journey',
			'path'=> '/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney',
			'full_path'=> '/data/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney',
			'created'=> '2007-08-01 09:00:41.0',
			'modified'=> '2007-08-01 09:00:41.0',
			'value'=> '0.1',
			'type'=> 'DECIMAL',
			'from_profile'=> false,
			'from_data'=> true,
		);
		$this->_helperCompareValuesOfArrays($data, $expected, 'XML');
	}
	
	public function testParsingXML_ref()
	{
		// http://my.amee.com/developers/wiki/DataItemValue 
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><Resources><DataItemValueResource><ItemValue Created="2007-08-01 09:00:41.0" Modified="2009-01-18 19:14:54.0" uid="C32B6E2EDCB0"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><Value>81.2000</Value><Unit /><PerUnit /><ItemValueDefinition uid="653828811D42"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-08-01 08:57:31.0" uid="8CB8A1789CD6"><Name>kgCO2PerJourney</Name><ValueType>DECIMAL</ValueType><Description /><Environment uid="5F5887BCF726" /></ValueDefinition><FromProfile>false</FromProfile><FromData>true</FromData></ItemValueDefinition><DataItem uid="FFC7A05D54AD" /></ItemValue><DataItem uid="FFC7A05D54AD" /><Path>/transport/plane/generic/FFC7A05D54AD/kgCO2PerPassengerJourney</Path></DataItemValueResource></Resources>';
		$url = '/data/transport/plane/generic/FFC7A05D54AD/kgCO2PerPassengerJourney';
		$t = $this->mockConnection(array('body' => $xml), $url);

		$ItemValue = new AMEE_DataItemValue();
  	$data = $ItemValue->get($t, $url);
		
		$expected = array(
			'uid'=> 'C32B6E2EDCB0',
			'name'=> 'kgCO2 Per Passenger Journey',
			'path'=> '/transport/plane/generic/FFC7A05D54AD/kgCO2PerPassengerJourney',
			'full_path'=> '/data/transport/plane/generic/FFC7A05D54AD/kgCO2PerPassengerJourney',
			'created'=> '2007-08-01 09:00:41.0',
			'modified'=> '2009-01-18 19:14:54.0',
			'value'=> '81.2000',
			'type'=> 'DECIMAL',
			'from_profile'=> false,
			'from_data'=> true,
		);
		$this->_helperCompareValuesOfArrays($data, $expected, 'XML');
	}

	public function testParsingJSON_withAnAuthenticatedConnection()
	{
		$json = '{"dataItem":{"uid":"AD63A83B4D41"},"itemValue":{"item":{"uid":"AD63A83B4D41"},"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","value":"0.1","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}}}';
		$url = '/data/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney';
		$t = $this->mockConnection(array('body' => $json), $url);

		$ItemValue = new AMEE_DataItemValue();
  	$data = $ItemValue->get($t, $url);
		
		$expected = array(
			'uid'=> '127612FA4921',
			'name'=> 'kgCO2 Per Passenger Journey',
			'path'=> '/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney',
			'full_path'=> '/data/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney',
			'created'=> '2007-08-01 09:00:41.0',
			'modified'=> '2007-08-01 09:00:41.0',
			'value'=> '0.1',
			'type'=> 'DECIMAL',
			'from_profile'=> false,
			'from_data'=> true,
		);
		$this->_helperCompareValuesOfArrays($data, $expected, 'JSON');
	}
	
	public function testParsingJSON_ref()
	{
		// http://my.amee.com/developers/wiki/DataItemValue 
		// BUG amee.com example doesn't parse as JSON!
		#$json = '{ "itemValue":{ "itemValueDefinition":{ "uid":"653828811D42", "name":"kgCO2 Per Passenger Journey", "path":"kgCO2PerPassengerJourney", "valueDefinition":{ "uid":"8CB8A1789CD6", "environment":{ "uid":"5F5887BCF726" }, "created":"2007-07-27 09:30:44.0", "description":"", "name":"kgCO2PerJourney", "valueType":"DECIMAL", "modified":"2007-08-01 08:57:31.0" } }, "perUnit":"", "uid":"C32B6E2EDCB0", "unit":"", "created":"2007-08-01 09:00:41.0", "item":{...}, "name":"kgCO2 Per Passenger Journey", "value":"81.2000", "path":"kgCO2PerPassengerJourney", "displayPath":"kgCO2PerPassengerJourney", "displayName":"kgCO2 Per Passenger Journey", "modified":"2009-01-18 19:14:54.0" }, "path":"/transport/plane/generic/FFC7A05D54AD/kgCO2PerPassengerJourney", "dataItem":{ "uid":"FFC7A05D54AD" }, "actions":{ "allowCreate":false, "allowView":true, "allowList":false, "allowModify":false, "allowDelete":false } }';
		$this->markTestIncomplete('TODO: check bug -- wrong JSON data on http://my.amee.com/developers/wiki/DataItemValue');
	}

	public function testXML_shouldFailGracefullyWithIncorrectData()
	{
		try {
			$xml = '<?xml version="1.0" encoding="UTF-8"?><Resources></Resources>';
			$data = array('body' => $xml);
			$t = $this->mockConnection($data, '/data');
			$ItemValue = new AMEE_DataItemValue();
  		$data = $ItemValue->get($t, '/data');
		} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't load DataItemValue from XML data. Check that your URL is correct.",
				$e->getMessage(),
				'should raise_error(AMEE::BadData, "Couldn\'t load DataItemValue from JSONV2 data. Check that your URL is correct.")'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testJSON_shouldFailGracefullyWithIncorrectData()
	{
		try {
			$t = $this->mockConnection(array('body' => '{'), '/data');
			$ItemValue = new AMEE_DataItemValue();
  		$data = $ItemValue->get($t, '/data');
		} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't load DataItemValue from JSON data. Check that your URL is correct.",
				$e->getMessage(), 'should raise_error(AMEE::BadData)'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function test_shouldFailGracefullyOnOtherErrors()
	{
		#$Err = @New Exception('unidentified error');
		try {
			$data = array('body' => '');
			$t = $this->mockConnection($data, '/data');
			$ItemValue = new AMEE_DataItemValue();
  		$data = $ItemValue->get($t, '/data');
		} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't load DataItemValue from JSONV2 data. Check that your URL is correct.",
				$e->getMessage(),
				'should raise_error(AMEE::BadData, "Couldn\'t load DataItemValue from JSONV2 data. Check that your URL is correct.")'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testAllowsUpdate()
	{
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2))
			->method('request')
			->will( $this->onConsecutiveCalls(
					array('body' => '{"dataItem":{"uid":"AD63A83B4D41"},"itemValue":{"item":{"uid":"AD63A83B4D41"},"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","value":"0.1","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}}}'),
					array('body' => '')
				)
			);
		$controller = new AMEE_Connection($Http);
		$ItemValue = new AMEE_DataItemValue();
		$data = $ItemValue->get($controller, '/data/transport/plane/generic/AD63A83B4D41/kgCO2PerPassengerJourney');
		try {
			$data->value = 42;
			$data->save($controller);
		} catch(Exception $e) {
			$this->fail('Failure, it should not raise an Exception: ' . $e->getMessage());
		}
	}

}