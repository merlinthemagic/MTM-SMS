<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Messages;

abstract class Base extends \MTM\SMSApi\Models\Base
{
	protected $_msgId=null;
	protected $_time=null;
	protected $_senderObj=null;
	protected $_recvObj=null;
	protected $_content=null;
	
	public function getId()
	{
		return $this->_msgId;
	}
	public function setId($id)
	{
		$this->_msgId = $id;
		return $this;
	}
	public function getContent()
	{
		return $this->_content;
	}
	public function setContent($data)
	{
		$this->_content = $data;
		return $this;
	}
	public function getTime()
	{
		return $this->_time;
	}
	public function setTime($epoch)
	{
		$this->_time = $epoch;
		return $this;
	}
	public function getSender()
	{
		return $this->_senderObj;
	}
	public function setSender($obj)
	{
		$this->_senderObj = $obj;
		return $this;
	}
	public function getReceiver()
	{
		return $this->_recvObj;
	}
	public function setReceiver($obj)
	{
		$this->_recvObj = $obj;
		return $this;
	}
}