import { test, expect, Page } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import postcss from 'postcss';
import tailwindcss from 'tailwindcss';
import config from '../../tailwind.config.js';
let referenceCss: string;
test.beforeAll(async()=>{
    referenceCss=(await postcss([tailwindcss({...config,content:['./landingpage.html']})]).process(fs.readFileSync('resources/css/landing.css','utf8'),{from:undefined})).css;
});

async function stabilize(page: Page, reference: boolean) {
    await page.evaluate(async (isReference) => {
        if (isReference) {
            // Remove precisely the two requested elements for the comparison.
            document.querySelector('.port-item[data-category="lainnya"]')?.remove();
            document.querySelector('.port-filter-btn[data-filter="lainnya"]')?.remove();
            // Reference HTML uses a global timer; the migrated page uses module scope.
            const stop = new Function('clearInterval(slideInterval); showSlide(0);');
            stop();
        } else {
            document.querySelector<HTMLButtonElement>('.slide-dot')?.click();
        }
        await document.fonts.ready;
        // Full-page comparison must request lazy images outside the viewport too.
        // Performance behavior remains covered separately by ReleaseReadinessTest.
        document.querySelectorAll<HTMLImageElement>('img[loading="lazy"]').forEach(img => { img.loading = 'eager'; });
        await Promise.all([...document.images].map(img => img.decode().catch(() => {})));
    }, reference);
    await page.addStyleTag({ content: `
        *, *::before, *::after { animation: none !important; transition: none !important; }
        .hero-slide.slide-active { transform: scale(1.05) !important; opacity: 1 !important; }
        .hero-slide.slide-inactive { opacity: 0 !important; }
    ` });
}

for (const [width, height] of [[390,844],[768,1024],[1440,900]]) {
    test(`landing matches HTML reference at ${width}px`, async ({ browser }, testInfo) => {
        const context = await browser.newContext({ viewport: { width, height } });
        // Pin reference dependencies locally: unavailable CDN scripts abort the old inline JS.
        await context.route('https://cdn.tailwindcss.com/**',route=>route.fulfill({contentType:'application/javascript',body:'window.tailwind = {};'}));
        await context.route('https://unpkg.com/lucide@latest',route=>route.fulfill({contentType:'application/javascript',body:fs.readFileSync('node_modules/lucide/dist/umd/lucide.min.js','utf8')}));
        // Autoplay is exercised separately; freeze intervals equally on both visual references.
        await context.addInitScript(() => { window.setInterval = (() => 0) as typeof window.setInterval; });
        // Share external image bytes so a remote image changing between requests cannot create a false visual diff.
        const imageCache = new Map<string, { body: Buffer, contentType: string }>();
        await context.route('https://images.unsplash.com/**', async route => {
            const url = route.request().url();
            let cached = imageCache.get(url);
            if (!cached) {
                try {
                    const response = await route.fetch({ timeout: 30000 });
                    if (response.ok()) cached = { body: await response.body(), contentType: response.headers()['content-type'] || 'image/jpeg' };
                } catch {}
                // Consistent placeholder only when the remote fixture is unavailable.
                cached ||= { body: Buffer.from('<svg xmlns="http://www.w3.org/2000/svg" width="1000" height="800"><rect width="100%" height="100%" fill="#ddd"/></svg>'), contentType:'image/svg+xml' };
                imageCache.set(url, cached);
            }
            await route.fulfill(cached);
        });
        const reference = await context.newPage();
        await reference.goto('file://' + path.resolve('landingpage.html').replaceAll('\\', '/'), { waitUntil: 'load' });
        await reference.addStyleTag({content:referenceCss});
        await stabilize(reference, true);
        const name = `landing-${width}.png`;
        const expectedPath = testInfo.snapshotPath(name);
        fs.mkdirSync(path.dirname(expectedPath), { recursive: true });
        fs.writeFileSync(expectedPath, await reference.screenshot({ fullPage:true, mask:[reference.locator('iframe')] }));

        const page = await context.newPage();
        const errors: string[] = [];
        page.on('pageerror', e => errors.push(e.message));
        await page.goto('/', { waitUntil: 'load' });
        await stabilize(page, false);
        await expect(page.locator('.port-item')).toHaveCount(5);
        await expect(page.locator('#layanan h3').filter({ hasText:'Rumah Dinas' })).toHaveCount(1);
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
        const actual = await page.screenshot({ fullPage:true, mask:[page.locator('iframe')] });
        expect(actual).toMatchSnapshot(name, { maxDiffPixelRatio:0.01 });
        fs.mkdirSync('artifacts/browser/migrated', { recursive:true });
        fs.writeFileSync(`artifacts/browser/migrated/${width}.png`, actual);
        expect(errors).toEqual([]);
        await context.close();
    });
}

test('portfolio filtering and hero dots work', async ({ page }) => {
    await page.goto('/');
    await page.locator('.port-filter-btn[data-filter="rumah"]').click();
    await expect(page.locator('.port-item:visible')).toHaveCount(2);
    await page.locator('.port-filter-btn[data-filter="renovasi"]').click();
    await expect(page.locator('.port-item:visible')).toHaveCount(2);
    await page.locator('.port-filter-btn[data-filter="commercial"]').click();
    await expect(page.locator('.port-item:visible')).toHaveCount(1);
    await page.locator('.port-filter-btn[data-filter="all"]').click();
    await expect(page.locator('.port-item:visible')).toHaveCount(5);
    await page.locator('.slide-dot').nth(2).click();
    await expect(page.locator('.hero-slide').nth(2)).toHaveClass(/slide-active/);
});

test('mobile navigation opens and closes after selecting a link', async ({ page }) => {
    await page.setViewportSize({ width:390, height:844 });
    await page.goto('/');
    await page.locator('#mobile-menu-btn').click();
    await expect(page.locator('#mobile-menu')).toBeVisible();
    await page.locator('#mobile-menu a[href="#tentang"]').click();
    await expect(page.locator('#mobile-menu')).toBeHidden();
});

test('guest is redirected to the admin login form', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);
    await expect(page.locator('input[type="email"]')).toBeVisible();
    await expect(page.locator('input[type="password"]')).toBeVisible();
});
