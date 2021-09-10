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
	public function getSelfContact()
	{
		$nbr			= "+".$this->getMsIsdn();
		$contactObj		= $this->getContactByNumber($nbr, false);
		if ($contactObj === null) {
			$contactObj			= new \MTM\SMSApi\Models\Contacts\Senders\V1();
			$contactObj->setNumber($nbr);
			$this->addContact($contactObj);
		}
		return $contactObj;
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
			
			$id	= intval($dataObj->id);
			if (array_key_exists($id, $this->_msgObjs) === false) {
				
				$contactObj	= $this->getContactByNumber($dataObj->number, false);
				if ($contactObj === null) {
					$contactObj			= new \MTM\SMSApi\Models\Contacts\V1();
					$contactObj->setNumber($dataObj->number);
					$this->addContact($contactObj);
				}
				
				if ($dataObj->tag === 0) {
					//we are receiving this message
					$msgObj		= new \MTM\SMSApi\Models\Messages\Ingress\V1();
					$msgObj->setSender($contactObj);
					$msgObj->setReceiver($this->getSelfContact());
					
				} else {
					//we sent this message
					$msgObj		= new \MTM\SMSApi\Models\Messages\Egress\V1();
					$msgObj->setSender($this->getSelfContact());
					$msgObj->setReceiver($contactObj);
				}
				
				$msgObj->setId($id);
				
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
	
	
	//http://192.168.0.1/goform/goform_get_cmd_process?isTest=false&cmd=apn_interface_version%2Cwifi_coverage%2Cm_ssid_enable%2Cimei%2Cwan_active_band%2Cnetwork_type%2Crssi%2Crscp%2Clte_rsrp%2Cimsi%2Csim_imsi%2Ccr_version%2Cwa_version%2Chardware_version%2Cweb_version%2Cwa_inner_version%2CMAX_Access_num%2CSSID1%2CAuthMode%2CWPAPSK1_encode%2Cm_SSID%2Cm_AuthMode%2Cm_HideSSID%2Cm_WPAPSK1_encode%2Cm_MAX_Access_num%2Clan_ipaddr%2Cmac_address%2Cmsisdn%2CLocalDomain%2Cwan_ipaddr%2Cstatic_wan_ipaddr%2Cipv6_wan_ipaddr%2Cipv6_pdp_type%2Cipv6_pdp_type_ui%2Cpdp_type%2Cpdp_type_ui%2Copms_wan_mode%2Cppp_status&multi_data=1&_=1631284031094
}