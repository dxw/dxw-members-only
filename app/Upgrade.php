<?php

namespace Dxw\MembersOnly;

class Upgrade
{
	/*
	* This plugin was previously known as new-members-only, and options were
	* named as new_members_only_[option_name]. If you previously had that plugin installed,
	* and you don't currently have dxw-members-only options setup, this will take the
	* new-members-only options and copy them across to the dxw_members_only format.
	*/

	public function transfer_new_members_only_options(): void
	{
		$options = [
			'list_type',
			'list_content',
			'ip_whitelist',
			'referrer_allow_list',
			'redirect',
			'redirect_root',
			'upload_default',
			'max_age',
			'max_age_static',
			'max_age_public'
		];
		foreach ($options as $option) {
			$old_option = 'new_members_only_' . $option;
			$new_option = 'dxw_members_only_'. $option;

			//use is_null because don't want to overwrite existing but empty options
			if (!is_null(get_option($old_option, null)) && is_null(get_option($new_option, null))) {
				add_option($new_option, get_option($old_option));
			}
		}
	}
}
