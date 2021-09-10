<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Devices\Zte;

abstract class Base extends \MTM\SMSApi\Models\Devices\Base
{
	protected $_clientObj=null;
	
	public function getClient()
	{
		if ($this->_clientObj === null) {
			$cObj				= \MTM\Http\Factories::getClients()->getCurl()->getNewClient();
			$this->_clientObj	= $cObj;
		}
		return $this->_clientObj;
	}
}