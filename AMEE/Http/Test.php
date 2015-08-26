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
* @version $id: Test.php,v 0.1.0a 2009/07/31 7:38:40 franck Exp $*/

class AMEE_Http_Test extends AMEE_Http
{

	public function request($request, $format=null)
	{
		echo self::debug(__METHOD__, array($request, $format));
		$res = '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}';
		return array('body'=>$res);
	}

	public function __call($k, $args)
	{
		echo self::debug(__CLASS__.'::'.$k, $args);
	}


	public function send_request($request, $options=null)
	{
		echo self::debug(__METHOD__, array($request, $options));
	}

	public function get($path)
	{
		echo self::debug(__METHOD__, $path);
		$res = '{"amountPerMonth":0,"userValueChoices":{"choices":[{"value":"","name":"distanceKmPerYear"},{"value":"","name":"journeysPerYear"},{"value":"-999","name":"lat1"},{"value":"-999","name":"lat2"},{"value":"-999","name":"long1"},{"value":"-999","name":"long2"}],"name":"userValueChoices"},"path":"/transport/plane/generic/AD63A83B4D41","dataItem":{"modified":"2007-08-01 09:00:41.0","created":"2007-08-01 09:00:41.0","itemDefinition":{"uid":"441BF4BEA15B"},"itemValues":[{"value":"0","uid":"127612FA4921","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"8CB8A1789CD6","name":"kgCO2PerJourney"},"uid":"653828811D42","path":"kgCO2PerPassengerJourney","name":"kgCO2 Per Passenger Journey"}},{"value":"0.158","uid":"7F27A5707101","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km","itemValueDefinition":{"valueDefinition":{"valueType":"DECIMAL","uid":"996AE5477B3F","name":"kgCO2PerKm"},"uid":"D7B4340D9404","path":"kgCO2PerPassengerKm","name":"kgCO2 Per Passenger Km"}},{"value":"-","uid":"FF50EC918A8E","path":"size","name":"Size","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"5D7FB5F552A5","path":"size","name":"Size"}},{"value":"domestic","uid":"FDD62D27AA15","path":"type","name":"Type","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"C376560CB19F","path":"type","name":"Type"}},{"value":"DfT INAS Division, 29 March 2007","uid":"9BE08FBEC54E","path":"source","name":"Source","itemValueDefinition":{"valueDefinition":{"valueType":"TEXT","uid":"CCEB59CACE1B","name":"text"},"uid":"0F0592F05AAC","path":"source","name":"Source"}}],"label":"domestic","dataCategory":{"uid":"FBA97B70DBDF","path":"generic","name":"Generic"},"uid":"AD63A83B4D41","environment":{"uid":"5F5887BCF726"},"path":"","name":"AD63A83B4D41"}}';
		return array('body'=>$res);
	}

}