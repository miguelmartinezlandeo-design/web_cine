<?php
session_start();
session_unset();
session_destroy();

header("Location:/ov1/index.php");
exit;
