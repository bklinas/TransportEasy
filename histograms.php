<?php require 'header.php'; ?>


<main class="stats">
    <section class="statistics-section">
        <div id="histograms-title">
        <h2>Statistiques</h2>
        </div>
        <div id="histogramChart">    
        </div>
        
        <?php
        // Read the CSV file to generate the histogram
        $csvFile = 'visited_stations.csv';
        $arretsFile = 'zones-d-arrets.csv';

        if (!file_exists($csvFile) || !file_exists($arretsFile)) {
            echo "CSV files do not exist.";
            exit();
        }

        $stationNames = [];
        if (($handle = fopen($arretsFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $stationNames[$data[0]] = $data[4];
            }
            fclose($handle);
        }


        $histogram = array();

        // Open the CSV file for reading
        if (($handle = fopen($csvFile, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $station_id = $data[1]; 


                if (isset($histogram[$station_id])) {
                    $histogram[$station_id]++;
                } else {
                    $histogram[$station_id] = 1;
                }
            }
            fclose($handle);
        }


        ksort($histogram);

        // Convert the histogram data to JSON for JavaScript usage
        $histogramData = [['Station ID', 'Visit Count', ['role' => 'tooltip']]];
        foreach ($histogram as $station_id => $count) {
            $station_name = isset($stationNames[$station_id]) ? $stationNames[$station_id] : 'Unknown';
            $tooltip = $station_name . ': ' . $count . ' visits';
            $histogramData[] = [$station_name, $count, $tooltip];
        }
        
        ?>

<script src="https://www.gstatic.com/charts/loader.js"></script>

        <script>
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($histogramData); ?>);

        var options = {
            title: 'Most Visited Stations',
            legend: { position: 'none' },
            hAxis: { title: 'Station ID' },
            vAxis: { title: 'Visit Count' },
            chartArea: { width: '40%', height: '60%', backgroundColor: '#8DC0C0' }, 
            bar: { groupWidth: '40%' }, 

        };

        var chart = new google.visualization.BarChart(document.getElementById('histogramChart'));
        chart.draw(data, options);
    }
</script>

    </section>
</main>


<?php require 'footer.php'; ?>