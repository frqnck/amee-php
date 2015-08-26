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
* @version $id: DataItem.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_DataItem extends AMEE_DataObject implements AMEE_DataObjectInterface
{

	public function __construct($data=null, $Connection=null)
	{
		foreach(array('values','choices','label','item_definition','total_amount') as $k) {
			$this->set($k, $data[$k], array());
		}
		parent::__construct($data, $Connection);
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
				'path' => $d->path,
				'uid' => $d->dataItem->uid,
				'created' => Amee::parseTime($d->dataItem->created),
				'modified' => Amee::parseTime($d->dataItem->modified),
				'name' => $d->dataItem->name,
				'label' => $d->dataItem->label,
				'item_definition' => $d->dataItem->itemDefinition->uid,
				'total_amount' => (float) $d->amountPerMonth
			);
			$values = $d->dataItem->itemValues;
			foreach($values as $k=>$v) {
				$data['values'][] = array(
					'uid' => $v->uid,
					'name' => $v->name,
					'path' => $v->path,
					'value' => $v->value
				);
			}
			$choices = $d->userValueChoices->choices;
			foreach($choices as $k=>$v) {
				$data['choices'][] = array(
					'name' => $v->name,
					'value' => $v->value
				);
			}
			return $data;
	}
	
	private function _xml($d)
	{
			$doc = $d->DataItemResource;
			$data = array(
				'path' => (string) $doc->Path,
				'uid' => (string) $doc->DataItem['uid'],
				'created' => (string) Amee::parseTime($doc->DataItem['created']),
				'modified' => (string) Amee::parseTime($doc->DataItem['modified']),
				'name' => (string) $doc->DataItem->Name,
				'label' => (string) $doc->DataItem->Label,
				'item_definition' => (string) $doc->DataItem->ItemDefinition['uid'],
				'total_amount' => (float) $doc->DataItem->AmountPerMonth
			);
			$foo = $doc->xpath('/Resources/DataItemResource/DataItem/ItemValues/ItemValue');
			foreach ($foo as $v) {
				$data['values'][] = array(
					'uid' => (string) $v['uid'],
					'name' => (string) $v->Name,
					'path' => (string) $v->Path,
					'value' => (string) $v->Value
				);
			}
			$foo = $doc->xpath('/Resources/DataItemResource/Choices/Choices/Choice');
			foreach ($foo as $v) {
				$data['choices'][] = array(
					'name' => (string) $v->Name,
					'value' => (string) $v->Value
				);
			}
			return $data;
	}

	public function get(AMEE_Connection $Connection, $path, $options=array())
	{
		$this->Connection = $Connection;
		$response = $Connection->get($path, $options);
		return $this->parse($response['body']);
	}

	public function update(AMEE_Connection $Connection, $options=array())
	{
		$this->Connection = $Connection;
		try {
			$response = $this->Connection->put($this->full_path, $options);
			return $this->parse($response['body']);
		} catch(Exception $e) {
			throw new AMEE_BadDataException("Couldn't update DataItem. Check that your information is correct.");
		}
	}

	public function value($token)
	{
		foreach($this->values as $v) {
			foreach(array('name', 'path', 'uid') as $k) {
				if($v[$k] == $token) return $v['value'];
			}
		}
		return null;
	}

}