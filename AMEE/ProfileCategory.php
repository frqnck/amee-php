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
* @version $id: ProfileCategory.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_ProfileCategory extends AMEE_ProfileObject
{

	public $children;
	public $items;
	public $total_amount;
	public $total_amount_unit;

	public function __construct($data=null)
	{
		$this->set('children', $data['children'], array());
		$this->set('items', $data['items'], array());
		$this->set('total_amount', $data['total_amount'], '');
		$this->set('total_amount_unit', $data['total_amount_unit'], '');
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
				return $this->_json2($data);
			case 'json':
				return $this->_json($data);
			case 'xmlV2':
				return $this->_xml2($data);
			case 'xml':
			default:
				return $this->_xml($data);
		}
	}

	// JSON V1
	private function _json($d)
	{
		$data = array(
			'profile_uid' => $d->profile->uid,
			'profile_date' => Amee::parseTime($d->profileDate),
			'name' => $d->dataCategory->name,
			'path' => $d->path,
			'total_amount' => $d->totalAmountPerMonth,
			'total_amount_unit' => 'kg/month'
		);
		if($d->children && $d->children->dataCategories) {
			foreach ($d->children->dataCategories as $child) {
				if($child) $data['children'][] = $this->parse_json_profile_category($child);
			}
		}
		$profile_items = (array) $d->children->profileItems->rows;
		$profile_items[] = is_null($d->profileItem)?null:$d->profileItem;
		foreach ($profile_items as $item) {
			if($item) $data['items'][] = $this->parse_json_profile_item($item);
		}
		return $data;
	}

	// JSON V2
	private function _json2($d)
	{
		$data = array(
			'profile_uid' => $d->profile->uid,
			'profile_date' => Amee::parseTime($d->profileDate),
			'name' => $d->dataCategory->name,
			'path' => $d->path,
			'total_amount' => $d->totalAmount->value, #$d->totalAmountPerMonth,
			'total_amount_unit' => $d->totalAmount->unit #kg/month'
		);
		if($d->profileCategories) {
			foreach ($d->profileCategories as $child) {
				if($child) $data['children'][] = $this->parse_json_profile_category($child);
			}
		}
		$profile_items = (array) $d->profileItems;
		$profile_items[] = is_null($d->profileItem)?null:$d->profileItem;

		foreach ($profile_items as $item) {
			if($item) $data['items'][] = $this->parse_json_profile_item($item);
		}
		return $data;
	}

	public function parse_json_profile_category($cat)
	{
		$data = isset($cat->dataCategory) ? $cat->dataCategory : $cat;
    $category_data = array(
    	'name' => $data->name,
    	'path' => $data->path,
    	'uid' => $data->uid,
    	'totalAmountPerMonth' => $cat->totalAmountPerMonth,
		);
		if($cat->children) {
			foreach ($cat->children as $k => $child) {
				if ($k == 'dataCategories') {
					foreach ($child as $child_cat) {
						$category_data['children'][] = $this->parse_json_profile_category($child_cat);
					}
				}
				if ($k == 'profileItems' && $child->rows) {
					foreach ($child->rows as $child_item) {
						$category_data['items'][] = $this->parse_json_profile_item($child_item);
					}
				}
			}
    }
    return $category_data;
	}

	public function parse_json_profile_item($items)
	{
		$items = (array) $items;
		foreach($items as $k => $v) {
			switch($k) {
					case 'dataItemLabel':
					case 'dataItemUid':
					case 'name':
					case 'path':
					case 'uid':
						$item_data[$k] = $v;
					break;
					case 'dataItem':
						$v = (array) $v;
						$item_data['dataItemLabel'] = $v['Label'];
						$item_data['dataItemUid'] = $v['uid'];
					break;
					case 'label': break; // ignore these
					case 'created': $item_data['created'] = Amee::parseTime($v); break;
					case 'modified': $item_data['modified'] = Amee::parseTime($v); break;
					case 'validFrom': $item_data['validFrom'] = Amee::parseTime($v); break; //DateTime.strptime(value, "%Y%m%d")
					case 'startDate': $item_data['startDate'] = Amee::parseTime($v); break;
					case 'endDate': $item_data['endDate'] = Amee::parseTime($v); break;
					case 'end': $item_data['end'] = $v==true?false:null; break;
					case 'amountPerMonth': $item_data['amountPerMonth'] = $v; break;
					case 'amount': $v = (array) $v; $item_data['amount'] = $v['value']; $item_data['unit'] = $v['unit']; break;
					case 'itemValues':
						foreach($v as $itemval) {
							$itemval = (array) $itemval;
        	  	$path = $itemval['path'];
          	  $item_data['values'][$path] = array();
            	$item_data['values'][$path]['name'] = $itemval['name'];
         	  	$item_data['values'][$path]['value'] = $itemval['value'];
         	  	$item_data['values'][$path]['unit'] = $itemval['unit'];
         	  	$item_data['values'][$path]['per_unit'] = $itemval['perUnit'];
						}
					break;
				default: 
					$item_data['values'][$k] = $v;
			}
		}
		// Fill in path if not retrieved from response
		$item_data['path'] = $item_data['path'] ? $item_data['path'] : $item_data['uid']; 
		return $item_data;
	}

	// XML v1
	private function _xml($d)
	{
			$doc = $d->ProfileCategoryResource;
			
			$path = $doc->xpath($doc . '/Resources/ProfileCategoryResource/Path | /Resources/ProfileCategoryResource/DataCategory/path');

			$data = array(
				'profile_uid' => (string) $doc->Profile['uid'],
				'profile_date' => (string) $doc->ProfileDate,
				'name' => (string) $doc->DataCategory->Name,
				'path' => (string) $path[0],
				'total_amount' => (string) $doc->TotalAmountPerMonth,
				'total_amount_unit' => 'kg/month'
			);

			$children = $doc->xpath($doc . 'Children/ProfileCategories/DataCategory'|$doc . 'Children/ProfileCategories/DataCategory');
			foreach ($children as $child) {
				$data['children'][] = array(
					'name' => (string) $child->Name,
					'path' => (string) $child->Path,
					'uid' => (string) $child['uid']
				);
			}
			$children = $doc->xpath($doc . 'Children/ProfileCategories/ProfileCategory');
			foreach ($children as $child) {
				$data['children'][] = $this->parse_xml_profile_category($child);
			}
			$items = $doc->xpath($doc . 'Children/ProfileItems/ProfileItem');
			foreach ($items as $item) {
				$data['items'][] = $this->parse_xml_profile_item($item);
			}
			$items = $doc->xpath($doc . 'ProfileItem');
			foreach ($items as $item) {
				$data['items'][] = $this->parse_xml_profile_item($item);
			}
			$items = $doc->xpath($doc . 'ProfileItems/ProfileItem');
			foreach ($items as $item) {
				$data['items'][] = $this->parse_xml_profile_item($item);
			}
		return $data;
	}
	
	public function parse_xml_profile_item($item)
	{
		$item_data = array();
		foreach($item as $k => $v) { //$element) {
				switch(strtolower($k)) {
        	case 'dataitemlabel': case 'dataitemuid': case 'name': case 'path':
						$item_data[$k] = (string) $v;
					break;
					case 'dataitem':
          	$item_data['dataItemUid'] = $element['uid'];
					break;
					case 'validfrom':
						$item_data['validFrom'] = (string) Amee::parseTime($v, '%Y%m%d');
          break;
          case 'end':
						$item_data['end'] = $v=="true"?true:false;
					break;
					case 'amountpermonth':
						$item_data['amountPerMonth'] = (string) $v; //.to_f
          break;
          default:
						$item_data['values'][$k] = (string) $v;
			}
		}
		$item_data['uid'] = (string) $item['uid'];				
    $item_data['created'] = (string) Amee::parseTime($item['created']);
    $item_data['modified'] = (string) Amee::parseTime($item['modified']);
		# item_data[:path] ||= item_data[:uid] # Fill in path if not retrieved from response
    $item_data['path'] = $item_data['uid']; // Fill in path if not retrieved from response
		return $item_data;
	}

	public function parse_xml_profile_category($cat)
	{
		$data = isset($cat->dataCategory) ? $cat->dataCategory : $cat;
    $category_data = array(
    	'name' => $data->name,
    	'path' => $data->path,
    	'uid' => $data->uid,
    	'totalAmountPerMonth' => $cat->totalAmountPerMonth,
		);
		if($cat->children) {
			foreach ($cat->children as $k => $child) {
				if ($k == 'dataCategories') {
					foreach ($child as $child_cat) {
						$category_data['children'][] = $this->parse_xml_profile_category($child_cat);
					}
				}
				if ($k == 'profileItems' && $child->rows) {
					foreach ($child->rows as $child_item) {
						$category_data['items'][] = $this->parse_xml_profile_item($child_item);
					}
				}
			}
    }
    return $category_data;
	}
	
	// XML V2
	private function _xml2($d)
	{
			$doc = $d->ProfileCategoryResource;
			$data = array(
				'profile_uid' => (string) $doc->Profile['uid'],
				#'profile_date' => (string) $doc->ProfileDate,
				'name' => (string) $doc->DataCategory->Name,
				'path' => (string) '/' . $doc->DataCategory->Path,
				'total_amount' => (string) $doc->TotalAmount,
				'total_amount_unit' => (string) $doc->TotalAmount['unit']
			);
			$children = $doc->ProfileCategories->DataCategory;
			$children = $children?$children:array();
			foreach ($children as $child) {
				$data['children'][] = array(
					'name' => (string) $child->Name,
					'path' => (string) $child->Path,
					'uid' => (string) $child['uid']
				);
			}
			$children = $doc->Children->ProfileCategories->ProfileCategory;
			$children = $children?$children:array();
			foreach ($children as $child) {
				$data['children'][] = $this->parse_xml_profile_category($child);
			}
			$items = $doc->ProfileItems->ProfileItem;
			$items = $items?$items:array();
			foreach($items as $item) {
				$data['items'][] = $this->parse_v2_xml_profile_item($item);
			}
			$items = $doc->ProfileItem;
			$items = $items?$items:array();
			foreach($items as $item) {
				$data['items'][] = $this->parse_v2_xml_profile_item($item);
			}
		return $data;
	}

	public function parse_v2_xml_profile_item($item)
	{
		$item_data = array();

		foreach($item as $k => $v) {
			switch(strtolower($k)) {
      	case 'name': case 'path':
					$item_data[strtolower($k)] = (string) $v;
				break;
        case 'dataitem':
					$item_data['dataItemLabel'] = (string) $v->Label;
					$item_data['dataItemUid'] = (string) $v['uid'];
				break;
				case 'validfrom':
					$item_data['validFrom'] = (string) Amee::parseTime($v, '%Y%m%d');
        break;
				case 'startdate':
					$item_data['startDate'] = (string) Amee::parseTime($v);
        break;
				case 'enddate':
					$item_data['validFrom'] = (string) Amee::parseTime($v);
        break;
        case 'end':
					$item_data['end'] = $v=="true"?true:false;
				break;
				case 'amount':
						$item_data['amount'] = (string) $v; //.to_f
						$item_data['amount_unit'] = (string) $v['unit']; //.to_f
        break;
				case 'itemvalues':
					foreach($v as $itemvalue) {
         		$path = (string) $itemvalue->Path;
            $item_data['values'][$path]['name'] = (string) $itemvalue->Name;
            $item_data['values'][$path]['value'] =  !empty($itemvalue->Value) ? (string) $itemvalue->Value : '0';
            $item_data['values'][$path]['unit'] =  (string) $itemvalue->Unit;
            $item_data['values'][$path]['per_unit'] = (string) $itemvalue->PerUnit;
					}
         break;

         default:
						$item_data['values'][$k] = (string) $v;
			}

		}				
		/*
            
            when 'amount'
              item_data[:amount] = element.text.to_f
              item_data[:amount_unit] = element.attributes['unit'].to_s
            when 'itemvalues'
              element.elements.each do |itemvalue|
                path = itemvalue.elements['Path'].text
                item_data[:values][path.to_sym] = {}
                item_data[:values][path.to_sym][:name] = itemvalue.elements['Name'].text
                item_data[:values][path.to_sym][:value] = itemvalue.elements['Value'].text || "0"
                item_data[:values][path.to_sym][:unit] = itemvalue.elements['Unit'].text
                item_data[:values][path.to_sym][:per_unit] = itemvalue.elements['PerUnit'].text
              end
            else
              item_data[:values][key.to_sym] = element.text
          end
        end
		*/
		$item_data['uid'] = (string) $item['uid'];				
    $item_data['created'] = (string) Amee::parseTime($item['created']);
    $item_data['modified'] = (string) Amee::parseTime($item['modified']);
		# item_data[:path] ||= item_data[:uid] # Fill in path if not retrieved from response
    $item_data['path'] = $item_data['uid']; // Fill in path if not retrieved from response
		return $item_data;
	}

	public function get(AMEE_Connection $Connection, $path, array $options=null)
	{
		$this->Connection = $Connection;

		if(!is_null($options)) {
			// Convert to AMEE options
			/*
        if options[:start_date] && connection.version < 2
          options[:profileDate] = options[:start_date].amee1_month 
        elsif options[:start_date] && connection.version >= 2
          options[:startDate] = options[:start_date].xmlschema
        end
        options.delete(:start_date)
        if options[:end_date] && connection.version >= 2
          options[:endDate] = options[:end_date].xmlschema
        end
        options.delete(:end_date)
        if options[:duration] && connection.version >= 2
          options[:duration] = "PT#{options[:duration] * 86400}S"
        end
			*/
		}
		// Load data from path
		$response = $this->Connection->get($path, $options);
		return $this->parse($response['body']);
	}

	public function child($child_path)
	{
		$response = $this->Connection->get($this->full_path . '/' . $this->child_path);
		return $this->parse($response['body']);
 	}
 

	public function create($Category, $data_item_uid, $options=array())
	{
		$this->createWithoutCategory($Category->Connection, $Category->full_path, $data_item_uid, $options);
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
				$Category = new AMEE_ProfileCategory($this->Connection);
				$Category->parse($response['body']);
        $location = $Category->full_path . '/' . $Category->items[0]['path'];
			}
			if($get_item === true) {
				$get_options = array();
        $get_options['returnUnit'] = isset($options['returnUnit'])?$options['returnUnit']:null;
        $get_options['returnPerUnit'] = isset($options['returnPerUnit'])?$options['returnPerUnit']:null;
        $get_options['format'] = isset($options['format'])?$options['format']:null;
				return $this->get($this->Connection, $location, $get_options);
			} else {
				return $location;
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