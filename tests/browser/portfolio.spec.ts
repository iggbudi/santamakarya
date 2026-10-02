import {test,expect} from '@playwright/test';
import fs from 'node:fs';
import {execFileSync} from 'node:child_process';
test.beforeAll(()=>{execFileSync('powershell.exe',['-NoProfile','-ExecutionPolicy','Bypass','-File','scripts/php.ps1','tests/fixtures/render-draft.php']);});
for(const width of [390,1440]){
 test(`compiled draft preserves frame and logos at ${width}px`,async({page})=>{
  await page.setViewportSize({width,height:900});
  await page.route('**/__sprint2/draft',route=>route.fulfill({contentType:'text/html',body:fs.readFileSync('.runtime/sprint2-draft.html','utf8')}));
  await page.goto('/__sprint2/draft');
  await expect(page.locator('#about-slideshow')).toHaveCSS('height','440px');
  await expect(page.locator('img[alt="SANTAMA KARYA"]')).toHaveCount(3);
  await page.locator('#portfolio').scrollIntoViewIfNeeded();
  for(const [filter,count] of [['rumah',2],['renovasi',2],['commercial',1],['all',5]]){
   await page.locator(`[data-filter="${filter}"]`).click(); await expect(page.locator('.port-item:visible')).toHaveCount(Number(count));
  }
  await page.screenshot({path:`artifacts/browser/sprint2-draft-${width}.png`,fullPage:true});
 });
}
test('empty portfolio has no filters',async({page})=>{
 await page.route('**/__sprint2/empty',route=>route.fulfill({contentType:'text/html',body:fs.readFileSync('.runtime/sprint2-empty.html','utf8')}));
 await page.goto('/__sprint2/empty'); await expect(page.locator('.port-filter-btn')).toHaveCount(0);
 await expect(page.getByText('Belum ada proyek yang ditampilkan.')).toBeVisible();
});
