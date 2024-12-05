<?php require 'header.php'; ?>
<div class="container">
<?php
// Read the elevator status data file and extract relevant information
$elevatorStatus = [];
$elevatorFile = fopen("etat-des-ascenseurs.csv", "r");
while (($data = fgetcsv($elevatorFile, 1000, ";")) !== FALSE) {
    $elevatorStatus[] = $data;
}
fclose($elevatorFile);

// Read the public toilet data from the JSON file
$toiletData = file_get_contents("sanitaires-reseau-ratp.json");
$toiletData = json_decode($toiletData, true);

// Read the parking relais data from the JSON file
$parkingRelaisData = file_get_contents("parkings_relais_paris_2024.json");
$parkingRelaisData = json_decode($parkingRelaisData, true);

// Read the air conditioning data for rail lines from the JSON file
$airConditioningData = file_get_contents("climatisation_lignes_transport.json");
$airConditioningData = json_decode($airConditioningData, true);
?>
<div class="station-info">
<?php

// Get the selected station name from the query parameter
if (isset($_GET['station_name'])) {
    $selectedStation = $_GET['station_name'];

    $elevatorAvailability = "Unknown";
    $additionalInfo = ""; // Initialize additional info variable

    echo "<p>Found station: $selectedStation</p>"; 

    foreach ($elevatorStatus as $elevator) {
        
        if (strcasecmp(trim($elevator[12]), trim($selectedStation)) === 0) {
            $elevatorAvailability = $elevator[4]; 
            if ($elevatorAvailability === "notavailable") {
                $additionalInfo = $elevator[3]; 
                break; 
            }
        }
    }

   
    if ($elevatorAvailability === "notavailable") {
        echo "<p>Elevator status for $selectedStation: $elevatorAvailability</p>";
        echo "<p>Additional Info: $additionalInfo</p>";
    } else {
        echo "<p>Elevator availability for $selectedStation: $elevatorAvailability</p>";
    }

   
    $toiletAvailability = "Not available";
    foreach ($toiletData as $toilet) {
        if (strcasecmp(trim($toilet['fields']['station']), trim($selectedStation)) === 0) {
            $toiletAvailability = "Available";
            break;
        }
    }

    echo "<p>Toilet availability for $selectedStation: $toiletAvailability</p>";

    
    $parkingRelaisAvailability = "Not available";
    foreach ($parkingRelaisData as $parkingRelais) {
        if (strcasecmp(trim($parkingRelais['fields']['nom_gare']), trim($selectedStation)) === 0) {
            $parkingRelaisAvailability = "Available";
            break;
        }
    }

  
    echo "<p>Parking relais availability for $selectedStation: $parkingRelaisAvailability</p>";

$airConditioningInfo = "Not available";
foreach ($airConditioningData as $acLine) {
    if (strcasecmp(trim($acLine['fields']['name_line']), trim($selectedStation)) === 0 && $acLine['fields']['transportmode'] === 'Rail') {
        $airConditioningInfo = $acLine['fields']['air_conditioning'];
        break;
    }
}


if ($airConditioningInfo !== "Not available") {
    echo "<p>Air conditioning for rail line $selectedStation: $airConditioningInfo</p>";
} else {
    echo "<p>No air conditioning information available for rail line at $selectedStation</p>";
}
}
?>
</div>
</div>
<?php require 'footer.php'; ?>
