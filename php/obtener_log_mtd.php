<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'config.php';
		
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			
			$returnJs = array();
			$mesActual = date("Y-m");
			
			$sql = "SELECT * FROM log where substring(fecha,1,7) = '{$mesActual}' ORDER BY fecha DESC";
			//error_log($sql);
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$contador =0;
				while($temp = $result->fetch_assoc()) {
				  $returnJs[] = $temp;
				  $contador ++;
				}
				
				
			}
			
			$result->free();
			
			$conn->close();
			
			echo json_encode($returnJs);
			
	}
?>

