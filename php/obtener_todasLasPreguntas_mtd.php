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
			
				
			$puesto = isset($_POST['puesto']) ? $_POST['puesto']+0 : 0;
			
			$returnJs = array();
						
				
				$sql = "SELECT a.*,p.puestoPonderacion,p.prioridad FROM area as a, puesto as p WHERE a.fk_puesto=p.pk_puesto and p.pk_puesto={$puesto}";
							
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
						
						// output data of each row
								while($row1 = $result->fetch_assoc()) {
									 $returnJs['preguntas'][]=$row1;
								
							
								$sql = "SELECT * FROM pregunta WHERE fk_area={$row1['pk_area']}";
								
								$result1 = $conn->query($sql);
								if ($result1->num_rows > 0) {
										
										// output data of each row
									  while($temp = $result1->fetch_assoc()) {
												
											$respuestas = array();
											$sql = "SELECT pk_respuesta,fk_pregunta,respuesta,correcta,respuestaPonderacion FROM respuesta WHERE fk_pregunta={$temp['pk_pregunta']} ORDER BY respuestaOrden";
											
											 $result2 = $conn->query($sql);
									
											if ($result2->num_rows > 0) {
												// output data of each row
													  
													   $returnJs['preguntas'][]=$temp;
														while($row = $result2->fetch_assoc()) {
															$returnJs['preguntas'][]=$row;
														}
											}
											
										}
									}
								}
				}
					
			
			$result->free();
			$result1->free();
			$result2->free();
			
			$conn->close();
			
			echo json_encode($returnJs);
		
	}
?>

