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
* @version $id: Profile.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_Profile extends AMEE_ProfileObject
{

	protected $post = false;

	public function __construct($options=null)
	{
		parent::__construct($options);
	}

	public function parse($data, $msg=null)
	{
		$data = self::_parse($data, __CLASS__, $msg);
		return new self($data);
	}

	public function getData($data, $type)
	{
		switch($type) {
			case 'jsonV2':
			case 'json':
				return $this->_json($data);
			case 'xmlV2':
			case 'xml':
			default:
				return $this->_xml($data);
		}
	}

	protected function _bare($v)
	{
		return array(
			'uid' => $v->uid,
			'created' => Amee::parseTime($v->created),
			'modified' => Amee::parseTime($v->modified),
			'name' => $v->name,
			'path' => ( $this->post==false?'/':null ) . (string) $v->path
		);
	}

	public function _json($d)
	{
		if($this->_post == true) {
			$v = $d->profile;
			$dat = $this->_bare($v);
			$data[] = new AMEE_Profile($dat);
		} else {
			foreach($d->profiles as $k=>$v) {
				$dat = $this->_bare($v);
				$data[] = new AMEE_Profile($dat);
			}
		}
		return $this->profiles = $data;
	}

	public function _xml($xml)
	{
		if($this->_post == true) {
			$doc = $xml->ProfilesResource->Profile;
		} else {
			$doc = $xml->ProfilesResource->Profiles->Profile;
		}
		foreach($doc as $map) {
			$dat = array(
				'uid' => (string) $map['uid'],
				'created' => (string) Amee::parseTime($map['created']),
				'modified' => (string) Amee::parseTime($map['modified']),
				'name' => (string) (empty($map->Name)?$map['uid']:$map->Name),
				'path' => (string) ($this->post==false?'/':'') . (empty($map->Path)?$map['uid']:$map->Path),
			);
			$data[] = new AMEE_Profile($dat);
		}
		return $data;
	}

	public function getList(AMEE_Connection $Connection, $items_per_page=10)
	{
		$list = $this->get($Connection, '/profiles', array('itemsPerPage'=>$items_per_page));
		return $list->properties;
	}

	public function get(AMEE_Connection $Connection, $path, array $options=array('itemsPerPage'=>10))
	{
		$this->Connection = $Connection;
		$response = $this->Connection->get($path, $options);
		return $this->parse($response['body']);
	}

	public function post(AMEE_Connection $Connection, $path, $items_per_page=10)
	{
		$this->_post = true;
		$this->Connection = $Connection;
		$response = $Connection->post($path, array('profile'=>true,'itemsPerPage'=>$items_per_page));
		return $this->parse($response['body'], "Couldn't create Profile.");
	}

	public function create(AMEE_Connection $Connection)
	{
		$profile = $this->post($Connection, '/profiles');
		return $profile->properties;
	}

	public function delete(AMEE_Connection $Connection, $uid)
	{
		// Deleting profiles takes a while... up the timeout to 60 seconds temporarily
    $initial_tineout = $Connection->timeout;
    $Connection->timeout = 60;
    $res = $Connection->delete("/profiles/${uid}");
    $Connection->timeout = $initial_tineout;
		return $res;
	}

}