# CSV Page Importer – Technical Task Deliverable

This repository contains a WordPress plugin (**csv-page-importer**) plus a DDEV-based local environment, unit tests, and helper scripts to import Pages from a CSV file and protect preview links with JWT.

---

## 1. Features
- Import CSV → create **Draft Pages**; store original row data as post meta.
- Custom template + shortcode to render imported fields.
- JWT token to secure preview links (auto-attached on preview; manual via REST/WP-CLI).
- PHPUnit test suite (wp-phpunit via composer).
- DDEV config for reproducible local dev.

---

## 2. Requirements
- Docker Desktop + DDEV ≥ 1.23
- PHP 8.1+ (container runs 8.3)
- Composer (host machine)

---

## 3. Quick start

```bash
# Install PHP deps
composer install

# Start DDEV
ddev start

# (If fresh WP) Install core
ddev exec wp --path=web core install \
  --url=http://wp-csv-import-assignment.ddev.site:8080 \
  --title="CSV Page Importer" \
  --admin_user=admin --admin_password=admin --admin_email=admin@example.com

# Activate plugin
ddev exec wp --path=web plugin activate csv-page-importer
```
---

##5. Repo structure
```
wp-csv-import-assignment/
├─ .ddev/
├─ dump/                      # contain db-export.sql(.gz) v.v.
├─ vendor/                    # composer deps
├─ README.md                  
├─ Makefile                   
├─ composer.json
├─ composer.lock
└─ web/
   ├─ wp-admin/               # core WP
   ├─ wp-includes/            # core WP
   ├─ wp-config.php
   ├─ wp-config-ddev.php
   ├─ wp-content/
   │  ├─ plugins/
   │  │  ├─ akismet/          # plugin WP default
   │  │  └─ csv-page-importer/
   │  │     ├─ includes/      # namespace CPI\
   │  │     │  ├─ class-csv-importer.php
   │  │     │  ├─ class-page-creator.php
   │  │     │  └─ helpers.php
   │  │     ├─ templates/
   │  │     │  └─ page-cpi.php
   │  │     ├─ tests/
   │  │     │  ├─ bootstrap.php
   │  │     │  ├─ wp-tests-config.php
   │  │     │  ├─ test-csv-importer.php
   │  │     │  └─ test-page-creator.php
   │  │     ├─ .phpcs.xml.dist
   │  │     ├─ phpunit.xml.dist
   │  │     ├─ csv-page-importer.php   # main plugin file
   │  │     ├─ hello.php / index.php   # (sample/placeholder)
   │  │     └─ README (optional)
   │  ├─ themes/
   │  ├─ uploads/
   │  └─ index.php
   └─ index.php
```
---
## 6. CSV format
```aiexclude
title,slug,content,excerpt
About Us,about-us,"This is the about page...","Learn more..."
```