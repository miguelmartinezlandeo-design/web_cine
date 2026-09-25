<?php
					require_once __DIR__ . '/db_credentials.php';
					//creamos conneccion
					$conn = mysqli_connect($servername, $username, $password, $database);
					define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ov1/');
					define('BASE_URL', '/ov1/');
					mysqli_set_charset($conn, "utf8mb4");
			//mysqli_close($conn);

