<?php
	class SendSingleSms {
		public $title;
		public $content;
		public $number;
		public $encoding;
		public $sender;
		public $pushUrl = null;
		public $periodicSettings = null;
		public $sendingDate = null;
		public $validity = 60;
		public $commercial = false;
		public $skipAhsQuery = null;
		public $customID = null;

	 	function toString() {
	 		$jsonSrting = [
				"type" => 1,
    			"sendingType" => 0,
    			"title" => $this->title,
    			"encoding" => $this->encoding,
    			"content" => $this->content,
    			"number" => $this->number,
    			"sender" => $this->sender,
    			"periodicSettings" => $this->periodicSettings,
    			"sendingDate" => $this->sendingDate,
    			"validity" => $this->validity,
    			"commercial" => $this->commercial
			];

			if(!is_null($this->pushUrl)){
				$jsonSrting["pushSettings"] = [
					"url" => $this->pushUrl
				];
    		}

			if(!is_null($this->skipAhsQuery)){
				$jsonSrting["skipAhsQuery"] = $this->skipAhsQuery;
    		}

    		if(!is_null($this->customID)){
				$jsonSrting["customID"] = $this->customID;
    		}

	    	return $jsonSrting;
	  	}
	}
?>