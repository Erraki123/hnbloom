#!/usr/bin/env python3
"""
prepare_images.py - prépare les images produits ZKTeco pour le site.

Pour chaque image de  output/images/products/<slug>/<slug>-N.png :
  1) supprime l'arrière-plan (réutilise l'algorithme de remove_bg_all.py)
  2) recadre autour du produit + marge
  3) redimensionne (max 1000 px) et enregistre en WebP avec transparence
     -> public/images/products/<slug>/<slug>-N.webp

Ne touche JAMAIS aux images existantes du site : lit ET écrit uniquement
dans les dossiers indiqués. Les originaux (output/...) restent intacts.

Mettre ce fichier dans le même dossier que remove_bg_all.py, puis :

    pip install pillow numpy scipy rembg onnxruntime
    python prepare_images.py --preview                 # aperçu seulement
    python prepare_images.py --only speedface --preview
    python prepare_images.py                           # application réelle
    python prepare_images.py --mode flood              # sans rembg (plus rapide)
"""
from __future__ import annotations

import argparse
from pathlib import Path

import numpy as np
from PIL import Image

import remove_bg_all as rb  # même dossier

SRC_DEFAULT = "output/images/products"
DST_DEFAULT = "public/images/products"
EXT = {".png", ".jpg", ".jpeg", ".webp"}


def already_transparent(img: Image.Image) -> bool:
    """True si l'image a déjà un vrai fond transparent."""
    if img.mode != "RGBA":
        return False
    alpha = np.array(img.getchannel("A"))
    return (alpha < 250).mean() > 0.02


def trim_and_pad(rgba: Image.Image, pad: float, thresh: int = 12) -> Image.Image:
    alpha = np.array(rgba.getchannel("A"))
    ys, xs = np.where(alpha > thresh)
    if len(xs) == 0:
        return rgba
    box = (xs.min(), ys.min(), xs.max() + 1, ys.max() + 1)
    rgba = rgba.crop(box)
    p = int(max(rgba.size) * pad)
    canvas = Image.new("RGBA", (rgba.width + 2 * p, rgba.height + 2 * p), (0, 0, 0, 0))
    canvas.paste(rgba, (p, p))
    return canvas


def fit_size(img: Image.Image, max_side: int, min_side: int) -> Image.Image:
    longest = max(img.size)
    if longest > max_side:
        scale = max_side / longest
    elif longest < min_side:
        scale = min_side / longest          # agrandissement doux (Lanczos)
    else:
        return img
    new = (round(img.width * scale), round(img.height * scale))
    return img.resize(new, Image.LANCZOS)


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--src", default=SRC_DEFAULT)
    ap.add_argument("--dst", default=DST_DEFAULT)
    ap.add_argument("--only", help="traiter seulement les slugs contenant ce mot")
    ap.add_argument("--mode", choices=["hybrid", "flood", "rembg"], default="hybrid")
    ap.add_argument("--tol", type=int, default=rb.TOL)
    ap.add_argument("--edge", type=int, default=rb.EDGE)
    ap.add_argument("--glass-alpha", type=int, default=rb.GLASS_ALPHA)
    ap.add_argument("--min-area", type=int, default=rb.MIN_GLASS_AREA)
    ap.add_argument("--max-side", type=int, default=1000)
    ap.add_argument("--min-side", type=int, default=600)
    ap.add_argument("--pad", type=float, default=0.06, help="marge autour du produit")
    ap.add_argument("--quality", type=int, default=90)
    ap.add_argument("--preview", action="store_true",
                    help="écrit des aperçus sur fond sombre dans <dst>_preview, sans toucher <dst>")
    a = ap.parse_args()

    src, dst = Path(a.src), Path(a.dst)
    if not src.exists():
        raise SystemExit(f"Dossier introuvable : {src.resolve()}")
    out_root = Path(str(dst) + "_preview") if a.preview else dst

    files = sorted(p for p in src.rglob("*")
                   if p.suffix.lower() in EXT and (not a.only or a.only.lower() in str(p).lower()))
    print(f"{len(files)} images  ->  {out_root}")

    mode, fallbacks, errors = a.mode, 0, 0
    for i, f in enumerate(files, 1):
        rel = f.relative_to(src)
        try:
            img = Image.open(f)
            img.load()
            if already_transparent(img):
                rgba, used = img.convert("RGBA"), "alpha"
            else:
                try:
                    rgba, used = rb.process(img, mode, a.tol, a.edge, a.glass_alpha, a.min_area)
                except ImportError:                 # rembg non installé
                    mode = "flood"
                    rgba, used = rb.process(img, "flood", a.tol, a.edge, a.glass_alpha, a.min_area)
                    print("   (rembg absent -> mode flood)")
                if used.endswith("*"):
                    fallbacks += 1

            rgba = trim_and_pad(rgba, a.pad)
            rgba = fit_size(rgba, a.max_side, a.min_side)

            if a.preview:
                target = (out_root / rel).with_suffix(".png")
                target.parent.mkdir(parents=True, exist_ok=True)
                rb.preview_on_dark(rgba).save(target)
            else:
                target = (out_root / rel).with_suffix(".webp")
                target.parent.mkdir(parents=True, exist_ok=True)
                rgba.save(target, format="WEBP", quality=a.quality, method=6)
            print(f"[{i}/{len(files)}] {used:7} {rel}")
        except Exception as e:                      # une image ratée n'arrête pas le reste
            errors += 1
            print(f"[{i}/{len(files)}] ERREUR {rel} : {e}")

    print(f"\nTerminé. {len(files) - errors} OK, {errors} erreurs, {fallbacks} en repli 'flood'.")
    if a.preview:
        print(f"Regardez les aperçus dans : {out_root.resolve()}")


if __name__ == "__main__":
    main()