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
* @version $id: DataCategory.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_DataCategory extends AMEE_DataObject implements AMEE_DataObjectInterface
{
	public $children;
	public $items;

	public function __construct($data=null)
	{
		$this->set('children', $data['children'], array());
		$this->set('items', $data['items'], array());
		parent:: __construct($data);
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

	// JSON
	private function _json($json)
	{
		$d = $json->dataCategory;
		$data = array(
			'uid' => (string) $d->uid,
			'created' => (string) Amee::parseTime($d->created),
			'modified' => (string) Amee::parseTime($d->modified),
			'name' => (string) $d->name,
			'path' => (string) $json->path,
			'children' => array()
		);
		$children = $json->children->dataCategories;
		foreach ($children as $child) {
			$data['children'][] = array(
				'name' => $child->name,
				'path' => $child->path,
				'uid' => $child->uid
			);
		}
		if($items = $json->children->dataItems->rows) {
			$data['items'] = array();
			foreach ($items as $k => $v) {
				$data['items'][$k] = $v;
			}
		}
		return $data;
	}

	// XML V1
	private function _xml($d)
	{
			$doc = $d->DataCategoryResource;
			$data = array(
				'uid' => (string) $doc->DataCategory['uid'],
				'created' => (string) Amee::parseTime($doc->DataCategory['created']),
				'modified' => (string) Amee::parseTime($doc->DataCategory['modified']),
				'name' => (string) $doc->DataCategory->Name,
				'path' => (string) $doc->Path,
			);

			$foo = $doc->xpath('/Resources/DataCategoryResource//Children/DataCategories/DataCategory');
			foreach ($foo as $v) {
				$data['children'][] = array(
					'name' => (string) $v->Name,
					'path' => (string) $v->Path,
					'uid' => (string) $v['uid']
				);
			}
			$foo = $doc->xpath('/Resources/DataCategoryResource//Children/DataItems/DataItem');
			$i = 0;
			foreach ($foo as $v) {
				$data['items'][$i] = array(
					'uid' => (string) $v['uid'],
          'path' => (string) ( is_null($v->Path) ? $v->Path : $v['uid'] ),
				);
				if(!empty($v)) {
					foreach($v as $k => $t) {
						$data['items'][$i][$k] = (string) $t;
					}
				}
				++$i;
			}
			return $data;
/*
		//$data['uid'] = 'REXML::XPath.first(doc, "/Resources/DataCategoryResource/DataCategory/@uid").to_s';

        data[:created] = DateTime.parse(REXML::XPath.first(doc, "/Resources/DataCategoryResource/DataCategory/@created").to_s)
        
        data[:modified] = DateTime.parse(REXML::XPath.first(doc, "/Resources/DataCategoryResource/DataCategory/@modified").to_s)
        
        data[:name] = REXML::XPath.first(doc, '/Resources/DataCategoryResource/DataCategory/?ame').text
        
        data[:path] = REXML::XPath.first(doc, '/Resources/DataCategoryResource//?ath').text || ""
        
        data[:children] = []
        
        REXML::XPath.each(doc, '/Resources/DataCategoryResource//Children/DataCategories/DataCategory') do |child|
        
        category_data = {}
        
        category_data[:name] = (child.elements['Name'] || child.elements['name']).text
          category_data[:path] = (child.elements['Path'] || child.elements['path']).text
          category_data[:uid] = child.attributes['uid'].to_s
          data[:children] << category_data
        end
        data[:items] = []
        
        
        REXML::XPath.each(doc, '/Resources/DataCategoryResource//Children/DataItems/DataItem') do |item|
          item_data = {}
          item_data[:uid] = item.attributes['uid'].to_s
          item.elements.each do |element|
            item_data[element.name.to_sym] = element.text
          end
          if item_data[:path].nil?
            item_data[:path] = item_data[:uid]
          end
          data[:items] << item_data
        end
        # Create object
        Category.new(data)
      rescue
        raise AMEE::BadData.new("Couldn't load DataCategory from XML data. Check that your URL is correct.")
      end
*/
	}

	public function get(AMEE_Connection $Connection, $path, $options=array())
	{
		$this->Connection = $Connection;
		$response = $Connection->get($path, $options);
		return $this->parse($response['body']);
	}

	public function root(AMEE_Connection $Connection)
	{
		return $this->get($Connection, '/data');
	}

	public function child(AMEE_Connection $Connection, $child_path)
	{
		return $this->get($Connection, $this->full_path . '/' . $child_path);
    // AMEE::Data::Category.get(connection, "#{full_path}/#{child_path}")
	}

	public function drill(AMEE_Connection $Connection)
	{
		return $this->get($Connection, $this->full_path . '/drill');
		// AMEE::Data::DrillDown.get(connection, "#{full_path}/drill")
	}

	public function item($options)
	{
		// Search fields - from most specific to least specific
		echo "TODO:" . __METHOD__;
		/*
		$item = items.find{ |x| (x[:uid] && x[:uid] == options[:uid]) ||
                               (x[:name] && x[:name] == options[:name]) ||
                               (x[:path] && x[:path] == options[:path]) ||
                               (x[:label] && x[:label] == options[:label]) }
        # Pass through some options
        new_opts = {}
        new_opts[:format] = options[:format] if options[:format]
        item ? AMEE::Data::Item.get(connection, "#{full_path}/#{item[:path]}", new_opts) : nil
		*/
	}

}