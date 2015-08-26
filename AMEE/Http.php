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
* @version $id: Http.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

abstract class AMEE_Http
{
	public $config = array();
	public $error;

	protected $_request = array();
	protected $_response = array();

	public function __construct($config=null)
	{
		$this->config = $config;
		if($this->config['cache']['enable']==true && !$this->cache) {
			require_once AMEE_PATH . '/Cache.php';
			$this->cache = new AMEE_Cache($this->config['cache']);
		}
	}
	
	#abstract public function send_request($request, $options=null);

	public function request(array $request, $options=null)
	{
		if($this->config['cache']['enable']!=true || $this->method!='GET') {
			return $this->fetch($request, $options);
		}
		$response = $this->cache->retrieve($this->path);
		if(empty($response)) {
			// If not in cache, go fetch it from AMEE
			$response = $this->fetch($request, $options); 
			if(!empty($response)) {
				$this->cache->insert($this->path, $response);
				$this->log("Adding to cache: $this->path");
			} else {
				$this->log("Blank response in " . __METHOD__ . " -  not caching.");
			}
		} else {
			$this->log("Cache hit: $this->path");
		}
		$this->cache->purge($this->config['cache']['lifetime']);
		return $response;
	}

	public function fetch(array $request, $options=null)
	{
		$this->_request = array_merge($this->_request, $request);
		$raw_request = $this->_getHttpRequestHeader($this->_request, $this->config['host']);
		$this->open($this->config['host'], $this->config['port']);	// Open HTTP connection
		
		$raw_response = $this->send_request( $raw_request );
		
		if($this->config['log']['verbosity']>1) $this->log(	$raw_request . implode('', $raw_response) );
		$response = $this->_parseResponse( $raw_response );
		$this->close();	// Shut HTTP connection
		return $response;
	}

	//abstract protected function _request($path, $options=null);

	abstract public function open($host=null, $port=null);

	abstract public function close($host=null, $port=null);

	public function __get($k)
	{
		#$this->debug("\t__get($k) -> " .  $this->_request->$k);
		#print_r( $this->_request[$k] );
		return $this->_request[$k];
	}

	public function __set($k, $v)
	{
		$this->_request[$k] = $v;
		#$this->debug("\t__set($k) -> $v");
	}

	public function __call($k, $args)
	{
		#$this->debug($k, $args);
		$k = strtoupper($k);
		if( in_array($k, array('GET', 'POST', 'PUT', 'DELETE'))) {
			$this->method = $k;
			
			// TODO
			#if($k != 'GET' && $this->config['cache']['enable']==true) {
			#	$this->cache->clear();
			#}
			return $this->path = $k . ' ' . $args[0];
		}
		throw new Exception("Calling object method '$k':\n");
	}

	static function factory($config)
	{
		$class = $config['adapter'];
		require_once (dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR
			. str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		return new $class($config);
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

	public function debug($msg, $mix=null)
	{
		if($this->config['debug'] === true) {
			echo "\n+ $msg\n";
			if(!is_null($mix)) {
				print_r($mix);
				#echo is_array($mix) ? implode(", ", $mix) : $mix;
			}
			//echo " ]\n";
		}
	}

}