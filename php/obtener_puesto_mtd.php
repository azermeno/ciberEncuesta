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
			//$sql = "SELECT * FROM puesto WHERE pk_puesto > 0";
			$sql = "SELECT Pu.pk_puesto,Pu.limite_minutos,
			Pu.puesto,(select count(Pre.pk_pregunta) from area as a, 
			pregunta as Pre WHERE a.fk_puesto=pk_puesto && Pre.fk_area=a.pk_area ) AS Preguntas 
			FROM puesto as Pu WHERE Pu.pk_puesto>0 && Pu.activo=1 && interno=0 && conPromedio=1";
			
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
						while($row = $result->fetch_assoc()) {
							$returnJs[]=$row;
						}
						
			}
						

		$result->free();
		
		$conn->close();
        echo json_encode($returnJs);
	}
?>

