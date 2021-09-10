<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Devices;

abstract class Base extends \MTM\SMSApi\Models\Base
{
	protected $_hostName=null;
	protected $_contactObjs=array();
	
	public function setHost($hostname)
	{
		$this->_hostName = $hostname;
		return $this;
	}
	public function getHost()
	{
		return $this->_hostName;
	}
	public function getContacts()
	{
		return array_values($this->_contactObjs);
	}
	public function addContact($obj)
	{
		$this->_contactObjs[$obj->getNumber()]	= $obj;
		return $this;
	}
	public function getContactByNumber($nbr, $throw=false)
	{
		foreach ($this->_contactObjs as $cNbr => $cObj) {
			if ($cNbr === $nbr) {
				return $cObj;
			}
		}
		if ($throw === true) {
			throw new \Exception("No contact with that number");
		} else {
			return null;
		}
	}
}