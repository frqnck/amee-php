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
* @version $id: view_profile_item.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require 'config.php';

require '../AMEE.php';
$AMEE = new AMEE($config);
$Connection = $AMEE->getConnection();

$Co2 = new AMEE_ProfileItem();

$path = '/profiles/0BCA29F951A7/home/energy/quantity';

echo "TODO: not finnish!\n";

try {
	//Create a new profile item
	$Profile = $Co2->get($Connection, $path);
} catch(Exception $e) {
	die('Error: ' . $e->getMessage());
}
$str = "\nLoaded item: {$Profile->name}";
foreach($Profile->values as $v) {
	$str .= "\n - ${v['name']}: ${v['value']}";
}
$str .= "\n - total: {$item->total_amount}";
echo "---------------------" . $str . "\n";