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
			$ponderaciones = array();
			$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : 0;
			$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;
			$identificador ="";
			$primaryKey = 0;
			$sql = "";
			foreach($_POST as $pk_registro => $ponderacion){
				
				 // error_log($pk_registro." ----- > ".$ponderacion);
				
				$identificador = substr($pk_registro,0,1);
				$primaryKey = substr($pk_registro,1);
				if($identificador == "A"){
					$sql = "UPDATE puesto SET puestoPonderacion = {$ponderacion} where pk_puesto = {$primaryKey};";
				} elseif($identificador == "P"){
					$sql = "UPDATE pregunta SET preguntaPonderacion = {$ponderacion} where pk_pregunta = {$primaryKey};";
				} else { // es "R"  
					$sql = "UPDATE respuesta SET respuestaPonderacion = {$ponderacion} where pk_respuesta = {$primaryKey};";
				}
				$conn->query($sql);
				 if ($conn->affected_rows > 0){
						error_log("Operacion UPDATE correcta de ponderacion tipo = {$identificador}, Key={$primaryKey} y ponderacion = {$ponderacion}");
				 } else {
						error_log("Error en UPDATE de ponderacion tipo = {$identificador} o no se modifico");
						}
				$returnJs['ponderacion'][$pk_registro] = $ponderacion;
			};			 
			
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
