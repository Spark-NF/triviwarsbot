#!/usr/bin/env bash

php ./vendor/doctrine/orm/bin/doctrine.php orm:schema:update --force
php ./vendor/doctrine/orm/bin/doctrine.php orm:generate-proxies