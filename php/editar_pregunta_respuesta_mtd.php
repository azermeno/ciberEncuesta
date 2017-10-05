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
			$editado = isset($_POST['editado']) ? $conn->real_escape_string($_POST['editado']) : 0;
			$idPreguntaRespesta = isset($_POST['idPreguntaRespesta']) ? $conn->real_escape_string($_POST['idPreguntaRespesta']) : 0;
			
			$id = substr($idPreguntaRespesta,2);
			
			if( substr($idPreguntaRespesta,0,1) === "P"){
				
				$sql = "UPDATE pregunta set pregunta='{$editado}' where pk_pregunta={$id};";
				
			} else {
				
				$sql = "UPDATE respuesta set respuesta='{$editado}' where pk_respuesta={$id};";
				
			}
						
					 $conn->query($sql);
							if ($conn->affected_rows > 0){
								
								$returnJs['asignado'] = true;
								
							} else {
								
								$returnJs['asignado'] = false;
								
							}
											 
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
