<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Devices\Zte;

class MF833 extends Base
{
	protected function exeRequest($qStr)
	{
		$url	= "http://".$this->getHost()."/".$qStr;
		$cObj	= $this->getClient();
		$cObj->setType("get")->setVerbose(false);
		$cObj->addHeader("Referer", "http://".$this->getHost()."/index.html");
		$cObj->setUrl($url);
		return $cObj->execute();
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
		foreach ($rData->messages as $dataObj) {
			
			if ($dataObj instanceof \stdClass === false) {
				throw new \Exception("Message is not standard class");
			} elseif (property_exists($dataObj, "id") === false) {
				throw new \Exception("Message does not have id");
			} elseif (property_exists($dataObj, "number") === false) {
				throw new \Exception("Message does not have number");
			} elseif (property_exists($dataObj, "content") === false) {
				throw new \Exception("Message does not have content");
			}
			$id	= intval($dataObj->id);
			if (array_key_exists($id, $this->_msgObjs) === false) {
				
				//Decode the message string
				$len	= strlen($dataObj->content) / 4;
				$data	= "";
				for ($x=0; $x < $len; $x++) {
					$data	.= html_entity_decode("&#" . hexdec(substr($dataObj->content, ($x * 4), 4)) . ";", ENT_NOQUOTES, "UTF-8");
				}
				$msgObj					= new \MTM\SMSApi\Models\Messages\Ingress\V1();
				$msgObj->setId($id)->setContent($data);
				$contactObj				= $this->getContactByNumber($dataObj->number, false);
				if ($contactObj === null) {
					$contactObj			= new \MTM\SMSApi\Models\Contacts\Senders\V1();
					$contactObj->setNumber($dataObj->number);
					$this->addContact($contactObj);
				}
				$msgObj->setSender($contactObj);
				$this->_msgObjs[$id]	= $msgObj;
			}
		}
		return array_values($this->_msgObjs);
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
}