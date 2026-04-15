<?php
session_start();
session_destroy();
echo 'Cache cleared. <a href="index.php">Go back</a>';
