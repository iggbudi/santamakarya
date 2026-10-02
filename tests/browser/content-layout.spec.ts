import {test,expect} from '@playwright/test';
import fs from 'node:fs';
import {execFileSync} from 'node:child_process';
test.beforeAll(()=>{execFileSync('powershell.exe',['-NoProfile','-ExecutionPolicy','Bypass','-File','scripts/php.ps1','tests/fixtures/render-content-layout.php']);});
for(const [width,height] of [[390,844],[768,1024],[1440,900]]){
 test(`long structured text and portrait/landscape preserve layout at ${width}px`,async({page})=>{
  await page.setViewportSize({width,height});
  await page.route('**/__sprint4/long-content',r=>r.fulfill({contentType:'text/html',body:fs.readFileSync('.runtime/sprint4-long-content.html','utf8')}));
  for(const variant of ['portrait','landscape']) await page.route(`**/__sprint4/${variant}.png`,r=>r.fulfill({contentType:'image/png',body:fs.readFileSync(`.runtime/sprint4-${variant}.png`)}));
  await page.route('**/www.google.com/maps/embed**',r=>r.fulfill({contentType:'text/html',body:'<p>Maps fixture</p>'}));
  const errors:string[]=[];page.on('pageerror',e=>errors.push(e.message));
  await page.goto('/__sprint4/long-content',{waitUntil:'domcontentloaded'});
  await expect(page.locator('#about-slideshow')).toHaveCSS('height','440px');
  await expect(page.locator('#layanan img')).toHaveCount(6);
  for(const selector of ['#layanan img','#about-slideshow img']){
   expect(await page.locator(selector).evaluateAll(images=>images.every(i=>getComputedStyle(i).objectFit==='cover'))).toBe(true);
  }
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
  await expect(page.locator('meta[property="og:image"]')).toHaveAttribute('content',/landscape.png$/);
  await page.screenshot({path:`artifacts/browser/sprint4-content-${width}.png`,fullPage:true,timeout:20000});
  expect(errors).toEqual([]);
 });
}
