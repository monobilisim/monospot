<?php

	if ($_POST['form_id'] == 'custom_login')
	{
		$user = Model::factory('User')->where('pasaport_no', $_POST['user']['pasaport_no'])->find_one();

		if (!$user)
		{
			$user = Model::factory('User')->create();
			$user->fillDefaults();
		}

		$user->fill($_POST['user']);

		$result = file_get_contents('http://10.1.70.103:8081/?pasaport_no=' . $_POST['user']['pasaport_no'] . '&hasta_no=' . $_POST['user']['hasta_no']);

		$found = $result === 'true';

		if ($found)
		{
			login($user, 'pasaport_no');
		}
		else
		{
			captiveportal_logportalauth($user->pasaport_no,$clientmac,$clientip,"FAILURE");
			$message = 'custom_error';
		}

		$form = 'custom_login';
	}
