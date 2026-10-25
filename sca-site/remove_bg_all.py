#!/usr/bin/env python3
"""
remove_bg_all.py - إزالة خلفية صور المنتجات مع الحفاظ على الزجاج الشفاف.

الفكرة:
  1) rembg يستخرج جسم المنتج (المعدن/الأجزاء الصلبة).
  2) "ملء من الحواف" مع حواجز حدود يكتشف الخلفية الحقيقية فقط.
     أي منطقة بيضاء مغلقة بإطار (مثل زجاج بوابة PMR) لا تُحذف،
     بل تصبح نصف شفافة فتبدو كزجاج حقيقي فوق الخلفية الداكنة.
  3) الدمج: المنتج من rembg + الزجاج المستعاد.

الاستخدام:
  python remove_bg_all.py --preview                 # معاينة فقط، لا يلمس صور الموقع
  python remove_bg_all.py --only portillon --preview
  python remove_bg_all.py                           # تطبيق فعلي على كل الصور
  python remove_bg_all.py --only couloir --mode hybrid --glass-alpha 90

الأصلية تبقى دائماً في *_backup ولا تُعدَّل أبداً.
"""
from __future__ import annotations

import argparse
import shutil
from pathlib import Path

import numpy as np
from PIL import Image, ImageFilter
from scipy import ndimage

# ----------------------------- الإعدادات ------------------------------
FOLDERS = ["public/images/products", "public/images/solutions"]
EXT = {".jpg", ".jpeg", ".png", ".webp"}

DEFAULT_MODE = "hybrid"      # hybrid | flood | rembg | keep
# استثناءات حسب كلمة في اسم الملف، مثال: {"banner": "keep", "hero": "keep"}
OVERRIDES: dict[str, str] = {}

TOL = 14             # تسامح لون الخلفية (0-255). أعلى = يحذف رمادياً أكثر
EDGE = 60            # حساسية الحواف الحاجزة. أقل = حواجز أكثر (حماية أكبر للزجاج)
GLASS_ALPHA = 120    # شفافية الزجاج (0 = شفاف تماماً، 255 = معتم)
MIN_GLASS_AREA = 400 # أصغر مساحة (بكسل) تُعتبر زجاجاً
PREVIEW_BG = (20, 17, 12)  # لون خلفية الموقع الداكنة للمعاينة

_session = None


def get_session():
    global _session
    if _session is None:
        from rembg import new_session
        _session = new_session("isnet-general-use")
    return _session


# ----------------------------- الخوارزميات -----------------------------
def analyse(rgb: np.ndarray, tol: int, edge: int, min_area: int):
    """يرجع: خلفية مؤكدة، لب الزجاج، منطقة الزجاج مع إطاره."""
    rgb16 = rgb.astype(np.int16)
    border = np.concatenate([rgb16[0], rgb16[-1], rgb16[:, 0], rgb16[:, -1]])
    bg_color = np.median(border, axis=0)
    near = np.abs(rgb16 - bg_color).max(axis=2) <= tol

    gray = rgb16.mean(axis=2)
    mag = np.hypot(ndimage.sobel(gray, axis=1), ndimage.sobel(gray, axis=0))
    edges = mag > edge                       # حدود المنتج تعمل كحواجز

    free = near & ~edges
    labels, _ = ndimage.label(free)          # اتصال رباعي: لا يتسرب من الزوايا
    border_ids = np.unique(np.concatenate(
        [labels[0], labels[-1], labels[:, 0], labels[:, -1]]))
    border_ids = border_ids[border_ids != 0]
    bg = np.isin(labels, border_ids)         # الخلفية الحقيقية فقط

    enclosed = free & ~bg                    # مناطق فاتحة محاطة بإطار = زجاج محتمل
    lab2, n = ndimage.label(enclosed)
    if n:
        sizes = ndimage.sum(enclosed, lab2, range(1, n + 1))
        core = np.isin(lab2, 1 + np.nonzero(sizes >= min_area)[0])
    else:
        core = np.zeros_like(bg)
    zone = ndimage.binary_dilation(core, iterations=3) & ~bg
    return bg, core, zone


def flood_alpha(bg, core, zone, glass_alpha):
    trim = ndimage.binary_dilation(bg, iterations=1) & ~zone   # إزالة هالة الحافة
    alpha = np.where(trim, 0, 255).astype(np.uint8)
    alpha[core] = glass_alpha
    soft = Image.fromarray(alpha).filter(ImageFilter.GaussianBlur(0.6))
    return np.array(soft)


def rembg_alpha(img: Image.Image) -> np.ndarray:
    from rembg import remove
    return np.array(remove(img, session=get_session(), only_mask=True))


def corners_opaque(alpha: np.ndarray) -> bool:
    h, w = alpha.shape
    pts = [alpha[2, 2], alpha[2, w - 3], alpha[h - 3, 2], alpha[h - 3, w - 3]]
    return max(int(p) for p in pts) > 20


def process(img: Image.Image, mode: str, tol, edge, glass_alpha, min_area):
    rgba = np.array(img.convert("RGBA"))
    rgb = rgba[..., :3]
    bg, core, zone = analyse(rgb, tol, edge, min_area)
    flood = flood_alpha(bg, core, zone, glass_alpha)

    if mode == "flood":
        alpha, used = flood, "flood"
    else:
        ra = rembg_alpha(img)
        if corners_opaque(ra):               # rembg فشل: الخلفية بقيت
            alpha, used = flood, "flood*"
        elif mode == "rembg":
            alpha, used = ra, "rembg"
        else:                                # hybrid: المنتج + الزجاج المستعاد
            glass_only = np.where(zone, np.where(core, glass_alpha, 255), 0).astype(np.uint8)
            alpha = np.maximum(ra, glass_only)
            alpha[core] = glass_alpha
            used = "hybrid"
    out = np.dstack([rgb, alpha]).astype(np.uint8)
    return Image.fromarray(out, "RGBA"), used


def preview_on_dark(rgba: Image.Image) -> Image.Image:
    bg = Image.new("RGBA", rgba.size, PREVIEW_BG + (255,))
    return Image.alpha_composite(bg, rgba).convert("RGB")


# --------------------------------- التشغيل ----------------------------
def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--only", help="معالجة الملفات التي يحتوي اسمها على هذه الكلمة")
    ap.add_argument("--mode", choices=["hybrid", "flood", "rembg", "keep"], default=DEFAULT_MODE)
    ap.add_argument("--tol", type=int, default=TOL)
    ap.add_argument("--edge", type=int, default=EDGE)
    ap.add_argument("--glass-alpha", type=int, default=GLASS_ALPHA)
    ap.add_argument("--min-area", type=int, default=MIN_GLASS_AREA)
    ap.add_argument("--preview", action="store_true",
                    help="يكتب المعاينات فقط في *_preview ولا يغيّر صور الموقع")
    a = ap.parse_args()

    for folder in FOLDERS:
        src = Path(folder)
        if not src.exists():
            continue
        backup = Path(folder + "_backup")
        if not backup.exists():
            shutil.copytree(src, backup)
            print("Backup créé :", backup)
        prev_dir = Path(folder + "_preview")

        files = [p for p in backup.rglob("*")
                 if p.suffix.lower() in EXT and (not a.only or a.only.lower() in p.stem.lower())]
        print(f"\n{folder} : {len(files)} images")

        for i, orig in enumerate(files, 1):
            rel = orig.relative_to(backup)
            mode = next((m for k, m in OVERRIDES.items() if k in orig.stem.lower()), a.mode)
            try:
                if mode == "keep":
                    if not a.preview:
                        (src / rel).parent.mkdir(parents=True, exist_ok=True)
                        shutil.copy2(orig, src / rel)
                    print(f"[{i}/{len(files)}] keep    {rel}")
                    continue
                res, used = process(Image.open(orig), mode, a.tol, a.edge,
                                    a.glass_alpha, a.min_area)
                if a.preview:
                    out = (prev_dir / rel).with_suffix(".png")
                    out.parent.mkdir(parents=True, exist_ok=True)
                    preview_on_dark(res).save(out)
                else:
                    target = src / rel               # نفس الاسم ونفس الامتداد
                    target.parent.mkdir(parents=True, exist_ok=True)
                    res.save(target, format="PNG")
                print(f"[{i}/{len(files)}] {used:7} {rel}")
            except Exception as e:
                print("ERREUR", orig, e)

    print("\nTerminé.")


if __name__ == "__main__":
    main()
