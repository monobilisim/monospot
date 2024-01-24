<?php
	
	require_once('Statistics.php');

	class SmsReportItem {
		public $id;
		public $customID;
		public $type;
		public $uuid;
		public $error;
		public $state;
		public $title;
		public $content;
		public $sender;
		public $encoding;
		public $validity;
		public $statistics;
		public $isScheduled;
		public $createDate;
		public $updateDate;
		public $sendingDate;
		public $processingDate;
	}
?>
