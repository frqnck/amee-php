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
* @version $id: ConnectionTest.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

class ConnectionTest extends PHPUnit_Framework_TestCase
{

	public function testTimeout()
	{
		$t = new AMEE_Connection();
		$this->assertSame(5, $t->timeout, "has a default timeout set to 5 seconds");
		$t->setTimeout(30);
		$this->assertSame(30, $t->timeout, "can set timeout");
	}

	public function testWithoutAuthentication()
	{
		$t = new AMEE_Connection();
		$this->assertSame(false, $t->isAuthenticated());
	}

	public function testWithAuthentication()
	{
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->once())
			->method('request')
			->will(
				$this->returnValue(
					array('code'=>'200', 'body'=>'', 'authToken'=>'dummy_auth_token_data')
				)
			);
		$t = new AMEE_Connection();
		$t->setHttp($http);
		$t->authenticate();
		$this->assertSame(true, $t->isAuthenticated());
		$this->assertSame('1.0', $t->apiVersion);
	}

	public function testDetectApiVersionInXml()
	{
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->once())
			->method('request')
			->will(
				$this->returnValue(
					array('code'=>'200', 'body'=>'<?xml version="1.0" encoding="UTF-8"?><Resources><SignInResource><Next>/auth</Next><User uid="DB2C6DA7EAA7"><Status>ACTIVE</Status><Type>STANDARD</Type><GroupNames><GroupName>amee</GroupName><GroupName>Standard</GroupName><GroupName>All</GroupName></GroupNames><ApiVersion>2.0</ApiVersion></User></SignInResource></Resources>', 'authToken'=>'dummy_auth_token_data')
				)
			);
		$t = new AMEE_Connection();
		$t->setHttp($http);
		$t->authenticate();
		$this->assertSame(true, $t->isAuthenticated());
		$this->assertSame('2.0', $t->apiVersion);
	}

	public function testDetectApiVersionInJson()
	{
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->once())
			->method('request')
			->will(
				$this->returnValue(
					array('code'=>'200', 'body'=>'{ "next" : "/auth","user" : { "apiVersion" : "2.0","groupNames" : [ "amee","Standard","All"],"status" : "ACTIVE","type" : "STANDARD","uid" : "DB2C6DA7EAA7"}}', 'authToken'=>'dummy_auth_token_data')
				)
			);
		$t = new AMEE_Connection();
		$t->setHttp($http);
		$t->authenticate();
		$this->assertSame(true, $t->isAuthenticated());
		$this->assertSame('2.0', $t->apiVersion);
	}

	public function testWithAuthenticationShouldAbleToGetPrivateUrls()
	{
		$t = new AMEE_Connection();
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->exactly(3))
			->method('request')
			->will( $this->onConsecutiveCalls(
					array('code' => '401', 'body' => ''),
					array('code' => '200', 'body' => '', 'authToken' => 'dummy_auth_token_data'),
					array('code' => '200', 'body' => '<?xml version="1.0" encoding="UTF-8"?><Resources><DataCategoryResource><Path/><DataCategory created="2007-07-27 09:30:44.0" modified="2007-07-27 09:30:44.0" uid="CD310BEBAC52"><Name>Root</Name><Path/><Environment uid="5F5887BCF726"/></DataCategory><Children><DataCategories><DataCategory uid="BBA3AC3E795E"><Name>Home</Name><Path>home</Path></DataCategory><DataCategory uid="9E5362EAB0E7"><Name>Metadata</Name><Path>metadata</Path></DataCategory><DataCategory uid="6153F468BE05"><Name>Test</Name><Path>test</Path></DataCategory><DataCategory uid="263FC0186834"><Name>Transport</Name><Path>transport</Path></DataCategory><DataCategory uid="2957AE9B6E6B"><Name>User</Name><Path>user</Path></DataCategory></DataCategories></Children></DataCategoryResource></Resources>')
				)
			);
		$t->setHttp($http);
		$this->assertNotNull( $t->get('/data') );
		$this->assertTrue($t->isAuthenticated(), "should be authenticated.");
	}

	public function testWithAuthenticationShouldHandle404Gracefully()
	{
		try {
			$http = $this->getMock('AMEE_Http');
			$http->expects($this->once())
				->method('request')
				->will($this->returnValue(array('code' => '404', 'body' => '')));
			$t = new AMEE_Connection();
			$t->setHttp($http);
			$t->get('/missing_url');
		} catch(AMEE_NotFoundException $e) {
			$this->assertSame("URL doesn't exist on server.", $e->getmessage());
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testShouldRaiseErrorIfPermissionForOperationIsDenied()
	{
		try {
			$http = $this->getMock('AMEE_Http');
			$http->expects($this->once())
				->method('request')
				->will($this->returnValue(array('code' => '403', 'body' => '*mocked*')));
			$t = new AMEE_Connection();
			$t->setHttp($http);
			$t->get('/data');
		} catch(AMEE_PermissionDeniedException $e) {
			$this->assertSame("You do not have permission to perform the requested operation. AMEE Response: *mocked*", $e->getmessage());
			#raise_error(AMEE::PermissionDenied,"You do not have permission to perform the requested operation. AMEE Response: ")
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testShouldRaiseErrorIfAuthenticationSucceedsButPermissionForOperationIsDenied()
	{
		$responses = array();
		$t = new AMEE_Connection();
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->exactly(3))
			->method('request')
			->will( $this->onConsecutiveCalls(
					array('code' => '401', 'body' => ''),
					array('code' => '200', 'body' => '', 'authToken' => 'dummy_auth_token_data'),
					array('code' => '403', 'body' => '')
				)
			);
		$t->setHttp($http);
		try {
			$this->assertNotNull( $t->get('/data') );
			$this->assertSame(true, $t->isAuthenticated(), "should be authenticated.");
		} catch(AMEE_PermissionDeniedException $e) {
			$this->assertSame("You do not have permission to perform the requested operation. AMEE Response: ", $e->getmessage());
			$this->assertSame(true, $t->isAuthenticated(), "should be authenticated.");
			return;
		}
	}

	public function testShouldRaiseErrorIfUnhandledErrorsOccurInConnection()
	{
		try {
			$http = $this->getMock('AMEE_Http');
			$http->expects($this->once())
				->method('request')
				->will($this->returnValue(array('code' => '500', 'body' => '')));
			$t = new AMEE_Connection();
			$t->setHttp($http);
			$t->get('/data');
		} catch(AMEE_UnknownErrorException $e) {
			$this->assertSame("An error occurred while talking to AMEE: HTTP response code 500. AMEE Response: ", $e->getmessage());
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testWithIncorrectServerName()
	{
		try {
			$http = $this->getMock('AMEE_Http');
			$http->expects($this->once())
				->method('request');
			$http->error = array('raised error!!!!');
			$t = new AMEE_Connection(null, array('host'=>'badservername.example.com', 'username'=>'username', 'password'=>'password'));
			$t->setHttp($http);
			$t->get('/');
		} catch(AMEE_ConnectionFailedException $e) {
			$this->assertSame("Connection failed. Check server name or network connection.", $e->getmessage());
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	// with bad authentication information

	public function testShouldBeCapableOfMakingRequestsForPublicUrls()
	{
		$http = $this->getMock('AMEE_Http');
		$http->will($this->returnValue(array('code' => '200', 'body' => '')));
		try {
			$t = new AMEE_Connection($http, array('host'=>'badservername.example.com', 'username'=>'wrong', 'password'=>'details'));
			$t->get('/');
		} catch(AMEE_Exception $e) {
			$this->fail('An exception has been raised. Exception: ' . $e->getmessage());
		}
	}

	public function testShouldGetAnAuthenticationFailureWhenAccessingPrivateUrls()
	{
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->any())
		->method('request')
		->will($this->returnValue(
			array('code' => '401', 'body' => '', 'authToken' => null)
		));
		try {
			$t = new AMEE_Connection($http);
			$t->get('/data');
		} catch(AMEE_AuthFailedException $e) {
			$this->assertSame("Authentication failed. Please check your username and password.", $e->getmessage());
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	// describe AMEE::Connection, "with authentication" do

	public function testPostPutAndDeleteWithAuthentication()
	{
		$http = $this->getMock('AMEE_Http');
		$http->expects($this->exactly(6))->method('request')
		->will($this->returnValue(array('code' => '200', 'body' => null)));
		$t = new AMEE_Connection();
		$t->setHttp($http);

		$response = $t->post('/profiles', array('test' => 1, 'test2' => 2));
		$this->assertNull($response['body'], "should be able to send POST requests");

		$response = $t->put('/profiles/ABC123', array('test' => 1, 'test2' => 2));
		$this->assertNull($response['body'], "should be able to send PUT requests");

		$response = $t->delete('/profiles/ABC123');
		$this->assertNull($response, "should be able to send DELETE requests");
	}

}