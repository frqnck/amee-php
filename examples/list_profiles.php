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
* @version $id: list_profiles.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require 'config.php';

require '../AMEE.php';
$AMEE = new AMEE($config);
$Connection = $AMEE->getConnection();

try {
	$p = new AMEE_Profile();
	$profiles = $p->getList($Connection, 12);
} catch(Exception $e) {
	die('Error: ' . $e->getMessage()."\n");
}

// List all available profiles
$total = count($profiles);
echo $total . ($total == 1 ? " profile" : " profiles");
echo " retrieved:\n";
foreach($profiles as $profile) {
  $str .= "\n  - UID #{$profile->uid}";
}
echo "-----------------------" . $str . "\n";
