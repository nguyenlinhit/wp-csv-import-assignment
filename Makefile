# ----------------------
# Global vars
WP    = wp --path=web
PLUG  = web/wp-content/plugins/csv-page-importer
VENDOR= vendor

start:
	ddev start

stop:
	ddev poweroff

bash:
	ddev ssh

wp:
	ddev exec $(WP) $(CMD)

# ===== Database =====
export-db:
	mkdir -p dump
	ddev export-db --file=dump/db-export.sql.gz

import-db:
	ddev import-db --src=dump/db-export.sql

# ===== Tests =====
# Ensure test DB & run phpunit
prepare-test-db:
	ddev mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS wordpress_test; GRANT ALL ON wordpress_test.* TO 'db'@'%'; FLUSH PRIVILEGES;"
	touch .testdb-prepared

phpunit: prepare-test-db
	cd $(PLUG) && WP_PHPUNIT__TESTS_CONFIG=tests/wp-tests-config.php \
	../../../../$(VENDOR)/bin/phpunit -c phpunit.xml.dist -v

test: phpunit

# ===== Tokens =====
token:
	@if [ -z "$(POST)" ]; then echo "Usage: make token POST=123 TTL=3600"; exit 1; fi
	ddev exec $(WP) eval "echo CPI\\generate_token_for_page($(POST),$${TTL:-3600});"

# ===== Coding Standards (optional) =====
phpcs:
	./vendor/bin/phpcs web/wp-content/plugins/csv-page-importer --standard=WordPress --ignore=vendor,tests

phpcbf:
	./vendor/bin/phpcbf web/wp-content/plugins/csv-page-importer --standard=WordPress --ignore=vendor,tests

# ===== Plugin Zip =====
zip-plugin:
	cd $(PLUG) && zip -r ../../../../csv-page-importer-1.0.0.zip . -x "tests/*" \
	".git/*" "vendor/bin/*" "node_modules/*"

.PHONY: start stop bash wp export-db import-db test phpunit token phpcs phpcbf zip-plugin