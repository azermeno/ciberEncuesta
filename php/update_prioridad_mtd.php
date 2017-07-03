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
			$contador = 0;
			$registroModificado = 0;
			$returnJs['asignado'] = false;
			foreach($_POST as $pk_puesto => $prioridad){
				 if($contador > 0){
					 $sql = "UPDATE puesto set prioridad={$prioridad} where pk_puesto={$pk_puesto};";
					 
					 $conn->query($sql);
							if ($conn->affected_rows > 0){
								error_log("UPDATE de prioridad correcto");
								$registroModificado ++;
								$returnJs['asignado'] = true;
							} else {
								
								error_log("Error en UPDATE de prioridad o no se modifico");
								
							}
				 }
				 $contador++;
			}
			
			$sql="SELECT pk_puesto, prioridad,puesto FROM puesto ORDER BY prioridad DESC";
			$result = $conn->query($sql);
			 if ($result->num_rows > 1){
				while($restultados = $result->fetch_assoc()){
					$returnJs['secciones'][]=$restultados;
				}
			 }
			
			$result->free();
									 
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
