<div style="text-align: center;">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define your API key
$api_key = 'F6AS50P2MZ7fVwJXuYjLqukmOYXG14cg7WapLIe7';

// URL of the APOD API with the API key included
$url = 'https://api.nasa.gov/planetary/apod?api_key=' . $api_key;

// Fetch data from the API
$response = file_get_contents($url);

// Decode JSON response
$data = json_decode($response, true);

// Check if data was successfully retrieved
if ($data && isset($data['title'], $data['explanation'], $data['media_type'], $data['url'])) {
    $title = $data['title'];
    $explanation = $data['explanation'];
    $media_type = $data['media_type'];
    $media_url = $data['url'];

    // Display the title and explanation
    echo "<h2>$title</h2>";
    echo "<p>$explanation</p>";

    // Display the media (image or video)
    if ($media_type === 'image') {
        echo "<img src='$media_url' alt='$title'>";
    } elseif ($media_type === 'video') {
        // Use the video element with a fallback image
        echo "<video controls width='100%' height='auto'>";
        echo "<source src='$media_url' type='video/mp4'>";
        echo "Your browser does not support the video tag.";
        echo "</video>";
    } else {
        echo "Unsupported media type.";
    }
} else {
    echo "Failed to fetch data from APOD API.";
}
?>
</div>