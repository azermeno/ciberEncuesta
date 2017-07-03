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
			$correcto = 1;
						 
			$sql="SELECT e.* FROM empaquetado as e,unidad as u WHERE fk_unidad=u.pk_unidad && u.req_codigo={$unidad}";
			$result = $conn->query($sql);
			 if ($result->num_rows > 1){
				while($restultados = $result->fetch_assoc()){
					$returnJs['empaquetado'][]=$restultados;
				}
			 }
			
			$result->free();
								 
			 echo json_encode($returnJs);
			$conn->close();
 	}
?>
