<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Contacts;

abstract class Base extends \MTM\SMSApi\Models\Base
{
	protected $_apiObj=null;
	protected $_msgObjs=array();
	protected $_nbr=null;
	protected $_name=null;
	
	public function setApi($obj)
	{
		$this->_apiObj	= $obj;
		return $this;
	}
	public function getApi()
	{
		return $this->_apiObj;
	}
	public function getNumber()
	{
		return $this->_nbr;
	}
	public function setNumber($nbr)
	{
		$this->_nbr = "+".ltrim($nbr, "+");
		return $this;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function setName($name)
	{
		$this->_name = $name;
		return $this;
	}
	public function addMessage($msgObj)
	{
		$this->_msgObjs[$msgObj->getId()]	= $msgObj;
		return $this;
	}
	public function getMessageById($id, $throw=false)
	{
		foreach ($this->_msgObjs as $mId => $msgObj) {
			if ($mId === $id) {
				return $msgObj;
			}
		}
		if ($throw === true) {
			throw new \Exception("No message with that id");
		} else {
			return null;
		}
	}
	public function getMessages()
	{
		return array_values($this->_msgObjs);
	}
}