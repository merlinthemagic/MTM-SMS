<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Devices;

abstract class Base extends \MTM\SMSApi\Models\Base
{
	protected $_hostName=null;
	protected $_msgObjs=array();
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
	public function addContact($obj)
	{
		foreach ($this->getContacts() as $contactObj) {
			if ($contactObj === $obj) {
				return $this;
			}
		}
		$obj->setApi($this);
		$this->_contactObjs[]	= $obj;
		return $this;
	}
	public function &getContacts()
	{
		return $this->_contactObjs;
	}
	public function getContactByNumber($nbr, $throw=false)
	{
		foreach ($this->getContacts() as $contactObj) {
			if ($contactObj->getNumber() === $nbr) {
				return $contactObj;
			}
		}
		if ($throw === true) {
			throw new \Exception("No contact with that number");
		} else {
			return null;
		}
	}
}