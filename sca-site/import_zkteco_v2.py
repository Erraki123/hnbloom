#!/usr/bin/env python3
"""
import_zkteco_v2.py - importe les catalogues ZKTeco (products.xlsx) dans le site.

Chaque produit est rangé selon le PDF d'origine (colonne pdf_source) :

  Access_Control_Terminal / Card_Tag_Reader / Access_Control_Panel
        -> Sécurité électronique > Contrôle d'accès électronique
  Security_Inspection_System
        -> Sécurité électronique > Systèmes d'inspection de sécurité
  Smart_Parking_Barrier
        -> Obstacles physiques automatisés > Barrières de parking intelligentes (catégorie créée)

Sécurité :
  - n'efface JAMAIS les produits déjà importés : seuls ceux présents dans l'Excel sont remplacés
  - sauvegarde automatique (.bak-AAAAMMJJ-HHMM) avant toute écriture
  - aucun prix importé (le site affiche seulement « Demande devis »)
  - --dry-run : affiche le résultat sans rien écrire

Utilisation :
    python import_zkteco_v2.py --xlsx products_new.xlsx --project . --dry-run
    python import_zkteco_v2.py --xlsx products_new.xlsx --project .
    python import_zkteco_v2.py --xlsx products.xlsx --project . --replace-access-lead
"""
from __future__ import annotations

import argparse
import json
import re
import shutil
import sys
from datetime import datetime
from pathlib import Path

import openpyxl

BRAND = "ZKTeco"


# --------------------------------------------------------------------------
# Règles de classement
# --------------------------------------------------------------------------
def norm(s) -> str:
    return re.sub(r"[^a-z0-9]", "", str(s or "").lower())


def by_rules(rules, default):
    def f(row):
        n = norm(row.get("nom") or row.get("slug"))
        for pattern, group in rules:
            if re.search(pattern, n):
                return group
        return default
    return f


def access_group(row):
    c = str(row.get("categorie") or "").lower()
    if "terminal" in c:
        return "Terminaux"
    if "lecteur" in c:
        return "Lecteurs"
    if "panneau" in c:
        return "Panneaux de contrôle"
    return "Autres"


TARGETS = [
    {
        "key": "access",
        "pdf": ["access_control_terminal", "card___tag_reader", "access_control_panel"],
        "solution": "securite-electroniques",
        "category": "terminaux-de-reconnaissance-faciale",
        "groups": ["Terminaux", "Lecteurs", "Panneaux de contrôle"],
        "group_of": access_group,
        "lead": ("HN BLOOM, distributeur ZKTeco au Maroc, propose une gamme complète de solutions de "
                 "contrôle d’accès : terminaux de reconnaissance faciale et biométriques, lecteurs de "
                 "cartes, QR code et UHF, panneaux de contrôle d’accès et contrôleurs d’ascenseur. "
                 "Notre équipe vous accompagne dans le choix des équipements et dans le support "
                 "technique de vos projets."),
        "lead_only_if_flag": True,
    },
    {
        "key": "inspection",
        "pdf": ["security_inspection"],
        "solution": "securite-electroniques",
        "category": "systeme-de-securite",
        "title": "Systèmes d’inspection de sécurité",
        "groups": ["Scanners rayons X", "Explosifs & liquides", "Détecteurs portatifs", "Portiques"],
        "group_of": by_rules([
            (r"^(zkx|ld5030)", "Scanners rayons X"),
            (r"^(zke8800|zkld)", "Explosifs & liquides"),
            (r"^zkd(100s|160|180|300)$", "Détecteurs portatifs"),
            (r"^(zkmd|zkd|mds)", "Portiques"),
        ], "Autres"),
        "lead": ("Scanners à rayons X, détecteurs d’explosifs et de liquides, détecteurs de métaux "
                 "portatifs et portiques : une gamme ZKTeco complète pour sécuriser aéroports, "
                 "administrations, établissements scolaires et sites sensibles."),
    },
    {
        "key": "parking",
        "pdf": ["smart_parking"],
        "solution": "obstacles-physiques-automatise",
        "category": "barrieres-de-parking",
        "create": {
            "title": "Barrières de parking intelligentes",
            "image_candidates": ["images/solutions/obstacles-physiques-automatise/barrieres-de-parking.jpg"],
            "image_from": "barrieres-automatiques",     # catégorie sœur dont on copie l'image par défaut
        },
        "groups": ["Barrières", "Bornes & armoires", "Distributeurs de tickets",
                   "Portes automatiques", "Accessoires"],
        "group_of": by_rules([
            (r"^(bglcd|probg|bgm|cmp[23]00[lr]$)", "Barrières"),
            (r"^(bol|plock|controlcabinet)", "Bornes & armoires"),
            (r"^tbm", "Distributeurs de tickets"),
            (r"^(zksl1500|zksl800ac$|zksw)", "Portes automatiques"),
        ], "Accessoires"),
        "lead": ("Barrières levantes, bornes, distributeurs de tickets, portes automatiques et "
                 "accessoires ZKTeco pour contrôler l’accès des véhicules à vos parkings et sites."),
    },
    {
        "key": "intercom",
        "pdf": ["video_intercom"],
        "solution": "securite-electroniques",
        "category": "videophonie",
        "groups": ["Platines extérieures", "Moniteurs intérieurs", "Kits", "Centre de gestion"],
        "group_of": by_rules([
            (r"^ve", "Platines extérieures"),
            (r"^vt", "Moniteurs intérieurs"),
            (r"^vk", "Kits"),
            (r"^vg", "Centre de gestion"),
        ], "Autres"),
        # NG-L210 = ampoule connectée NGTeco : ce n'est pas un interphone, on l'écarte
        "exclude": [r"^ngl"],
        "lead": None,
    },
    {
        "key": "gates",
        "pdf": ["smart_gate"],
        "solution": "obstacles-physiques-automatise",
        "category": "portillons-tourniquets-zkteco",
        "create": {
            "title": "Portillons & tourniquets ZKTeco",
            "image_candidates": ["images/solutions/obstacles-physiques-automatise/portillons-tourniquets-zkteco.jpg"],
        },
        "groups": ["Barrières battantes", "Barrières pivotantes", "Pleine hauteur", "Tripodes"],
        "group_of": by_rules([
            (r"^fbl", "Barrières battantes"),
            (r"^mars(pro)?f\d", "Barrières battantes"),
            (r"^saturnt\d", "Tripodes"),
            (r"^m?ts\d", "Tripodes"),
            (r"^fht", "Pleine hauteur"),
            (r"^(saturns|comets|sbt|mars(pro)?[sb]\d)", "Barrières pivotantes"),
        ], "Autres"),
        "lead": ("Portillons à barrières battantes et pivotantes, tourniquets tripodes et pleine hauteur "
                 "ZKTeco pour maîtriser les flux piétons de vos bâtiments et sites sensibles."),
    },
]


# --------------------------------------------------------------------------
# Nettoyage des textes
# --------------------------------------------------------------------------
LABELS = {
    "Communication": "Communication", "Power Supply": "Alimentation",
    "Operating Temperature": "Température de fonctionnement",
    "Operating Humidity": "Humidité de fonctionnement", "Storage Temperature": "Température de stockage",
    "Dimension": "Dimensions", "Dimensions": "Dimensions", "Dimensions (L × W × H)": "Dimensions (L × l × H)",
    "Access Control Interface": "Interface de contrôle d'accès", "Hardware": "Matériel",
    "Card Capacity": "Capacité de cartes", "Card Holders Capacity": "Capacité de porteurs de cartes",
    "Transaction Capacity": "Capacité de transactions", "Net Weight": "Poids net",
    "Gross Weight": "Poids brut", "Weight": "Poids", "Package Weight": "Poids du colis",
    "Certifications": "Certifications", "Ingress Protection Rating": "Indice de protection (IP)",
    "Standard Functions": "Fonctions standard", "Optional Functions": "Fonctions optionnelles",
    "Display": "Écran", "Installation": "Installation", "Mounting": "Montage", "Model": "Modèle",
    "Fingerprint Capacity": "Capacité d'empreintes", "Fingerprint Template Capacity": "Capacité de modèles d'empreintes",
    "Face Capacity": "Capacité de visages", "Palm Capacity": "Capacité de paumes",
    "User Capacity": "Capacité d'utilisateurs", "Log Events Capacity": "Capacité d'événements",
    "Authentication Method": "Méthode d'authentification", "Card Type": "Type de carte",
    "Supported Software": "Logiciels compatibles", "Operation System": "Système d'exploitation",
    "Operating System": "Système d'exploitation", "Biometric Algorithm": "Algorithme biométrique",
    "Verification Speed": "Vitesse de vérification", "Specification": "Spécification",
    "Housing Material": "Matériau du boîtier", "Enclosure": "Boîtier", "Reading Distance": "Distance de lecture",
    "Recognition Distance": "Distance de reconnaissance", "Number of Readers Supported": "Nombre de lecteurs pris en charge",
    "Number of Inputs": "Nombre d'entrées", "Number of Outputs": "Nombre de sorties",
    "Number of Doors Controlled": "Nombre de portes contrôlées", "Wiegand Signal": "Signal Wiegand",
    "Operating Environment": "Environnement de fonctionnement", "Operating Voltage": "Tension de fonctionnement",
    "Compatibility": "Compatibilité", "Camera": "Caméra", "Material": "Matériau", "Motor": "Moteur",
    "Boom Length": "Longueur de la lisse", "Opening Time": "Temps d'ouverture", "Closing Time": "Temps de fermeture",
    "Detection Zones": "Zones de détection", "Sensitivity Levels": "Niveaux de sensibilité",
    "Tunnel Size": "Dimensions du tunnel", "Conveyor Speed": "Vitesse du convoyeur", "Load Capacity": "Charge maximale",
}


def norm_label(label: str) -> str:
    s = str(label or "").strip()
    s = re.sub(r"(?<=[a-z])(?=[A-Z])", " ", s)
    s = re.sub(r"\bNumberof\b", "Number of", s)
    s = re.sub(r"\bTypesof\b", "Types of", s)
    s = re.sub(r"\s+", " ", s)
    return LABELS.get(s, s)


def clean_description(text: str) -> tuple[str, bool]:
    t = re.sub(r"\s+", " ", (text or "")).strip()
    if not t.endswith("…"):
        return t, False
    t = t[:-1].rstrip()
    cut = max(t.rfind(". "), t.rfind(".\u00a0"))
    if cut >= 50:
        return t[: cut + 1], True
    cut = t.rfind(" ")
    return (t[:cut] if cut > 0 else t).rstrip(" ,;:-"), True


def short_lead(text: str, limit: int = 140) -> str:
    if len(text) <= limit:
        return text
    cut = text.rfind(" ", 0, limit)
    return text[: cut if cut > 0 else limit].rstrip(" ,;:-") + "…"


def natural_key(s: str):
    return [int(p) if p.isdigit() else p.lower() for p in re.split(r"(\d+)", s or "")]


def load_json(path: Path, default):
    return json.loads(path.read_text(encoding="utf-8")) if path.exists() else default


def backup(path: Path, stamp: str):
    if path.exists():
        shutil.copy2(path, path.with_name(path.name + f".bak-{stamp}"))


def find_target(pdf_source: str):
    s = (pdf_source or "").lower()
    for t in TARGETS:
        if any(k in s for k in t["pdf"]):
            return t
    return None


# --------------------------------------------------------------------------
def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--xlsx", default="products.xlsx")
    ap.add_argument("--project", required=True, help="racine du projet Laravel")
    ap.add_argument("--dry-run", action="store_true")
    ap.add_argument("--no-check-images", action="store_true")
    ap.add_argument("--replace-access-lead", action="store_true",
                    help="remplace le texte de la catégorie Contrôle d'accès (qui parle de Hikvision)")
    a = ap.parse_args()

    root = Path(a.project)
    data_dir, public = root / "database" / "data", root / "public"
    products_file, solutions_file = data_dir / "products.json", data_dir / "solutions.json"
    if not solutions_file.exists():
        sys.exit(f"Introuvable : {solutions_file}")

    # ---------- lecture Excel ----------
    wb = openpyxl.load_workbook(a.xlsx, data_only=True)
    head = [c.value for c in next(wb["products"].iter_rows(min_row=1, max_row=1))]
    rows = [dict(zip(head, [c.value for c in r])) for r in wb["products"].iter_rows(min_row=2)]
    rows = [r for r in rows if r.get("slug")]

    specs_by_slug: dict[str, list[tuple[str, str]]] = {}
    for slug, _o, label, value in wb["specs"].iter_rows(min_row=2, values_only=True):
        if slug and label and value not in (None, ""):
            specs_by_slug.setdefault(slug, []).append((label, str(value).strip()))

    existing = load_json(products_file, {})
    if isinstance(existing, list):
        sys.exit("products.json est une liste : format inattendu (le site attend une clé = slug).")

    new_by_target: dict[str, list[dict]] = {t["key"]: [] for t in TARGETS}
    stats = dict(truncated=0, missing_img=0, no_img=0, renamed=0, unmatched=[], excluded=[])

    for r in rows:
        target = find_target(r.get("pdf_source"))
        if not target:
            stats["unmatched"].append(f"{r['slug']}  ({r.get('pdf_source')})")
            continue

        if any(re.search(x, norm(r.get("nom") or r.get("slug"))) for x in target.get("exclude", [])):
            stats["excluded"].append(str(r.get("nom") or r["slug"]))
            continue

        src_slug = str(r["slug"]).strip().lower()
        slug = src_slug
        if slug in existing and existing[slug].get("brand") != BRAND:
            slug = "zkteco-" + src_slug
            stats["renamed"] += 1

        gallery = []
        for p in str(r.get("images") or "").split(" | "):
            name = Path(p.strip()).stem
            if not name:
                continue
            rel = f"images/products/{src_slug}/{name}.webp"
            if a.no_check_images or (public / rel).exists():
                gallery.append(rel)
            else:
                stats["missing_img"] += 1
        if not gallery:
            stats["no_img"] += 1

        desc, was_cut = clean_description(r.get("description") or "")
        stats["truncated"] += was_cut

        seen, specs = set(), []
        for label, value in specs_by_slug.get(src_slug, []):
            lab = norm_label(label)
            if lab.lower() in seen:
                continue
            seen.add(lab.lower())
            specs.append({"label": lab, "value": value})

        new_by_target[target["key"]].append({
            "slug": slug,
            "title": (r.get("nom") or src_slug).strip(),
            "subtitle": desc,
            "lead": short_lead(desc),
            "brand": BRAND,
            "group": target["group_of"](r),
            "image": gallery[0] if gallery else None,
            "gallery": gallery,
            "features": [],
            "paragraphs": [],
            "specs": specs,
            "pdfs": [],
            "solution": target["solution"],
            "category": target["category"],
        })

    # ---------- fusion products.json (on ajoute / remplace, on n'efface rien) ----------
    merged = dict(existing)
    for lst in new_by_target.values():
        for p in lst:
            merged[p["slug"]] = p

    # ---------- solutions.json ----------
    solutions = load_json(solutions_file, [])
    notes = []
    for t in TARGETS:
        new = new_by_target[t["key"]]
        if not new:
            continue
        sol = next((s for s in solutions if s.get("slug") == t["solution"]), None)
        if not sol:
            sys.exit(f"Solution introuvable dans solutions.json : {t['solution']}")
        cat = next((c for c in sol.get("categories", []) if c.get("slug") == t["category"]), None)

        if not cat and t.get("create"):
            cr = t["create"]
            img = next((c for c in cr["image_candidates"] if (public / c).exists()), None)
            if not img:
                img = sol.get("image")
                notes.append(f"Catégorie « {cr['title']} » : image par défaut {img} - "
                             f"mettez votre photo dans {cr['image_candidates'][0]} puis relancez.")
            cat = {"slug": t["category"], "title": cr["title"], "url": "", "image": img,
                   "lead": "", "products": [], "children": []}
            sol["categories"].append(cat)
            notes.append(f"Catégorie créée : {sol['slug']} > {cat['title']}")
        if not cat:
            sys.exit(f"Catégorie introuvable : {t['solution']}/{t['category']}")

        # photo dédiée déposée plus tard : on la prend pour la catégorie créée par ce script
        if t.get("create"):
            mine = next((c for c in t["create"]["image_candidates"] if (public / c).exists()), None)
            if mine and cat.get("image") != mine:
                cat["image"] = mine
                notes.append(f"Image de la catégorie « {cat['title']} » : {mine}")

        if t.get("title") and cat.get("title") != t["title"]:
            notes.append(f"Titre de catégorie : « {cat.get('title')} » -> « {t['title']} »")
            cat["title"] = t["title"]
        if t.get("lead"):
            if not cat.get("lead") or (t.get("lead_only_if_flag") and a.replace_access_lead):
                cat["lead"] = t["lead"]
                notes.append(f"Texte de la catégorie « {cat['title']} » mis à jour")

        def key(slug, groups=t["groups"]):
            p = merged[slug]
            g = p.get("group")
            return (groups.index(g) if g in groups else 99, natural_key(p.get("title")))

        cur = [s for s in cat.get("products", []) if s in merged]
        others = [s for s in cur if merged[s].get("brand") != BRAND]
        zk = list(dict.fromkeys([s for s in cur if merged[s].get("brand") == BRAND] + [p["slug"] for p in new]))
        cat["products"] = others + sorted(zk, key=key)

    # ---------- rapport ----------
    print("=" * 60)
    for t in TARGETS:
        lst = new_by_target[t["key"]]
        if not lst:
            continue
        g = {}
        for p in lst:
            g[p["group"]] = g.get(p["group"], 0) + 1
        print(f"{t['key']:11} {len(lst):3} produits  ->  {t['solution']} / {t['category']}")
        print(f"            groupes : {g}")
    print("=" * 60)
    print(f"Sans image              : {stats['no_img']}")
    print(f"Images manquantes       : {stats['missing_img']}  (lancez d'abord prepare_images.py)")
    print(f"Descriptions tronquées  : {stats['truncated']}  (nettoyées)")
    print(f"Slugs renommés (conflit): {stats['renamed']}")
    if stats["excluded"]:
        print(f"Écartés (hors catégorie)  : {', '.join(stats['excluded'])}")
    if stats["unmatched"]:
        print(f"PDF non reconnu ({len(stats['unmatched'])}) : ignorés ->", *stats["unmatched"][:5], sep="\n   ")
    for n in notes:
        print("•", n)
    print(f"products.json           : {len(existing)} -> {len(merged)} produits")

    if a.dry_run:
        print("\n--dry-run : rien n'a été écrit.")
        return

    stamp = datetime.now().strftime("%Y%m%d-%H%M")
    data_dir.mkdir(parents=True, exist_ok=True)
    backup(products_file, stamp)
    backup(solutions_file, stamp)
    products_file.write_text(json.dumps(merged, ensure_ascii=False, indent=2), encoding="utf-8")
    solutions_file.write_text(json.dumps(solutions, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"\nÉcrit. Sauvegardes : *.bak-{stamp}")


if __name__ == "__main__":
    main()