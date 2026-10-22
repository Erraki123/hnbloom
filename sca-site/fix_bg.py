import sys
from pathlib import Path
from rembg import remove, new_session
from PIL import Image

# الاستعمال: python fix_bg.py "public/images/products/sh1-sas-desinfection-des-mains-trx"
folder = Path(sys.argv[1])
BACKUP = Path("public/images/products_backup") / folder.relative_to("public/images/products")

session = new_session("birefnet-general")   # أدق من isnet للخلفيات الصعبة

for orig in BACKUP.glob("*"):
    if orig.suffix.lower() not in {".jpg", ".jpeg", ".png", ".webp"}:
        continue
    img = Image.open(orig).convert("RGBA")
    res = remove(
        img,
        session=session,
        alpha_matting=True,
        alpha_matting_foreground_threshold=240,
        alpha_matting_background_threshold=20,
        alpha_matting_erode_size=10,
    )
    out = folder / orig.name
    res.save(out, format="PNG")
    print("OK", out)
