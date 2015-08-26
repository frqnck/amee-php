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
* @version $id: DataItemTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class DataItemTest extends PHPUnit_Framework_TestCase
{

	public function testIsDataObject()
	{
		$Item = new AMEE_DataItem();
		$this->assertTrue($Item instanceof AMEE_DataObject, 'should have common AMEE object properties');
		$this->assertSame(array(), $Item->values, 'values: should have values');
    $this->assertSame(array(), $Item->choices, 'choices: should have choices');
    $this->assertSame(array(), $Item->label, 'label: should have label');

    $data = array('uid' => 'ABCD1234');
		$Item = new AMEE_DataItem($data);
		$this->assertSame($data['uid'], $Item->uid, 'should initialize AMEE::Object data on creation');

		$data = array(
			'values' => array('one', 'two'),
			'choices' => array('name' => 'one', 'value' => 'two'),
    	'label' => 'test'
    );
		$Item = new AMEE_DataItem($data);
		$this->assertSame($data['values'], $Item->values, 'values: can be created with an array of data');
    $this->assertSame($data['choices'], $Item->choices, 'choices: can be created with an array of data');
    $this->assertSame($data['label'], $Item->label, 'label: can be created with an array of data');
	}

	public function mockConnection(array $data, $path=null, $method='request', Exception $Exception=null)
	{
		$controller = new AMEE_Connection($Http);
		#$controller->enable_debug = true;
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

	private function _testParsingData($data, $format)
	{
		$this->assertSame('AD63A83B4D41', $data->uid, 'uid.should == "AD63A83B4D41" with ' . $format);
		$this->assertSame('/transport/plane/generic/AD63A83B4D41', $data->path, 'path.should == "/transport/plane/generic/AD63A83B4D41" with ' . $format);
		$this->assertSame('/data/transport/plane/generic/AD63A83B4D41', $data->full_path, 'full_path.should == "/data/transport/plane/generic/AD63A83B4D41" with ' . $format);
		$this->assertSame('2007-08-01 09:00:41.0', $data->created, 'created.should == "2007-08-01 09:00:41.0" with ' . $format);
		$this->assertSame('2007-08-01 09:00:41.0', $data->modified, 'modified.should == "2007-08-01 09:00:41.0" with ' . $format);
		$this->assertSame('domestic', $data->label, 'label.should == "domestic" with ' . $format);
		$this->assertSame('441BF4BEA15B', $data->item_definition, 'item_definition.should == "441BF4BEA15B" with ' . $format);

		// Values
		$this->assertSame(5, count($data->values), 'values: count the number of values with ' . $format);
		$item = $data->values[0];
		$this->assertSame('kgCO2 Per Passenger Journey', $item['name'], 'values: get an item name with ' . $format);
		$this->assertSame('kgCO2PerPassengerJourney',  $item['path'], 'values: get an item path with ' . $format);
		$this->assertSame('0',  $item['value'], 'values: get an item value with ' . $format);
		$this->assertSame('127612FA4921', $item['uid'], 'values: get an item uid with ' . $format);

		// Choices
		$this->assertSame(6, count($data->choices), 'choices: size.should == 6 with ' . $format);
		$this->assertSame('distanceKmPerYear', $data->choices[0]['name'], 'choices[0][:name].should == "distanceKmPerYear" with ' . $format);
		$this->assertSame('', $data->choices[0]['value'], 'choices[0][:value].should be_empty with ' . $format);
		$this->assertSame('journeysPerYear', $data->choices[1]['name'], 'choices[1][:name].should == "journeysPerYear" with ' . $format);
		$this->assertSame('', $data->choices[1]['value'], 'choices[1][:value].should be_empty with ' . $format);
		$this->assertSame('lat1', $data->choices[2]['name'], 'choices[2][:name].should == "lat1" with ' . $format);
		$this->assertSame('-999', $data->choices[2]['value'], 'choices[2][:value].should == "-999" with ' . $format);
		$this->assertSame('lat2', $data->choices[3]['name'], 'choices[3][:name].should == "lat2" with ' . $format);
		$this->assertSame('-999', $data->choices[3]['value'], 'choices[3][:value].should == "-999" with ' . $format);
		$this->assertSame('long1', $data->choices[4]['name'], 'choices[4][:name].should == "long1" with ' . $format);
		$this->assertSame('-999', $data->choices[4]['value'], 'choices[4][:value].should == "-999" with ' . $format);
		$this->assertSame('long2', $data->choices[5]['name'], 'choices[5][:name].should == "long2" with ' . $format);
		$this->assertSame('-999', $data->choices[5]['value'], 'choices[5][:value].should == "-999" with ' . $format);
	}

	public function testXML_WithAnAuthenticatedConnection()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?><Resources><DataItemResource><DataItem created="2007-08-01 09:00:41.0" modified="2007-08-01 09:00:41.0" uid="AD63A83B4D41"><Name>AD63A83B4D41</Name><ItemValues><ItemValue uid="127612FA4921"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><Value>0</Value><ItemValueDefinition uid="653828811D42"><Path>kgCO2PerPassengerJourney</Path><Name>kgCO2 Per Passenger Journey</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="8CB8A1789CD6"><Name>kgCO2PerJourney</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="7F27A5707101"><Path>kgCO2PerPassengerKm</Path><Name>kgCO2 Per Passenger Km</Name><Value>0.158</Value><ItemValueDefinition uid="D7B4340D9404"><Path>kgCO2PerPassengerKm</Path><Name>kgCO2 Per Passenger Km</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="996AE5477B3F"><Name>kgCO2PerKm</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="FF50EC918A8E"><Path>size</Path><Name>Size</Name><Value>-</Value><ItemValueDefinition uid="5D7FB5F552A5"><Path>size</Path><Name>Size</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="FDD62D27AA15"><Path>type</Path><Name>Type</Name><Value>domestic</Value><ItemValueDefinition uid="C376560CB19F"><Path>type</Path><Name>Type</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="9BE08FBEC54E"><Path>source</Path><Name>Source</Name><Value>DfT INAS Division, 29 March 2007</Value><ItemValueDefinition uid="0F0592F05AAC"><Path>source</Path><Name>Source</Name><FromProfile>false</FromProfile><FromData>true</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue></ItemValues><Environment uid="5F5887BCF726"/><ItemDefinition uid="441BF4BEA15B"/><DataCategory uid="FBA97B70DBDF"><Name>Generic</Name><Path>generic</Path></DataCategory><Path>AD63A83B4D41</Path><Label>domestic</Label></DataItem><Path>/transport/plane/generic/AD63A83B4D41</Path><Choices><Name>userValueChoices</Name><Choices><Choice><Name>distanceKmPerYear</Name><Value/></Choice><Choice><Name>journeysPerYear</Name><Value/></Choice><Choice><Name>lat1</Name><Value>-999</Value></Choice><Choice><Name>lat2</Name><Value>-999</Value></Choice><Choice><Name>long1</Name><Value>-999</Value></Choice><Choice><Name>long2</Name><Value>-999</Value></Choice></Choices></Choices><AmountPerMonth>0.000</AmountPerMonth></DataItemResource></Resources>';
		$url = '/data/transport/plane/generic/AD63A83B4D41';
		$t = $this->mockConnection(array('body' => $xml), $url);
		$Item = new AMEE_DataItem();
  	$data = $Item->get($t, $url);
		$this->_testParsingData($data, 'XML');
	}

	public function testJSON_WithAnAuthenticatedConnection()
	{
		$json = '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}';
		$url = '/data/transport/plane/generic/AD63A83B4D41';
		$t = $this->mockConnection(array('body' => $json), $url);
		$Item = new AMEE_DataItem();
  	$data = $Item->get($t, $url);
		$this->_testParsingData($data, 'JSON');
	}

	public function testXML_shouldFailGracefullyWithIncorrectData()
	{
		try {
			$xml = '<?xml version="1.0" encoding="UTF-8"?><Resources></Resources>';
			$json = '{}';
			$data = array('body' => $xml);
			$t = $this->mockConnection($data, '/data');
			$Item = new AMEE_DataItem();
  		$data = $Item->get($t, '/data');
		} catch(AMEE_Exception $e) {
			$this->assertSame("Couldn't load DataItem from XML data. Check that your URL is correct.",
				$e->getMessage(),
				'should raise_error(AMEE::BadData, "Couldn\'t load DataItem from XML. Check that your URL is correct.")'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testJSON_shouldFailGracefullyWithIncorrectData()
	{
		try {
			$t = $this->mockConnection(array('body' => '{'), '/data');
			$Item = new AMEE_DataItem();
  		$data = $Item->get($t, '/data');
		} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't load DataItem from JSON data. Check that your URL is correct.",
				$e->getMessage(), 'should raise_error(AMEE::BadData)'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testShouldFailGracefullyWithIncorrectData()
	{
		try {
			$data = array('body' => '');
			$t = $this->mockConnection($data, '/data');
			$Item = new AMEE_DataItem();
  		$data = $Item->get($t, '/data');
			} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't load DataItem from JSONV2 data. Check that your URL is correct.",
				$e->getMessage(), 'should raise_error(AMEE::BadData)'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
		/*
		describe AMEE::Data::Item, "with an authenticated connection" do
	  it "should fail gracefully on other errors" do
	    connection = flexmock "connection"
	    connection.should_receive(:get).with("/data", {}).and_raise("unidentified error")
	    lambda{AMEE::Data::Item.get(connection, "/data")}.should raise_error(AMEE::BadData, "Couldn't load DataItem. Check that your URL is correct.")
	  end
		end
		*/
	}

	public function testwithSensibleData()
	{
		$json = '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}';
		$url = '/data/transport/plane/generic/AD63A83B4D41';
		$data = array('body' => $json);
		$t = $this->mockConnection($data, $url);
		$Item = new AMEE_DataItem();
 		$data = $Item->get($t, $url);
		$this->assertNotNull($data->value('kgCO2 Per Passenger Km'), 'allows client to get a value by name, value("kgCO2 Per Passenger Km").should_not be_nil');
		$this->assertNotNull($data->value('Source'), 'allows client to get a value by name, value("Source").should_not be_nil');
		$this->assertNotNull($data->value('kgCO2PerPassengerKm'), 'allows client to get a value by path, value("kgCO2PerPassengerKm").should_not be_nil');
		$this->assertNotNull($data->value('source'), 'allows client to get a value by path, value("source").should_not be_nil');
	}

	public function testAllowsUpdate()
	{
		$controller = new AMEE_Connection();
		#$controller->enable_debug = true;
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2))
			->method('request')
			->will( $this->onConsecutiveCalls(
					array('body' => '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}'),
					array('body' => '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.159","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}')
				)
			);
		$controller->setHttp($Http);

		$Item = new AMEE_DataItem();
		$data = $Item->get($controller, '/data/transport/plane/generic/AD63A83B4D41');
		$this->assertSame('0.158', $data->value('kgCO2PerPassengerKm'), 'value("kgCO2PerPassengerKm").should == "0.158"');
		$res = $data->update($controller, array('kgCO2PerPassengerKm'=>'0.159'));
		// as per amee-ruby from return value:
		$this->assertSame('0.159', $res->value('kgCO2PerPassengerKm'), 'once updated, from return value');
		// FIX: ideally should be from same object
		#$this->assertSame('0.159', $data->value('kgCO2PerPassengerKm'), 'once updated, from same object');
	}

	public function testshouldFailGracefullyIfUpdateFails()
	{
		try {
			$Http = $this->getMock('AMEE_Http');
			$Http->expects($this->exactly(2))
				->method('request')
			->will( $this->onConsecutiveCalls(
					array('body' => '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}'),
					array('body' => '{}')
				)
			);
			$controller = new AMEE_Connection($Http);
			$Item = new AMEE_DataItem();
			$data = $Item->get($controller, '/data/transport/plane/generic/AD63A83B4D41');
			$data = $data->update($controller, array('kgCO2PerPassengerKm'=>'0.159'));
		} catch(AMEE_BadDataException $e) {
			$this->assertSame(
				"Couldn't update DataItem. Check that your information is correct.",
				$e->getMessage()
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

}