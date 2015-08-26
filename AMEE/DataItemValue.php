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
* @version $id: DataItemValue.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_DataItemValue extends AMEE_DataObject implements AMEE_DataObjectInterface
{
	public $type;
	public $value;
	public $from_profile;
	public $from_data;

	public function __construct($data=null)
	{
		foreach(array('value','type','from_profile','from_data') as $k) {
			$this->set($k, $data[$k], null);
		}
		parent::__construct($data);
	}

	public function parse($data)
	{
		$data = self::_parse($data, __CLASS__);
		return new self($data);
	}

	public function getData($data, $type)
	{
		switch($type) {
			case 'jsonV2':
			case 'json':
				return $this->_json($data);
			break;
			case 'xml':
			default:
				return $this->_xml($data);
		}
	}

	private function _json($d)
	{
			$data = array(
				'path' => preg_replace('/^\/data/', '', $this->path),
				'uid' => $d->itemValue->uid,
				'created' => Amee::parseTime($d->itemValue->created),
				'modified' => Amee::parseTime($d->itemValue->modified),
				'name' => $d->itemValue->name,
				'value' => (string) $d->itemValue->value,
				'type' => (string) $d->itemValue->itemValueDefinition->valueDefinition->valueType,
			);
			return $data;
	}
	
	private function _xml($d)
	{
			$doc = $d->DataItemValueResource;
			$data = array(
				'path' => preg_replace('/^\/data/', '', $this->path),
				'uid' => (string) $doc->ItemValue['uid'],
				'created' => (string) Amee::parseTime($doc->ItemValue['Created']),
				'modified' => (string) Amee::parseTime($doc->ItemValue['Modified']),
				'name' => (string) $doc->ItemValue->Name,
				'value' => (string) $doc->ItemValue->Value,
				'type' => (string) $doc->ItemValue->ItemValueDefinition->ValueDefinition->ValueType,
				'from_profile' => (string) $doc->ItemValue->ItemValueDefinition->FromProfile == "true" ? true : false,
				'from_data' => (string) $doc->ItemValue->ItemValueDefinition->FromData == "true" ? true : false
			);
			return $data;
	}

	public function get(AMEE_Connection $Connection, $path, $options=array())
	{
		$this->Connection = $Connection;
		$this->path = $path;
		$response = $this->Connection->get($this->path, $options);
		return $this->parse($response['body']);
	}

	public function save(AMEE_Connection $Connection)
	{
		$this->Connection = $Connection;
		$this->Connection->put($this->full_path, array('value'=>$this->value));
	}

}