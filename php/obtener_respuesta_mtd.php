<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'configMySQL.php';
		//require_once 'respuesta_valor.php';
		
		session_start();
				
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			$pk_mes = isset($_POST['mes']) ? $_POST['mes'] : 0;
			$pk_unidad = isset($_POST['codigo']) ? $_POST['codigo'] : 0;
			$mesEncuesta = isset($_POST['mesEncuesta']) ? $conn->real_escape_string($_POST['mesEncuesta']) : '';
			$returnJs = array();
			
				
			if($mesEncuesta != ''){				
				
				//WEB SERVICE
				$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');

				$resultWebService = array();
				$datos = array("unidad" => $pk_unidad,"mesEncuesta" => $mesEncuesta);
				
				$resultWebService = $cliente->SelectClientes($datos);
				 
				//$result = $conn->query($sql);
				//if($result->num_rows == 1){
				if(!is_soap_fault($resultWebService)){
					
					$temporal= json_decode($resultWebService->SelectClientesResult);
										
					$returnJs['unidad'] = get_object_vars($temporal);
					
				}
								
				$sql ="SELECT pk_mes FROM mes WHERE mes='{$mesEncuesta}'";
				
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					
					$row = $result->fetch_array();
					
					$pk_mes = $row[0];
				}
				
			}
					
					$sql = "SELECT m.fk_mes,m.fk_unidad,pk_mesEncuesta, a.calificacion, a.pk_aspirante,a.fk_puesto,a.tiempo_inicio,c.*,r.*,p.*,area.*,puesto.*".
					" FROM mesEncuesta as m, aspirante as a, contestado as c, respuesta as r, pregunta as p, area, puesto WHERE".
					" m.fk_unidad={$pk_unidad} AND m.fk_mes={$pk_mes} AND a.fk_mesEncuesta=m.pk_mesEncuesta AND c.fk_aspirante=a.pk_aspirante".
					" AND c.fk_respuesta=r.pk_respuesta AND r.fk_pregunta=p.pk_pregunta AND p.fk_area=area.pk_area AND ".
					"area.fk_puesto=puesto.pk_puesto ORDER BY a.pk_aspirante, p.pk_pregunta";
													 
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()){
							$returnJs['contestado'][]=$row;
						}
						$result->free();
					}
						 echo json_encode($returnJs);
					  $conn->close();
	        
	}
?>