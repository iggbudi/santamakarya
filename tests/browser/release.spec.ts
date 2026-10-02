import { test, expect } from '@playwright/test';

test('keyboard can open and dismiss mobile navigation with visible focus', async ({page}) => {
 await page.setViewportSize({width:390,height:844});
 await page.goto('/');
 const menu=page.locator('#mobile-menu-btn');
 await expect(menu).toHaveAttribute('aria-expanded','false');
 await page.keyboard.press('Tab'); await page.keyboard.press('Tab');
 await expect(menu).toBeFocused();
 expect(await menu.evaluate(e=>getComputedStyle(e).outlineStyle)).not.toBe('none');
 await page.keyboard.press('Enter');
 await expect(page.locator('#mobile-menu')).toBeVisible();
 await page.keyboard.press('Escape');
 await expect(page.locator('#mobile-menu')).toBeHidden();
 await expect(menu).toBeFocused();
});

test('published contact links are safe and photos have descriptions',async({page})=>{
 await page.goto('/');
 const wa=await page.locator('a[href^="https://wa.me/"]').evaluateAll(links=>links.map(a=>(a as HTMLAnchorElement).href));
 expect(wa.length).toBeGreaterThanOrEqual(6);
 const numbers=new Set(wa.map(link=>new URL(link).pathname));expect(numbers.size).toBe(1);
 expect([...numbers][0]).toMatch(/^\/[1-9][0-9]{7,14}$/);
 const phone=await page.locator('a[href^="tel:"]').first().getAttribute('href');expect(phone).toBe('tel:+'+[...numbers][0].slice(1));
 for(const link of await page.locator('#lokasi a[target="_blank"]').evaluateAll(links=>links.map(a=>(a as HTMLAnchorElement).href))){
  const url=new URL(link);expect(url.protocol).toBe('https:');expect(['maps.google.com','maps.app.goo.gl','goo.gl','www.google.com','google.com']).toContain(url.hostname);
 }
 expect(await page.locator('img').evaluateAll(images=>images.every(i=>(i.getAttribute('alt')||'').trim().length>0))).toBe(true);
});

test('failed photos keep frames and reduced motion keeps hero static',async({page})=>{
 await page.emulateMedia({reducedMotion:'reduce'});await page.setViewportSize({width:390,height:844});
 await page.route('https://images.unsplash.com/**',r=>r.abort());
 const errors:string[]=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto('/');await page.locator('#tentang').scrollIntoViewIfNeeded();
 const about=page.locator('#tentang img').first();await expect(about).toHaveCSS('height','440px');
 await expect(page.locator('.hero-slide').first()).toHaveClass(/slide-active/);
 await expect(page.locator('.slide-dot').first()).toBeHidden();
 expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth)).toBe(true);
 expect(errors).toEqual([]);
});
