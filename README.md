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

# 1. Installation von wp-cli

Da du ohnehin gerne selbst Hand anlegst, ist der Standard-Weg über das Phar-Archiv am saubersten:

```php
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

Prüfe die Funktion mit `wp --info`.

## 2. Grundlegende Nutzung

**Wichtig:** `wp-cli` darf (und sollte) **niemals als root** ausgeführt werden, wenn du WordPress-Dateien verwaltest. Du musst es als der User ausführen, dem die Web-Dateien gehören (z. B. `www-data` oder dein spezifischer User).

## Die wichtigsten Befehle:

* **Updates prüfen:** `wp core check-update`
* **Alles updaten (Core, Plugins, Themes):**

```sh
wp core update
wp plugin update --all
wp theme update --all
```
* **Datenbank-Backup (vor dem Update ratsam):**

```sh
wp db export backup.sql
```

## 3. wp-cli via Crontab (Automatisierung)

Ja, das ist absolut möglich und sogar sehr empfohlen. Da du **kein systemd** hast, nutzt du den klassischen `vixie-cron` oder `dcron`.

## Das Problem mit dem User & Pfad

Cron läuft oft in einer sehr eingeschränkten Umgebung. Du musst zwei Dinge beachten:

1. Den richtigen **User** nutzen.
2. In das **WordPress-Verzeichnis** wechseln (oder den Pfad mit `--path` angeben).

## Beispiel für ein Cron-Skript

```php
#!/bin/sh
# Pfad zu deinem WordPress-Verzeichnis
WP_PATH="/var/www/my-website"

# Zum WordPress-Verzeichnis wechseln
cd $WP_PATH

# Updates durchführen
/usr/local/bin/wp core update --quiet
/usr/local/bin/wp plugin update --all --quiet
/usr/local/bin/wp theme update --all --quiet
/usr/local/bin/wp core update-db --quiet
```

Vergiss nicht: `chmod +x /usr/local/bin/wp-auto-update.sh`

## Eintrag in die Crontab

Editiere die Crontab des Web-Users (z. B. `www-data`):

```php
crontab -u www-data -e
```

```crontab
0 4 * * * /usr/local/bin/wp-auto-update.sh
```


