<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'config.php';
		
		session_start();
		
		
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			$info = isset($_POST['info']) ? $conn->real_escape_string($_POST['info']) : '';
			$info = trim($info);
			$info = base64_decode($info);
			$sql = "SELECT p.* FROM pregunta as p,area as a WHERE p.fk_area=a.pk_area && a.fk_puesto={$info}";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$obtener_resultados = 1;
				// output data of each row
					while($row = $result->fetch_assoc()) {
						$returnJs['preguntas'][]=$row;
					}
						
				foreach($returnJs['preguntas'] as $temp){
					$sql = "SELECT pk_respuesta,fk_pregunta,respuesta FROM respuesta WHERE fk_pregunta={$temp['pk_pregunta']} ORDER BY respuestaOrden";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						// output data of each row
							   
						while($row = $result->fetch_assoc()) {
							$returnJs['respuestas'][]=$row;
						}
					}
				}
				
			}		
				
			
			$result->free();
			
			$conn->close();
			
			echo json_encode($returnJs);
			
	}
?>

