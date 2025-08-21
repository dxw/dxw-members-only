<?php

describe(Dxw\MembersOnly\Upgrade::class, function () {
	beforeEach(function () {
		$this->upgrade = new \Dxw\MembersOnly\Upgrade();
	});
	describe('->transfer_new_members_only_options()', function () {
		context('no old options exist', function () {
			it('does nothing', function () {
				allow('get_option')->toBeCalled()->andReturn(null);
				expect('add_option')->not->toBeCalled();

				$this->upgrade->transfer_new_members_only_options();
			});
		});

		context('old options exist, but new ones are already set', function () {
			it('does nothing', function () {
				allow('get_option')->toBeCalled()->andReturn('foo');
				expect('add_option')->not->toBeCalled();

				$this->upgrade->transfer_new_members_only_options();
			});
		});

		context('old options exist, but new ones are not yet set', function () {
			it('sets the new options with the same value as the old ones', function () {
				allow('get_option')->toBeCalled()->andRun(function ($optionName) {
					return str_contains($optionName, 'new_members_only') ? 'foo' : null;
				});
				allow('add_option')->toBeCalled();

				$newOptionNames = [
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
				foreach ($newOptionNames as $optionName) {
					expect('add_option')->toBeCalled()->once()->with('dxw_members_only_' . $optionName, 'foo');
				}

				$this->upgrade->transfer_new_members_only_options();
			});
		});
	});
});
