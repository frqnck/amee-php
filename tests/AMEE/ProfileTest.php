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
* @version $id: ProfileTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class ProfileTest extends PHPUnit_Framework_TestCase
{

	protected $Profile;

  public function setUp()
  {
    $this->Profile = new AMEE_Profile;
  }

  public function tearDown()
  {
    unset($this->Profile);
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
		if(!is_null($Exception)) throw $Exception;
		return $controller;
	}

	public function testIsProfileObject()
	{
		$this->assertTrue($this->Profile instanceof AMEE_ProfileObject, "Should have common AMEE object properties");
	}

	public function testProfileUid()
  {
		$options = array('uid'=>'ABC1234');
		$profile = new AMEE_ProfileObject($options);
		$this->assertSame($options['uid'],  $profile->uid, "Should initialize AMEE::Object data on creation");
	}

	// with an authenticated connection

	public function testProvidesAccessToListOfProfiles()
	{
		$data = array('body'=>'<?xml version="1.0" encoding="UTF-8" standalone="no"?><Resources xmlns="http://schemas.amee.cc/2.0"><ProfilesResource><Profiles><Profile created="2009-04-18 12:42:26.0" modified="2009-04-18 12:42:26.0" uid="A1BA0BB6E925"><Path/><Name/><Environment uid="5F5887BCF726"/><Permission created="2009-04-18 12:42:26.0" modified="2009-04-18 12:42:26.0" uid="6E10194B7105"><Environment uid="5F5887BCF726"/><Name>amee</Name><Username>franck</Username></Permission></Profile><Profile created="2009-04-18 12:29:05.0" modified="2009-04-18 12:29:05.0" uid="1D37D686D016"><Path/><Name/><Environment uid="5F5887BCF726"/><Permission created="2009-04-18 12:29:05.0" modified="2009-04-18 12:29:05.0" uid="162EEF13E953"><Environment uid="5F5887BCF726"/><Name>amee</Name><Username>franck</Username></Permission></Profile></Profiles><Pager><Start>0</Start><From>1</From><To>2</To><Items>2</Items><CurrentPage>1</CurrentPage><RequestedPage>1</RequestedPage><NextPage>-1</NextPage><PreviousPage>-1</PreviousPage><LastPage>1</LastPage><ItemsPerPage>10</ItemsPerPage><ItemsFound>2</ItemsFound></Pager></ProfilesResource></Resources>');
		$Connection = $this->mockConnection($data);
		$res = $this->Profile->getList($Connection);
		$this->assertSame(2, count($res), "Should provide access to list of profiles");
	}

	public function testFailGracefullyWithIncorrectProfileListData()
	{
		try {
			$Connection = $this->mockConnection(array(null));
			$this->Profile->getList($Connection);
		} catch(AMEE_Exception $e) {
			$this->assertSame("Couldn't load Profile from JSONV2 data. Check that your URL is correct.", $e->getmessage(), "Should fail gracefully with incorrect profile list data");
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testJsonParsing()
	{
		$data = array('body'=>'{"apiVersion":"2.0","pager":{"to":1,"lastPage":1,"nextPage":-1,"items":1,"start":0,"itemsFound":1,"requestedPage":1,"currentPage":1,"from":1,"itemsPerPage":10,"previousPage":-1},"profiles":[{"uid":"1D37D686D016","environment":{"uid":"5F5887BCF726"},"created":"2009-04-18 12:29:05.0","name":"1D37D686D016","path":"1D37D686D016","permission":{"uid":"162EEF13E953","created":"2009-04-18 12:29:05.0","group":{"uid":"AC65FFA5F9D9","name":"amee"},"environmentUid":"5F5887BCF726","auth":{"uid":"DF6E3D6EFE47","username":"franck"},"modified":"2009-04-18 12:29:05.0"},"modified":"2009-04-18 12:29:05.0"}]}');
		$Connection = $this->mockConnection($data);
		$profiles = $this->Profile->getList($Connection);
		
		$this->assertSame('1D37D686D016', $profiles[0]->uid);
		$this->assertSame('1D37D686D016', $profiles[0]->name);
		$this->assertSame('/1D37D686D016', $profiles[0]->path);
		$this->assertSame('2009-04-18 12:29:05.0', $profiles[0]->created);
		$this->assertSame('2009-04-18 12:29:05.0', $profiles[0]->modified);
		$this->assertSame('/profiles/1D37D686D016', $profiles[0]->full_path);
	}

	public function testXlmParsing()
	{
		$data = array('body'=>'<?xml version="1.0" encoding="UTF-8" standalone="no"?><Resources xmlns="http://schemas.amee.cc/2.0"><ProfilesResource><Profiles><Profile created="2009-04-18 12:29:05.0" modified="2009-04-18 12:29:05.0" uid="1D37D686D016"><Path/><Name/><Environment uid="5F5887BCF726"/><Permission created="2009-04-18 12:29:05.0" modified="2009-04-18 12:29:05.0" uid="162EEF13E953"><Environment uid="5F5887BCF726"/><Name>amee</Name><Username>franck</Username></Permission></Profile></Profiles><Pager><Start>0</Start><From>1</From><To>1</To><Items>1</Items><CurrentPage>1</CurrentPage><RequestedPage>1</RequestedPage><NextPage>-1</NextPage><PreviousPage>-1</PreviousPage><LastPage>1</LastPage><ItemsPerPage>10</ItemsPerPage><ItemsFound>1</ItemsFound></Pager></ProfilesResource></Resources>');
		$Connection = $this->mockConnection($data);
		$profiles = $this->Profile->getList($Connection);

		$this->assertSame('1D37D686D016', $profiles[0]->uid);
		$this->assertSame('1D37D686D016', $profiles[0]->name);
		$this->assertSame('/1D37D686D016', $profiles[0]->path);
		$this->assertSame('2009-04-18 12:29:05.0', $profiles[0]->created);
		$this->assertSame('2009-04-18 12:29:05.0', $profiles[0]->modified);
		$this->assertSame('/profiles/1D37D686D016', $profiles[0]->full_path);
	}

	public function testCreatingNewProfileXMLVersion1()
	{
		$data = array('body'=>'<?xml version="1.0" encoding="UTF-8" standalone="no"?><Resources><ProfilesResource><Profile created="Wed Mar 18 10:18:41 GMT 2009" modified="Wed Mar 18 10:18:41 GMT 2009" uid="358E3BCF690E"><Path /><Name /><Environment uid="5F5887BCF726" /><Permission created="Wed Mar 18 10:18:41 GMT 2009" modified="Wed Mar 18 10:18:41 GMT 2009" uid="FFF3B406794D"><Environment uid="5F5887BCF726" /><Name>amee</Name><Username>v1user</Username></Permission></Profile></ProfilesResource></Resources>');
		$Connection = $this->mockConnection($data); //, 'post');
		$profiles = $this->Profile->create($Connection);

		$this->assertFalse(AMEE_Parser::isV2Xml($data['body']));
		$this->assertSame('358E3BCF690E', $profiles[0]->uid);
		$this->assertSame('358E3BCF690E', $profiles[0]->name);
		$this->assertSame('/358E3BCF690E', $profiles[0]->path);
		$this->assertSame('Wed Mar 18 10:18:41 GMT 2009', $profiles[0]->created);
		$this->assertSame('Wed Mar 18 10:18:41 GMT 2009', $profiles[0]->modified);
		$this->assertSame('/profiles/358E3BCF690E', $profiles[0]->full_path);
	}
	
	public function testCreatingNewProfileXMLVersion2()
	{
		$data = array('body'=>'<?xml version="1.0" encoding="UTF-8" standalone="no"?><Resources xmlns="http://schemas.amee.cc/2.0"><ProfilesResource><Profile created="Wed Mar 18 10:23:54 GMT 2009" modified="Wed Mar 18 10:23:54 GMT 2009" uid="7C7D68C2A7CD"><Path /><Name /><Environment uid="5F5887BCF726" /><Permission created="Wed Mar 18 10:23:54 GMT 2009" modified="Wed Mar 18 10:23:54 GMT 2009" uid="0D7EAF107FEB"><Environment uid="5F5887BCF726" /><Name>amee</Name><Username>v2user</Username></Permission></Profile></ProfilesResource></Resources>');
		$Connection = $this->mockConnection($data);
		$profiles = $this->Profile->create($Connection);

		$this->assertTrue(AMEE_Parser::isV2Xml($data['body']));
		$this->assertSame('7C7D68C2A7CD', $profiles[0]->uid);
		$this->assertSame('7C7D68C2A7CD', $profiles[0]->name);
		$this->assertSame('/7C7D68C2A7CD', $profiles[0]->path);
		$this->assertSame('Wed Mar 18 10:23:54 GMT 2009', $profiles[0]->created);
		$this->assertSame('Wed Mar 18 10:23:54 GMT 2009', $profiles[0]->modified);
		$this->assertSame('/profiles/7C7D68C2A7CD', $profiles[0]->full_path);
}

	public function testCreatingNewProfileJSONversion1()
	{
		$data = array('body'=>'{ "profile":{ "uid":"17A4CE4C3D91", "environment":{ "uid":"5F5887BCF726" }, "created":"Wed Mar 18 10:18:43 GMT 2009", "name":"17A4CE4C3D91", "path":"17A4CE4C3D91", "permission":{ "uid":"6D607EA39D71", "created":"Wed Mar 18 10:18:43 GMT 2009", "group":{ "uid":"AC65FFA5F9D9", "name":"amee" }, "environmentUid":"5F5887BCF726", "auth":{ "uid":"1A6307E2B531", "username":"v1user" }, "modified":"Wed Mar 18 10:18:43 GMT 2009" }, "modified":"Wed Mar 18 10:18:43 GMT 2009" } }');
		$Connection = $this->mockConnection($data); //, null, 'post');
		$profiles = $this->Profile->create($Connection);

		$this->assertFalse(AMEE_Parser::isV2Json($data['body']));
		$this->assertSame('17A4CE4C3D91', $profiles[0]->uid);
		$this->assertSame('17A4CE4C3D91', $profiles[0]->name);
		$this->assertSame('/17A4CE4C3D91', $profiles[0]->path);
		$this->assertSame('Wed Mar 18 10:18:43 GMT 2009', $profiles[0]->created);
		$this->assertSame('Wed Mar 18 10:18:43 GMT 2009', $profiles[0]->modified);
		$this->assertSame('/profiles/17A4CE4C3D91', $profiles[0]->full_path);
	}

	public function testCreatingNewProfileJSONversion2()
	{
		$data = array('body'=>'{ "apiVersion":"2.0", "profile":{ "uid":"180D73DA5229", "environment":{ "uid":"5F5887BCF726" }, "created":"Wed Mar 18 10:23:59 GMT 2009", "name":"180D73DA5229", "path":"180D73DA5229", "permission":{ "uid":"2F093CD55011", "created":"Wed Mar 18 10:23:59 GMT 2009", "group":{ "uid":"AC65FFA5F9D9", "name":"amee" }, "environmentUid":"5F5887BCF726", "auth":{ "uid":"BA6EB0039D69", "username":"v2user" }, "modified":"Wed Mar 18 10:23:59 GMT 2009" }, "modified":"Wed Mar 18 10:23:59 GMT 2009" } }');
		$Connection = $this->mockConnection($data); //, null, 'post');
		$profiles = $this->Profile->create($Connection);

		$this->assertTrue(AMEE_Parser::isV2Json($data['body']));
		$this->assertSame('180D73DA5229', $profiles[0]->uid);
		$this->assertSame('180D73DA5229', $profiles[0]->name);
		$this->assertSame('/180D73DA5229', $profiles[0]->path);
		$this->assertSame('Wed Mar 18 10:23:59 GMT 2009', $profiles[0]->created);
		$this->assertSame('Wed Mar 18 10:23:59 GMT 2009', $profiles[0]->modified);
		$this->assertSame('/profiles/180D73DA5229', $profiles[0]->full_path);
	}

	public function testThrowExceptionOnProfileCreationFailure()
	{
		try {
			$Connection = $this->mockConnection(array(null)); //, null , 'post');
			$this->Profile->create($Connection);
		} catch(AMEE_BadDataException $e) {
			$this->assertSame("Couldn't create Profile.", $e->getmessage(), "Should fail gracefully if new profile creation fails");
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

}