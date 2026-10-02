import {test,expect} from '@playwright/test';
import fs from 'node:fs';

test('edit → private preview → publish → restore preserves draft and guest isolation',async({page,browser,request})=>{
 test.skip(!fs.existsSync('.runtime/admin-account.json'),'Akun lokal belum disiapkan');
 page.setDefaultTimeout(15000);
 const account=JSON.parse(fs.readFileSync('.runtime/admin-account.json','utf8').replace(/^\uFEFF/,''));
 const originalPublic=await (await request.get('/')).text();
 const guest=await browser.newContext();const visitor=await guest.newPage();
 const denied=await visitor.goto('/admin/preview');await expect(visitor).toHaveURL(/\/admin\/login$/);
 await page.goto('/admin/login');await page.locator('input[type=email]').fill(account.email);await page.locator('input[type=password]').fill(account.password);await page.locator('button[type=submit]').click();await page.waitForURL('**/admin');
 await page.goto('/admin/publication');
 const initialVersion=(await page.locator('p').filter({hasText:'Versi publik aktif:'}).innerText()).match(/#(\d+)/)![1];
 await page.goto('/admin/identity-settings');const name=page.getByRole('textbox',{name:/^Nama pada logo/});const originalName=await name.inputValue();
 const marker='SANTAMA KARYA UJI';let published=false;
 async function saveIdentity(){
  const response=page.waitForResponse(r=>/\/livewire-[^/]+\/update$/.test(new URL(r.url()).pathname) && r.request().method()==='POST',{timeout:15000});
  await page.getByRole('button',{name:'Simpan Draft',exact:true}).click();
  expect((await response).ok()).toBe(true);
  await expect(page.getByText('Identitas tersimpan sebagai draft')).toBeVisible({timeout:15000});
 }
 async function restoreInitial(){
  await page.goto('/admin/publication');const row=page.locator('tr').filter({has:page.getByText('#'+initialVersion,{exact:true})});
  await row.getByRole('button',{name:'Pulihkan',exact:true}).click();
  await page.getByRole('alertdialog').getByRole('button',{name:'Pulihkan',exact:true}).click();
  await expect(page.getByText('Versi berhasil dipulihkan',{exact:true})).toBeVisible();published=false;
 }
 try {
  await name.fill(marker);await saveIdentity();
  expect(await (await request.get('/')).text()).toBe(originalPublic);
  const preview=await page.goto('/admin/preview',{waitUntil:'domcontentloaded'});
  expect(preview!.headers()['cache-control']).toContain('no-store');expect(preview!.headers()['x-robots-tag']).toContain('noindex');
  await expect(page.getByRole('status')).toContainText('Preview Draft');await expect(page.locator('#navbar')).toContainText(marker);
  await expect(page.locator('img[alt="'+marker+'"]')).toHaveCount(3);
  await page.goto('/admin/publication');await page.getByRole('button',{name:'Publikasikan',exact:true}).click();
  await page.getByRole('alertdialog').getByRole('button',{name:'Publikasikan',exact:true}).click();await expect(page.getByText('Draft berhasil dipublikasikan',{exact:true})).toBeVisible();published=true;
  const visible=await request.get('/');expect(await visible.text()).toContain(marker);expect(await visible.text()).not.toContain('Preview Draft');expect(visible.headers()['cache-control']).toContain('no-cache');
  await visitor.goto('/',{waitUntil:'domcontentloaded'});await expect(visitor.locator('#navbar')).toContainText(marker);
  await page.screenshot({path:'artifacts/browser/sprint3-publication-history.png',fullPage:true,timeout:15000});
  await restoreInitial();expect(await (await request.get('/')).text()).toBe(originalPublic);
  await page.goto('/admin/identity-settings');await expect(name).toHaveValue(marker);
 } finally {
  // A publish may succeed before its notification is observed; inspect server state too.
  await page.goto('/admin/publication');
  const activeVersion=(await page.locator('p').filter({hasText:'Versi publik aktif:'}).innerText()).match(/#(\d+)/)![1];
  if(published || activeVersion!==initialVersion) await restoreInitial();
  await page.goto('/admin/identity-settings');await name.fill(originalName);await saveIdentity();
  await guest.close();
 }
});


