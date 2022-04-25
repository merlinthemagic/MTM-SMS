<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Factories;

class Devices extends Base
{
	public function getZte()
	{
		if (array_key_exists(__FUNCTION__, $this->_s) === false) {
			$this->_s[__FUNCTION__]	= new \MTM\SMSApi\Factories\Devices\Zte();
		}
		return $this->_s[__FUNCTION__];
	}
}