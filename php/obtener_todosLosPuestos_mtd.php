<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'config.php';
        
        
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
				
			$returnJs['puesto'] = array();
			
			$sql = "SELECT Pu.interno,Pu.pk_puesto,Pu.activo,
			Pu.limite_minutos,Pu.puesto,Pu.conPromedio, (select count(Pre.pk_pregunta) from
			area as a, pregunta as Pre WHERE a.fk_puesto=pk_puesto && Pre.fk_area=a.pk_area ) AS
			Preguntas FROM puesto as Pu WHERE Pu.pk_puesto>0 ";
			
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$returnJs['puesto'][]=$row;
				}
						
			}
			$sql = "SELECT * FROM seccion WHERE pk_seccion > 0 ORDER BY seccion";
			
			$result1 = $conn->query($sql);
			
				$returnJs['seccion'] = array();
			if ($result1->num_rows > 0) {
				// output data of each row
				while($row = $result1->fetch_assoc()) {
					$returnJs['seccion'][]=$row;
				}
			}
			
		$result->free();
		$result1->free();
		
		$conn->close();
        echo json_encode($returnJs);
	}
?>
