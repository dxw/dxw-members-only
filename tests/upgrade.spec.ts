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
  test.describe('when the new options already exist', () => {
    test('it does nothing', async({ page }) => {
      execSync('local/bin/wp option update dxw_members_only_list_content "foo" --quiet', { stdio: 'inherit'});
      execSync('local/bin/wp option update new_members_only_list_content "bar" --quiet', { stdio: 'inherit'});
      await loginAndDeactivateAndActivatePlugin(page)

      const optionValue = execSync('local/bin/wp option get dxw_members_only_list_content', { encoding: 'utf-8' }).trim();
      expect(optionValue).toEqual("foo");
    });
  });
  test.describe('when only the old options exist', () => {
    test('it creates the new options with the same values', async({ page }) => {
      execSync('local/bin/wp option update new_members_only_list_content "bar" --quiet', { stdio: 'inherit'});
      await loginAndDeactivateAndActivatePlugin(page)

      const optionValue = execSync('local/bin/wp option get dxw_members_only_list_content', { encoding: 'utf-8' }).trim();
      expect(optionValue).toEqual("bar");
    });
  });
});