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
* @version $id: ProfileItemTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class ProfileItemTest extends PHPUnit_Framework_TestCase
{

	public function mockConnection(array $data, $path=null, $method='request', Exception $Exception=null)
	{
		$controller = new AMEE_Connection($Http);
		#$controller->enable_debug = true;
		$Http = $this->getMock('AMEE_Http');
		$Http->expects( $this->once() )
			->method( $method )
			->will( $this->returnValue($data) );
		$controller = new AMEE_Connection($Http);
		if(!is_null($Exception)) throw $Exception;
		return $controller;
	}

	public function testIsDataObject()
	{
		$Item = new AMEE_ProfileItem();
		$this->assertTrue($Item instanceof AMEE_ProfileObject, 'should have common AMEE object properties');
		$this->assertSame(array(), $Item->values, 'values: should have values');

    $data = array('uid' => 'ABCD1234');
		$Item = new AMEE_ProfileItem($data);
		$this->assertSame($data['uid'], $Item->uid, 'should initialize AMEE::Object data on creation');

		$data = array('values' => array('one', 'two'));
		$Item = new AMEE_ProfileItem($data);
		$this->assertSame($data['values'], $Item->values, 'values: can be created with an array of data');
	}

	private function _testParsingData($data, $format)
	{
		$this->assertSame(2.06, $data->total_amount, 'total_amount.should be_close(2.06, 1e-9) with ' . $format);
		$this->assertSame('kg/month', $data->total_amount_unit, 'total_amount_unit == "kg/month" with ' . $format);
	}

	public function testShouldbeAbleToCreateNewProfileItems()
	{
		$data['xml'] = array(
			'get'=> array(	
				'url'=>'/profiles/E54C5525AA3E/home/energy/quantity',
				'params' =>array(),
				'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home/energy/quantity</Path><ProfileDate>200901</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="A92693A99BAD"><Name>Quantity</Name><Path>quantity</Path></DataCategory><Children><ProfileCategories/><ProfileItems/><Pager><Start>0</Start><From>0</From><To>0</To><Items>0</Items><CurrentPage>1</CurrentPage><RequestedPage>1</RequestedPage><NextPage>-1</NextPage><PreviousPage>-1</PreviousPage><LastPage>1</LastPage><ItemsPerPage>10</ItemsPerPage><ItemsFound>0</ItemsFound></Pager></Children><TotalAmountPerMonth>0.000</TotalAmountPerMonth></ProfileCategoryResource></Resources>'
			),
			'post'=>array(	
				'url'=>'/profiles/E54C5525AA3E/home/energy/quantity',
				'params' =>array('dataItemUid' => '66056991EE23', 'kWhPerMonth' => "10"),
				'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home/energy/quantity</Path><ProfileDate>200901</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="A92693A99BAD"><Name>Quantity</Name><Path>quantity</Path></DataCategory><ProfileItem uid="62BCC8C84D0C"><Name>62BCC8C84D0C</Name><ItemValues><ItemValue uid="D281CE71180D"><Path>kgPerMonth</Path><Name>kg Per Month</Name><Value>0</Value><ItemValueDefinition uid="51D072825D41"><Path>kgPerMonth</Path><Name>kg Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="36A771FC1D1A"><Name>kg</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="67DD1D3A00C4"><Path>kWhPerMonth</Path><Name>kWh Per Month</Name><Value>10</Value><ItemValueDefinition uid="4DF458FD0E4D"><Path>kWhPerMonth</Path><Name>kWh Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="26A5C97D3728"><Name>kWh</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="2BEEF89EFCAB"><Path>litresPerMonth</Path><Name>Litres Per Month</Name><Value>0</Value><ItemValueDefinition uid="C9B7E19269A5"><Path>litresPerMonth</Path><Name>Litres Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="06B8CFC5A521"><Name>litre</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="7C8968300299"><Path>kWhReadingCurrent</Path><Name>kWh reading current</Name><Value>0</Value><ItemValueDefinition uid="8A468E75C8E8"><Path>kWhReadingCurrent</Path><Name>kWh reading current</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="F68E3F8F988B"><Path>kWhReadingLast</Path><Name>kWh reading last</Name><Value>0</Value><ItemValueDefinition uid="2328DC7F23AE"><Path>kWhReadingLast</Path><Name>kWh reading last</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="1932882328F0"><Path>paymentFrequency</Path><Name>Payment frequency</Name><Value/><ItemValueDefinition uid="E0EFED6FD7E6"><Path>paymentFrequency</Path><Name>Payment frequency</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="826512435B43"><Path>greenTariff</Path><Name>Green tariff</Name><Value/><ItemValueDefinition uid="63005554AE8A"><Path>greenTariff</Path><Name>Green tariff</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="4D2D471F2F04"><Path>season</Path><Name>Season</Name><Value/><ItemValueDefinition uid="527AADFB3B65"><Path>season</Path><Name>Season</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="583D56F63CD5"><Path>includesHeating</Path><Name>Includes Heating</Name><Value>false</Value><ItemValueDefinition uid="1740E500BDAB"><Path>includesHeating</Path><Name>Includes Heating</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="95DF5F127142"><Path>numberOfDeliveries</Path><Name>Number of deliveries</Name><Value/><ItemValueDefinition uid="AA1D1C349119"><Path>numberOfDeliveries</Path><Name>Number of deliveries</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue></ItemValues><AmountPerMonth>2.060</AmountPerMonth><ValidFrom>20090101</ValidFrom><End>false</End><DataItem uid="66056991EE23"/></ProfileItem></ProfileCategoryResource></Resources>'
			),
			'get'=>array(
				'url'	=> '/profiles/E54C5525AA3E/home/energy/quantity/62BCC8C84D0C',
				'params' =>array(),
				'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileItemResource><ProfileItem created="2009-01-28 23:35:00.0" modified="2009-01-28 23:35:00.0" uid="62BCC8C84D0C"><Name>62BCC8C84D0C</Name><ItemValues><ItemValue uid="95DF5F127142"><Path>numberOfDeliveries</Path><Name>Number of deliveries</Name><Value/><ItemValueDefinition uid="AA1D1C349119"><Path>numberOfDeliveries</Path><Name>Number of deliveries</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="4D2D471F2F04"><Path>season</Path><Name>Season</Name><Value/><ItemValueDefinition uid="527AADFB3B65"><Path>season</Path><Name>Season</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="583D56F63CD5"><Path>includesHeating</Path><Name>Includes Heating</Name><Value>false</Value><ItemValueDefinition uid="1740E500BDAB"><Path>includesHeating</Path><Name>Includes Heating</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="826512435B43"><Path>greenTariff</Path><Name>Green tariff</Name><Value/><ItemValueDefinition uid="63005554AE8A"><Path>greenTariff</Path><Name>Green tariff</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="1932882328F0"><Path>paymentFrequency</Path><Name>Payment frequency</Name><Value/><ItemValueDefinition uid="E0EFED6FD7E6"><Path>paymentFrequency</Path><Name>Payment frequency</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="F68E3F8F988B"><Path>kWhReadingLast</Path><Name>kWh reading last</Name><Value>0</Value><ItemValueDefinition uid="2328DC7F23AE"><Path>kWhReadingLast</Path><Name>kWh reading last</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="7C8968300299"><Path>kWhReadingCurrent</Path><Name>kWh reading current</Name><Value>0</Value><ItemValueDefinition uid="8A468E75C8E8"><Path>kWhReadingCurrent</Path><Name>kWh reading current</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="D281CE71180D"><Path>kgPerMonth</Path><Name>kg Per Month</Name><Value>0</Value><ItemValueDefinition uid="51D072825D41"><Path>kgPerMonth</Path><Name>kg Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="36A771FC1D1A"><Name>kg</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="67DD1D3A00C4"><Path>kWhPerMonth</Path><Name>kWh Per Month</Name><Value>10</Value><ItemValueDefinition uid="4DF458FD0E4D"><Path>kWhPerMonth</Path><Name>kWh Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="26A5C97D3728"><Name>kWh</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue><ItemValue uid="2BEEF89EFCAB"><Path>litresPerMonth</Path><Name>Litres Per Month</Name><Value>0</Value><ItemValueDefinition uid="C9B7E19269A5"><Path>litresPerMonth</Path><Name>Litres Per Month</Name><FromProfile>true</FromProfile><FromData>false</FromData><ValueDefinition uid="06B8CFC5A521"><Name>litre</Name><ValueType>DECIMAL</ValueType></ValueDefinition></ItemValueDefinition></ItemValue></ItemValues><Environment uid="5F5887BCF726"/><ItemDefinition uid="212C818D8F16"/><DataCategory uid="A92693A99BAD"><Name>Quantity</Name><Path>quantity</Path></DataCategory><AmountPerMonth>2.060</AmountPerMonth><ValidFrom>20090101</ValidFrom><End>false</End><DataItem uid="66056991EE23"/><Profile uid="E54C5525AA3E"/></ProfileItem><Path>/home/energy/quantity/62BCC8C84D0C</Path><Profile uid="E54C5525AA3E"/></ProfileItemResource></Resources>'
			)
		);
		$data['json'] = array(
			'get'=> array(	
				'url'=>'/profiles/E54C5525AA3E/home/energy/quantity',
				'params' =>array(),
				'body' => '{"totalAmountPerMonth":0,"dataCategory":{"uid":"A92693A99BAD","path":"quantity","name":"Quantity"},"profileDate":"200901","path":"/home/energy/quantity","profile":{"uid":"E54C5525AA3E"},"children":{"pager":{"to":0,"lastPage":1,"start":0,"nextPage":-1,"items":0,"itemsPerPage":10,"from":0,"previousPage":-1,"requestedPage":1,"currentPage":1,"itemsFound":0},"dataCategories":[],"profileItems":{"rows":[],"label":"ProfileItems"}}}'
			),
			'post'=>array(	
				'url'=>'/profiles/E54C5525AA3E/home/energy/quantity',
				'params' =>array('dataItemUid' => '66056991EE23', 'kWhPerMonth' => "10"),
				'body' => '{"dataCategory":{"uid":"A92693A99BAD","path":"quantity","name":"Quantity"},"profileDate":"200901","path":"/home/energy/quantity","profile":{"uid":"E54C5525AA3E"},"profileItem":{"validFrom":"20090101","amountPerMonth":2.06,"itemValues":[{"value":"0","uid":"01591644B296","path":"kgPerMonth","name":"kg Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"36A771FC1D1A","name":"kg"},"uid":"51D072825D41","path":"kgPerMonth","name":"kg Per Month"}},{"value":"10","uid":"94B617C13137","path":"kWhPerMonth","name":"kWh Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"26A5C97D3728","name":"kWh"},"uid":"4DF458FD0E4D","path":"kWhPerMonth","name":"kWh Per Month"}},{"value":"0","uid":"1F5AF1A6BD65","path":"litresPerMonth","name":"Litres Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"06B8CFC5A521","name":"litre"},"uid":"C9B7E19269A5","path":"litresPerMonth","name":"Litres Per Month"}},{"value":"0","uid":"B2FBB1BFF60F","path":"kWhReadingCurrent","name":"kWh reading current","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"8A468E75C8E8","path":"kWhReadingCurrent","name":"kWh reading current"}},{"value":"0","uid":"A97ADD0FCB82","path":"kWhReadingLast","name":"kWh reading last","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"2328DC7F23AE","path":"kWhReadingLast","name":"kWh reading last"}},{"value":"","uid":"1D96093AD6D7","path":"paymentFrequency","name":"Payment frequency","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"E0EFED6FD7E6","path":"paymentFrequency","name":"Payment frequency"}},{"value":"","uid":"ED12DF35A1C3","path":"greenTariff","name":"Green tariff","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"63005554AE8A","path":"greenTariff","name":"Green tariff"}},{"value":"","uid":"9494FB0F7DE8","path":"season","name":"Season","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"527AADFB3B65","path":"season","name":"Season"}},{"value":"false","uid":"ECB936330FEF","path":"includesHeating","name":"Includes Heating","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"1740E500BDAB","path":"includesHeating","name":"Includes Heating"}},{"value":"","uid":"C85E51E8D26C","path":"numberOfDeliveries","name":"Number of deliveries","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"AA1D1C349119","path":"numberOfDeliveries","name":"Number of deliveries"}}],"end":"false","uid":"8C7BD1AB69D3","dataItem":{"uid":"66056991EE23"},"name":"8C7BD1AB69D3"}}'
			),
			'get2'=>array(
				'url'	=> '/profiles/E54C5525AA3E/home/energy/quantity/8C7BD1AB69D3',
				'params' =>array(),
				'body' => '{"path":"/home/energy/quantity/8C7BD1AB69D3","profile":{"uid":"E54C5525AA3E"},"profileItem":{"created":"2009-01-29 00:11:33.0","itemValues":[{"value":"","uid":"9494FB0F7DE8","path":"season","name":"Season","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"527AADFB3B65","path":"season","name":"Season"}},{"value":"false","uid":"ECB936330FEF","path":"includesHeating","name":"Includes Heating","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"1740E500BDAB","path":"includesHeating","name":"Includes Heating"}},{"value":"","uid":"C85E51E8D26C","path":"numberOfDeliveries","name":"Number of deliveries","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"AA1D1C349119","path":"numberOfDeliveries","name":"Number of deliveries"}},{"value":"","uid":"ED12DF35A1C3","path":"greenTariff","name":"Green tariff","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"63005554AE8A","path":"greenTariff","name":"Green tariff"}},{"value":"","uid":"1D96093AD6D7","path":"paymentFrequency","name":"Payment frequency","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"E0EFED6FD7E6","path":"paymentFrequency","name":"Payment frequency"}},{"value":"0","uid":"A97ADD0FCB82","path":"kWhReadingLast","name":"kWh reading last","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"2328DC7F23AE","path":"kWhReadingLast","name":"kWh reading last"}},{"value":"0","uid":"B2FBB1BFF60F","path":"kWhReadingCurrent","name":"kWh reading current","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"45433E48B39F","name":"amount"},"uid":"8A468E75C8E8","path":"kWhReadingCurrent","name":"kWh reading current"}},{"value":"0","uid":"01591644B296","path":"kgPerMonth","name":"kg Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"36A771FC1D1A","name":"kg"},"uid":"51D072825D41","path":"kgPerMonth","name":"kg Per Month"}},{"value":"0","uid":"1F5AF1A6BD65","path":"litresPerMonth","name":"Litres Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"06B8CFC5A521","name":"litre"},"uid":"C9B7E19269A5","path":"litresPerMonth","name":"Litres Per Month"}},{"value":"10","uid":"94B617C13137","path":"kWhPerMonth","name":"kWh Per Month","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"26A5C97D3728","name":"kWh"},"uid":"4DF458FD0E4D","path":"kWhPerMonth","name":"kWh Per Month"}}],"dataCategory":{"uid":"A92693A99BAD","path":"quantity","name":"Quantity"},"end":"false","uid":"8C7BD1AB69D3","environment":{"uid":"5F5887BCF726"},"profile":{"uid":"E54C5525AA3E"},"modified":"2009-01-29 00:11:33.0","validFrom":"20090101","amountPerMonth":2.06,"itemDefinition":{"uid":"212C818D8F16"},"dataItem":{"uid":"66056991EE23"},"name":"8C7BD1AB69D3"}}'
			)
		);

		// connect		$t = $this->mockConnection(array('body' => $xml), '/data/transport/plane/generic/AD63A83B4D41');

		$controller = new AMEE_Connection();
		$controller->enable_debug = false;
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2)) // 3
			->method('request')
			#->with($this->stringContains($data['json']['get']['url']))
			#->with($this->stringContains($data['json']['post']['url']))
			#->with($this->stringContains($data['json']['get']['url']))
			->will( $this->onConsecutiveCalls(
					array('body' => $data['json']['get']['body']),
					array('body' => $data['json']['post']['body']),
					array('body' => $data['json']['get2']['body'])
				)
			);
		$controller->setHttp($Http);

    $Cat = new AMEE_ProfileCategory();
    $Category = $Cat->get($controller, '/profiles/E54C5525AA3E/home/energy/quantity');

 		$Item = new AMEE_ProfileItem();
    $data = $Item->create($controller, $Category, '66056991EE23', array('kWhPerMonth' => '10'));
		$this->_testParsingData($data, 'JSONV2');

		$this->markTestIncomplete('TODO: all of profile_item_spec.rb');
	}

	public function testshouldFailGracefullyWithIncorrectData()
	{
		try {
			$data = array('body' => null);
			$t = $this->mockConnection($data, '/data');
			$Obj = new AMEE_ProfileItem;
  		$data = $Obj->get($t, '/data');
			#new Exception('unidentified error');
			} catch(AMEE_BadDataException $e) {
			$this->assertSame(
				"Couldn't load ProfileItem from JSONV2 data. Check that your URL is correct.",
				$e->getMessage(),
				'should raise_error(AMEE::BadData)'
			);
			return;
		}
		$this->fail('An expected exception has not been raised.');
		/*
		describe AMEE::Data::Item, "with an authenticated connection" do
	  it "should fail gracefully on other errors" do
	    connection = flexmock "connection"
	    connection.should_receive(:get).with("/data", {}).and_raise("unidentified error")
	    lambda{AMEE::Data::Item.get(connection, "/data")}.should raise_error(AMEE::BadData, "Couldn't load ProfileItem. Check that your URL is correct.")
	  end
		end
		*/
	}

}