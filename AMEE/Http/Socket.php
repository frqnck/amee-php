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
* @version $id: Socket.php,v 0.1.0a 2009/07/31 7:38:40 franck Exp $*/

class AMEE_Http_Socket extends AMEE_Http
{

	private $_socket = null;

	public function _request($path, $format=null)
	{
		#$this->_open();
		// get response
		$this->_response['raw'] = $this->send_request();
		#$this->_close();
		// parse response
		$response = $this->_parseResponse($this->_response['raw']);
		return $response;
	}

	public function send_request($request) {
  	socket_write($this->_socket, $request, strlen($request));
		$lines=array();
  	$lines[0]='';
  	$i=0;
  	while(true) {
  		$c = socket_read($this->_socket, 1);
     	if($c == "\n" || strlen($c) === 0) {
				if(strpos($lines[$i], 'Set-Cookie:') !== false) {
					$this->_storeCookies($lines[$i]);
				}
       	if(strlen($c) === 0) break;
       		$i++;
     			$lines[$i] = '';
     	} else {
     		$lines[$i] .= $c;
   		}
		}
		return $lines;
		}

	public function _parseResponse($lines)
	{
		$status = array_shift($lines);
		$r = @array_combine(
			array('protocol','code','phrase'),
			explode(' ', $status)
		);
		$r['status'] = $status;
		foreach($lines as $line) {
			if(strpos($line, ': ') !== false) {
					$e = explode(': ', $line);
					$r[$e[0]] = $e[1];
			} else if(empty($line)) {
				break;
			}
		}
		$r['body'] = $lines[count($lines)-1];
		return $r;
	}

	public function open($host=null, $port=null)
	{
		$this->_socket = socket_create(AF_INET, SOCK_STREAM, 0);
		if(!socket_connect($this->_socket, gethostbyname($host), $port)) {
			throw new AMEE_ConnectionFailedException("Connection failed. Check server name or network connection.");
		}
	}

	public function close($host=null, $port=null)
	{
		socket_close($this->_socket);
	}

	protected function _getHttpRequestHeader($req, $host)
	{
		return $req['path'] . " HTTP/1.0\n"
			. $this->_getCookieLines()		// TODO: Insert cookies
			. "Accept: {$req['Accept']}\n"
			#.  "Accept: application/xml\n"
			. (empty($req['Authtoken']) ? null : 'Authtoken: ' . $req['Authtoken'] . "\n")
			. "Host: $host\n"
			. "Content-Type: application/x-www-form-urlencoded\n"
	    . "Content-Length: " . strlen($req['body']) . "\n"
	    . "\n" . $req['body'];
	}

	/**
	*	Store cookies in _cookies
	* Note: cookies are essential right now, but may be for live server
	* @param string $line should be a cookie line from the HTTP response
	*/
	protected function _storeCookies($line) {
		$p1 = strpos($line, ':');
		$s = substr($line, $p1 + 1);
		$ss = split('=', $s, 2);
		$this->_cookies[trim($ss[0])] = trim($ss[1]);
	}

	/**
	* Creates header lines for cookies
	* @return string
	*/
	protected function _getCookieLines() {
		if(!empty($this->_cookies)) {
			$lines = '';
			foreach($this->_cookies as $name => $value){
				$lines .= 'Cookie: '.$name.'='.$value."\n";
			}
			return $lines;
		}
	}

}