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
* @version $id: Cache.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_Cache
{
	protected $_config = array();
	
	public $db;

	public function __construct($config=null)
	{
		$this->_config = $config;

		set_include_path('/web/Zend/library');
		require 'Zend/Loader.php';
		Zend_Loader::registerAutoload();

#		require_once '/web/Zend/library/Zend/Db.php';

		$this->db = Zend_Db::factory($config['db']['adapter'],
		array(
			'host' => $config['db']['host'],
			'username' => $config['db']['username'],
			'password' => $config['db']['password'],
			'dbname' => $config['db']['dbname'],
			'port' => $config['db']['port']
		));
		$this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
	}

	public function init()
	{
		$sql = 'CREATE TABLE cache (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			key varchar NOT NULL,
			data text NOT NULL,
			timestamp text NOT NULL
		);';
	}

	public function hash($k)
	{
		return md5($k);
	}

	public function insert($k, $data)
	{
		#$this->log("Adding to cache: $k");
		$sql = 'INSERT INTO cache (key, data, timestamp) VALUES (:key, :data, :ts)';
		if($this->_config['db']['adapter'] != 'pdo_sqlite') {
			$sql .= ' ON DUPLICATE KEY UPDATE data=:data, timestamp=:ts';
		}
		$this->db->query( $sql, array(
				'key'		=>	$this->hash($k),
				'data'	=>	serialize($data),
				'ts'		=>	gmdate('Y-m-d H:i:s')
			)
		);
	}

	public function retrieve($k)
	{
		$data = $this->db->fetchOne("SELECT data FROM cache WHERE key=?", $this->hash($k));
		if(!empty($data)) {
			#$this->log("Cache hit: $k");
			return unserialize($data);
		}
	}

	public function purge($timeout='1 week')
	{
		#$this->log("Purging cache: " . $timeout);
		$this->db->delete('cache', 'timestamp < \''. gmdate('Y-m-d H:i:s', strtotime(gmdate('Y-m-d H:i:s').'-' . $timeout))."'");
	}

	static function factory($config)
	{
		$class = $config['adapter'];
		require_once (dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		return new $class($config);
	}
	
	public function clear()
	{
		$this->log('Clearing out cache');
		return $this->db->query('DELETE FROM cache');
	}

	/*
	* Log a timestamped message to the log
	*/
	protected function log($msg) {
		if($this->config['log']['enable']==true && !empty($msg)) {
			file_put_contents($this->config['log']['file'], date('Y-m-d H:i:s')
				. "\t$msg\n", FILE_APPEND | LOCK_EX);
		}
	}

}