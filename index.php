<?php
/**
 * @file
 * This file contains PHP code for handling station search and form submission.
 */
// Read the data file and extract station names for departure and arrival
$stations = [];
$file = fopen("zones-d-arrets.csv", "r");
while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
    $stations[] = $data; 
}
fclose($file);
?>
<?php
// Read the elevator status data file and extract relevant information
$elevatorStatus = [];
$elevatorFile = fopen("etat-des-ascenseurs.csv", "r");
while (($data = fgetcsv($elevatorFile, 1000, ";")) !== FALSE) {
    $elevatorStatus[] = $data; 
}
fclose($elevatorFile);
?>
<?php require 'header.php'; ?>
<div class="wrapper">
	<svg>
		<text x="50%" y="50%" dy=".35em" text-anchor="middle">
			Bienvenue sur TransportEasy!
		</text>
	</svg>
</div>
<div class="introduction">
   
    <h2 style="text-align: center;">Planifiez votre voyage en utilisant notre formulaire intuitif ci-dessous. Que vous soyez un voyageur régulier </h2>
    <h2 style="text-align: center;"> ou un explorateur occasionnel,TransportEasy vous accompagne à chaque étape de votre trajet.</h2>
</div>


<main>
    
    <!-- Random image display -->
    <aside id="left">
        <?php
       
        $dossier = "photos/";

        
        $images = array("gare.avif", "gare_du_nord.jpeg", "Impression_Gare_de_ST_Lazare.jpg", "station.jpg", "painting.jpeg");

        
        shuffle($images);

       
        $imageAleatoire = $images[0];

       
        $imagePath = $dossier . $imageAleatoire;

       
        $altText = "Random image"; 

       
        echo '<figure style="text-align: center;">';
        echo '<img src="' . $imagePath . '" alt="' . $altText . '" class="random-image">';
echo '</figure>';

        ?>
    </aside>

    <div class="intro" id="right">
        <img src="/photos/location.png" alt="Your Image">
        <h3>Où allons-nous ?</h3>
       
        <form action="search.php" method="get" id="searchForm">
        <label for="departure_station">DÉPART (adresse, arrêt, lieu...) (obligatoire)</label>
<input type="text" id="departure_station" name="departure_station" list="departure_list" required>
<datalist id="departure_list">
    <?php foreach ($stations as $station): ?>
        <?php if ($station[8] === 'railStation'): ?>
            <option value="<?php echo $station[4]; ?>">
        <?php endif; ?>
    <?php endforeach; ?>
</datalist>

<label for="arrival_station">ARRIVÉE (adresse, arrêt, lieu...) (obligatoire)</label>
<input type="text" id="arrival_station" name="arrival_station" list="arrival_list" required onchange="fetchStationId()">
<datalist id="arrival_list">
    <?php foreach ($stations as $station): ?>
        <?php if ($station[8] === 'railStation'): ?>
            <option value="<?php echo $station[4]; ?>">
        <?php endif; ?>
    <?php endforeach; ?>
</datalist>

            <label for="datetime_option">Quand :</label>
            <select id="datetime_option" onchange="showDateTimeFields()">
                <option value="now">Partir maintenant</option>
                <option value="custom">Choisir une date et une heure</option>
            </select>

            <div id="datetime_fields" style="display: none;">
                <label for="date">Date :</label>
                <input type="date" id="date" name="date">

                <label for="time">Heure :</label>
                <input type="time" id="time" name="time">
            </div>
            <!-- Hidden input field to hold the station_id -->
            <input type="hidden" name="station_id" id="station_id">
            <label for="detail_level">Niveau de détail :</label>
<select id="detail_level" name="detail_level" onchange="toggleStationDetailsInput()">
    <option value="general">Informations générales</option>
    <option value="detailed">Info trafic</option>
    <option value="details">Détails de la station</option> 
</select>

<!-- Div to hold station details input -->
<div id="station_details_input" style="display: none;">
    <label for="station_name">Nom de la station :</label>
    <input type="text" id="station_name" list="station_list">
    <datalist id="station_list">
        <?php
        // Read the elevator status data file and extract station names
        $elevatorStations = [];
        $elevatorFile = fopen("etat-des-ascenseurs.csv", "r");
        while (($data = fgetcsv($elevatorFile, 1000, ";")) !== FALSE) {
            $elevatorStations[] = $data[12]; // Assuming station name is in the 13th column (index 12)
        }
        fclose($elevatorFile);

        // Remove duplicates and display options
        $uniqueStations = array_unique($elevatorStations);
        foreach ($uniqueStations as $station) {
            echo "<option value=\"$station\">";
        }
        ?>
    </datalist>
    <button onclick="fetchStationDetails(); clearSearchBars();">Obtenir les détails</button>
</div>
    




   



            <input type="submit" value="Rechercher">
            
        </form>
    
   
    </div>      
</main>



<script>
    function clearSearchBars() {
    document.getElementById("departure_station").value = "";
    document.getElementById("arrival_station").value = "";
}

    function toggleStationDetailsInput() {
    var detailOption = document.getElementById("detail_level").value;
    var stationDetailsInput = document.getElementById("station_details_input");

   
    if (detailOption === "details") {
        stationDetailsInput.style.display = "block";
    } else {
        stationDetailsInput.style.display = "none";
    }
}

    function showDateTimeFields() {
    var option = document.getElementById("datetime_option").value;
    var datetimeFields = document.getElementById("datetime_fields");
    if (option === "custom") {
        datetimeFields.style.display = "block";
    } else {
        datetimeFields.style.display = "none";
    }
    
    
    var detailOption = document.getElementById("detail_level").value;
    var stationDetailsInput = document.getElementById("station_details_input");
    if (detailOption === "details") {
        stationDetailsInput.style.display = "block";
        fetchStationDetails();
    } else {
        stationDetailsInput.style.display = "none";
    }
}

function fetchStationDetails() {
    var stationName = document.getElementById("station_name").value;

    
    window.location.href = "details.php?station_name=" + encodeURIComponent(stationName);
}



    function fetchStationId() {
        var arrivalStation = document.getElementById("arrival_station").value;
        var stations = <?php echo json_encode($stations); ?>; 

        for (var i = 0; i < stations.length; i++) {
            var station = stations[i];
            if (station[4] === arrivalStation) { 
                document.getElementById("station_id").value = station[0]; 
                break; 
            }
        }

       
        var date = document.getElementById("date").value;
        var time = document.getElementById("time").value;
        var dateTime = date + "T" + time + ":00"; // Combine date and time in ISO 8601 format
        document.getElementById("datetime").value = dateTime;
    }
</script>
<?php if(isset($_COOKIE['mode']) && ($_COOKIE['mode'] === 'night')): ?>
    <div class="image-container">
    <img src="/photos/rerb.png" alt="Description of the image" style="max-width: 500px; height: auto;">
    </div>
<?php endif; ?>
<?php if(isset($_COOKIE['mode']) && ($_COOKIE['mode'] === 'day')): ?>
    <div class="image-container">
    <img src="/photos/rer.png" alt="Description of the image" style="max-width: 500px; height: auto;">
    </div>
<?php endif; ?>
<?php require 'footer.php'; ?>
