<?php
$lang = 'fr';

if (basename($_SERVER['PHP_SELF']) === 'tech.php') {
    
    $lang = 'en';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        // Check if mode cookie exists
        if(isset($_COOKIE['mode']) && ($_COOKIE['mode'] === 'night')) {
            echo '<link rel="stylesheet" href="night.css">';
            
        } else {
            echo '<link rel="stylesheet" href="day.css">';
        }
    ?>
    <link rel="shortcut icon" href="/photos/trainn.png" type="image/png">
    <title>TransportEasy</title>
    <style>
   
    ul {
        list-style-type: none; /* Remove default list bullets */
        padding: 0;
    }

    li {
    list-style-type: none; /* Remove default list bullets */
    padding: 8px; /* Add padding to each list item */
    margin-bottom: 5px; /* Add some spacing between list items */
    background-color: #f5f5f5; /* Add a background color */
    color: #333;
    border: 1px solid #ccc; /* Add a light grey border */
    border-radius: 5px; /* Add rounded corners */
}

</style>
</head>
<body>
    <header>
        <h1>Recherche de Trajets</h1>
        <a href="histograms.php" id="statistics-link"><img src="/photos/statistics.png" alt="Statistics" style="width: 50px; height: 50px;"></a>
        <a href="index.php" id="home-link"><img src="/photos/maison.png" alt="HomePage" style="width: 50px; height: 50px;"></a>
        <div class="flipswitch">
           
            <input type="checkbox" name="flipswitch" class="flipswitch-cb" id="fs" <?php if(isset($_COOKIE['mode']) && ($_COOKIE['mode'] === 'night')) echo 'checked'; ?>>
            <label class="flipswitch-label" for="fs">
            <span class="flipswitch-inner"></span>
            <span class="flipswitch-switch"></span>
            </label>
            
            <script>
                document.getElementById('fs').addEventListener('change', function() {
                    var mode = this.checked ? 'night' : 'day';
                    document.cookie = 'mode=' + mode + '; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
                    location.reload(); 
                });
            </script>
        </div>
    </header>
    

