<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'configMySQL.php';
		
		session_start();
		
		
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			
			$unidad = isset($_POST['unidad']) ? $conn->real_escape_string($_POST['unidad']) : '';
			$_SESSION['unidad'] = $unidad;
			$mesEncuesta = isset($_POST['mesEncuesta']) ? $conn->real_escape_string($_POST['mesEncuesta']) : '';
			
			$mesEncuesta = trim($mesEncuesta);
			$_SESSION['encuesta'] = $mesEncuesta;
			$info = trim($unidad);
			$returnJs = array();
			$encuestas = array();
			//WEB SERVICE
			$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');

			$resultWebService = array();
			$datos = array("unidad" => $unidad,"mesEncuesta" => $mesEncuesta);
			
			$resultWebService = $cliente->SelectClientes($datos);
			 
			//$result = $conn->query($sql);
			//if($result->num_rows == 1){
			if(!is_soap_fault($resultWebService)){
				
				$temporal= json_decode($resultWebService->SelectClientesResult);
						//error_log($temporal->txtResponsable);
						$_SESSION['txtFirma'] = $temporal->txtResponsable;
						$_SESSION['txtNombre'] = $temporal->txtNombre;
						 $returnJs['unidad'] = get_object_vars($temporal);
						// error_log(print_R($returnJs['unidad'],true));
					//obtenemos el anio y el mes y si no existe en la tabla mes lo creamos
					
					//$anioMes = date('Ym'); se remplaza por $_SESSION['encuesta']
					
					
					$sql = "SELECT * FROM mes WHERE mes='{$mesEncuesta}'";
					$result = $conn->query($sql);
					
					$pk_mes = 0;
					
					if($result->num_rows == 1){
						 $row = $result->fetch_assoc();
						
						$pk_mes=$row['pk_mes'];
						
					} else {
						
							$sql = "INSERT INTO mes(mes) VALUES ('{$mesEncuesta}');";
							
							$result = $conn->query($sql);
							if($conn->affected_rows==1){
								$pk_mes = $conn->insert_id;
							} else {
								
								error_log('Linea  de obtener_preguntas_mtd, nos se pudo crear el registro mes del clienteId ='.$unidad);
							}
					}
					if($pk_mes != 0){
						 $pk_mesEncuesta = 0;
						 $vecesContestado = 0;
						//Checamos si existe su registro mesEncuesta que le corresponde a este mes
						$sql = "SELECT * FROM unidad WHERE req_codigo={$unidad}";
								$result = $conn->query($sql);
						
								$pk_unidad = 0;
								
								if($result->num_rows == 1){
									 $row = $result->fetch_assoc();
									
									$pk_unidad=$row['pk_unidad'];
									
								} else {
									
										$sql = "INSERT INTO unidad(req_codigo) VALUES ({$unidad});";
										
										$result = $conn->query($sql);
										if($conn->affected_rows==1){
											$pk_unidad = $conn->insert_id;
										} else {
											
											error_log('Linea  de obtener_preguntas_mtd, nos se pudo crear el registro unidad del clienteId ='.$unidad);
										}
								}
						
						$sql = "SELECT pk_mesEncuesta,contestado,vecesContestado FROM mesEncuesta WHERE fk_mes={$pk_mes} && fk_unidad={$pk_unidad};";
						
						$result = $conn->query($sql);
						
						if ($result->num_rows >= 1) {
							$row = $result->fetch_assoc();
							$_SESSION['mesEncuesta']= $row['pk_mesEncuesta'];
							$pk_mesEncuesta = $row['pk_mesEncuesta'];
							$vecesContestado = $row['vecesContestado'];
						} else { //lo creamos
															
							$sql = "INSERT INTO mesEncuesta(fk_mes,fk_unidad) VALUES({$pk_mes},{$pk_unidad});";
							
							$result = $conn->query($sql);
							
							if($conn->affected_rows == 1){
								
								$pk_mesEncuesta= $conn->insert_id;
								$_SESSION['mesEncuesta']=$pk_mesEncuesta;
							} else {
								
								error_log("Linea 78 de obtener_preguntas_mtd, no se pudo crear el registro mesEncuesta del clienteId = ".$unidad);
							}
							
						}
					}
			}
			$sql = "SELECT e.activo,e.fk_puesto,p.puesto,p.puestoPonderacion FROM empaquetado as e, puesto as p WHERE e.fk_unidad={$pk_unidad} && e.fk_puesto=pk_puesto && e.activo=1 ORDER BY p.prioridad DESC;";
			
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				$contador =0;
				while($temp1 = $result->fetch_assoc()) {
				  $encuestas[] = $temp1;
				  $contador ++;
				}
				
					foreach($encuestas as $encuesta){	
						$sql = "SELECT p.* FROM pregunta as p,area as a WHERE p.fk_area=a.pk_area && a.fk_puesto={$encuesta['fk_puesto']}";
						
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							$obtener_resultados = 1;
							
							$returnJs['preguntas'][]=$encuesta;
							
							while($row = $result->fetch_assoc()) {
								$returnJs['preguntas'][]=$row;
							}
									
						}		
						
					}
				
						foreach($returnJs['preguntas'] as $temp){
							if(isset($temp['puesto'])){
								//error_log($temp['puesto']);
							} else {
								$sql = "SELECT pk_respuesta,fk_pregunta,respuesta,respuestaPonderacion FROM respuesta WHERE fk_pregunta={$temp['pk_pregunta']} ORDER BY respuestaOrden";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									// output data of each row
										   
									while($row = $result->fetch_assoc()) {
										$returnJs['respuestas'][]=$row;
									}
								}
							}
						}
				
				
			}
			
			$sql = "INSERT INTO log(accion) VALUES('La unidad {$returnJs['unidad']['txtNombre']} con el códogo {$unidad} accedio a la ciber_encuesta con el mes: {$mesEncuesta}');";
			$conn->query($sql);
				
			$result->free();
			
			$conn->close();
			
			echo json_encode($returnJs);
			
	}
?>

