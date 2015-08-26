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
* @version $id: ProfileItem.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_ProfileItem extends AMEE_ProfileObject
{

	public function __construct($data=null, $Connection=null)
	{
		foreach(array('values','total_amount','total_amount_unit','data_item_uid') as $k) {
			$this->set($k, $data[$k], array());
		}
		$this->set('start_date', $data['start_date'], $data['valid_from']);
		$this->set('end_date', $data['end_date'], $data['end']);

		$this->set('duration', $data['end_date']?$data['end_date']-$data['start_date']:null, null);
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
				return $this->_json2($data);
			case 'json':
				return $this->_json($data);
			case 'xml':
			default:
				return $this->_xml($data);
		}
	}

	// TODO: JSON V2
	private function _json2($d)
	{
		$data = array(
			'profile_uid' => $d->profile->uid,
			'data_item_uid' => $d->profileItem->dataItem->uid,
			'uid' => $d->profileItem->uid,
			'name' => $d->profileItem->name,
			'path' => $d->path,
			'total_amount' => (float) $d->profileItem->amount->value,
			'total_amount_unit' => $d->profileItem->amount->unit,
			'start_date' => Amee::parseTime($d->profileItem->startDate),
			'end_date' => Amee::parseTime($d->profileItem->endDate),
			'values' => array()
		);
		$values = $d->profileItem->itemValues;
		if(is_array($values)) {
			foreach($values as $k=>$v) {
				if(in_array($k, array('name','path', 'uid', 'value', 'unit'))) {
					$data['values'][][strtolower($k)] = $v;
				} elseif($k == 'perUnit') $data['values'][]['per_unit'] = $v;
			}
		}
		return $data;
	}

	// JSON V1
	private function _json($d)
	{
		$data = array(
			'profile_uid' => $d->profile->uid,
			'data_item_uid' => $d->profileItem->dataItem->uid,
			'uid' => $d->profileItem->uid,
			'name' => $d->profileItem->name,
			'path' => $d->profileItem->path,
			'total_amount' => (float) $d->profileItem->amountPerMonth,
			'total_amount_unit' => 'kg/month',
			'valid_from' => Amee::parseTime($d->profileItem->validFrom),
			'end' => $d->profileItem->end=='false'?false:true,
			'values' => array()
		);
		if($type == 'jsonV2') {
			$data['total_amount'] = (float) $d->profileItem->amount->value;
			$data['total_amount_unit'] = $d->profileItem->amount->unit;
			$data['start_date'] = Amee::parseTime($d->profileItem->startDate);
			$data['end_date'] = Amee::parseTime($d->profileItem->endDate);
		}
		$values = $d->profileItem->itemValues;
		if(is_array($values)) {
			foreach($values as $k=>$v) {
				if(in_array($k, array('name','path', 'uid', 'value', 'unit'))) {
					$data['values'][][strtolower($k)] = $v;
				} elseif($k == 'perUnit') $data['values'][]['per_unit'] = $v;
			}
		}
		return $data;
	}
	
	// TODO: XML V1
	private function _xml($d)
	{
			$doc = $d->ProfileItemResource;
			$data = array(
				'path' => (string) $doc->Path,
				'uid' => (string) $doc->ProfileItem['uid'],
				'created' => (string) Amee::parseTime($doc->ProfileItem['created']),
				'modified' => (string) Amee::parseTime($doc->ProfileItem['modified']),
				'name' => (string) $doc->ProfileItem->Name,
				'label' => (string) $doc->ProfileItem->Label,
				'item_definition' => (string) $doc->ProfileItem->ItemDefinition['uid'],
				'total_amount' => (string) $doc->ProfileItem->AmountPerMonth
			);
			$foo = $doc->xpath('/Resources/ProfileItemResource/ProfileItem/ItemValues/ItemValue');
			foreach ($foo as $v) {
				$data['values'][] = array(
					'uid' => (string) $v['uid'],
					'name' => (string) $v->Name,
					'path' => (string) $v->Path,
					'value' => (string) $v->Value
				);
			}
			$foo = $doc->xpath('/Resources/ProfileItemResource/Choices/Choices/Choice');
			foreach ($foo as $v) {
				$data['choices'][] = array(
					'name' => (string) $v->Name,
					'value' => (string) $v->Value
				);
			}
			return $data;
	}

	public function get(AMEE_Connection $Connection, $path, array $options=null)
	{
		$this->Connection = $Connection;

		if(!is_null($options)) {
			// Convert to AMEE options
			/*
		        if $options['start_date'] && category.connection.version < 2
          $options['profileDate] = options[:start_date'].amee1_month
        elsif $options['start_date'] && category.connection.version >= 2
          $options['startDate] = options[:start_date'].xmlschema
        end
        options.delete(:start_date)
        if $options['end_date'] && category.connection.version >= 2
          $options['endDate] = options[:end_date'].xmlschema
        end
        options.delete(:end_date)
        if $options['duration'] && category.connection.version >= 2
          $options['duration] = "PT#{options[:duration'] * 86400}S"
        end
			*/
		}
		// Load data from path
		$response = $this->Connection->get($path, $options);
		return $this->parse($response['body']);
	}

	public function create($Connection, $Category, $data_item_uid, $options=array())
	{
		$this->Connection = $Connection;
		return $this->createWithoutCategory($this->Connection, $Category->full_path, $data_item_uid, $options);
	}

	public function createWithoutCategory(AMEE_Connection $Connection, $path, $data_item_uid, $options=array())
	{
		// Do we want to automatically fetch the item afterwards?
		$get_item = $this->options->delete['get_item'] ? true : null;
		
		// Store format if set
    $format = $options['format'];
		// Sets dates
		if($options['start_date'] && $this->Connection->version < 2) {
			$options['validFrom'] = $options['start_date'];
		} elseif($options['start_date'] && $this->Connection->version >= 2) {
			$options['startDate'] = $options['start_date']; //.xmlschema
		}
		#$options->delete('start_date');
		if($options['end_date'] && $this->Connection->version >= 2) {
			$options['endDate'] = $options['end_date']; //.xmlschema
		}
		#$options->delete(:end_date)
     if($options['duration'] && $this->Connection->version >= 2) {
     	$options['duration'] = $options['duration']*86400;
		}
		// Send data to path
		$options['dataItemUid'] = $data_item_uid; // ????
		try {
			$response = $this->Connection->post($path, $options);
			if($response['Location']) {
				$location = $response['Location']; // TODO.match("http://.*?(/.*)")[1]
			} else {
				$Category = new AMEE_ProfileItem();  //Category();
				$Category = $Category->parse($response['body']);
        $Category->location = $Category->full_path . '/' . $Category->items[0]['path'];
#print_r($Category);
			}
			if($get_item === true) {
				$get_options = array();
        $get_options['returnUnit'] = isset($options['returnUnit'])?$options['returnUnit']:null;
        $get_options['returnPerUnit'] = isset($options['returnPerUnit'])?$options['returnPerUnit']:null;
        $get_options['format'] = isset($options['format'])?$options['format']:null;
				return $this->get($this->Connection, $Category, $get_options);
			} else {
				return $Category;
			}
		} catch(Exception $e) {
			throw new AMEE_BadDataException("Couldn't create ProfileItem. Check that your information is correct.");
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