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
* @version $id: ProfileObject.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_ProfileObject extends AMEE_Object
{

	public $profile_uid, $profile_date, $full_path;
	
	public function __construct($options=array())
	{
		$this->profile_uid = isset($options['profile_uid']) ? $options['profile_uid'] : null;
		$this->profile_date = isset($options['profile_date']) ? $options['profile_date'] : null;
		$this->full_path = '/profiles';
		if(!empty( $this->profile_uid )) {
				$this->full_path .=  '/' . $this->profile_uid;
		}
		if(isset($options['path'])) $this->full_path .= preg_match('/^\//', $options['path']) ? $options['path'] : '/' . $options['path'];
		parent:: __construct($options);
	}

}