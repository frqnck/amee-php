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
* @version $id: Object.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_Object {

  /**
   * Object properties.
   *
   * @var array
   */
  protected $properties = array();

  /**
   * Class constructor.
   * Create object.
   *
   */
	public function __construct($data=null, $Connection=null)
	{
		$this->properties = $data;
		if(!is_null($Connection)) {
			$this->setConnection($Connection);
		}
	}

  /**
   * Set a property
   *
   * @param string $k
   * @param string $v
   * @default mixed $default
   */
	public function setProperty($k, $v, $default=null)
  {
		$this->properties[$k] = isset($v) ? $v : $default;
	}

  /**
   * Magic setter
   *
   * @param string $k
   * @param string $v
   */
	#	public function __set($k, $v)
	public function set($k, $v, $default=null)
  {
		$this->$k = isset($v) ? $v : $default;
	}

  /**
   * Get a previously a property
   *
   * @see setProperty
   * @param string $k
   * @param string $default return this if the property isn't set
   * @return string
   */
  public function getProperty($k, $default=null)
  {
    return isset($this->properties[$k]) ? $this->properties[$k] : $default;
  }

 /**
   * Magic accessor
   *
   * @param string $k
   * @return mixed
   */
  public function __get($k)
  {
		switch($k) {
			case 'created':
			case 'modified':
				return $this->getProperty($k, time()); // TODO: strftime(AMEE::$strftime)
			break;
			case 'properties':
      	return $this->properties;
				break;
			case 'connection':
	    case 'profile':
	    case 'profiles':
	    case 'children':
	    case 'items':

	    case 'uid':
	    case 'path':
	    case 'name':
     	default:
				return $this->getProperty($k);
    		#throw new InvalidArgumentException('Invalid property access [' . __CLASS__ . ' :: ' . $k . ']');
		}

	}

  /**
   * Return a string representation of this object.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->properties;
  }

  /**
   * Bridge/parse XML, JSON, ATOM
   *
   * @param string $data
   * @return array
   */
	final protected function _parse($data, $class='class', $msg=null)
	{
		$type = AMEE_Parser::getType($data);
		try {
			$data = call_user_func(array('AMEE_Parser', $type), $data);
			#return call_user_func(array($this, 'parse' . $type), $data);
			return call_user_func(array($this, 'getData'), $data, $type);
		} catch(AMEE_Exception $e) {
			$method = substr(strrchr($class, '_'), 1);
			$type = strtoupper($type);
			if(is_null($msg)) $msg = "Couldn't load $method from $type data. Check that your URL is correct.";
			$msg .= $e->getMessage();
			throw New AMEE_BadDataException($msg);
		}
	}

  /**
   * TODO: Object Connection.
   *
   * @var object
   */
	/*
  protected $Connection = null;

	public function setConnection(AMEE_Connection $Connection)
	{
		$this->Connection = $Connection;
	}
	*/
}