import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

test.beforeAll(async() => {
  execSync('local/bin/wp option update dxw_members_only_max_age "10" --quiet', { stdio: 'inherit'});
  execSync('local/bin/wp option update dxw_members_only_max_age_static "30" --quiet', { stdio: 'inherit'});
  execSync('local/bin/wp option update dxw_members_only_max_age_public "600" --quiet', { stdio: 'inherit'});
});

test.describe('when all allow lists are blank', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });
  test('Root URL redirects to wp-login.php', async ({ page }) => {
    await page.goto('http://localhost/');
    await expect(page).toHaveTitle('Log In ‹ dxw Members Only — WordPress');
  });
  test('can view root URL once logged in', async ({ page }) => {
    await page.goto('http://localhost/');
    await page.locator('#user_login').fill('admin');
    await page.locator('#user_pass').fill('admin');
    await page.locator('#wp-submit').click();
    await expect(page).toHaveTitle('dxw Members Only');
    // Confirm we're logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(1);
  });
  test('are redirected to required page after login', async ({ page }) => {
    await page.goto('http://localhost/sample-page/');
    await page.locator('#user_login').fill('admin');
    await page.locator('#user_pass').fill('admin');
    await page.locator('#wp-submit').click();
    await expect(page).toHaveTitle('Sample Page – dxw Members Only');
    // Confirm we're logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(1);
  });
});

test.describe('when the user\'s IP address is included in the IP allow list', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option update dxw_members_only_ip_whitelist "0.0.0.0/0" --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });
  test('can view site without logging in, and it is served with a cache control age of the default max age', async ({ page }) => {
    const response = await page.goto('http://localhost/');
    await expect(page).toHaveTitle('dxw Members Only');
    // Confirm we're not logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(0);
    expect(response?.headers()['cache-control']).toBe('private, max-age=10');
  });
});

test.describe('when a specific URL is allow-listed', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option update dxw_members_only_list_content "/sample-page" --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });
  test('URL can be viewed without logging in, and is served with a cache control age of the public max age', async ({ page }) => {
    const response = await page.goto('http://localhost/sample-page/');
    await expect(page).toHaveTitle('Sample Page – dxw Members Only');
    // Confirm we're not logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(0);
    expect(response?.headers()['cache-control']).toBe('public, max-age=600');
  });
  test('other URLs still require a login', async ({ page }) => {
    await page.goto('http://localhost/');
    await expect(page).toHaveTitle('Log In ‹ dxw Members Only — WordPress');
    // Confirm we're not logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(0);
  });
});