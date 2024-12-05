<?php
// Toggle mode
if(isset($_COOKIE['mode']) && ($_COOKIE['mode'] === 'night')) {
    setcookie("cookie_name", "", time() - 3600, "/"); // Replace "cookie_name" with the name of the cookie you want to delete

} else {
    
    setcookie("cookie_name", "", time() - 3600, "/"); // Replace "cookie_name" with the name of the cookie you want to delete

}

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
