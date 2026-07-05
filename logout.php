<?php
	session_start(); // Initialize the session.
	session_unset(); // Unset all of the session variables.
	session_destroy(); // Finally, destroy the session.
	header("location:index.php");
?>