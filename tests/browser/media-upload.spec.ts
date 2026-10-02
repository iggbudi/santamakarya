import {test,expect} from '@playwright/test';
import fs from 'node:fs';
test('admin uploads image through real browser',async({page})=>{
 test.skip(!fs.existsSync('.runtime/admin-account.json'),'Akun lokal belum disiapkan');
 const filename='sprint2-upload-'+Date.now()+'.png';
 const account=JSON.parse(fs.readFileSync('.runtime/admin-account.json','utf8').replace(/^\uFEFF/,''));
 await page.goto('/admin/login');await page.locator('input[type=email]').fill(account.email);await page.locator('input[type=password]').fill(account.password);await page.locator('button[type=submit]').click();await page.waitForURL('**/admin');
 await page.goto('/admin/media-assets');await page.getByRole('button',{name:'Unggah Gambar',exact:true}).click();
 await page.locator('input[type=file]').setInputFiles({name:filename,mimeType:'image/png',buffer:fs.readFileSync('resources/branding/logo-light.png')});
 await expect(page.locator('.filepond--file-status-main')).toHaveText('Pengunggahan selesai',{timeout:30000});
 await page.getByRole('button',{name:'Unggah',exact:true}).click();
 await expect(page.getByRole('dialog',{name:'Unggah Gambar'})).toBeHidden({timeout:30000});
 await expect(page.locator('.fi-modal-window-ctn:visible')).toHaveCount(0);
 const row=page.locator('tr').filter({hasText:filename});
 await expect(row).toBeVisible();
 await row.getByRole('button',{name:'Hapus',exact:true}).click();
 await page.getByRole('alertdialog',{name:'Hapus gambar'}).getByRole('button',{name:'Hapus',exact:true}).click();
 await expect(row).toHaveCount(0);
});




