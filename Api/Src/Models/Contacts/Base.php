<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Contacts;

abstract class Base extends \MTM\SMSApi\Models\Base
{
	protected $_apiObj=null;
	protected $_nbr=null;
	protected $_name=null;
	
	public function getNumber()
	{
		return $this->_nbr;
	}
	public function setNumber($nbr)
	{
		$this->_nbr = $nbr;
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
	public function setApi($obj)
	{
		$this->_apiObj	= $obj;
		return $this;
	}
	public function getApi()
	{
		return $this->_apiObj;
	}
}