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
* @version $id: config-sample.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

$config = array(
	'http'	=> array(
		'host' => 'stage.co2.dgen.net', 
		'port' => 80,
		'username' => 'YOUR-AMEE-USERNAME',
		'password' => 'YOUR-AMEE-PASWORD',
		'adapter' => 'AMEE_Http_Socket',
		#'adapter' => 'AMEE_Http_Test',
		'debug' => true, // STDIN debug
		'log' => array(
			'enable' => true,
			'file' => '/tmp/amee_http.log',
			'verbosity' => 5, // level 1 to 5
		),
		'cache' => array(
			'enable' => false,
			'lifetime' => '1 week',
			'adapter' => 'AMEE_Cache_ZendDb',
			'db' => array(
				'adapter' => 'pdo_sqlite',
				'dbname' => '/tmp/amee_cache.sqlite',
				'username' => 'user',
				'password' => 'pass',
				'host' => 'localhost',
				'port' => ''
			)
		)
	),
	'timezone'	=> 'UTC',
 );