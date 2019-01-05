<?php

class User extends Model
{

	public function fillDefaults()
	{
		global $settings;
		$this->daily_limit = $settings['daily_limit'];
		$this->weekly_limit = $settings['weekly_limit'];
		$this->monthly_limit = $settings['monthly_limit'];
		$this->yearly_limit = $settings['yearly_limit'];
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

	public function validate($post, $new = false)
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

		if (isset($post['username']) && $new)
		{
			$user = Model::factory('User')->where_equal('username', $_POST['user']['username'])->find_one();
			if ($user) $errors['username'] = 'Aynı kullanıcı adına sahip bir kullanıcı zaten var.';
		}

		return $errors;
	}

}
