<?php
require_once 'config.php';

function getCities() {
    if (file_exists(CITIES_CACHE_FILE) &&
        (time() - filemtime(CITIES_CACHE_FILE)) < CACHE_LIFETIME) {
        return unserialize(file_get_contents(CITIES_CACHE_FILE));
    }

    $cities = fetchCitiesFromService();

    if ($cities) {
        file_put_contents(CITIES_CACHE_FILE, serialize($cities));
        return $cities;
    }

    if (file_exists(CITIES_CACHE_FILE)) {
        return unserialize(file_get_contents(CITIES_CACHE_FILE));
    }

    return ['Москва'];
}

function fetchCitiesFromService() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, CITIES_SERVICE_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    var_dump($response);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $cities = json_decode($response, true);
        return is_array($cities) ? $cities : false;
    }

    return false;
}
?>