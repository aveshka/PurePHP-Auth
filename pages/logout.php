<?php
require __DIR__ . '/../utils/config.php';

session_destroy();
header("Location: ../index.php");
exit();
?>