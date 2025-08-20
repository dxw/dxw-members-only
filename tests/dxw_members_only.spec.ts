import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';

let imageUrl: string

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

test.describe('media uploads', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
    imageUrl = execSync('local/bin/wp media import /usr/src/app/local/puppy.jpg --porcelain=url', { encoding: 'utf-8' }).trim();
  });
  test('upload cannot be viewed when not logged in', async ({ page }) => {
    await page.goto(imageUrl);
    await expect(page).toHaveTitle('Log In ‹ dxw Members Only — WordPress');
    // Confirm we're not logged in
    await expect(page.locator('#wpadminbar')).toHaveCount(0);
  });
  test('upload can be viewed when logged in, and has static max age', async ({ page }) => {
    await page.goto('http://localhost/wp-login.php');
    await page.locator('#user_login').fill('admin');
    await page.locator('#user_pass').fill('admin');
    await page.locator('#wp-submit').click();
    const response = await page.goto(imageUrl);
    await expect(page).not.toHaveTitle('Log In ‹ dxw Members Only — WordPress');
    expect(response?.headers()['cache-control']).toBe('private, max-age=30');
  });
});

test.describe('The cache-control header for the redirect response pointing to the login page', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });
  // Currently, the redirect gets served with the default max cache age
  // That can result in users getting redirected even when correctly authenticated
  // If they try to visit a page they were previously bounced to the login page from
  // This test will fail until that bug is fixed
  test.fail('should always be private, and have a max age of 0', async ({ page }) => {
    var redirectCacheControlHeader: string = '';

    page.on('response', response => {
      if (response.status() == 303) {
        redirectCacheControlHeader = response.headers()['cache-control'];
      }
    });
    await page.goto('http://localhost/');
    expect(redirectCacheControlHeader).toContain('max-age=0');
    expect(redirectCacheControlHeader).toContain('private');
  });
});

test.describe('The cache-control header served once a user has successfully logged in', () => {
  test.beforeAll(async () => {
    execSync('local/bin/wp option delete dxw_members_only_list_content --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_ip_whitelist --quiet', { stdio: 'inherit' });
    execSync('local/bin/wp option delete dxw_members_only_referrer_allow_list --quiet', { stdio: 'inherit' });
  });

  test.fail('should use the default max-age value from the plugin, not the 0 value WordPress core provides', async ({ page }) => {
    await page.goto('http://localhost/wp-login.php');
    await page.locator('#user_login').fill('admin');
    await page.locator('#user_pass').fill('admin');
    await page.locator('#wp-submit').click();

    const response = await page.goto('http://localhost');
    expect(response?.headers()['cache-control']).toContain('max-age=10');
    expect(response?.headers()['cache-control']).not.toContain('max-age=0');
  });
});
