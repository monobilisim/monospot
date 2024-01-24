<?php
	class SmsItem {
		public $nr;
	 	public $msg;

	 	public function __construct($nr, $msg) {
	        $this->nr = $nr;
	        $this->msg = $msg;
	    }
	}
?>