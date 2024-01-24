<?php

	class GetSmsReportDetails {
		public $pkgID;
		public $target = null;
		public $state = null;
		public $customID = null;
		public $pageIndex = 0;
		public $pageSize = 100;
	
		function toString() {
	 		$jsonSrting = [
				"pkgID" => $this->pkgID,
				"target" => $this->target,
				"state" => $this->state,
				"pageIndex" => $this->pageIndex,
				"pageSize" => $this->pageSize
			];

			if(!is_null($this->customID)){
				$jsonSrting["customID"] = $this->customID;
    		}
    		
	    	return $jsonSrting;
	  	}
	}
?>				