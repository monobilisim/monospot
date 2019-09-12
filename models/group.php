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

        // MAC adresleri geçerli mi kontrolü
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
            $errors['macs'] = 'Aşağıdaki MAC adresleri geçersiz:<br>' . implode('<br>', $invalid_macs);
        }

        // MAC adresleri mükerrer mi kontrolü
        $duplicate_macs = array();
        if ($this->id) {
            $previous_groups = Model::factory('Group')->where_not_equal('id', $this->id)->find_many();
        } else {
            $previous_groups = Model::factory('Group')->find_many();
        }
        foreach ($previous_groups as $previous_group) {
            foreach ($macs as $mac) {
                if (strpos($previous_group->macs, $mac) !== false) {
                    $duplicate_macs[] = $mac . ' <em>(Grup adı: ' . $previous_group->name . ', Grup ID\'si: ' . $previous_group->id . ')</em>';
                }
            }
        }

        if ($duplicate_macs) {
            $errors['macs'] = 'Aşağıdaki MAC adresleri başka gruplarda yer alıyor:<br>' . implode('<br>', $duplicate_macs);
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
        return file_put_contents(__DIR__ . '/../settings_group' . $this->id . '.inc', '<?php' . "\n\n" . 'return ' . var_export($post, true) . ';');
    }

    public function deleteSettings()
    {
        unlink(__DIR__ . '/../settings_group' . $this->id . '.inc');
    }

    public function getSettings($posted_settings = false)
    {
        global $hotspot;
        $global_settings = include __DIR__ . '/../settings.inc';

        if ($posted_settings) {
            $group_settings = $posted_settings;
        } else {
            $group_settings = include __DIR__ . '/../settings_group'. $this->id . '.inc';
        }

        if (!isset($hotspot['mac_grup_ayarlari']) || !in_array('authentication', $hotspot['mac_grup_ayarlari'])) {
            $group_settings['authentication'] = $global_settings['authentication'];
            if (isset($global_settings['sms'])) {
                $group_settings['sms'] = $global_settings['sms'];
            } else {
                unset($group_settings['sms']);
            }
            if (isset($global_settings['contact'])) {
                $group_settings['contact'] = $global_settings['contact'];
            } else {
                unset($group_settings['contact']);
            }
            if (isset($global_settings['id_number'])) {
                $group_settings['id_number'] = $global_settings['id_number'];
            } else {
                unset($group_settings['id_number']);
            }
        }

        if (!isset($hotspot['mac_grup_ayarlari']) || !in_array('general', $hotspot['mac_grup_ayarlari'])) {
            $group_settings['terms'] = $global_settings['terms'];
            $group_settings['terms_checked'] = $global_settings['terms_checked'];
            $group_settings['session_timeout'] = $global_settings['session_timeout'];
            $group_settings['valid_for'] = $global_settings['valid_for'];
            $group_settings['valid_for_unit'] = $global_settings['valid_for_unit'];
            $group_settings['disallow_multiple_logins'] = $global_settings['disallow_multiple_logins'];
            $group_settings['disallow_multiple_logins_for'] = $global_settings['disallow_multiple_logins_for'];
            $group_settings['daily_limit'] = $global_settings['daily_limit'];
            $group_settings['weekly_limit'] = $global_settings['weekly_limit'];
            $group_settings['monthly_limit'] = $global_settings['monthly_limit'];
            $group_settings['yearly_limit'] = $global_settings['yearly_limit'];
            $group_settings['min_interval'] = $global_settings['min_interval'];
        }

        $group_settings['daily_global_limit'] = $global_settings['daily_global_limit'];
        $group_settings['name'] = $global_settings['name'];
        $group_settings['color'] = $global_settings['color'];
        $group_settings['items_per_page'] = $global_settings['items_per_page'];
        $group_settings['custom_fields'] = $global_settings['custom_fields'];

        return $group_settings;
    }
}
