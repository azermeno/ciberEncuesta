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
			$conn->query("START TRANSACTION");	
			$correcto = true;
			$fecha = date('Y-m-d H:i:s');
			$fk_seccion = 0;
			
			$id_puesto = isset($_POST['puesto']) ? $conn->real_escape_string($_POST['puesto']) : '';
			$id_puesto = trim($id_puesto);
			$id_puesto = base64_decode($id_puesto);
			
			$identificador = isset($_POST['indentificador']) ? $conn->real_escape_string($_POST['indentificador']) : '';
			$identificador = trim($identificador);
			$identificador = base64_decode($identificador);
			
			$sql = "SELECT * FROM seccion WHERE seccion='{$identificador}'";
			
			$result1 = $conn->query($sql);
			if ($result1->num_rows > 0) {
					// output data of each row
				while($row = $result1->fetch_assoc()){
					$fk_seccion = $row['pk_seccion'];
				}
			} else {
				$sql = "INSERT INTO seccion(seccion) VALUES('{$identificador}')";
				
				$conn->query($sql);
				
				if($conn->affected_rows != 1){
					$correcto = false;
					
				} else {
					$fk_seccion = $conn->insert_id;
				}
			}
			
			if($fk_seccion != 0){
				
				$sql = "INSERT INTO aspirante(email,nombre,fk_puesto,estatus_contestado,tiempo_inicio,fk_seccion)VALUES('','',{$id_puesto},1,'{$fecha}',{$fk_seccion})";
						
				$conn->query($sql);
				if($conn->affected_rows != 1){
					$correcto = false;
										
				} else {
					$pk_aspirante = $conn->insert_id;
					
				}
					
					$returnJs = array();
					$primerDato = 0;
					$pk_respuesta_anterior = 0;
				foreach($_POST as $pregunta => $resultado){
					
					if($primerDato < 2){
						$primerDato ++;
					} else {
						$respuestaAbierta = explode('/',$pregunta);
						if(isset($respuestaAbierta[1])){
													
							$sql = "UPDATE contestado SET comentario='{$resultado}' WHERE fk_respuesta={$pk_respuesta_anterior} && fk_aspirante={$pk_aspirante} ";
							
							$conn->query($sql);
							
							if($conn->affected_rows != 1){
								$correcto = false;
								
							} 
						} else {
							
							$sql = "INSERT INTO contestado(fk_respuesta,fk_aspirante) VALUE ({$resultado},{$pk_aspirante})";
							$conn->query($sql);
								
								
							if($conn->affected_rows == 1){
								$pk_respuesta_anterior = $resultado;
								
							} else {
								$correcto = false;
								
							}
						}
					}
				}
			}	
					
		   if ($correcto==true) {
			    $result1->free();
				$conn->query("COMMIT");
				echo json_encode(array('msg' => 'Respuesta enviadas correctamente','status' => true));
				$conn->close();
			} else {
			    $result1->free();
				$conn->query("ROLLBACK");
				echo json_encode(array('msg' => 'Por el momento no esta activa la funcionalidad, reporte el inconveniente a medicoNet','status' => false));
				$conn->close();
			}
	}
?>

