<?php require 'header.php'; ?>

<main class="container">
    <section class="search-section">
        
        <div id="border">
        <h2 class="search-heading">Résultats de la recherche</h2>
        <?php
        // Check if the station_id is provided in the URL
        if(isset($_GET['station_id'])) {
            $station_id = $_GET['station_id'];

            setcookie('last_visited_station', $station_id . '|' . time(), time() + (86400 * 30), "/"); // Cookie lasts for 30 days
        } else {
            // If station_id is not provided, check if the cookie exists
            if(isset($_COOKIE['last_visited_station'])) {
                $cookie_data = explode('|', $_COOKIE['last_visited_station']);
                $station_id = $cookie_data[0];
                $last_visited_time = date('Y-m-d H:i:s', $cookie_data[1]);
                echo "<p>Last visited station: $station_id (Last visited: $last_visited_time)</p>";
            } else {
                echo "Station ID not provided.";
                exit();
            }
        }
      



        
        
        // Determine the detail level
        $detail_level = isset($_GET['detail_level']) ? $_GET['detail_level'] : 'general';

        // Construct the appropriate API endpoint URL based on the detail level
        if ($detail_level === 'detailed') {
            // Use the detailed endpoint for traffic information
            $url = 'https://prim.iledefrance-mobilites.fr/marketplace/disruptions_bulk/disruptions/v2';
        } else {
            // Use the general endpoint for traffic information
            $url = 'https://prim.iledefrance-mobilites.fr/marketplace/stop-monitoring?MonitoringRef=STIF:StopPoint:Q:' . $station_id . ':';
        }

        $headers = array(
            'Accept: application/json',
            'apikey: NqvFZMpMX714fPx2OwptF5yugbzpou6o'
        );

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set the path to the SSL certificate
        curl_setopt($ch, CURLOPT_CAINFO, '/home/inas/cert/prim.iledefrance-mobilites.fr.crt');

        $response = curl_exec($ch);

        // Check for errors and display results
        if ($response === false) {
            echo 'Error: ' . curl_error($ch) . curl_errno($ch);
        } else {
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
                $response_data = json_decode($response, true);

                // Check if data is present and process it
                if ($detail_level === 'detailed' && isset($response_data['disruptions'])) {
                    $disruptions = $response_data['disruptions'];

                   // Loop through each disruption and filter out only those related to trains
foreach ($disruptions as $disruption) {
    // Check if the disruption title or message contains keywords related to trains
    if (strpos($disruption['title'], 'train') !== false || strpos($disruption['message'], 'train') !== false) {
        // If the disruption is related to trains, display it
        $train_disruptions[] = $disruption;
    }
}

// Display only train disruptions
foreach ($train_disruptions as $disruption) {
    // Extract relevant information such as ID, title, message, etc.
    $disruption_id = $disruption['id'];
    $title = $disruption['title'];
    $message = $disruption['message'];
    
    // Display the information as needed
    echo "<div>";
    echo "<h3>Disruption ID: $disruption_id</h3>";
    echo "<p>Title: $title</p>";
    echo "<div>"; // Change <p> to <div> for message section
    echo "<p>Message: $message</p>"; // Change to wrap message in a <p> tag if necessary
    echo "</div>";
    echo "</div>";
}



                } elseif ($detail_level !== 'detailed' && isset($response_data['Siri']['ServiceDelivery']['StopMonitoringDelivery'][0]['MonitoredStopVisit'])) {
                    // Process general traffic information as before
                    $monitored_visits = $response_data['Siri']['ServiceDelivery']['StopMonitoringDelivery'][0]['MonitoredStopVisit'];

                    $current_time = time(); // Get the current Unix timestamp

                    foreach ($monitored_visits as $visit) {
                        $destination = $visit['MonitoredVehicleJourney']['DestinationName'][0]['value'];

                        // Check if 'ExpectedArrivalTime' key exists
                        if (isset($visit['MonitoredVehicleJourney']['MonitoredCall']['ExpectedArrivalTime'])) {
                            $expected_arrival = strtotime($visit['MonitoredVehicleJourney']['MonitoredCall']['ExpectedArrivalTime']);
                        } else {
                            $expected_arrival = 0; // Provide a default value if 'ExpectedArrivalTime' is not present
                        }

                        // Check if 'DepartureStatus' key exists
                        if (isset($visit['MonitoredVehicleJourney']['MonitoredCall']['DepartureStatus'])) {
                            $departure_status = $visit['MonitoredVehicleJourney']['MonitoredCall']['DepartureStatus'];
                        } else {
                            $departure_status = ""; // Provide a default value if 'DepartureStatus' is not present
                        }

                        // Check if the user has specified date and time
                        if (isset($_GET['date']) && isset($_GET['time'])) {
                            // Combine date and time provided by the user
                            $user_datetime = strtotime($_GET['date'] . ' ' . $_GET['time']);
                            
                            // Display only trains that are scheduled after the user-specified date and time
                            if ($expected_arrival > $user_datetime) {
                                echo "<div>";
                                echo "<h3>Destination: $destination</h3>";
                                echo "<p>Expected Arrival: " . date('Y-m-d H:i:s', $expected_arrival) . "</p>";
                                if ($departure_status !== "") {
                                    echo "<p>Departure Status: $departure_status</p>";
                                }
                                echo "</div>";
                            }
                        } else {
                            // If the user hasn't specified date and time, display only trains that are about to arrive
                            if ($expected_arrival > $current_time) {
                                echo "<div>";
                                echo "<h3>Destination: $destination</h3>";
                                echo "<p>Expected Arrival: " . date('Y-m-d H:i:s', $expected_arrival) . "</p>";
                                if ($departure_status !== "") {
                                    echo "<p>Departure Status: $departure_status</p>";
                                }
                                echo "</div>";
                            }
                        }
                    } // Log the visited station to CSV file
                    $logfile = fopen("visited_stations.csv", "a");
                    $current_time = date('Y-m-d H:i:s');
                    fputcsv($logfile, array($current_time, $station_id));
                    fclose($logfile);

                } else {
                    echo "<p>Aucun résultat trouvé pour cette recherche.</p>";
                }
            } else {
                echo "No response from API";
            }
        }

        curl_close($ch);
        ?>
</div>
    </section>
    
    
</main>

<?php require 'footer.php'; ?>