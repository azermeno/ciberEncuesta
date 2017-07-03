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
			
				
			$id = isset($_POST['id']) ? $_POST['id']+0 : 0;
			$estado = isset($_POST['estado']) ? $conn->real_escape_string($_POST['estado']) : '';
		
		if($estado !=''){
				
					 if($estado=='true'){
						 
						 $estado = 1;
						 $mensaje = 'Se activo el puesto correctamente';
					 } else if($estado=='false'){
						 
						 $estado = 0;
						 $mensaje = 'Se desactivo el puesto correctamente';
						 
					 }
					
					$sql = "UPDATE puesto SET activo={$estado} WHERE pk_puesto={$id}";
					
					
					$conn->query($sql);
					
					if ($conn->affected_rows > 0) {
						
					
					   echo json_encode(array('msg'=>$mensaje,'status'=>true));
				
					} else { //actualizó la pagina y es para que lo saque
							
							echo json_encode(array('msg'=>'No se actualizo el estado','status'=>false));
						
					}
			} else {
				
				echo json_encode(array('msg'=>'Error el estado es nulo','status'=>false));
			}
	
			$conn->close();
	
	}
?>

