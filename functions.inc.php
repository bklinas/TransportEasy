<?php

// Function to get the visitor's IP address if not already defined
if (!function_exists('getVisitorIP')) {
    function getVisitorIP() {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to fetch geographical information from the geoplugin API
function getGeopluginData($ip) {
    $geoplugin_url = 'http://www.geoplugin.net/xml.gp?ip=' . $ip;
    $geoplugin_data = file_get_contents($geoplugin_url);

    if ($geoplugin_data !== false) {
        $xml = simplexml_load_string($geoplugin_data);

        if ($xml !== false && property_exists($xml, 'geoplugin_countryName') && property_exists($xml, 'geoplugin_region') && property_exists($xml, 'geoplugin_city') && property_exists($xml, 'geoplugin_latitude') && property_exists($xml, 'geoplugin_longitude')) {
            return [
                'country' => $xml->geoplugin_countryName,
                'region' => $xml->geoplugin_region,
                'city' => $xml->geoplugin_city,
                'latitude' => $xml->geoplugin_latitude,
                'longitude' => $xml->geoplugin_longitude,
            ];
        }
    }

    return false;
}

// Function to fetch CSV data from the geoplugin API
function getGeopluginCSV($ip) {
    $csv_url = 'http://www.geoplugin.net/csv.gp?ip=' . $ip;
    $csv_data = file_get_contents($csv_url);

    if ($csv_data !== false) {
        $csv_rows = str_getcsv($csv_data, "\n");
        return $csv_rows;
    }

    return false;
}

// Function to fetch additional information from ipinfo.io
function getIpinfoData($ip) {
    $ipinfo_url = 'https://ipinfo.io/' . $ip . '/geo/json';
    $ipinfo_data = file_get_contents($ipinfo_url);

    if ($ipinfo_data !== false) {
        $ipinfo_json = json_decode($ipinfo_data);

        if ($ipinfo_json !== null && isset($ipinfo_json->country) && isset($ipinfo_json->region) && isset($ipinfo_json->city) && isset($ipinfo_json->loc)) {
            return [
                'country' => $ipinfo_json->country,
                'region' => $ipinfo_json->region,
                'city' => $ipinfo_json->city,
                'latitude' => $ipinfo_json->loc[0],
                'longitude' => $ipinfo_json->loc[1],
            ];
        }
    }

    return false;
}

function whatismyapi() {
    // Your API Key
    $apiKey = 'f2382c00d396cd371965837f75763473';
    
    // Get the user's IP address
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Construct the URL with the API key and IP address
    $url = "https://api.whatismyip.com/ip-address-lookup.php?key=$apiKey&input=$ip&output=xml";

    // Fetch data from the API
    $xmlString = file_get_contents($url);
    
    // Check if data was fetched successfully
    if ($xmlString === false) {
        return "Errors: failed to fetch data from the API";
    } else {
        // Parse XML response
        $xml = simplexml_load_string($xmlString);

        // Check if XML parsing was successful and response status is OK
        if ($xml !== false && isset($xml->query_status->query_status_code) && $xml->query_status->query_status_code == 'OK') {
            // Extract relevant information
            $serverData = $xml->server_data;
            $status = (string)$serverData->status;
            $ip = (string)$serverData->ip;
            $country = (string)$serverData->country;
            $region = (string)$serverData->region;
            $city = (string)$serverData->city;
            $latitude = (string)$serverData->latitude;
            $longitude = (string)$serverData->longitude;

            // Display the extracted information
            return "<p>Status: $status</p>
                    <p>IP: $ip</p>
                    <p>Country: $country</p>
                    <p>Region: $region</p>
                    <p>City: $city</p>
                    <p>Latitude: $latitude</p>
                    <p>Longitude: $longitude</p>";
        } else {
            return "Invalid or incomplete data in the XML response from WhatIsMyAPI.";
        }
    }
}


echo whatismyapi();
