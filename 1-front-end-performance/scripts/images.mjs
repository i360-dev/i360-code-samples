// scripts/images.mjs
import sharp from 'sharp';
import { readdirSync } from 'fs';

const src = 'resources/images-src', out = 'public/images/hero';
for (const f of readdirSync(src).filter(f => /\.(jpe?g|png)$/i.test(f))) {
    const name = f.replace(/\.\w+$/, '');
    for (const w of [800, 1200, 1600]) {
        const img = sharp(`${src}/${f}`).resize({ width: w, height: Math.round(w * 9 / 16), fit: 'cover' });
        await img.clone().jpeg({ quality: 70, mozjpeg: true }).toFile(`${out}/${name}-${w}.jpg`);
        await img.clone().webp({ quality: 72 }).toFile(`${out}/${name}-${w}.webp`);
    }
}
