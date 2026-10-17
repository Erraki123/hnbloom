<?php

/*
 * CONTENU DU SITE HN BLOOM (équivalent de content.js).
 * Tout ce qui s'affiche sur les pages publiques se modifie ICI.
 * Source : analyse de hnbloom.com (16/10/2026). Les points marqués "À VALIDER"
 * doivent être confirmés avec HN BLOOM avant la mise en ligne.
 *
 * Après chaque modification :  php artisan config:clear
 */

$u = fn ($id, $w = 1200) => "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q=80";

return [
    'name'     => 'HN BLOOM',
    'monogram' => 'H',
    'tagline'  => 'Distributeur de solutions de sécurité électronique et physique',

    // ---------- IMAGES (dossier public/images) ----------
    'images' => [
        'hero'      => 'images/hero.jpg',        // fond de la page d'accueil
        'about'     => 'images/about.jpg',       // photo "À propos" (accueil)
        'projects'  => 'images/projects.jpg',
        'contact'   => 'images/contact.jpg',
        'solutions' => 'images/solutions.jpg',   // fond de la page Nos solutions
    ],

    // ---------- VIDÉOS (dossier public/videos) ----------
    'hero_video'      => 'videos/HN_BLOOM_cinematic_loop.mp4',  // vidéo du hero (accueil)
    'hero_poster'     => 'images/hero-night.jpg',               // image affichée pendant le chargement
    'about_video'     => 'videos/about.mp4',                    // vidéo "À propos" (accueil)
    'solutions_video' => 'videos/solutions.mp4',                // vidéo du hero de la page Nos solutions

    // ---------- ÉCRAN D'OUVERTURE (vidéo -> bouton Entrer) ----------
    'intro' => [
        'video'            => 'videos/intro.mp4',
        'poster'           => 'images/hero-night.jpg',
        'once_per_session' => false,   // false = visible à chaque chargement (test) | true = une seule fois par visite
    ],

    // ---------- Cartes de la page d'accueil (4 premières affichées) ----------
    'solutions' => [
        ['title' => 'Obstacles physiques automatisés', 'icon' => 'construction', 'text' => 'Solutions physiques pour le contrôle des accès.',
         'href' => '/solutions/obstacles-physiques-automatises', 'image' => 'images/obstacles.jpg', 'fallback' => $u('1558002038-1055907df827', 900)],
        ['title' => 'Hygiène agro-alimentaire', 'icon' => 'droplets', 'text' => 'Solutions pour l’hygiène en milieu agro-alimentaire.',
         'href' => '/solutions/hygiene-agro-alimentaire', 'image' => 'images/hygiene.jpg', 'fallback' => $u('1557597774-9d273605dfa9', 900)],
        ['title' => 'Sécurité électronique', 'icon' => 'shield-check', 'text' => 'Solutions électroniques de sécurité.',
         'href' => '/solutions/securite-electronique', 'image' => 'images/securite.jpg', 'fallback' => $u('1518770660439-4636190af475', 900)],
        ['title' => 'Détection incendie', 'icon' => 'flame', 'text' => 'Solutions de détection incendie.',
         'href' => '/solutions/detection-incendie', 'image' => 'images/incendie.jpg', 'fallback' => $u('1558618666-fcd25c85cd64', 900)],
        ['title' => 'Domotique', 'icon' => 'cpu', 'text' => 'Représentant exclusif de la marque SMART G4 CONTROL.',
         'href' => '/domotique', 'image' => 'images/domotique.jpg', 'fallback' => null],
    ],

    'steps' => [
        ['n' => '01', 'title' => 'Étude',             'text' => 'Analyse de vos besoins et choix des solutions adaptées.'],
        ['n' => '02', 'title' => 'Installation',      'text' => 'Mise en place des équipements sur site.'],
        ['n' => '03', 'title' => 'Commercialisation', 'text' => 'Distribution des solutions de sécurité et de domotique.'],
    ],

    // Aperçu "Réalisations" de l'accueil (exemples de la maquette : à remplacer par de vrais projets)
    'projects' => [
        ['title' => 'Villa résidentielle', 'city' => 'Casablanca', 'image' => 'images/projet1.jpg', 'fallback' => $u('1600607687939-ce8a6c25118c', 900)],
        ['title' => 'Bâtiment tertiaire',  'city' => 'Rabat',       'image' => 'images/projet2.jpg', 'fallback' => $u('1486406146926-c627a92ad1ab', 900)],
        ['title' => 'Résidence de luxe',   'city' => 'Marrakech',   'image' => 'images/projet3.jpg', 'fallback' => $u('1600566753086-00f18fb6b3ea', 900)],
    ],

    'about' => [
        'fallback' => $u('1600607687920-4e2a09cf159d', 1200),
        'lines' => [
            'Distributeur des solutions de sécurité électroniques et physiques.',
            'Représentant exclusif de la marque SMART G4 CONTROL des solutions domotiques.',
            'Spécialisée dans l’étude, l’installation et la commercialisation de solutions de domotique, sécurité, éclairage et sonorisation.',
        ],
    ],

    'contact' => [
        'phones'   => ['06 61 86 46 02', '05 22 35 13 33'],
        'email'    => 'contact@hnbloom.com',
        // À VALIDER : Facebook indique une autre adresse (174 Bd Zerktouni, étage 5, bureau 12)
        'address'  => '120 Bdv Grande Ceinture, Imm Lilya 2, 3ème étage, N° 27, Casablanca',
        'hours'    => 'Lundi au samedi · 9h – 18h',
        'whatsapp' => '212661864602',
        'socials'  => [
            ['label' => 'Facebook',  'href' => 'https://www.facebook.com/people/HN-BLoom/61563889342090/?mibextid=ZbWKwL'],
            ['label' => 'Instagram', 'href' => 'https://www.instagram.com/hn.bloom.official/'],
            ['label' => 'LinkedIn',  'href' => 'https://www.linkedin.com/company/hn-bloom-official/'],
            ['label' => 'TikTok',    'href' => 'https://www.tiktok.com/@hn.bloom.official'],
        ],
    ],

    // ---------- PAGE "QUI SOMMES-NOUS" ----------
    'about_page' => [
        'lead' => 'HN BLOOM est une entreprise marocaine de solutions techniques pour les bâtiments, les sites industriels et les établissements recevant du public, basée à Casablanca.',
        'roles' => [
            ['icon' => 'factory',     'title' => 'Fabricant',     'text' => 'Obstacles physiques automatisés et équipements de contrôle d’accès avec lavage ou désinfection.'], // À VALIDER : références réellement fabriquées au Maroc
            ['icon' => 'package',     'title' => 'Distributeur',  'text' => 'Solutions de sécurité électroniques et physiques, terminaux de contrôle d’accès et de reconnaissance faciale.'],
            ['icon' => 'cpu',         'title' => 'Représentant exclusif', 'text' => 'SMART G4 CONTROL au Maroc pour la domotique et le smart building.'],
            ['icon' => 'construction','title' => 'Intégrateur',   'text' => 'Étude, conseil, installation, mise en service et support technique.'],
        ],
        'values' => [
            ['title' => 'Qualité artisanale',      'text' => 'Un soin particulier apporté à la conception et à la finition des équipements.'],
            ['title' => 'Innovation technologique','text' => 'Des solutions modernes, intégrées et évolutives.'],
            ['title' => 'Durabilité',              'text' => 'Des équipements pensés pour durer dans le temps.'],
            ['title' => 'Service client',          'text' => 'Proximité, réactivité et accompagnement technique local.'],
        ],
        'method' => [
            ['n' => '01', 'title' => 'Écoute',       'text' => 'Qualification de votre besoin.'],
            ['n' => '02', 'title' => 'Étude',        'text' => 'Proposition technique adaptée à votre bâtiment.'],
            ['n' => '03', 'title' => 'Fourniture',   'text' => 'Fourniture et intégration des équipements.'],
            ['n' => '04', 'title' => 'Installation', 'text' => 'Installation et mise en service sur site.'],
            ['n' => '05', 'title' => 'Support',      'text' => 'Formation, maintenance et support technique.'],
        ],
    ],

    // ---------- PAGES "NOS SOLUTIONS" (une page par famille) ----------
    // Chaque famille peut avoir : 'image' (public/images), 'video' (public/videos).
    // Les noms de gammes viennent du site actuel. Les caractéristiques techniques (normes,
    // dimensions, capacités) ne sont PAS inventées : elles viendront des fiches techniques HN BLOOM.
    'families' => [
        'obstacles-physiques-automatises' => [
            'title' => 'Obstacles physiques automatisés', 'icon' => 'construction', 'eyebrow' => 'Contrôle d’accès',
            'lead'  => 'Gamme TRX de tourniquets, couloirs rapides, portillons et barrières pour filtrer et réguler les flux d’entrée et de sortie.',
            'image' => 'images/obstacles.jpg', 'fallback' => $u('1558002038-1055907df827', 1800),
            'video' => 'videos/obstacles.mp4',
            'offers' => [
                ['title' => 'Tourniquets tripodes',       'text' => 'Régulation des passages piétons.'],
                ['title' => 'Couloirs rapides',           'text' => 'Passage fluide pour les sites à fort trafic.'],
                ['title' => 'Portillons PMR',             'text' => 'Accès adapté aux personnes à mobilité réduite.'],
                ['title' => 'Barrières automatiques',     'text' => 'Contrôle des accès véhicules.'],
                ['title' => 'Tourniquets pleine hauteur', 'text' => 'Sécurisation renforcée des périmètres.'],
            ],
            'benefits' => ['Filtrage et régulation des flux', 'Contrôle des entrées et sorties', 'Intégration avec badge, biométrie ou reconnaissance faciale', 'Accessibilité PMR', 'Support technique local', 'Solution adaptable à votre projet'],
            'targets'  => ['Entreprises et bureaux', 'Usines et sites industriels', 'Administrations', 'Écoles et centres de formation', 'Salles de sport et stades', 'Parkings et infrastructures de transport', 'Événements'],
        ],
        'hygiene-agro-alimentaire' => [
            'title' => 'Hygiène agro-alimentaire', 'icon' => 'droplets', 'eyebrow' => 'Hygiène & flux',
            'lead'  => 'Lave-mains, lave-semelles, lave-bottes et sas de désinfection pour maîtriser l’hygiène des opérateurs.',
            'image' => 'images/hygiene.jpg', 'fallback' => $u('1557597774-9d273605dfa9', 1800),
            'video' => 'videos/hygiene.mp4',
            'offers' => [
                ['title' => 'LM1',     'text' => 'Lave-main automatisé.'],
                ['title' => 'LS1',     'text' => 'Lave-semelles automatisé.'],
                ['title' => 'LSH',     'text' => 'Sas de désinfection des mains et semelles.'],
                ['title' => 'LSMH',    'text' => 'Sas hygiène complet.'],
                ['title' => 'LVB',     'text' => 'Lave-botte manuel.'],
                ['title' => 'LVBA',    'text' => 'Lave-bottes automatisé.'],
                ['title' => 'LVMX-IR', 'text' => 'Lave-main à commande infrarouge.'],
                ['title' => 'SH1',     'text' => 'Sas de désinfection des mains.'],
            ],
            'benefits' => ['Hygiène des opérateurs', 'Maîtrise des flux de personnes', 'Équipements automatisés ou manuels selon le site', 'Étude et installation sur site'],
            'targets'  => ['Industries agro-alimentaires', 'Abattoirs', 'Usines de transformation', 'Laboratoires', 'Cuisines centrales', 'Sites pharmaceutiques'],
        ],
        'securite-electronique' => [
            'title' => 'Sécurité électronique', 'icon' => 'shield-check', 'eyebrow' => 'Sécurité',
            'lead'  => 'Contrôle d’accès, reconnaissance faciale, vidéophonie et alarmes pour protéger les personnes et les biens.',
            'image' => 'images/securite.jpg', 'fallback' => $u('1518770660439-4636190af475', 1800),
            'video' => 'videos/securite.mp4',
            'offers' => [
                ['title' => 'Contrôle d’accès et reconnaissance faciale', 'text' => 'Terminaux ZKTeco : reconnaissance faciale assistée par IA, badge, empreinte, connexion TCP/IP et Wi-Fi.'],
                ['title' => 'Scanners à bagages',           'text' => 'Contrôle de sécurité des sites sensibles. Détails et fiche technique sur demande.'],
                ['title' => 'Vidéophonie',                  'text' => 'Systèmes résidentiels, professionnels et industriels avec gestion centralisée sur ordinateur ou mobile.'],
                ['title' => 'Alarmes filaires et sans fil', 'text' => 'Systèmes complets pour environnements résidentiels, professionnels et industriels.'],
            ],
            'benefits' => ['Gestion centralisée sur PC ou mobile', 'Support technique permanent', 'Étude, installation et mise en service', 'Intégration avec les obstacles physiques'],
            'targets'  => ['Villas et résidences', 'Immeubles et copropriétés', 'Bureaux et entreprises', 'Sites industriels', 'Hôtels', 'Établissements scolaires'],
        ],
        'detection-incendie' => [
            'title' => 'Détection incendie', 'icon' => 'flame', 'eyebrow' => 'Prévention incendie',
            'lead'  => 'Détection conventionnelle ou adressable et extinction automatique pour protéger vos bâtiments.',
            'image' => 'images/incendie.jpg', 'fallback' => $u('1558618666-fcd25c85cd64', 1800),
            'video' => 'videos/incendie.mp4',
            // À VALIDER avant d'afficher des normes : origine des équipements (Portugal ou Grande-Bretagne ?) et certificats EN 54 / CPR
            'offers' => [
                ['title' => 'Détection conventionnelle',            'text' => 'Zones de détection pour les bâtiments de taille courante.'],
                ['title' => 'Détection adressable',                 'text' => 'Localisation précise de chaque point de détection.'],
                ['title' => 'Extinction automatique',               'text' => 'Réaction automatique en cas de départ de feu.'],
                ['title' => 'Autres systèmes de détection incendie','text' => 'Solutions complémentaires selon le site.'],
            ],
            'benefits' => ['Étude, installation et mise en service', 'Support technique', 'Formation assurée par nos ingénieurs et techniciens', 'Approvisionnement rapide'],
            'targets'  => ['Hôtels', 'Immeubles', 'Usines et entrepôts', 'Écoles', 'Établissements de santé', 'Centres commerciaux', 'Bureaux', 'Restaurants', 'Parkings'],
        ],
    ],
    'contact_video' => 'videos/contact.mp4',   // vidéo du hero de la page Contact (optionnel)
'projects_video'  => 'videos/projects.mp4',
    // ---------- PAGE "DOMOTIQUE" ----------
    'domotique' => [
        'lead' => 'Représentant exclusif au Maroc de SMART G4 CONTROL, HN BLOOM étudie et installe des systèmes qui améliorent le confort, la sécurité et l’efficacité énergétique des bâtiments.',
        'image' => 'images/domotique.jpg',
        'fallback' => $u('1558002038-1055907df827', 1800),

        // Fond de la page Domotique (vidéo + image de secours, comme l'accueil)
        'bg_video' => 'videos/domotique.mp4',
        'bg_poster' => 'images/domotique.jpg',
        'bg_image' => 'images/domotique.jpg',

        'markets' => [
            [
                'slug' => 'smart-home', 'icon' => 'house', 'title' => 'Smart Home',
                'category' => 'RÉSIDENTIEL',
                'text' => 'Maisons et immeubles intelligents.',
                'image' => 'images/smart-home.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1000&q=85',
                'items' => [
                    ['name' => 'Portail et entrée des visiteurs', 'text' => 'Ouverture et surveillance des accès.', 'image' => null],
                    ['name' => 'Visiophone et contrôle d’accès', 'text' => 'Dialogue et ouverture à distance.', 'image' => null],
                    'Éclairage et ambiances', 'Scènes de couleurs', 'Rideaux motorisés', 'Climatisation (CVC)',
                    'Irrigation', 'Fontaines et piscine', 'Gestion de l’énergie',
                    'Contrôle vocal et applications mobiles', 'Sécurité',
                ],
            ],
            [
                'slug' => 'smart-hotel', 'icon' => 'hotel', 'title' => 'Smart Hotel',
                'category' => 'HÔTELLERIE',
                'text' => 'Solution GRMS pour hôtels, Airbnb et appartements meublés.',
                'image' => 'images/smart-hotel.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=85',
                'items' => ['Carte-clé et détection d’occupation', 'Statuts DND et MUR', 'Économie d’énergie', 'Éclairage, température et CVC', 'Demandes de service', 'Rideaux, prises et interfaces murales', 'Musique, vidéo et télévision', 'BMS', 'Sonorisation (BGM, PA)', 'Signalisation et affichage LED', 'Gestion du stationnement', 'Contrôle des ascenseurs'],
            ],
            [
                'slug' => 'smart-hospital', 'icon' => 'heart-pulse', 'title' => 'Smart Hospital',
                'category' => 'SANTÉ',
                'text' => 'Solutions SMART G4 pour les établissements de santé et d’accueil. Chaque projet est étudié selon les exigences de l’établissement.',
                'image' => 'images/smart-hospital.jpg',
                'fallback' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1000&q=85',
                'items' => ['Appels infirmiers', 'Soins dentaires intelligents', 'Flux des cliniques ambulatoires', 'Systèmes de bâtiments hospitaliers', 'Personnes âgées', 'Personnes handicapées', 'Spas de santé', 'Orphelinats'],
            ],
        ],
    ],

    // ---------- PAGE "NOS PROJETS" ----------
    // À VALIDER : autorisation d'afficher les noms + rôle exact de HN BLOOM sur chaque projet
    'projects_done' => [
        'Autoroute du Maroc — péage Bouznika', 'Événementiel SIAM 2019 et 2023', 'OCP', 'Institut Royal des Études Stratégiques',
        'Renault', 'Auto Expo 2018', 'Salle de sport Plazza', 'Salle de sport Passage Fitness', 'Richbond', 'Lear Corporation',
        'Netflix', 'Centre d’appel Majorel', 'Laprophan', 'CMS-SOGEA — Tour AWB Casa Finance City',
        'The American School of Benguerir', 'TIMAR', 'Yazaki', 'Stade Moulay Abdellah de Rabat',
    ],
    'projects_brand' => [
        'Sheraton Harghada (Égypte)', 'Sofitel Hotel Casablanca', 'Sheraton Dubai Creek', 'Le Méridien Al Khobar',
        'Hôtel à Kos Island (Grèce)', 'Wyndham Manama Bahrain Bay', 'The Gomeh Sea of Galilee Hotel',
    ],
];
