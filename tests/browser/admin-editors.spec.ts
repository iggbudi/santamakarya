import { test, expect } from '@playwright/test';
import fs from 'node:fs';
test('admin draft editors load and identity save leaves public unchanged', async({page,request})=>{
 test.skip(!fs.existsSync('.runtime/admin-account.json'),'Akun lokal belum disiapkan');
 const account=JSON.parse(fs.readFileSync('.runtime/admin-account.json','utf8').replace(/^\uFEFF/,''));
 const before=await (await request.get('/')).text();
 await page.goto('/admin/login'); await page.locator('input[type=email]').fill(account.email);
 await page.locator('input[type=password]').fill(account.password); await page.locator('button[type=submit]').click(); await page.waitForURL('**/admin');
 for(const slug of ['identity-settings','hero-slideshow','about-slideshow','media-assets','portfolio-projects','portfolio-categories']) {
  await page.goto('/admin/'+slug); await expect(page.locator('h1')).toBeVisible();
  await expect(page.locator('body')).not.toContainText('Server Error');
  await page.screenshot({path:'artifacts/browser/sprint2-admin-'+slug+'.png',fullPage:true});
 }
 await page.goto('/admin/identity-settings');
 const saveResponse=page.waitForResponse(r=>/\/livewire-[^/]+\/update$/.test(new URL(r.url()).pathname) && r.request().method()==='POST',{timeout:15000});
 await page.getByRole('button',{name:'Simpan Draft'}).click();
 expect((await saveResponse).ok()).toBe(true);
 await expect(page.getByText('Identitas tersimpan sebagai draft')).toBeVisible({timeout:15000});
 expect(await (await request.get('/')).text()).toBe(before);
});
