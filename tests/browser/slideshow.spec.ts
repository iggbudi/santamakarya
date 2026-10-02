import { test, expect } from '@playwright/test';
import fs from 'node:fs';
const source = fs.readFileSync('resources/js/slideshow.js','utf8').replace('export function','function');
async function fixture(page, reduced=false) {
 await page.emulateMedia({reducedMotion:reduced?'reduce':'no-preference'});
 await page.setContent(`<div id="hero" data-interval="3000"><div data-slide>H1</div><div data-slide>H2</div></div><button data-slide-for="hero">Next</button><button data-slide-for="hero">Next</button><div id="about" data-interval="6000"><div data-slide>A1</div><div data-slide>A2</div></div><div id="single"><div data-slide>S1</div></div><div id="empty"></div>`);
 await page.evaluate(code => eval(code), source + `; window.cleanups = ['hero','about','single','empty'].map(id => initSlideshow(document.getElementById(id)));`);
}
test('hero and about advance independently',async({page})=>{
 await page.clock.install(); await fixture(page);
 await page.clock.runFor(3100);
 await expect(page.locator('#hero [data-slide]').nth(1)).toHaveClass('slide-active');
 await expect(page.locator('#about [data-slide]').first()).toHaveClass('slide-active');
 await page.clock.runFor(3000); await expect(page.locator('#about [data-slide]').nth(1)).toHaveClass('slide-active');
});
test('one slide and zero slides create no timers; cleanup clears timers',async({page})=>{
 await fixture(page);
 const counts=await page.evaluate(code => eval(code), source+`;let calls=0;let cleared=0;const original=window.setInterval;window.setInterval=()=>{calls++;return 99;};const originalClear=window.clearInterval;window.clearInterval=(id)=>{if(id===99)cleared++;};const a=initSlideshow(document.getElementById('single'));const b=initSlideshow(document.getElementById('empty'));const before=calls;const c=initSlideshow(document.getElementById('hero'));c();a();b();window.setInterval=original;window.clearInterval=originalClear;({before,calls,cleared});`);
 expect(counts).toEqual({before:0,calls:1,cleared:1});
});
test('reduced motion keeps first slide static and hides controls',async({page})=>{
 await page.clock.install(); await fixture(page,true); await page.clock.runFor(20000);
 await expect(page.locator('#hero [data-slide]').first()).toHaveClass('slide-active');
 await expect(page.locator('[data-slide-for="hero"]').first()).toBeHidden();
});

