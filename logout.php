<?php
    error_reporting(E_ALL);
    include("mysql.php");
    include("functions.php");
	include("header.inc");

    session_start();
    include("autologout.php");


    // User ausloggen
    doLogout();
    // $_SESSION leeren
    $_SESSION = array();
    // Session löschen
    session_destroy();
    echo "Sie wurden erfolgreich ausgeloggt.<br>\n".
    "Zur <a href=\"index.php\">Startseite</a>\n";
	
	include("footer.inc");
?>