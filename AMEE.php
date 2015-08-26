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
* @package AMEE-PHP
* @author Franck Cassedanne <kifranky [at] gmail [dot] com>
* @version $id: AMEE.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

define('AMEE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AMEE' . DIRECTORY_SEPARATOR);

require_once AMEE_PATH . 'Exception.php';
require_once AMEE_PATH . 'Connection.php';
require_once AMEE_PATH . 'Http.php';
require_once AMEE_PATH . 'Parser.php';

require_once AMEE_PATH . 'Object.php';
require_once AMEE_PATH . 'ProfileObject.php';
require_once AMEE_PATH . 'DataObject.php';

require_once AMEE_PATH . 'DataItem.php';
require_once AMEE_PATH . 'DataItemValue.php';
require_once AMEE_PATH . 'DataCategory.php';

require_once AMEE_PATH . 'Profile.php';
require_once AMEE_PATH . 'ProfileCategory.php';

// TODO
require_once AMEE_PATH . 'ProfileItem.php';
#require_once AMEE_PATH . 'ProfileItemValue.php';
#require_once AMEE_PATH . 'DrillDown.php';

class AMEE
{
	public $version = '0.1.0alpha';
	protected $_config;
	public $Connection = null;
		
	public function __construct(array $config)
	{
		$this->_config = $config;
		$this->setTimezone($config['timezone']);
	}

	public function getConnection()
	{
		if(is_null($this->Connection)) {
			$Http = AMEE_Http::factory($this->_config['http']);
			$this->Connection = new AMEE_Connection($Http);
		}
		return $this->Connection;
	}

	public function setTimezone($str='UTC')
	{
		date_default_timezone_set($str);
	}

	static public function parseTime($str)
	{
		//TODO: generic $strftime = "%Y%m%d"; 
		return $str;
	}

}