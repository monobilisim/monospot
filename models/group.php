<?php

class Group extends Model
{

	public function fillDefaults()
	{
		global $settings;
		$this->settings = $settings;
	}

	public function fill($post)
	{
        foreach ($post as $column => $value)
        {
            $this->$column = $value;
        }
	}

	public function validate($post, $new = false)
    {
        $val = new Validation;

        $fields = array();


        $fields['name'] = array(
            'rules' => 'required|name',
            'label' => 'Grup Adı',
        );
        $fields['macs'] = array(
            'rules' => 'required|macs',
            'label' => 'MAC Adresleri',
        );

        $errors = $val->validate($fields, $post);

        $macs = array_unique(explode("\n", $_POST['group']['macs']));

        $invalid_macs = array();
        foreach ($macs as $i => $mac) {
            if ($mac) {
                if (preg_match('/(([0-9A-Fa-f]{2}[-:]){5}[0-9A-Fa-f]{2})|(([0-9A-Fa-f]{4}\.){2}[0-9A-Fa-f]{4})/', $mac)) {
                    $macs[$i] = strtolower(str_replace('-', ':', $mac));
                } else {
                    $invalid_macs[] = $mac;
                }
            }
        }

        if ($invalid_macs) {
            $errors['macs'] = "Aşağıdaki MAC adresleri geçersiz:<br>" . implode("<br>", $invalid_macs);
        }

        return $errors;
    }

    public function save()
    {
        $macs = explode("\n", $this->macs);
        foreach ($macs as $i => $mac) {
            if (preg_match('/(([0-9A-Fa-f]{2}[-:]){5}[0-9A-Fa-f]{2})|(([0-9A-Fa-f]{4}\.){2}[0-9A-Fa-f]{4})/', $mac)) {
                $macs[$i] = strtolower(str_replace('-', ':', $mac));
            }
        }
        $this->macs = implode("\n", $macs);
        return parent::save();
    }

    public function saveSettings($post)
    {
        unset($post['group']);
        return file_put_contents(__DIR__ . '/../settings_group' . $this->id . '.inc', '<?php' . "\n\n" . 'return ' . var_export($_POST, true) . ';');
    }
}
