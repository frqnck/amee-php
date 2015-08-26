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
* @version $id: create_profile_item.php,v 0.1.0a 2009/07/31 7:34:43 franck Exp $*/

require 'config.php';

require '../AMEE.php';
$AMEE = new AMEE($config);
$Connection = $AMEE->getConnection();

try {

	# Create a new profile item
	$Category = new AMEE_ProfileCategory();
	$cat = $Category->get($Connection, '/profiles/595A5DB77A32/home/energy/quantity');
	echo "loaded category: {$cat->name}\n";

	$Item = new AMEE_ProfileItem();
	$newItem = $Item->create($Connection, $cat, '66056991EE23');

	/*
	TODO: dataItemUid: 	The UID of the Data Item which a new Profile Item should be based upon. This should be obtained from a DrillDown request.
	*/

	if($newItem) {
		echo "created item in {$cat->name} OK";
	} else {
	  echo "error creating item in {$cat->name}";
	}

} catch(Exception $e) {
	die('Error: ' . $e->getMessage()."\n");
}
