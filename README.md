# README SIMONNEELLE

```tree
/var/www/simonneelle_de/
├── .env                  <-- Sensible Daten (DB, API-Keys)
├── composer.json         <-- Deine Abhängigkeiten & Autoload-Regeln
├── composer.lock
├── composer.phar         <-- Dein (sicheres) Tool
├── vendor/               <-- Drittanbieter-Code (Dotenv, etc.)
├── src/                  <-- DEIN EIGENTLICHER CODE (Klassen, Logik)
│   └── GoogleShopping/
│       └── FeedGenerator.php
└── wordpress/            <-- Nur der "dumme" Core & Content
    ├── wp-content/
    │   └── mu-plugins/
    │       └── loader.php <-- Die einzige Brücke nach draußen
    └── ...
```