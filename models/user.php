<?php

class User extends Model
{

	public function defaults()
	{
		global $settings;
		if (isset($settings['authentication']['sms']))
		{
			$this->daily_limit = $settings['daily_limit'];
			$this->weekly_limit = $settings['weekly_limit'];
			$this->monthly_limit = $settings['monthly_limit'];
			$this->yearly_limit = $settings['yearly_limit'];
		}
		if (isset($settings['authentication']['sms']) || isset($settings['authentication']['manual_user']))
		{
			$this->expires = strtotime('+' . $settings['valid_for'] . 'days');
		}
	}

	public function fill($post)
	{
		global $settings;

		if (isset($post['expires'])) // posted from admin screen, we need to sanitize data
		{
			if (isset($post['gsm']))
				$post['gsm'] = ltrim(trim($post['gsm']), '0');

			if ($post['expires'])
				$post['expires'] = strtotime(str_replace('-', '', $post['expires']));
			else
				$post['expires'] = strtotime('+' . $settings['valid_for'] . ' days');
		}
		foreach ($post as $column => $value)
		{
			$this->$column = $value;
		}
	}

	public function validate($post)
	{
		global $settings;

		$val = new Validation;

		$fields = array();

		if ($settings['authentication'] == 'sms')
		{
			$fields['gsm'] = array(
				'rules' => 'required|phone',
				'label' => 'GSM',
			);
			$fields['password'] = array(
				'rules' => 'required',
				'label' => 'Şifre',
			);
			$fields['daily_limit'] = array(
				'rules' => 'required|integer',
				'label' => 'Günlük SMS limiti',
			);
			$fields['weekly_limit'] = array(
				'rules' => 'required|integer',
				'label' => 'Haftalık SMS limiti',
			);
			$fields['monthly_limit'] = array(
				'rules' => 'required|integer',
				'label' => 'Aylık SMS limiti',
			);
			$fields['yearly_limit'] = array(
				'rules' => 'required|integer',
				'label' => 'Yıllık SMS limiti',
			);
		}

		if ($settings['authentication'] == 'id_number')
		{
			$fields['id_number'] = array(
				'rules' => 'required',
				'label' => 'TC Kimlik No',
			);
		}

		$errors = $val->validate($fields, $post);
		return $errors;
	}

}