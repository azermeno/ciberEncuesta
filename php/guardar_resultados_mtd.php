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
			
			$returnJs = array();
			
			if(count($_POST)>0){
				foreach($_POST as $resultado){
					$sql = "INSERT INTO contestado(fk_respuesta,fk_aspirante) VALUE ({$resultado},{$_SESSION['idUsuario']})";
										
					$conn->query($sql);
					
						if($conn->affected_rows != 1){
						$correcto = false;
						}
					}
			}
						
			//}
			$sql = "UPDATE aspirante SET estatus_contestado=1 WHERE pk_aspirante={$_SESSION['idUsuario']}";
			
			if($correcto==true){
				$conn->query($sql);
				if($conn->affected_rows != 1){
					$correcto = false;
				}
			}
		
		
		
		
		if ($correcto==true) {
				$conn->query("COMMIT");
				echo json_encode(array('msg' => 'Respuesta enviadas correctamente','status' => true));
						$conn->close();
			} else {
				$conn->query("ROLLBACK");
				echo json_encode(array('msg' => 'Por el momento no esta activa la funcionalidad, reporte el inconveniente a medicoNet','status' => false));
				$conn->close();

			}
       
	}
?>

