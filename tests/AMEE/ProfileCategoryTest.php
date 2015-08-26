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
* @version $id: ProfileCategoryTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class ProfileCategoryTest extends PHPUnit_Framework_TestCase
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
		$Cat = new AMEE_ProfileCategory();
		$this->assertTrue($Cat instanceof AMEE_ProfileObject, 'should have common AMEE object properties');
		$this->assertSame('', $Cat->total_amount, 'should have a total co2 amount');
		$this->assertSame('', $Cat->total_amount_unit, 'should have a total co2 unit');
		$this->assertSame(array(), $Cat->children, 'should have children');
		$this->assertSame(array(), $Cat->items, 'should have items');

    $data = array('uid' => 'ABCD1234');
		$Cat = new AMEE_ProfileCategory($data);
		$this->assertSame($data['uid'], $Cat->uid, 'should initialize AMEE::Object data on creation');

		$data = array( 'children' => array('one', 'two'), 'items' => array('three', 'four') );
		$Cat = new AMEE_ProfileCategory($data);
		$this->assertSame($data['children'], $Cat->children, 'children: can be created with an array of data');
		$this->assertSame($data['items'], $Cat->items, 'items: can be created with an array of data');
	}


	// describe AMEE::Profile::Category, "with an authenticated XML connection" do

	public function testXML_shouldLoadProfileCategory()
	{
		$io = array(
			'xml_v1' => array(
				'url'=>'/profiles/E54C5525AA3E/home',
				'body'=>'<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home</Path><ProfileDate>200809</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="BBA3AC3E795E"><Name>Home</Name><Path>home</Path></DataCategory><Children><ProfileCategories><DataCategory uid="427DFCC65E52"><Name>Appliances</Name><Path>appliances</Path></DataCategory><DataCategory uid="30BA55A0C472"><Name>Energy</Name><Path>energy</Path></DataCategory><DataCategory uid="A46ECFA19333"><Name>Heating</Name><Path>heating</Path></DataCategory><DataCategory uid="150266DD4434"><Name>Lighting</Name><Path>lighting</Path></DataCategory></ProfileCategories></Children></ProfileCategoryResource></Resources>'
			),
			'xml_v2' => array(
				'url'=>'/profiles/26532D8EFA9D/home',
				'body'=>'<?xml version="1.0" encoding="UTF-8"?> <Resources xmlns="http://schemas.amee.cc/2.0"><ProfileCategoryResource><Path>/home</Path><Profile uid="26532D8EFA9D"/><Environment created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="5F5887BCF726"><Name>AMEE</Name><Path/><Description/><Owner/><ItemsPerPage>10</ItemsPerPage><ItemsPerFeed>10</ItemsPerFeed></Environment><DataCategory uid="BBA3AC3E795E"><Name>Home</Name><Path>home</Path></DataCategory><ProfileCategories><DataCategory uid="427DFCC65E52"><Name>Appliances</Name><Path>appliances</Path></DataCategory><DataCategory uid="30BA55A0C472"><Name>Energy</Name><Path>energy</Path></DataCategory><DataCategory uid="A46ECFA19333"><Name>Heating</Name><Path>heating</Path></DataCategory><DataCategory uid="150266DD4434"><Name>Lighting</Name><Path>lighting</Path></DataCategory><DataCategory uid="6553150F96CE"><Name>Waste</Name><Path>waste</Path></DataCategory><DataCategory uid="07362DCC9E7B"><Name>Water</Name><Path>water</Path></DataCategory></ProfileCategories></ProfileCategoryResource></Resources>'
			) 
		);

		// V1 XML
		$t = $this->mockConnection(array('body'=>$io['xml_v1']['body']), $io['xml_v1']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['xml_v1']['url']);
		$this->assertSame('E54C5525AA3E', $data->profile_uid);
		$this->assertSame('200809', $data->profile_date);
		$this->assertSame('Home', $data->name);
		$this->assertSame('/home', $data->path);
		$this->assertSame('/profiles/E54C5525AA3E/home', $data->full_path);
		$this->assertSame(4, count($data->children));
		$this->assertSame('427DFCC65E52', $data->children[0]['uid']);
		$this->assertSame('Appliances', $data->children[0]['name']);
		$this->assertSame('appliances', $data->children[0]['path']);

		// V2 XML
		$t = $this->mockConnection(array('body'=>$io['xml_v2']['body']), $io['xml_v2']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['xml_v2']['url']);
		$this->assertSame('26532D8EFA9D', $data->profile_uid);
		$this->assertSame('Home', $data->name);
		$this->assertSame('/home', $data->path);
		$this->assertSame('/profiles/26532D8EFA9D/home', $data->full_path);
		$this->assertSame(6, count($data->children));
		$this->assertSame('427DFCC65E52', $data->children[0]['uid']);
		$this->assertSame('Appliances', $data->children[0]['name']);
		$this->assertSame('appliances', $data->children[0]['path']);
	}

	public function testXML_shouldProvideAccessToChildObjects()
	{
		$io = array(
			'xml' => array(
				array('url'=>'/profiles/E54C5525AA3E/home', 'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home</Path><ProfileDate>200809</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="BBA3AC3E795E"><Name>Home</Name><Path>home</Path></DataCategory><Children><ProfileCategories><DataCategory uid="427DFCC65E52"><Name>Appliances</Name><Path>appliances</Path></DataCategory><DataCategory uid="30BA55A0C472"><Name>Energy</Name><Path>energy</Path></DataCategory><DataCategory uid="A46ECFA19333"><Name>Heating</Name><Path>heating</Path></DataCategory><DataCategory uid="150266DD4434"><Name>Lighting</Name><Path>lighting</Path></DataCategory></ProfileCategories></Children></ProfileCategoryResource></Resources>'),
				array('url'=>'/profiles/E54C5525AA3E/home/appliances', 'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home/appliances</Path><ProfileDate>200809</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="427DFCC65E52"><Name>Appliances</Name><Path>appliances</Path></DataCategory><Children><ProfileCategories><DataCategory uid="3FE23FDC8CEA"><Name>Computers</Name><Path>computers</Path></DataCategory><DataCategory uid="54C8A44254AA"><Name>Cooking</Name><Path>cooking</Path></DataCategory><DataCategory uid="75AD9B83B7BF"><Name>Entertainment</Name><Path>entertainment</Path></DataCategory><DataCategory uid="4BD595E1873A"><Name>Kitchen</Name><Path>kitchen</Path></DataCategory><DataCategory uid="700D0771870A"><Name>Televisions</Name><Path>televisions</Path></DataCategory></ProfileCategories></Children></ProfileCategoryResource></Resources>')
			)
		);
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2))
			->method('request')
			->will( $this->onConsecutiveCalls(
					array('body' => $io['xml'][0]['body']),
					array('body' =>  $io['xml'][1]['body'])
				)
			);
		$controller = new AMEE_Connection($Http);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($controller, $io['xml'][0]['url']);
    $child = $Cat->child('appliances');
		$this->assertSame('/home/appliances', $child->path);
		$this->assertSame($io['xml'][1]['url'], $child->full_path);
		$this->assertSame(5, count($child->children));
	}

	public function testXML_shouldParseDataItems()
	{
		$io = array(
			'xml_v1' => array('url'=>'/profiles/E54C5525AA3E/home/energy/quantity', 'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><ProfileCategoryResource><Path>/home/energy/quantity</Path><ProfileDate>200809</ProfileDate><Profile uid="E54C5525AA3E"/><DataCategory uid="A92693A99BAD"><Name>Quantity</Name><Path>quantity</Path></DataCategory><Children><ProfileCategories/><ProfileItems><ProfileItem created="2008-09-03 11:37:35.0" modified="2008-09-03 11:38:12.0" uid="FB07247AD937"><validFrom>20080902</validFrom><end>false</end><kWhPerMonth>12</kWhPerMonth><amountPerMonth>2.472</amountPerMonth><dataItemUid>66056991EE23</dataItemUid><kgPerMonth>0</kgPerMonth><path>FB07247AD937</path><litresPerMonth>0</litresPerMonth><name>gas</name><dataItemLabel>gas</dataItemLabel></ProfileItem><ProfileItem created="2008-09-03 11:40:44.0" modified="2008-09-03 11:41:54.0" uid="D9CBCDED44C5"><validFrom>20080901</validFrom><end>false</end><kWhPerMonth>500</kWhPerMonth><amountPerMonth>103.000</amountPerMonth><dataItemUid>66056991EE23</dataItemUid><kgPerMonth>0</kgPerMonth><path>D9CBCDED44C5</path><litresPerMonth>0</litresPerMonth><name>gas2</name><dataItemLabel>gas</dataItemLabel></ProfileItem></ProfileItems><Pager><Start>0</Start><From>1</From><To>2</To><Items>2</Items><CurrentPage>1</CurrentPage><RequestedPage>1</RequestedPage><NextPage>-1</NextPage><PreviousPage>-1</PreviousPage><LastPage>1</LastPage><ItemsPerPage>10</ItemsPerPage><ItemsFound>2</ItemsFound></Pager></Children><TotalAmountPerMonth>105.472</TotalAmountPerMonth></ProfileCategoryResource></Resources>'),
			'xml_v2' => array('url'=>'/profiles/26532D8EFA9D/home/energy/quantity', 'body' => '<?xml version="1.0" encoding="UTF-8"?> <Resources xmlns="http://schemas.amee.cc/2.0"><ProfileCategoryResource><Path>/home/energy/quantity</Path><Profile uid="9BFB0C1CD78A"/><Environment created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="5F5887BCF726"><Name>AMEE</Name><Path/><Description/><Owner/><ItemsPerPage>10</ItemsPerPage><ItemsPerFeed>10</ItemsPerFeed></Environment><DataCategory uid="A92693A99BAD"><Name>Quantity</Name><Path>quantity</Path></DataCategory><ProfileCategories/><ProfileItems><ProfileItem created="2009-02-11T13:50+0000" modified="2009-02-11T13:50+0000" uid="30C00AD33033"><Name>gas</Name><ItemValues><ItemValue uid="570843C78E93"><Path>paymentFrequency</Path><Name>Payment frequency</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="E0EFED6FD7E6"><Path>paymentFrequency</Path><Name>Payment frequency</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="C71B646F8502"><Path>greenTariff</Path><Name>Green tariff</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="63005554AE8A"><Path>greenTariff</Path><Name>Green tariff</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="8231B25E416F"><Path>season</Path><Name>Season</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="527AADFB3B65"><Path>season</Path><Name>Season</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="AE5413F7A459"><Path>includesHeating</Path><Name>Includes Heating</Name><Value>false</Value><Unit/><PerUnit/><ItemValueDefinition uid="1740E500BDAB"><Path>includesHeating</Path><Name>Includes Heating</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="20FC928B045A"><Path>massPerTime</Path><Name>Mass Per Time</Name><Value/><Unit>kg</Unit><PerUnit>year</PerUnit><ItemValueDefinition uid="80F561BE56E2"><Path>massPerTime</Path><Name>Mass Per Time</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kg</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="3835DF705F9D"><Path>energyConsumption</Path><Name>Energy Consumption</Name><Value>13</Value><Unit>kWh</Unit><PerUnit>month</PerUnit><ItemValueDefinition uid="9801C6552128"><Path>energyConsumption</Path><Name>Energy Consumption</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="064C0DB99F75"><Path>currentReading</Path><Name>Current Reading</Name><Value/><Unit>kWh</Unit><PerUnit/><ItemValueDefinition uid="6EF1FF3361F0"><Path>currentReading</Path><Name>Current Reading</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="06B35B089155"><Path>lastReading</Path><Name>Last Reading</Name><Value/><Unit>kWh</Unit><PerUnit/><ItemValueDefinition uid="7DDB0BB0B6CA"><Path>lastReading</Path><Name>Last Reading</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="CEBEA6C086B8"><Path>volumePerTime</Path><Name>Volume Per Time</Name><Value/><Unit>L</Unit><PerUnit>year</PerUnit><ItemValueDefinition uid="CDA01AFCF91B"><Path>volumePerTime</Path><Name>Volume Per Time</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>L</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="0E77ACB084A5"><Path>deliveries</Path><Name>Number of deliveries</Name><Value/><Unit>year</Unit><PerUnit/><ItemValueDefinition uid="DEB369A7AD4E"><Path>deliveries</Path><Name>Number of deliveries</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>year</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue></ItemValues><Amount unit="kg/year">29.664</Amount><StartDate>2008-09-02T01:00+0100</StartDate><EndDate/><DataItem uid="66056991EE23"><Label>gas</Label></DataItem></ProfileItem><ProfileItem created="2009-02-11T14:13+0000" modified="2009-02-11T14:13+0000" uid="BC0B730255FB"><Name>BC0B730255FB</Name><ItemValues><ItemValue uid="C3DEE5535925"><Path>paymentFrequency</Path><Name>Payment frequency</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="E0EFED6FD7E6"><Path>paymentFrequency</Path><Name>Payment frequency</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="DFBD9254685A"><Path>greenTariff</Path><Name>Green tariff</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="63005554AE8A"><Path>greenTariff</Path><Name>Green tariff</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="CB8705EB55C5"><Path>season</Path><Name>Season</Name><Value/><Unit/><PerUnit/><ItemValueDefinition uid="527AADFB3B65"><Path>season</Path><Name>Season</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="A282A7875AEB"><Path>includesHeating</Path><Name>Includes Heating</Name><Value>false</Value><Unit/><PerUnit/><ItemValueDefinition uid="1740E500BDAB"><Path>includesHeating</Path><Name>Includes Heating</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CCEB59CACE1B"><Name>text</Name><ValueType>TEXT</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="2624A9954F17"><Path>massPerTime</Path><Name>Mass Per Time</Name><Value/><Unit>kg</Unit><PerUnit>year</PerUnit><ItemValueDefinition uid="80F561BE56E2"><Path>massPerTime</Path><Name>Mass Per Time</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kg</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="44518F8EC8DC"><Path>energyConsumption</Path><Name>Energy Consumption</Name><Value/><Unit>kWh</Unit><PerUnit>year</PerUnit><ItemValueDefinition uid="9801C6552128"><Path>energyConsumption</Path><Name>Energy Consumption</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="C97FDB7CE8DF"><Path>currentReading</Path><Name>Current Reading</Name><Value/><Unit>kWh</Unit><PerUnit/><ItemValueDefinition uid="6EF1FF3361F0"><Path>currentReading</Path><Name>Current Reading</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="B540FB4C05C7"><Path>lastReading</Path><Name>Last Reading</Name><Value/><Unit>kWh</Unit><PerUnit/><ItemValueDefinition uid="7DDB0BB0B6CA"><Path>lastReading</Path><Name>Last Reading</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>kWh</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="32A873ED2142"><Path>volumePerTime</Path><Name>Volume Per Time</Name><Value/><Unit>L</Unit><PerUnit>year</PerUnit><ItemValueDefinition uid="CDA01AFCF91B"><Path>volumePerTime</Path><Name>Volume Per Time</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>L</Unit><PerUnit>year</PerUnit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue><ItemValue uid="88C0DC759E16"><Path>deliveries</Path><Name>Number of deliveries</Name><Value/><Unit>year</Unit><PerUnit/><ItemValueDefinition uid="DEB369A7AD4E"><Path>deliveries</Path><Name>Number of deliveries</Name><ValueDefinition created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="45433E48B39F"><Name>amount</Name><ValueType>DECIMAL</ValueType><Description/><Environment uid="5F5887BCF726"/></ValueDefinition><Unit>year</Unit><FromProfile>true</FromProfile><FromData>false</FromData></ItemValueDefinition></ItemValue></ItemValues><Amount unit="kg/year">0.000</Amount><StartDate>2009-02-11T14:13+0000</StartDate><EndDate/><DataItem uid="A70149AF0F26"><Label>coal</Label></DataItem></ProfileItem></ProfileItems><Pager><Start>0</Start><From>1</From><To>2</To><Items>2</Items><CurrentPage>1</CurrentPage><RequestedPage>1</RequestedPage><NextPage>-1</NextPage><PreviousPage>-1</PreviousPage><LastPage>1</LastPage><ItemsPerPage>10</ItemsPerPage><ItemsFound>2</ItemsFound></Pager><TotalAmount unit="kg/year">1265.664</TotalAmount></ProfileCategoryResource></Resources>')
		);

		// V1 XML
		$t = $this->mockConnection(array('body'=>$io['xml_v1']['body']), $io['xml_v1']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['xml_v1']['url']);
		$this->assertSame('105.472', $data->total_amount, 'total_amount.should be_close(105.472, 1e-9)');
		$this->assertSame('kg/month', $data->total_amount_unit);
		$this->assertSame(2, count($data->items));
		$this->assertSame('FB07247AD937', $data->items[0]['uid']);
		$this->assertSame('gas', $data->items[0]['name']);
		$this->assertSame('FB07247AD937', $data->items[0]['path']);
		$this->assertSame('gas', $data->items[0]['dataItemLabel']);
		$this->assertSame('66056991EE23', $data->items[0]['dataItemUid']);
		$this->assertSame('20080902', $data->items[0]['validFrom']);
		$this->assertSame(false, $data->items[0]['end']);
		$this->assertSame('2.472', $data->items[0]['amountPerMonth'], 'items[0][:amountPerMonth].should be_close(2.472, 1e-9)');
		$this->assertSame(3, count($data->items[0]['values']));
		$this->assertSame('12', $data->items[0]['values']['kWhPerMonth']);
		$this->assertSame('0', $data->items[0]['values']['kgPerMonth']);
		$this->assertSame('0', $data->items[0]['values']['litresPerMonth']);

		// V2 XML
		$t = $this->mockConnection(array('body'=>$io['xml_v2']['body']), $io['xml_v2']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['xml_v2']['url']);
		$this->assertSame('1265.664', $data->total_amount, 'total_amount.should be_close(1265.664, 1e-9)');
		$this->assertSame('kg/year', $data->total_amount_unit);
		$this->assertSame(2, count($data->items));
		$this->assertSame('30C00AD33033', $data->items[0]['uid'], 'items[0][:uid].should == "4A69B256D62D"');
		$this->assertSame('gas', $data->items[0]['name']);
		$this->assertSame('30C00AD33033', $data->items[0]['path']);
		$this->assertSame('gas', $data->items[0]['dataItemLabel'], 'items[0][:dataItemLabel].should == "gas"');
		$this->assertSame('66056991EE23', $data->items[0]['dataItemUid']);
		$this->assertSame('2008-09-02T01:00+0100', $data->items[0]['startDate']);
		$this->assertSame(null, $data->items[0]['endDate']);
		$this->assertSame('29.664', $data->items[0]['amount'], 'items[0][:amountPerMonth].should be_close(29.664, 1e-9)');
		$this->assertSame('kg/year', $data->items[0]['amount_unit']);
		$this->assertSame(10, count($data->items[0]['values']));
		$this->assertSame('13', $data->items[0]['values']['energyConsumption']['value']);
		$this->assertSame('kWh', $data->items[0]['values']['energyConsumption']['unit']);
		$this->assertSame('month', $data->items[0]['values']['energyConsumption']['per_unit']);
		$this->assertSame('0', $data->items[0]['values']['massPerTime']['value']);
		$this->assertSame('0', $data->items[0]['values']['volumePerTime']['value']);
	}

	// describe AMEE::Profile::Category, "with an authenticated JSON connection" do
	// describe AMEE::Profile::Category, "with an authenticated V2 JSON connection" do

	public function testJSON_shouldLoadProfileCategory()
	{
		$io = array(
			'json_v1' => array(
				'url'=>'/profiles/E54C5525AA3E/home',
				'body'=>'{"totalAmountPerMonth":"0","dataCategory":{"uid":"BBA3AC3E795E","path":"home","name":"Home"},"profileDate":"200809","path":"/home","profile":{"uid":"E54C5525AA3E"},"children":{"pager":{},"dataCategories":[{"uid":"427DFCC65E52","path":"appliances","name":"Appliances"},{"uid":"30BA55A0C472","path":"energy","name":"Energy"},{"uid":"A46ECFA19333","path":"heating","name":"Heating"},{"uid":"150266DD4434","path":"lighting","name":"Lighting"}],"profileItems":{}}}'
			),
			'json_v2' => array(
				'url'=>'/profiles/447C40EB29FB/home',
				'body'=>'{"apiVersion":"2.0","pager":{},"profileCategories":[{"uid":"427DFCC65E52","name":"Appliances","path":"appliances"},{"uid":"30BA55A0C472","name":"Energy","path":"energy"},{"uid":"A46ECFA19333","name":"Heating","path":"heating"},{"uid":"150266DD4434","name":"Lighting","path":"lighting"},{"uid":"6553150F96CE","name":"Waste","path":"waste"},{"uid":"07362DCC9E7B","name":"Water","path":"water"}],"dataCategory":{"uid":"BBA3AC3E795E","dataCategory":{"uid":"CD310BEBAC52","name":"Root","path":""},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10},"created":"2007-07-27 09:30:44.0","name":"Home","path":"home","modified":"2007-07-27 09:30:44.0"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"created":"2007-07-27 09:30:44.0","description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10,"modified":"2007-07-27 09:30:44.0"},"totalAmount":"0","path":"/home","profileItemActions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"actions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"profileItems":{},"profile":{"uid":"447C40EB29FB"}}'
			) 
		);

		// V1 JSON
		$t = $this->mockConnection(array('body'=>$io['json_v1']['body']), $io['json_v1']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['json_v1']['url']);
		$this->assertSame('E54C5525AA3E', $data->profile_uid);
		$this->assertSame('200809', $data->profile_date);
		$this->assertSame('Home', $data->name);
		$this->assertSame('/home', $data->path);
		$this->assertSame('/profiles/E54C5525AA3E/home', $data->full_path);
		$this->assertSame(4, count($data->children));
		$this->assertSame('427DFCC65E52', $data->children[0]['uid']);
		$this->assertSame('Appliances', $data->children[0]['name']);
		$this->assertSame('appliances', $data->children[0]['path']);

		// V2 JSON
		$t = $this->mockConnection(array('body'=>$io['json_v2']['body']), $io['json_v2']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['json_v2']['url']);
		$this->assertSame('447C40EB29FB', $data->profile_uid);
		$this->assertSame('Home', $data->name);
		$this->assertSame('/home', $data->path);
		$this->assertSame('/profiles/447C40EB29FB/home', $data->full_path);
		$this->assertSame(6, count($data->children));
		$this->assertSame('427DFCC65E52', $data->children[0]['uid']);
		$this->assertSame('Appliances', $data->children[0]['name']);
		$this->assertSame('appliances', $data->children[0]['path']);
	}

	public function testJSON_shouldProvideAccessToChildObjects()
	{
		$io = array(
			'json_v1' => array(
				array('url'=>'/profiles/E54C5525AA3E/home', 'body' => '{"totalAmountPerMonth":"0","dataCategory":{"uid":"BBA3AC3E795E","path":"home","name":"Home"},"profileDate":"200809","path":"/home","profile":{"uid":"E54C5525AA3E"},"children":{"pager":{},"dataCategories":[{"uid":"427DFCC65E52","path":"appliances","name":"Appliances"},{"uid":"30BA55A0C472","path":"energy","name":"Energy"},{"uid":"A46ECFA19333","path":"heating","name":"Heating"},{"uid":"150266DD4434","path":"lighting","name":"Lighting"}],"profileItems":{}}}'),
				array('url'=>'/profiles/E54C5525AA3E/home/appliances', 'body' => '{"totalAmountPerMonth":"0","dataCategory":{"uid":"427DFCC65E52","path":"appliances","name":"Appliances"},"profileDate":"200809","path":"/home/appliances","profile":{"uid":"E54C5525AA3E"},"children":{"pager":{},"dataCategories":[{"uid":"3FE23FDC8CEA","path":"computers","name":"Computers"},{"uid":"54C8A44254AA","path":"cooking","name":"Cooking"},{"uid":"75AD9B83B7BF","path":"entertainment","name":"Entertainment"},{"uid":"4BD595E1873A","path":"kitchen","name":"Kitchen"},{"uid":"700D0771870A","path":"televisions","name":"Televisions"}],"profileItems":{}}}')
			),
			'json_v2' => array(
				array('url'=>'/profiles/447C40EB29FB/home', 'body' => '{"apiVersion":"2.0","pager":{},"profileCategories":[{"uid":"427DFCC65E52","name":"Appliances","path":"appliances"},{"uid":"30BA55A0C472","name":"Energy","path":"energy"},{"uid":"A46ECFA19333","name":"Heating","path":"heating"},{"uid":"150266DD4434","name":"Lighting","path":"lighting"},{"uid":"6553150F96CE","name":"Waste","path":"waste"},{"uid":"07362DCC9E7B","name":"Water","path":"water"}],"dataCategory":{"uid":"BBA3AC3E795E","dataCategory":{"uid":"CD310BEBAC52","name":"Root","path":""},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10},"created":"2007-07-27 09:30:44.0","name":"Home","path":"home","modified":"2007-07-27 09:30:44.0"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"created":"2007-07-27 09:30:44.0","description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10,"modified":"2007-07-27 09:30:44.0"},"totalAmount":"0","path":"/home","profileItemActions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"actions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"profileItems":{},"profile":{"uid":"447C40EB29FB"}}'),
				array('url'=>'/profiles/447C40EB29FB/home/appliances', 'body' => '{"apiVersion":"2.0","pager":{},"profileCategories":[{"uid":"3FE23FDC8CEA","name":"Computers","path":"computers"},{"uid":"54C8A44254AA","name":"Cooking","path":"cooking"},{"uid":"75AD9B83B7BF","name":"Entertainment","path":"entertainment"},{"uid":"4BD595E1873A","name":"Kitchen","path":"kitchen"},{"uid":"700D0771870A","name":"Televisions","path":"televisions"}],"dataCategory":{"uid":"427DFCC65E52","dataCategory":{"uid":"BBA3AC3E795E","name":"Home","path":"home"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10},"created":"2007-07-27 09:30:44.0","name":"Appliances","path":"appliances","modified":"2007-07-27 09:30:44.0"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"created":"2007-07-27 09:30:44.0","description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10,"modified":"2007-07-27 09:30:44.0"},"totalAmount":"0","path":"/home/appliances","profileItemActions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"actions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"profileItems":{},"profile":{"uid":"447C40EB29FB"}}')
			)
		);

		// V1 JSON
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2))
			->method('request')
			#->with($this->stringContains($io['json_v1'][1]['url']))
			#->with($this->stringContains($io['json_v1'][1]['url']))
			->will( $this->onConsecutiveCalls(
					array('body' => $io['json_v1'][0]['body']),
					array('body' =>  $io['json_v1'][1]['body'])
				)
			);

		// connection.should_receive(:version).and_return(1.0)
		$controller = new AMEE_Connection($Http);
		//echo 'api: ' . $controller->apiVersion;
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($controller, $io['json1'][0]['url']);
    $child = $Cat->child('appliances');
		$this->assertSame('/home/appliances', $child->path);
		$this->assertSame($io['json_v1'][1]['url'], $child->full_path);
		$this->assertSame(5, count($child->children));

		// V2 JSON
		$Http = $this->getMock('AMEE_Http');
		$Http->expects($this->exactly(2))
			->method('request')
			#->with($this->stringContains($io['json_v2'][0]['url']))
			#->with($this->stringContains($io['json_v2'][1]['url']))
			->will( $this->onConsecutiveCalls(
					array('body' => $io['json_v2'][0]['body']),
					array('body' =>  $io['json_v2'][1]['body'])
				)
			);
		$controller = new AMEE_Connection($Http);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($controller, $io['json_v2'][0]['url']);
    $child = $Cat->child('appliances');
		$this->assertSame('/home/appliances', $child->path);
		$this->assertSame($io['json_v2'][1]['url'], $child->full_path);
		$this->assertSame(5, count($child->children));
	}
	
	public function testJSON_shouldParseDataItems()
	{
		$io = array(
			'json_v1' => array('url'=>'/profiles/E54C5525AA3E/home/energy/quantity', 'body' => '{"totalAmountPerMonth":105.472,"dataCategory":{"uid":"A92693A99BAD","path":"quantity","name":"Quantity"},"profileDate":"200809","path":"/home/energy/quantity","profile":{"uid":"E54C5525AA3E"},"children":{"pager":{"to":2,"lastPage":1,"start":0,"nextPage":-1,"items":2,"itemsPerPage":10,"from":1,"previousPage":-1,"requestedPage":1,"currentPage":1,"itemsFound":2},"dataCategories":[],"profileItems":{"rows":[{"created":"2008-09-03 11:37:35.0","kgPerMonth":"0","dataItemLabel":"gas","end":"false","uid":"FB07247AD937","modified":"2008-09-03 11:38:12.0","dataItemUid":"66056991EE23","validFrom":"20080902","amountPerMonth":"2.472","label":"ProfileItem","litresPerMonth":"0","path":"FB07247AD937","kWhPerMonth":"12","name":"gas"},{"created":"2008-09-03 11:40:44.0","kgPerMonth":"0","dataItemLabel":"gas","end":"false","uid":"D9CBCDED44C5","modified":"2008-09-03 11:41:54.0","dataItemUid":"66056991EE23","validFrom":"20080901","amountPerMonth":"103.000","label":"ProfileItem","litresPerMonth":"0","path":"D9CBCDED44C5","kWhPerMonth":"500","name":"gas2"}],"label":"ProfileItems"}}}'),
			'json_v2' => array('url'=>'/profiles/447C40EB29FB/home/energy/quantity', 'body' => '{"apiVersion":"2.0","pager":{"to":2,"lastPage":1,"nextPage":-1,"items":2,"start":0,"itemsFound":2,"requestedPage":1,"currentPage":1,"from":1,"itemsPerPage":10,"previousPage":-1},"profileCategories":[],"dataCategory":{"uid":"A92693A99BAD","dataCategory":{"uid":"30BA55A0C472","name":"Energy","path":"energy"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10},"created":"2007-07-27 09:30:44.0","name":"Quantity","path":"quantity","itemDefinition":{"uid":"212C818D8F16","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","name":"Energy Quantity","drillDown":"type","modified":"2007-07-27 09:30:44.0"},"modified":"2007-07-27 09:30:44.0"},"environment":{"uid":"5F5887BCF726","itemsPerFeed":10,"created":"2007-07-27 09:30:44.0","description":"","name":"AMEE","owner":"","path":"","itemsPerPage":10,"modified":"2007-07-27 09:30:44.0"},"totalAmount":{"unit":"kg/year","value":1532.058000},"path":"/home/energy/quantity","profileItemActions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"actions":{"allowCreate":true,"allowView":true,"allowList":true,"allowModify":true,"allowDelete":true},"profileItems":[{"amount":{"unit":"kg/year","value":32.058},"uid":"4A69B256D62D","startDate":"2008-09-02T01:00+0100","itemValues":[{"itemValueDefinition":{"uid":"E0EFED6FD7E6","name":"Payment frequency","path":"paymentFrequency","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"B732461AB225","unit":"","name":"Payment frequency","value":"","path":"paymentFrequency","displayPath":"paymentFrequency","displayName":"Payment frequency"},{"itemValueDefinition":{"uid":"63005554AE8A","name":"Green tariff","path":"greenTariff","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"1B5F54675626","unit":"","name":"Green tariff","value":"","path":"greenTariff","displayPath":"greenTariff","displayName":"Green tariff"},{"itemValueDefinition":{"uid":"527AADFB3B65","name":"Season","path":"season","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"EBCDC97E8866","unit":"","name":"Season","value":"","path":"season","displayPath":"season","displayName":"Season"},{"itemValueDefinition":{"uid":"1740E500BDAB","choices":"true=true,false=false","name":"Includes Heating","path":"includesHeating","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"F3400F402132","unit":"","name":"Includes Heating","value":"false","path":"includesHeating","displayPath":"includesHeating","displayName":"Includes Heating"},{"itemValueDefinition":{"perUnit":"year","uid":"9337F5526A61","unit":"kg","choices":"0","name":"Mass Per Time","path":"massPerTime","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"16BEF21E6ADD","unit":"kg","name":"Mass Per Time","value":"0","path":"massPerTime","displayPath":"massPerTime","displayName":"Mass Per Time"},{"itemValueDefinition":{"perUnit":"year","uid":"9B455839C862","unit":"kWh","choices":"0","name":"Energy Consumption","path":"energyConsumption","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"month","uid":"8BB3F49E2BB3","unit":"kWh","name":"Energy Consumption","value":"13","path":"energyConsumption","displayPath":"energyConsumption","displayName":"Energy Consumption"},{"itemValueDefinition":{"perUnit":"year","uid":"DAEC096CF138","unit":"kWh","choices":"0","name":"Current Reading","path":"currentReading","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"8CDC8101111C","unit":"kWh","name":"Current Reading","value":"0","path":"currentReading","displayPath":"currentReading","displayName":"Current Reading"},{"itemValueDefinition":{"perUnit":"year","uid":"F08A18BEE5E8","unit":"kWh","choices":"0","name":"Last Reading","path":"lastReading","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"3D9E46FBFDBF","unit":"kWh","name":"Last Reading","value":"0","path":"lastReading","displayPath":"lastReading","displayName":"Last Reading"},{"itemValueDefinition":{"perUnit":"year","uid":"87E2DB9BE8BD","unit":"L","choices":"0","name":"Volume Per Time","path":"volumePerTime","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"5D354D0A1E9B","unit":"L","name":"Volume Per Time","value":"0","path":"volumePerTime","displayPath":"volumePerTime","displayName":"Volume Per Time"},{"itemValueDefinition":{"perUnit":"year","uid":"F0DE162CA7E7","choices":"0","name":"Number of deliveries","path":"deliveries","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"7071D7893AD4","unit":"","name":"Number of deliveries","value":"0","path":"deliveries","displayPath":"deliveries","displayName":"Number of deliveries"}],"created":"2009-02-19T12:15+0000","name":"gas","endDate":"","dataItem":{"uid":"66056991EE23","Label":"gas"},"modified":"2009-02-19T12:16+0000"},{"amount":{"unit":"kg/year","value":1500},"uid":"D67664ACBFA8","startDate":"2009-02-19T12:15+0000","itemValues":[{"itemValueDefinition":{"uid":"E0EFED6FD7E6","name":"Payment frequency","path":"paymentFrequency","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"17F10F458D27","unit":"","name":"Payment frequency","value":"","path":"paymentFrequency","displayPath":"paymentFrequency","displayName":"Payment frequency"},{"itemValueDefinition":{"uid":"63005554AE8A","name":"Green tariff","path":"greenTariff","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"BF8CA2944776","unit":"","name":"Green tariff","value":"","path":"greenTariff","displayPath":"greenTariff","displayName":"Green tariff"},{"itemValueDefinition":{"uid":"527AADFB3B65","name":"Season","path":"season","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"7999924C5AD5","unit":"","name":"Season","value":"","path":"season","displayPath":"season","displayName":"Season"},{"itemValueDefinition":{"uid":"1740E500BDAB","choices":"true=true,false=false","name":"Includes Heating","path":"includesHeating","valueDefinition":{"uid":"CCEB59CACE1B","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"text","valueType":"TEXT","modified":"2007-07-27 09:30:44.0"}},"perUnit":"","uid":"23E81F1D94CB","unit":"","name":"Includes Heating","value":"false","path":"includesHeating","displayPath":"includesHeating","displayName":"Includes Heating"},{"itemValueDefinition":{"perUnit":"year","uid":"9337F5526A61","unit":"kg","choices":"0","name":"Mass Per Time","path":"massPerTime","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"B98508BB0AF4","unit":"kg","name":"Mass Per Time","value":"0","path":"massPerTime","displayPath":"massPerTime","displayName":"Mass Per Time"},{"itemValueDefinition":{"perUnit":"year","uid":"9B455839C862","unit":"kWh","choices":"0","name":"Energy Consumption","path":"energyConsumption","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"2DD082B17B24","unit":"kWh","name":"Energy Consumption","value":"0","path":"energyConsumption","displayPath":"energyConsumption","displayName":"Energy Consumption"},{"itemValueDefinition":{"perUnit":"year","uid":"DAEC096CF138","unit":"kWh","choices":"0","name":"Current Reading","path":"currentReading","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"E3A7EB47237A","unit":"kWh","name":"Current Reading","value":"0","path":"currentReading","displayPath":"currentReading","displayName":"Current Reading"},{"itemValueDefinition":{"perUnit":"year","uid":"F08A18BEE5E8","unit":"kWh","choices":"0","name":"Last Reading","path":"lastReading","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"D3777A95F147","unit":"kWh","name":"Last Reading","value":"0","path":"lastReading","displayPath":"lastReading","displayName":"Last Reading"},{"itemValueDefinition":{"perUnit":"year","uid":"87E2DB9BE8BD","unit":"L","choices":"0","name":"Volume Per Time","path":"volumePerTime","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"month","uid":"CEF81F741A34","unit":"L","name":"Volume Per Time","value":"50","path":"volumePerTime","displayPath":"volumePerTime","displayName":"Volume Per Time"},{"itemValueDefinition":{"perUnit":"year","uid":"F0DE162CA7E7","choices":"0","name":"Number of deliveries","path":"deliveries","valueDefinition":{"uid":"45433E48B39F","environment":{"uid":"5F5887BCF726"},"created":"2007-07-27 09:30:44.0","description":"","name":"amount","valueType":"DECIMAL","modified":"2007-07-27 09:30:44.0"}},"perUnit":"year","uid":"DE940C4A6FAD","unit":"","name":"Number of deliveries","value":"0","path":"deliveries","displayPath":"deliveries","displayName":"Number of deliveries"}],"created":"2009-02-19T12:15+0000","name":"D67664ACBFA8","endDate":"","dataItem":{"uid":"878854C275BC","Label":"biodiesel"},"modified":"2009-02-19T12:17+0000"}],"profile":{"uid":"447C40EB29FB"}}')
		);

		// V1 JSON
		$t = $this->mockConnection(array('body'=>$io['json_v1']['body']), $io['json_v1']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['json_v1']['url']);
		$this->assertSame(105.472, $data->total_amount, 'total_amount.should be_close(105.472, 1e-9)');
		$this->assertSame('kg/month', $data->total_amount_unit);
		$this->assertSame(2, count($data->items));
		$this->assertSame('FB07247AD937', $data->items[0]['uid']);
		$this->assertSame('gas', $data->items[0]['name']);
		$this->assertSame('FB07247AD937', $data->items[0]['path']);
		$this->assertSame('gas', $data->items[0]['dataItemLabel']);
		$this->assertSame('66056991EE23', $data->items[0]['dataItemUid']);
		$this->assertSame('20080902', $data->items[0]['validFrom']);
		$this->assertSame(false, $data->items[0]['end']);
		$this->assertSame('2.472', $data->items[0]['amountPerMonth'], 'items[0][:amountPerMonth].should be_close(2.472, 1e-9)');
		
		$this->assertSame(3, count($data->items[0]['values']));
		$this->assertSame('12', $data->items[0]['values']['kWhPerMonth']);
		$this->assertSame('0', $data->items[0]['values']['kgPerMonth']);
		$this->assertSame('0', $data->items[0]['values']['litresPerMonth']);

		// V2 JSON
		$t = $this->mockConnection(array('body'=>$io['json_v2']['body']), $io['json_v2']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$data = $Cat->get($t, $io['json_v2']['url']);
		$this->assertSame(1532.058, $data->total_amount, 'total_amount.should be_close(1532.058, 1e-9)');
		$this->assertSame('kg/year', $data->total_amount_unit);
		$this->assertSame(2, count($data->items));
		$this->assertSame('4A69B256D62D', $data->items[0]['uid'], 'items[0][:uid].should == "4A69B256D62D"');
		$this->assertSame('gas', $data->items[0]['name']);
		$this->assertSame('4A69B256D62D', $data->items[0]['path']);
		$this->assertSame('gas', $data->items[0]['dataItemLabel'], 'items[0][:dataItemLabel].should == "gas"');
		$this->assertSame('66056991EE23', $data->items[0]['dataItemUid']);
		$this->assertSame('2008-09-02T01:00+0100', $data->items[0]['startDate']);
		$this->assertSame('', $data->items[0]['endDate']);
		$this->assertSame(32.058, $data->items[0]['amount'], 'items[0][:amountPerMonth].should be_close(32.058, 1e-9)');
		
		$this->assertSame(10, count($data->items[0]['values']));
		$this->assertSame('13', $data->items[0]['values']['energyConsumption']['value']);
		$this->assertSame('kWh', $data->items[0]['values']['energyConsumption']['unit']);
		$this->assertSame('month', $data->items[0]['values']['energyConsumption']['per_unit']);
		$this->assertSame('0', $data->items[0]['values']['massPerTime']['value']);
		$this->assertSame('0', $data->items[0]['values']['volumePerTime']['value']);
	}

	public function testJSON_parsesRecursiveGetRequests()
	{
		$io = array(
			'json_v1' => array('url'=>'/profiles/BE22C1732952/transport/car', 'body'=>'{"totalAmountPerMonth":"0","dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"profileDate":"200901","path":"/transport/car","profile":{"uid":"BE22C1732952"},"children":{"pager":{},"dataCategories":[{"dataCategory":{"modified":"2008-04-21 16:42:10.0","created":"2008-04-21 16:42:10.0","itemDefinition":{"uid":"C6BC60C55678"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"883ADD27228F","environment":{"uid":"5F5887BCF726"},"path":"bands","name":"Bands"},"path":"/transport/car/bands"},{"totalAmountPerMonth":0.265,"dataCategory":{"modified":"2007-07-27 09:30:44.0","created":"2007-07-27 09:30:44.0","itemDefinition":{"uid":"123C4A18B5D6"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"87E55DA88017","environment":{"uid":"5F5887BCF726"},"path":"generic","name":"Generic"},"path":"/transport/car/generic","children":{"dataCategories":[{"dataCategory":{"modified":"2008-09-23 12:18:03.0","created":"2008-09-23 12:18:03.0","itemDefinition":{"uid":"E6D0BB09578A"},"dataCategory":{"uid":"87E55DA88017","path":"generic","name":"Generic"},"uid":"417DD367E9AA","environment":{"uid":"5F5887BCF726"},"path":"electric","name":"Electric"},"path":"/transport/car/generic/electric"}],"profileItems":{"rows":[{"created":"2009-01-05 13:58:52.0","ecoDriving":"false","tyresUnderinflated":"false","dataItemLabel":"diesel, large","kmPerLitre":"0","distanceKmPerMonth":"1","end":"false","uid":"8450D6D97D2D","modified":"2009-01-05 13:59:05.0","airconFull":"false","dataItemUid":"4F6CBCEE95F7","validFrom":"20090101","amountPerMonth":"0.265","kmPerLitreOwn":"0","country":"","label":"ProfileItem","occupants":"-1","airconTypical":"true","path":"8450D6D97D2D","name":"","regularlyServiced":"true"}],"label":"ProfileItems"}}},{"dataCategory":{"modified":"2007-07-27 09:30:44.0","created":"2007-07-27 09:30:44.0","itemDefinition":{"uid":"07EBA32512DF"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"95E76249584D","environment":{"uid":"5F5887BCF726"},"path":"specific","name":"Specific"},"path":"/transport/car/specific"}],"profileItems":{}}}'),
			'json_v2' => null
		);

		// V1 JSON
		$t = $this->mockConnection(array('body'=>$io['json_v1']['body']), $io['json_v1']['url']);
		$Cat = new AMEE_ProfileCategory();
  	$cat = $Cat->get($t, $io['json_v1']['url']); //, array('recurse'=>true));
		$this->assertSame(0, count($cat->items));
		$this->assertSame(3, count($cat->children));
		$this->assertSame('Generic', $cat->children[1]['name'], "name: should match 'Generic'");
		$this->assertSame('generic', $cat->children[1]['path']);
		$this->assertSame('87E55DA88017', $cat->children[1]['uid']);
		$this->assertSame(0.265, $cat->children[1]['totalAmountPerMonth'], "totalAmountPerMonth: should match 0.265");
		
		$this->assertSame(1, count($cat->children[1]['children']));
		$this->assertSame('Electric', $cat->children[1]['children'][0]['name']);
		$this->assertSame('electric', $cat->children[1]['children'][0]['path']);
		$this->assertSame('417DD367E9AA', $cat->children[1]['children'][0]['uid']);

		$this->assertSame(1, count($cat->children[1]['items']));
		$this->assertSame('0.265', $cat->children[1]['items'][0]['amountPerMonth']);
		$this->assertSame('diesel, large', $cat->children[1]['items'][0]['dataItemLabel']);
		$this->assertSame('4F6CBCEE95F7', $cat->children[1]['items'][0]['dataItemUid']);
		$this->assertSame('true', $cat->children[1]['items'][0]['values']['airconTypical']);
		$this->assertSame('8450D6D97D2D', $cat->children[1]['items'][0]['uid']);

		// V2 JSON		
		$this->markTestIncomplete('TODO: missing in amee-ruby?!');
		#  it "parses recursive GET requests" do
		#    connection = flexmock "connection"
		#    connection.should_receive(:version).and_return(2.0)
		#    connection.should_receive(:get).with("/profiles/BE22C1732952/transport/car", {:recurse => true}).and_return('{"totalAmountPerMonth":"0","dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"profileDate":"200901","path":"/transport/car","profile":{"uid":"BE22C1732952"},"children":{"pager":{},"dataCategories":[{"dataCategory":{"modified":"2008-04-21 16:42:10.0","created":"2008-04-21 16:42:10.0","itemDefinition":{"uid":"C6BC60C55678"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"883ADD27228F","environment":{"uid":"5F5887BCF726"},"path":"bands","name":"Bands"},"path":"/transport/car/bands"},{"totalAmountPerMonth":0.265,"dataCategory":{"modified":"2007-07-27 09:30:44.0","created":"2007-07-27 09:30:44.0","itemDefinition":{"uid":"123C4A18B5D6"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"87E55DA88017","environment":{"uid":"5F5887BCF726"},"path":"generic","name":"Generic"},"path":"/transport/car/generic","children":{"dataCategories":[{"dataCategory":{"modified":"2008-09-23 12:18:03.0","created":"2008-09-23 12:18:03.0","itemDefinition":{"uid":"E6D0BB09578A"},"dataCategory":{"uid":"87E55DA88017","path":"generic","name":"Generic"},"uid":"417DD367E9AA","environment":{"uid":"5F5887BCF726"},"path":"electric","name":"Electric"},"path":"/transport/car/generic/electric"}],"profileItems":{"rows":[{"created":"2009-01-05 13:58:52.0","ecoDriving":"false","tyresUnderinflated":"false","dataItemLabel":"diesel, large","kmPerLitre":"0","distanceKmPerMonth":"1","end":"false","uid":"8450D6D97D2D","modified":"2009-01-05 13:59:05.0","airconFull":"false","dataItemUid":"4F6CBCEE95F7","validFrom":"20090101","amountPerMonth":"0.265","kmPerLitreOwn":"0","country":"","label":"ProfileItem","occupants":"-1","airconTypical":"true","path":"8450D6D97D2D","name":"","regularlyServiced":"true"}],"label":"ProfileItems"}}},{"dataCategory":{"modified":"2007-07-27 09:30:44.0","created":"2007-07-27 09:30:44.0","itemDefinition":{"uid":"07EBA32512DF"},"dataCategory":{"uid":"1D95119FB149","path":"car","name":"Car"},"uid":"95E76249584D","environment":{"uid":"5F5887BCF726"},"path":"specific","name":"Specific"},"path":"/transport/car/specific"}],"profileItems":{}}}'))
		#    cat = AMEE::Profile::Category.get(connection, "/profiles/BE22C1732952/transport/car", :recurse => true)
		#    cat.items.size.should == 0
		#    cat.children.size.should == 3
		#    cat.children[1][:name].should == "Generic"
		#    cat.children[1][:path].should == "generic"
		#    cat.children[1][:uid].should == "87E55DA88017"
		#    cat.children[1][:totalAmountPerMonth].should == 0.265
		#    cat.children[1][:children].size.should == 1
		#    cat.children[1][:children][0][:name].should == "Electric"
		#    cat.children[1][:children][0][:path].should == "electric"
		#    cat.children[1][:children][0][:uid].should == "417DD367E9AA"
		#    cat.children[1][:items].size.should == 1
		#    cat.children[1][:items][0][:amountPerMonth].should == 0.265
		#    cat.children[1][:items][0][:dataItemLabel].should == "diesel, large"
		#    cat.children[1][:items][0][:dataItemUid].should == "4F6CBCEE95F7"
		#    cat.children[1][:items][0][:values][:airconTypical].should == "true"
		#    cat.children[1][:items][0][:uid].should == "8450D6D97D2D"
		#  end
	}

	public function testShouldFailGracefullyWithIncorrectData()
	{
		$formats = array(
			array('desc'=>'V1 JSON', 'format'=>'JSON', 'url'=>'/profiles/E54C5525AA3E', 'body'=>'{'),
			array('desc'=>'V2 JSON', 'format'=>'JSON2', 'url'=>'/profiles/E54C5525AA3E', 'body'=>'{"apiVersion":"2.0"}'),
			array('desc'=>'V1 XML', 'format'=>'XML', 'url'=>'/profiles/E54C5525AA3E', 'body'=>'<?xml version="1.0" encoding="UTF-8"?><Resources></Resources>'),
			array('desc'=>'V2 XML', 'format'=>'XMLV2', 'url'=>'/profiles/E54C5525AA3E', 'body'=>'<?xml version="1.0" encoding="UTF-8"?><Resources xmlns="http://schemas.amee.cc/2.0"></Resources>'),
			array('desc'=>'V2 ATOM', 'format'=>'ATOM', 'url'=>'/profiles/E54C5525AA3E', 'body'=>'<?xml version="1.0" encoding="UTF-8"?><feed xmlns="http://www.w3.org/2005/Atom" xmlns:amee="http://schemas.amee.cc/2.0"></feed>'),
		);
		foreach($formats as $v) {
			try {
				$t = $this->mockConnection(array('body' => $v['body']), $v['url']);
				$Cat = new AMEE_ProfileCategory;
  			$Cat->get($t, $v['url']);
			} catch(AMEE_BadDataException $e) {
				$this->assertSame("Couldn't load ProfileCategory from " . strtoupper($v['format']) . " data. Check that your URL is correct.",
				$e->getMessage(), $v['format'] . ': should raise_error(AMEE::BadData")');
			}
		}
	}

	// describe AMEE::Profile::Category, "with an authenticated version 2 Atom connection" do

	public function testATOM_shouldLoadProfileCategory()
	{
		$this->markTestIncomplete('TODO');
	}

	public function testATOM_shouldParseDataItems()
	{
		$this->markTestIncomplete('TODO');
	}

	public function testATOM_parsesRecursiveGetRequests()
	{
		$this->markTestIncomplete('TODO');
	}

}