<?php
include 'header.php';
include 'apod.php';
?>

<div style="text-align: center;">
<?php
include 'functions.inc.php';
// Main logic
$ipToUse = isset($_GET['ip']) ? filter_var($_GET['ip'], FILTER_VALIDATE_IP) : getVisitorIP();

// Get geoplugin data
$geopluginData = getGeopluginData($ipToUse);
$geopluginCSV = getGeopluginCSV($ipToUse);

// Display geoplugin data
if ($geopluginData !== false) {
    echo "<h2>Geographical Information:</h2>";
    echo "<p>Country: {$geopluginData['country']}</p>";
    echo "<p>Region: {$geopluginData['region']}</p>";
    echo "<p>City: {$geopluginData['city']}</p>";
    echo "<p>Latitude: {$geopluginData['latitude']}</p>";
    echo "<p>Longitude: {$geopluginData['longitude']}</p>";
} else {
    echo "Failed to fetch geoplugin data.";
}

// Display geoplugin CSV data
if ($geopluginCSV !== false) {
    echo "<h2>CSV Data:</h2>";
    echo "<ul>";
    foreach ($geopluginCSV as $row) {
        echo "<li>$row</li>";
    }
    echo "</ul>";
} else {
    echo "Failed to fetch geoplugin CSV data.";
}

// Additional functionality for the new API URLs
$ipinfoData = getIpinfoData($ipToUse);

// Display ipinfo.io data
if ($ipinfoData !== false) {
    echo "<h2>Additional Information (ipinfo.io):</h2>";
    echo "<p>Country: {$ipinfoData['country']}</p>";
    echo "<p>Region: {$ipinfoData['region']}</p>";
    echo "<p>City: {$ipinfoData['city']}</p>";
    echo "<p>Latitude: {$ipinfoData['latitude']}</p>";
    echo "<p>Longitude: {$ipinfoData['longitude']}</p>";
} else {
    echo "Failed to fetch ipinfo.io data.";
}

// Display WhatIsMyAPI data
echo "<h2>Additional Information (WhatIsMyAPI):</h2>";
$whatismyapiData = whatismyapi();
echo $whatismyapiData;
?>
</div>

<?php
include 'footer.php';
?>

