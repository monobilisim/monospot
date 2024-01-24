<?php
	class SendOTPSms {
		public $content;
		public $number;
		public $encoding;
		public $sender;
		public $pushUrl = null;
		public $validity = 3;
		public $commercial = false;
		public $skipAhsQuery = null;
		public $customID = null;

	 	function toString() {
	 		$jsonSrting = [
    			"number" => $this->number,
				"sender" => $this->sender,
    			"encoding" => $this->encoding,
    			"content" => $this->content,
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