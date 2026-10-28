<?php  
session_start();   
session_destroy();   
// Set a session flash message before destroy (quick hack)  
$_SESSION['flash'] = 'Logged out successfully! Returning to portal...';  
header("Location: ./index.php?msg=logout");   
exit;  
?>  