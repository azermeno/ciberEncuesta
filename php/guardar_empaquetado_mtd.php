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
			$returnJs = array();
			$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : 0;
			$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;
			$puesto = isset($_POST['seccion']) ? $_POST['seccion'] : 0;
			$accion = isset($_POST['accion']) ? $_POST['accion'] : 0;
			$unidadesSinEmpaquetar = '';
			//error_log("unidad: ".$unidad.' estado: '.$estado." seccion: ".$puesto);
			if($estado == 0){		 
				
				
					$sql="SELECT * FROM empaquetado as e, unidad as u where e.fk_unidad =u.pk_unidad && u.req_codigo={$unidad} && e.fk_puesto={$puesto};";
					
					$resultado = $conn->query($sql);
					
					if ($resultado->num_rows != 1){
						$sql="SELECT pk_unidad FROM unidad WHERE req_codigo={$unidad};";
						$resultado = $conn->query($sql);
						//error_log();
						if ($resultado->num_rows == 1){
							$resultados = $resultado->fetch_assoc();
							
							//error_log("linea31");
							//error_log($resultados);
							//error_log(print_r($resultados));
							 $sql1 = "INSERT INTO empaquetado(fk_unidad,fk_puesto,activo)VALUES({$resultados['pk_unidad']},{$puesto},{$accion});";
							// error_log($sql1);
							 $conn->query($sql1);
								if ($conn->affected_rows == 1){
									
									//error_log("Nuevo registro correctamente");
									
								} else {
									
									error_log("Error en Nuevo registro");
									error_log($sql);
									
								}
						} else {
							
							$returnJs['asignado'] = false;
							
						}	
					
					}
					
			} else {
				$sql="SELECT pk_unidad FROM unidad;";
				
				//Saca las unidades que no existe su relación de empaquetado y las crea
				$unidadesSinEmpaquetar = "SELECT pk_unidad FROM unidad WHERE pk_unidad not in (SELECT fk_unidad FROM empaquetado WHERE fk_puesto = {$puesto});";
				//$conn->query($unidadesSinEmpaquetar);
				$result = $conn->query($unidadesSinEmpaquetar);
			
				 if ($result->num_rows > 0){
					 
					 while($resultados = $result->fetch_assoc()){
						 $sql1 = "INSERT INTO empaquetado(fk_unidad,fk_puesto,activo) VALUES({$resultados['pk_unidad']},{$puesto},{$accion});";
						
							 $conn->query($sql1);
							
							//error_log($conn->affected_rows);
				
							if ($conn->affected_rows == 1){
								
								//error_log("Nuevo registro correctamente");
								
							} else {
								
								error_log("Error en Nuevo registro multiple");
								error_log($sql);
								
							}
					 }
					 
				 }
		    }
			
			$result = $conn->query($sql);
			$contador = 0;
			 if ($result->num_rows > 0){
				    $resultados = $result->fetch_assoc();
					
					 //Si existe el reguistro
							 $fk_unidad = $estado == 1 ? "" : "fk_unidad ={$resultados['pk_unidad']}  &&";
							 $empaquetado = $result->fetch_assoc();
							 $sql = "UPDATE empaquetado set activo={$accion} where ".$fk_unidad." fk_puesto={$puesto};";
							 //error_log($sql);
							 $conn->query($sql);
							 //error_log($conn->affected_rows);
				
							if ($conn->affected_rows > 0){
								//error_log("UPDATE correcto");
								$returnJs['asignado'] = true;
							} else {
								
								//error_log("Error en UPDATE ");
								$returnJs['asignado'] = false; 
							}
						 
						 
					 
						 
						 
					 
				
			 }
			
			$result->free();
								 
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
