<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi;

class Facts
{
	//USE: $aFact		= \MTM\SMSApi\Facts::__METHOD_();
	
	protected static $_s=array();
	
	public static function getDevices()
	{
		if (array_key_exists(__FUNCTION__, self::$_s) === false) {
			self::$_s[__FUNCTION__]	=	new \MTM\SMSApi\Factories\Devices();
		}
		return self::$_s[__FUNCTION__];
	}
}