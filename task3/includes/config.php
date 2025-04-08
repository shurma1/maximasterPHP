<?php
define('CITIES_SERVICE_URL', 'http://localhost:8010/service/city/');
define('DELIVERY_SERVICE_URL', 'http://localhost:8010/service/delivery/');

define('CACHE_DIR', __DIR__ . '/../cache');
define('CITIES_CACHE_FILE', CACHE_DIR . '/cities.cache');
define('CACHE_LIFETIME', 24 * 60 * 60);

if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}
?>