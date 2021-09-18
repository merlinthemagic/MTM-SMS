<?php
//© 2021 Martin Peter Madsen
namespace MTM\SMSApi\Models\Contacts;

class V1 extends Base
{
	public function newMessage($id)
	{
		$msgObj				= new \MTM\SMSApi\Models\Messages\V1();
		$msgObj->setSender($this)->setId($id);
		$this->addMessage($msgObj);
		return $msgObj;
	}
	public function sendText($nbr, $str)
	{
		$apiObj		= $this->getApi();
		$recvObj	= $apiObj->getContactByNumber($nbr, false);
		if ($recvObj === null) {
			$recvObj	= $apiObj->newContact($nbr);
		}
		
		//Encode the message body
		$len		= strlen($str);
		$msgBody	= "";
		for ($x=0; $x < $len; $x++) {
			$char	= utf8_encode(substr($str, $x, 1));
			$char	= dechex((ord(substr($char, 1, 1)) * 256) + ord(substr($char, 0, 1)));
			$chLen	= strlen($char);
			if ($chLen < 4) {
				$char = str_repeat("0", (4 -$chLen)).$char;
			}
			$msgBody	.= $char;
		}

		$time		= time();
		$date		= \DateTime::createFromFormat("U", $time, new \DateTimeZone("UTC"));
		$smsTime	= $date->format("y;m;d;H;m;i").";+1";
		$recvNbr	= urlencode($recvObj->getNumber());
		
		$apiObj->getMessages();
		$msgObjs	= $this->getMessages();
		$lastId		= -1;
		if (count($msgObjs) > 0) {
			$lastMsg	= end($msgObjs);
			$lastId		= $lastMsg->getId();
		}

		$url		= "http://".$apiObj->getHost()."/goform/goform_set_cmd_process";
		$cObj		= $apiObj->getClient();
		
		//Something f'ed up when setting with addData()....
		$data	= "isTest=false";
		$data	.= "&goformId=SEND_SMS";
		$data	.= "&notCallback=true";
		$data	.= "&Number=".$recvNbr;
		$data	.= "&sms_time=".$smsTime;
		$data	.= "&MessageBody=".$msgBody;
		$data	.= "&ID=-1";
		$data	.= "&encode_type=UNICODE";
		$cObj->setData($data);
		
		$cObj->addHeader("Referer", "http://".$apiObj->getHost()."/index.html");
		$cObj->setType("post");
		$cObj->setUrl($url);
	
		$rData	= $cObj->execute();
		$json	= json_decode($rData);
		if ($json instanceof \stdClass === false) {
			throw new \Exception("Return is not json");
		} elseif (property_exists($json, "result") === false) {
			throw new \Exception("Return does not have result: ".$rData);
		}
		if ($json->result === "success") {
			
			//Locate the sent message
			$curId		= 0;
			$tTime		= time() + 10;
			while (true) {
				usleep(250000);
				$apiObj->getMessages();
				$msgObjs	= $this->getMessages();
				if (count($msgObjs) > 0) {
					$lastMsg	= end($msgObjs);
					$curId		= $lastMsg->getId();
				}
				if ($curId != $lastId) {
					foreach ($msgObjs as $msgObj) {
						if (
							$msgObj->getId() > $lastId
							&& $msgObj->getReceiver()->getNumber() == $recvObj->getNumber()
						) {
							return $msgObj;
						}
					}
				}
				if ($tTime < time()) {
					throw new \Exception("Failed to locate sent message");
				}
			}
			
		} else {
			throw new \Exception("Failed with result:".$json->result);
		}
	}
}