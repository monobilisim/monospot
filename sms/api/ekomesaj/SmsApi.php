<?php

	require_once('Err.php');
	require_once('DataCoding.php');
	require_once('SendSingleSms.php');
	require_once('SendOTPSms.php');
	require_once('SendMultiSms.php');
	require_once('SendDynamicSms.php');
	require_once('SmsResponse.php');
	require_once('CancelResponse.php');
	require_once('GetCreditResponse.php');
	require_once('GetSendersResponse.php');
	require_once('GetSmsReports.php');
	require_once('GetSmsReportsResponse.php');
	require_once('GetSmsReportDetails.php');
	require_once('GetSmsReportDetailsResponse.php');

	class SmsApi {
		private $username;
		private $password;
		private $host;

	 	public function __construct($host, $username, $password, $port = '9587') {
	        $this->username = $username;
	        $this->password = $password;
	        $this->host = "http://".$host.":9587/";	
	        
	        if($port == '9588'){
	        	$this->host = "https://".$host.":9588/";
	        }
	    }

	    function getCredit(){
	    	$response = new GetCreditResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'user/credit');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, "");
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->credit = $result['data']['credit'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function getSenders(){
	    	$response = new GetSendersResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/list-sender');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, "");
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->totalRecord = $result['data']['stats']['totalRecord'];

						$senders = [];
						$data = $result['data']['list'];
						
						foreach($data as $item){
							$temp = new Sender();
							$temp->uuid = $item['uuid'];
							$temp->status = $item['status'];
							$temp->title = $item['title'];

							$senders[] = $temp;
						}

						$response->list = $senders;

						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function cancel($request){
	    	$response = new CancelResponse();

	 		$username = $this->username;
	 		$password = $this->password;

		 	$json = json_encode(["id" => $request]);

			try {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/cancel');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->status = $result['data']['status'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function cancelCustomId($request){
	    	$response = new CancelResponse();

	 		$username = $this->username;
	 		$password = $this->password;

		 	$json = json_encode(["customID" => $request]);

			try {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/cancel');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->status = $result['data']['status'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function sendOTPSms($request){
	    	$response = new SmsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {	
		 		$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/create-otp');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->pkgID = $result['data']['pkgID'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    } 

	    function sendSingleSms($request){
	    	$response = new SmsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {	
		 		$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/create');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->pkgID = $result['data']['pkgID'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    } 

	    function sendMultiSms($request){
	    	$response = new SmsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {	
		 		$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/create');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->pkgID = $result['data']['pkgID'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    } 

	    function sendDynamicSms($request){
	    	$response = new SmsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

			try {	

				$request->content = "Dinamik Sms";
				
		 		$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/create');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->pkgID = $result['data']['pkgID'];
						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function getSmsReports($request){
	    	$response = new GetSmsReportsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

	 		if($request->pageSize<10 || 1000<$request->pageSize){
	 			$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = "pageSize 10 ile 1000 arasında olmalıdır";
				$response->err->message = "pageSize 10 ile 1000 arasında olmalıdır";

				return $response;
	 		}

			try {

				$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/list');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->totalRecord = $result['data']['stats']['totalRecord'];

						$reports = [];
						$data = $result['data']['list'];
						
						foreach($data as $item){
							$temp = new SmsReportItem();
							$temp->id = $item['id'];
							$temp->customID = isset($item['customID']) ? $item['customID']:"";
							$temp->type = $item['type'];
							$temp->uuid = $item['uuid'];
							$temp->error = $item['error'];
							$temp->state = $item['state'];
							$temp->title = $item['title'];
							$temp->content = $item['content'];
							$temp->sender = $item['senders'][0];
							$temp->encoding = $item['encoding'];
							$temp->validity = $item['validity'];
							$temp->isScheduled = $item['isScheduled'];
							$temp->sendingDate = $item['sendingDate'];
							$temp->processingDate = $item['processingDate'];
							$temp->createDate = $item['processInfo']['create']['date'];
							$temp->updateDate = $item['processInfo']['update']['date'];

							$statistics = new Statistics();

							$statistics->total = $item['statistics']['total'];
							$statistics->credit = $item['statistics']['credit'];
							$statistics->rCount = $item['statistics']['rCount'];
							$statistics->delivered = $item['statistics']['delivered'];
							$statistics->undelivered = $item['statistics']['undelivered'];
							$temp->statistics = $statistics;

							$reports[] = $temp;
						}

						$response->list = $reports;

						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	    function getSmsReportDetails($request){
	    	$response = new GetSmsReportDetailsResponse();

	 		$username = $this->username;
	 		$password = $this->password;

	 		if($request->pageSize<10 || 1000<$request->pageSize){
	 			$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = "pageSize 10 ile 1000 arasında olmalıdır";
				$response->err->message = "pageSize 10 ile 1000 arasında olmalıdır";

				return $response;
	 		}

			try {

				$json = json_encode($request->toString());

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->host.'sms/list-item');
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
        			"authorization: Basic ".base64_encode($username.":".$password),
					'Content-Length: ' . strlen($json))
				);

				$result = curl_exec($ch);

				$result = json_decode($result, true);

 				$info = curl_getinfo($ch);
				$hasError = 'Hata';

				switch (json_last_error()) {
				    case JSON_ERROR_NONE:
				        $hasError = '';
				    break;
				    case JSON_ERROR_DEPTH:
				        $hasError = 'JSON_ERROR_DEPTH';
				    break;
				    case JSON_ERROR_STATE_MISMATCH:
				        $hasError = 'JSON_ERROR_STATE_MISMATCH';
				    break;
				    case JSON_ERROR_CTRL_CHAR:
				        $hasError = 'JSON_ERROR_CTRL_CHAR';
				    break;
				    case JSON_ERROR_SYNTAX:
				        $hasError = 'JSON_ERROR_SYNTAX';
				    break;
				    case JSON_ERROR_UTF8:
				        $hasError = 'JSON_ERROR_UTF8';
				    break;
				    default:
				        $hasError = 'Hata';
				    break;
				}

				if($hasError == ''){
					if(is_null($result['err'])){
						$response->totalRecord = $result['data']['stats']['totalRecord'];

						$reports = [];
						$data = $result['data']['list'];
						
						foreach($data as $item){
							$temp = new SmsReportItem();
							$temp->id = $item['id'];
							$temp->msg = $item['msg'];
							$temp->error = $item['error'];
							$temp->state = $item['state'];
							$temp->credit = $item['credit'];
							$temp->sender = $item['sender'];
							$temp->target = $item['target'];
							$temp->setState = $item['setState'];
							$temp->deliveryDate = $item['deliveryDate'];
							$temp->sendingDate = $item['sendingDate'];
							$temp->processingDate = $item['processingDate'];

							$reports[] = $temp;
						}

						$response->list = $reports;

						$response->err = null;
					}else{
						$response->err = new Err();
						$response->err->status = $info['http_code'];
						$response->err->code = $result['err']['code'];
						$response->err->message = $result['err']['message'];
					}
				}else{
					$response->err = new Err();
					$response->err->status = 500;
					$response->err->code = $hasError;
					$response->err->message = $hasError;
				}

			}catch (Exception $e) {
				$response->err = new Err();
				$response->err->status = 500;
				$response->err->code = $e->getMessage();
				$response->err->message = $e->getMessage();
			}

			return $response;
	    }

	}
?>