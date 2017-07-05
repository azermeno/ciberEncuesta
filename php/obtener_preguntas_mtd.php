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
			$mail = isset($_POST['mail']) ? $conn->real_escape_string($_POST['mail']) : '';
			$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
			$mail = trim($mail);
			//$mail = $puesto.' '.$mail;
			$name = trim($name);
			$name = str_replace('+',' ',$name);
			$aspirante = array();
			$fecha_actual = date("Y-n-j H:i:s");
			$tiempo_restante = -1;
			$obtener_resultados =0;
			
			$sql = "SELECT a.pk_aspirante,a.estatus_contestado,a.tiempo_inicio,p.limite_minutos FROM aspirante as a, puesto as p 
			WHERE a.email='{$mail}' && a.fk_puesto={$puesto}";
			
			$result = $conn->query($sql);
			
			if ($result->num_rows < 1) {
				$conn->query("START TRANSACTION");
				$sql = "INSERT INTO aspirante(email,nombre,fk_puesto) VALUE ('{$mail}','{$name}',{$puesto})";
				
				$conn->query($sql);
				$returnJs = array();
					if($conn->affected_rows==1){
						$_SESSION['idUsuario'] = $conn->insert_id;
					} else {
						
						$obtener_resultados=2;
					}
			} else {
				            // output data of each row
							while($row = $result->fetch_assoc()) {
								$aspirante[]=$row;
							}
							
							if ($aspirante[0]['estatus_contestado']==1) {
					
							$obtener_resultados=2;
									
						} else {
							
							//obtenemos el valor de inicio con el actual para ver si se le permite nuevamente hacer el examen
							$tiempo_transcurrido = (strtotime($fecha_actual) - strtotime($aspirante[0]['tiempo_inicio']));
							if($tiempo_transcurrido < ($aspirante[0]['limite_minutos'] * 60)){
								$tiempo_restante = $aspirante[0]['limite_minutos'] - (intval($tiempo_transcurrido/60));
								//error_log($tiempo_transcurrido);
								//error_log($tiempo_restante);
								$_SESSION['idUsuario'] = $aspirante[0]['pk_aspirante'];
								
							} else {
								
								$obtener_resultados=2;
								
							}

						}
				
			}
			
		if($obtener_resultados!=2){
				$temps = array();
			
				$sql = "SELECT p.* FROM pregunta as p,area as a WHERE p.fk_area=a.pk_area && a.fk_puesto={$puesto}";
							
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$obtener_resultados = 1;
					// output data of each row
							while($row = $result->fetch_assoc()) {
								$temps[]=$row;
							
							}
							
					foreach($temps as $temp){
						$respuestas = array();
						$sql = "SELECT pk_respuesta,fk_pregunta,respuesta FROM respuesta WHERE fk_pregunta={$temp['pk_pregunta']} ORDER BY respuesta";
						
						 $result = $conn->query($sql);
				
						if ($result->num_rows > 0) {
							// output data of each row
								   $returnJs['preguntas'][]=$temp;
									while($row = $result->fetch_assoc()) {
										$returnJs['preguntas'][]=$row;
									}
								
						}
							

					
					}
					
					if($tiempo_restante>=0){
						error_log($tiempo_restante);
						$returnJs['limite']=array('limite_minutos'=>$tiempo_restante);
						
					} else {
						
						$sql = "SELECT limite_minutos FROM puesto WHERE pk_puesto={$puesto}";
								//error_log($sql);
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
						// output data of each row
								while($row = $result->fetch_assoc()) {
									$returnJs['limite']=$row;
								
								}
						}
						
					}
				}		
					
				
			
			if($obtener_resultados==1){
				$conn->query('COMMIT');
			} else {
				$conn->query('ROLLBACK');
			}
			$result->free();
			
			$conn->close();
			
			echo json_encode($returnJs);
		
		} else { //actualizó la pagina y es para que lo saque
		
				 echo json_encode(array('msg'=>'Solo se permite un intento'));
				 $result->free();
				
				$conn->close();
			}
	
	
	}
?>

