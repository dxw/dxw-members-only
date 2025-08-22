import { test, expect, Page } from '@playwright/test';
import { execSync } from 'child_process';

export async function loginAndDeactivateAndActivatePlugin(page:Page) {
  await page.goto('http://localhost/wp-admin');
  await page.locator('#user_login').fill('admin');
  await page.locator('#user_pass').fill('admin');
  await page.locator('#wp-submit').click();
  await page.waitForURL('http://localhost/wp-admin/');
  await page.goto('http://localhost/wp-admin/plugins.php');
  await page.locator('#deactivate-dxw-members-only').click();
  await page.locator('#activate-dxw-members-only').click();
}

test.describe('the upgrade from new-members-only', () => {
  test.beforeEach(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });
  test.describe('when the new option already exists and has a value', () => {
    test('it does nothing', async({ page }) => {
      execSync('local/bin/wp option update dxw_members_only_list_content "foo" --quiet', { stdio: 'inherit'});
      execSync('local/bin/wp option update new_members_only_list_content "bar" --quiet', { stdio: 'inherit'});
      await loginAndDeactivateAndActivatePlugin(page)

      const optionValue = execSync('local/bin/wp option get dxw_members_only_list_content', { encoding: 'utf-8' }).trim();
      expect(optionValue).toEqual("foo");
    });
  });
  test.describe('when only the old option exists, and a default value does not exit for the equivalent new option', () => {
    test('it creates the new option with the same value', async({ page }) => {
      execSync('local/bin/wp option update new_members_only_referrer_allow_list "bar" --quiet', { stdio: 'inherit'});
      await loginAndDeactivateAndActivatePlugin(page)

      const optionValue = execSync('local/bin/wp option get dxw_members_only_referrer_allow_list', { encoding: 'utf-8' }).trim();
      expect(optionValue).toEqual("bar");
    });
  });
  // There is an existing bug where, for options where we set a default value in dmometasettings.php
  // The default value gets set before the activation hook runs to import the old new-members-only values
  // And therefore the old values do not get imported correctly, as the import process sees the options
  // As already populated
  test.describe('when only the old option exists, and a default value does exit for the equivalent new option', () => {
    test.fail('it creates the new option with the old value, not the default', async({ page }) => {
      execSync('local/bin/wp option update new_members_only_list_content "bar" --quiet', { stdio: 'inherit'});
      await loginAndDeactivateAndActivatePlugin(page)

      const optionValue = execSync('local/bin/wp option get dxw_members_only_list_content', { encoding: 'utf-8' }).trim();
      expect(optionValue).toEqual("bar");
    });
  });
});