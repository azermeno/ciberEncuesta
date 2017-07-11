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
			
			$correcto = 1;
										 			
		   //WEB SERVICE PARA OBTENER LOS DATOS DE LA BASE PRICIPAL DE SIBER-ENCUESTA (SQL)
			$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');
			
			$resultWebService = array();
			
			// al mandar false nos regresa todas las unidades activas
			$resultWebService = $cliente->PersonasEncuesta(array("encuestado" => false));
			
			if(is_soap_fault($resultWebService)){
			 $correcto = 0;
			}
			
			$returnJs['unidadesWs'] = json_decode($resultWebService->PersonasEncuestaResult);
		  
			 /*
			 *Se va hacer un método para actualizar las nuevas unidades de MSSQl con la parte de unidades de MySQL
			 *
			 */
			 if($correcto){
				
				 foreach($returnJs['unidadesWs'] as $row){
					 
					 $sql="SELECT * FROM unidad WHERE req_codigo=".$row -> req_codigo;
					 $result = $conn->query($sql);
					 if ($result->num_rows !=1){
					 $sql="INSERT INTO unidad(req_codigo) VALUES({$row -> req_codigo})" ;
						
						$conn->query($sql);
						 
					 } 
				}
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
