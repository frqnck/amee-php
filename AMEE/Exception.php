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
* @version $id: Exception.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

class AMEE_Exception extends Exception {
	
	public function __toString()
	{
		$str = 'Exception "' . __CLASS__ . '" with message "' . $this->getMessage() . '"';
		$str .= ' in ' . $this->getFile() . ':' . $this->getLine();
		$str .= "\nStack trace:\n" . $this->getTraceAsString();
		return $str;
	}

}

class AMEE_BadDataException extends AMEE_Exception {}

class AMEE_AuthFailedException extends AMEE_Exception {}  

class AMEE_PermissionDeniedException extends AMEE_Exception {}

class AMEE_ConnectionFailedException extends AMEE_Exception {}

class AMEE_NotFoundException extends AMEE_Exception {}

class AMEE_UnknownErrorException extends AMEE_Exception {}