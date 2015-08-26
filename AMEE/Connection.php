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
* @version $id: Connection.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_Connection
{
	public $Http = null;

	public $timeout = 5;

	public $auth_token = null;
	public $authenticated = false;

	public $apiVersion;
	
	public $format;

	//public $request = array();

	public $response = null;
	public $body = null;

	public $enable_caching;
	public $cache;
	
	public $enable_debug = false; // OBSOLETE

	public function __construct(AMEE_Http $Http = null)
	{
		// Make connection to server
		$this->Http = $Http;

		$default_format = function_exists('json_decode')?'json':'xml';
		$this->format = isset($options['format'])?$options['format']:$default_format;

    $this->enable_caching = isset($options['enable_caching'])?$options['enable_caching']:null;
    if($this->enable_caching) {
			$this->cache = array();
		}
    #$this->enable_debug = $options['enable_debug'];
		

		$this->Http->read_timeout = $this->timeout;
		if($this->enable_debug === true) {
			$this->Http->set_debug_output();
		}
	}

	public function setHttp($Http)
	{
		$this->Http = $Http;
	}

	public function setTimeout($int)
	{
		$this->timeout = $int;
	}

	public function debug($name, $data=null)
	{
		if($this->enable_debug) {
			echo "\n$name\n";
			print_r( $data);
			echo "\n";
		}
	}

	public function setFormat($data, $default)
	{
		// Allow format override ?????? // TODO
		return isset($data['delete']['format']) ? $data['delete']['format'] : $default;
	}


	public function get($path, $data=array())
	{
		$format = $this->setFormat($data['delete']['format'], $this->format);
		if(!empty($data)) $path .= '?' . http_build_query($data, NULL, '&');
		return $this->do_request($this->Http->get($path), $format);
	}

	function post($path, $data=array())
	{
		$format = $this->setFormat($data['delete']['format'], $this->format);

		// Clear cache
		//$this->clear_cache();

		$post = $this->Http->post($path);
    $this->body = http_build_query($data, NULL, '&');
		// Send request
		return $this->do_request($post, $format);
	}

	public function raw_post($path, $body, $options=array())
	{
		$format = $this->setFormat($options['delete']['format'], $this->format);
		
		// Clear cache
		$this->clear_cache();
   	
   	// Create POST request
		$post = $this->Http->post($path);
    $post->ContentType = $options['content_type']?$options['content_type']:$this->getContentType($format);
		$post->body = $body;
		// Send request
    do_request($post, $format);
		/*
    def raw_post(path, body, options = {})
      # Allow format override
      format = options.delete(:format) || @format
      # Clear cache
      clear_cache
      # Create POST request
      post = Net::HTTP::Post.new(path)
      post['Content-type'] = options[:content_type] || content_type(format)
      post.body = body
      # Send request
      do_request(post, format)
    end
		*/
	}

	public function put($path, $data=array())
	{
		$format = $this->setFormat($data['delete']['format'], $this->format);

		// Clear cache
		$this->clear_cache();
		// Create PUT request
		$put = $this->Http->put($path);
    $put->body = http_build_query($data, NULL, '&');

		// Send request
		return $this->do_request($put, $format);
		/*
	  def put(path, data = {})
      # Allow format override
      format = data.delete(:format) || @format
      # Clear cache
      clear_cache
      # Create PUT request
      put = Net::HTTP::Put.new(path)
      body = []
        data.each_pair do |key, value|
        body << "#{CGI::escape(key.to_s)}=#{CGI::escape(value.to_s)}"
      end
      put.body = body.join '&'
      # Send request
      do_request(put, format)
    end
		*/
	}

	public function raw_put($path, $body, $options=array())
	{
		// Allow format override
		$this->format = $options['delete']['format']?$options['delete']['delete']:$this->format;
		// Clear cache
		$this->clear_cache();
   	// Create PUT request
		$put = $this->putNetHttp($path);
    $put->ContentType = $options['content_type']?$options['content_type']:$this->getContentType($this->format);
		$put->body = $body;
		// Send request
    do_request($put, $this->format);
		/*
    def raw_put(path, body, options = {})
      # Allow format override
      format = options.delete(:format) || @format
      # Clear cache
      clear_cache
      # Create PUT request
      put = Net::HTTP::Put.new(path)
      put['Content-type'] = options[:content_type] || content_type(format)
      put.body = body
      # Send request
      do_request(put, format)
    end
		*/
	}

	public function delete($path)
	{
		$this->clear_cache();
   	// Create DELETE request
		$delete = $this->Http->delete($path);
		// Send request
    $this->do_request($delete);
		/*
    def delete(path)
      clear_cache
      # Create DELETE request
      delete = Net::HTTP::Delete.new(path)
      # Send request
      do_request(delete)
    end
		*/
	}

	public function isAuthenticated()
	{
		if($this->authenticated != true) {
  		$this->authenticated = is_null($this->auth_token) ? false : true;
		}
		return $this->authenticated;
	}

	public function authenticate()
	{
		$post = $this->Http->post('/auth/signIn');
		$req = array(
			'path' => $post,
			'body' => 'username=' . $this->Http->config['username'] . '&password=' . $this->Http->config['password'],
			'Accept' => $this->getContentType('xml')
		);

		$response = $this->Http->request($req);

		$this->auth_token = isset($response['authToken'])?$response['authToken']:null;

    if($this->isAuthenticated() === false) {
    	throw new AMEE_AuthFailedException("Authentication failed. Please check your username and password.");
		}
		$this->detectApiVersion($response['body']);
	}

	// Detect API version
	public function detectApiVersion($body)
	{
		if( substr($body, 0, 1) == '{' ) {
			$v = json_decode($body);
			$this->apiVersion = $v->user->apiVersion?$v->user->apiVersion:'1.0';
    } elseif( substr($body, 0, 5) == '<?xml' ) {
			preg_match('/<ApiVersion>(.*?)<\/ApiVersion>/', $body, $m);
    	$this->apiVersion = $m[1]?$m[1]:'1.0';
    } else {
			$this->apiVersion = '1.0';
		}
	}

	protected function getContentType($format)
	{
		$format = $format?$format:$this->format;
		switch($format) {
      case 'json': return 'application/json';
      case 'atom': return 'application/atom+xml';
			case 'xml': default: return 'application/xml';
		}
	}
	
	protected function redirect($response)
	{
		return $response['code']==301||$response['code']==302?true:false;
	}

	protected function response_ok($response)
	{
		switch($response['code']) {
			case 200: case 201: return '200';
			case 403: throw new AMEE_PermissionDeniedException("You do not have permission to perform the requested operation. AMEE Response: {$response['body']}", 403);
			case 401: $this->authenticate(); 
				#return $this->auth_token ? true : false ;
				return $this->authenticated;
				return false;
			default: 
				if(isset($response['code'])) {
					throw new AMEE_UnknownErrorException("An error occurred while talking to AMEE: HTTP response code {$response['code']}. AMEE Response: {$response['body']}");
				}
		}
	}

	protected function do_request($request, $format=null)
	{
		do {
			$response = $this->send_request($request, $format);
			if($r == '200') break;
		} while( $r = $this->response_ok($response) );
		
		if(!is_null($this->Http->error)) {
			throw new AMEE_ConnectionFailedException("Connection failed. Check server name or network connection.", $response['code']);
		}
		return $response; 
	}
	
	protected function send_request($path, $format=null)
	{
		$request = array(
			'path' => $path,
			'Authtoken' => $this->auth_token,
			'Accept' => $this->getContentType($format)
		);
		if($this->body) $request['body'] = $this->body; 
		$response = $this->Http->request($request);

		// Handle 404s
		if($response['code'] == 404) {
			throw new AMEE_NotFoundException("URL doesn't exist on server.", 404);
		}
		return $response;
	}

	public function clear_cache()
	{
		if($this->enable_caching) {
			$this->cache=array();
		}
	}

}