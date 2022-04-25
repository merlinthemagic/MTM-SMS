<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Factories\Devices;

class Zte extends Base
{
	public function getMf833($ipAddr=null)
	{
		$rObj	= new \MTM\SMSApi\Models\Devices\Zte\MF833();
		if ($ipAddr !== null) {
			$rObj->setHost($ipAddr);
		}
		return $rObj;
	}
}