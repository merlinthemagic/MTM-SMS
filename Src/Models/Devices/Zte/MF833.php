<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Devices\Zte;

class MF833 extends Base
{
	protected $_selfContact=null;
	
	public function newContact($nbr)
	{
		$cObj		= new \MTM\SMSApi\Models\Contacts\V1();
		$cObj->setApi($this)->setNumber($nbr);
		$this->addContact($cObj);
		return $cObj;
	}
	public function getSelfContact()
	{
		if ($this->_selfContact === null) {
			$nbr	= "+".$this->getMsIsdn();
			$cObj	= $this->getContactByNumber($nbr, false);
			if ($cObj === null) {
				$cObj	= $this->newContact($nbr);
			}
			$this->_selfContact	= $cObj;
		}
		return $this->_selfContact;
	}
	public function getMessages()
	{
		##TODO: paginate
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=sms_data_total&page=0&data_per_page=500&mem_store=1&tags=10&order_by=order+by+id+desc";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "messages") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		$rObjs		= array();
		$tFormat	= "Y-m-d H:i:s";
		$timeTool	= \MTM\Utilities\Factories::getTime()->getUnixEpochTool();
		foreach ($rData->messages as $dataObj) {
			
			if ($dataObj instanceof \stdClass === false) {
				throw new \Exception("Message is not standard class");
			} elseif (property_exists($dataObj, "id") === false) {
				throw new \Exception("Message does not have id");
			} elseif (property_exists($dataObj, "number") === false) {
				throw new \Exception("Message does not have number");
			} elseif (property_exists($dataObj, "content") === false) {
				throw new \Exception("Message does not have content");
			} elseif (property_exists($dataObj, "tag") === false) {
				throw new \Exception("Message does not have a tag");
			} elseif (property_exists($dataObj, "date") === false) {
				throw new \Exception("Message does not have date");
			}
			
			$nbr	= trim($dataObj->number);
			$cObj	= $this->getContactByNumber($nbr, false);
			if ($cObj === null) {
				$cObj	= $this->newContact($nbr);
			}
			if ($dataObj->tag == 0) {
				//we are receiving this message
				$sendObj	= $cObj;
				$recvObj	= $this->getSelfContact();
			} else {
				//we sent this message
				$sendObj	= $this->getSelfContact();
				$recvObj	= $cObj;
			}
			
			$id			= intval($dataObj->id);
			$msgObj		= $sendObj->getMessageById($id, false);
			if ($msgObj === null) {
				$msgObj		= $sendObj->newMessage($id);
				
				//Set the time
				$tps		= explode(",", $dataObj->date);
				$inTime		= "20".$tps[0]."-".$tps[1]."-".$tps[2]." ".$tps[3].":".$tps[4].":".$tps[5];
				$time		= $timeTool->getFromUtcByFormat($inTime, $tFormat);
				$msgObj->setTime($time);
				
				//Decode the message string
				$len	= strlen($dataObj->content) / 4;
				$data	= "";
				for ($x=0; $x < $len; $x++) {
					$data	.= html_entity_decode("&#" . hexdec(substr($dataObj->content, ($x * 4), 4)) . ";", ENT_NOQUOTES, "UTF-8");
				}
				$msgObj->setContent($data);
				
				//set receiver
				$msgObj->setReceiver($recvObj);
			}
			$rObjs[]	= $msgObj;
		}
		return $rObjs;
	}
	public function getRssi()
	{
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=rssi&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "rssi") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return intval($rData->rssi);
	}
	public function getNetworkType()
	{
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=network_type&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "network_type") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return $rData->network_type;
	}
	public function getWanActiveBand()
	{
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=wan_active_band&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "wan_active_band") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return $rData->wan_active_band;
	}
	public function getRscp()
	{
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=rscp&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "rscp") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return intval($rData->rscp);
	}
	public function getLteRsrp()
	{
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=lte_rsrp&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "lte_rsrp") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return intval($rData->lte_rsrp);
	}
	public function getMsIsdn()
	{
		//Phone number
		//Src: https://www.wirelesslogic.com/iot-glossary/what-is-msisdn/
		$qStr		= "goform/goform_get_cmd_process/?isTest=false&cmd=msisdn&multi_data=1";
		$jsonData	= $this->exeRequest($qStr);
		$rData		= json_decode($jsonData);
		if (
			$rData instanceof \stdClass === false
			|| property_exists($rData, "msisdn") === false
		) {
			throw new \Exception("Invalid json return:".$jsonData);
		}
		return $rData->msisdn;
	}
	protected function exeRequest($qStr)
	{
		$url	= "http://".$this->getHost()."/".$qStr;
		$cObj	= $this->getClient();
		$cObj->setType("get")->addHeader("Referer", "http://".$this->getHost()."/index.html");
		$cObj->setData(array())->setUrl($url);
		return $cObj->execute();
	}
}