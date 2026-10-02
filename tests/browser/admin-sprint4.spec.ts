import {test,expect} from '@playwright/test';
import fs from 'node:fs';
for(const width of [390,1440]){
 test(`new admin editors and profile work at ${width}px without publishing`,async({page,request})=>{
  test.skip(!fs.existsSync('.runtime/admin-account.json'),'Akun lokal belum disiapkan');page.setDefaultTimeout(15000);
  const account=JSON.parse(fs.readFileSync('.runtime/admin-account.json','utf8').replace(/^\uFEFF/,''));
  await page.setViewportSize({width,height:900});const before=await(await request.get('/')).text();
  await page.goto('/admin/login');await page.locator('input[type=email]').fill(account.email);await page.locator('input[type=password]').fill(account.password);await page.locator('button[type=submit]').click();await page.waitForURL('**/admin');
  await expect(page.getByText('Proyek aktif (draft)',{exact:true})).toBeVisible();
  await page.screenshot({path:`artifacts/browser/sprint4-dashboard-${width}.png`,fullPage:true,timeout:20000});
  for(const slug of ['content-settings','contact-settings','seo-settings','profile']){
   await page.goto('/admin/'+slug);await expect(page.locator('h1')).toBeVisible();
   expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
   await page.screenshot({path:`artifacts/browser/sprint4-${slug}-${width}.png`,fullPage:true,timeout:20000});
  }
  // Saving unchanged SEO exercises a real form request without altering contact or public snapshot.
  await page.goto('/admin/seo-settings');await page.getByRole('button',{name:'Simpan Draft',exact:true}).click();await expect(page.getByText('Konten tersimpan sebagai draft')).toBeVisible();
  expect(await(await request.get('/')).text()).toBe(before);
 });
}
