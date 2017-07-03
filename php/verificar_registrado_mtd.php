<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
			require_once 'config.php';
		
			$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 
				$conn->set_charset("utf8");
					
				$puesto = isset($_POST['puesto']) ? $_POST['puesto']+0 : 0;
				$mail = isset($_POST['mail']) ? $conn->real_escape_string($_POST['mail']) : '';
				$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
				$aspirante = array();
				//$puesto_tiempo = array();
				$fecha_actual = date("Y-n-j H:i:s");
				
				
				$sql = "SELECT a.estatus_contestado,a.tiempo_inicio,p.limite_minutos FROM aspirante as a, puesto as p 
				WHERE a.email='{$mail}' && a.fk_puesto={$puesto}";
				//error_log($sql);
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// output data of each row
							while($row = $result->fetch_assoc()) {
								$aspirante[]=$row;
							}
							
							if ($aspirante[0]['estatus_contestado']==1) {
					
							echo json_encode(array('msg' => 'Solo se permite un intento..','permite' => false));
									
						} else {
							
							//obtenemos el valor de inicio con el actual para ver si se le permite nuevamente hacer el examen
							$tiempo_transcurrido = (strtotime($fecha_actual) - strtotime($aspirante[0]['tiempo_inicio']));
							if($tiempo_transcurrido < ($aspirante[0]['limite_minutos'] * 60)){
								echo json_encode(array('msg' => 'inicia','permite' => true));
								
							} else {
								
								echo json_encode(array('msg' => 'Solo se permite un intento','permite' => false));
								
								
							}

						}
							
				} else {
					
					echo json_encode(array('msg' => 'inicia','permite' => true));
					
				}
				
				
			$result->free();
			
			$conn->close();
		}
	
?>

