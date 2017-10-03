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
			$editado = isset($_POST['editado']) ? $_POST['editado'] : 0;
			$idPreguntaRespesta = isset($_POST['idPreguntaRespesta']) ? $_POST['idPreguntaRespesta'] : 0;
			
			 $sql = "UPDATE puesto set prioridad={$prioridad} where pk_puesto={$pk_puesto};";
					 
					 $conn->query($sql);
							if ($conn->affected_rows > 0){
								error_log("UPDATE de prioridad correcto");
								$registroModificado ++;
								$returnJs['asignado'] = true;
							} else {
								
								error_log("Error en UPDATE de prioridad o no se modifico");
								
							}
											 
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
