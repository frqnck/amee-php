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
* @version $id: Parser.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

abstract class AMEE_Parser
{

	static public function getType($data=null)
	{
		switch($data) {
			case self::isV2Json($data): return 'jsonV2'; break;
			case self::isJson($data): return 'json'; break;
			case self::isV2Atom($data): return 'atom'; break;
			case self::isV2Xml($data): return 'xmlV2'; break;
			case self::isXml($data): return 'xml'; break;
		}
		throw new AMEE_BadDataException("Couldn't load/parse data. Check that your URL is correct.");
	}

	static public function isJson($data)
	{
		return substr($data, 0, 1) == '{' ? true : false; 
	}

	static public function isV2Json($data)
	{
		return self::isJson($data) && preg_match('/"apiVersion"\s?:\s?"2.0"/', $data);
	}

	static public function isXml($data)
	{
		return substr($data, 0, 5) == '<?xml' ? true : false; 
	}

	static function isV2Xml($data)
	{
		return self::isXml($data) && strpos($data, '<Resources xmlns="http://schemas.amee.cc/2.0">');
	}

	static public function isV2Atom($data)
	{
		return self::isXml($data) && (
			strpos($data, '<feed ')
			|| strpos($data, '<entry ')
			&& strpos($data, 'xmlns:amee="http://schemas.amee.cc/2.0"')
		);
	}

	// JSON V1
	static public function json($data) {
		return self::_check(json_decode($data));
	}

	// JSON V2
	static public function jsonV2($data) {
		return self::_check(json_decode($data));
	}

	// XML V1
	static public function xml($data) {
		$data = new SimpleXMLIterator($data);
		return self::_check($data);
	}

	// XML V2
	static public function xmlV2($data) {
		$data = new SimpleXMLIterator($data);
		return self::_check($data);
	}

	// Atom V2
	static public function atom($data) {
		$data = new SimpleXMLIterator($data);
		return self::_check($data);
	}

	// Check consistency
	protected function _check($parsed) {
		#$parsed = call_user_func($func, $data);
		if(empty($parsed)) { 
			throw new AMEE_BadDataException();
		}
		return $parsed;
	}

}