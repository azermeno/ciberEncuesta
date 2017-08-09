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
			$returnJs['mesRegistrado'] = false;
			$returnJs['anio'] = date('Ym');
			$correcto = 1;
					$sql = "SELECT * FROM mes WHERE pk_mes > 0 ORDER BY mes DESC;";
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()){
							$returnJs['mes'][]=$row;
						}
						$result->free();
					} 
					 			
			           //WEB SERVICE PARA OBTENER LOS DATOS DE LA BASE PRICIPAL DE SIBER-ENCUESTA (SQL)
						$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');
						
						$resultWebService = array();
						$datos = array();
						//Al mandar true nos regresa sólo las unidades encuestadas activas
						
						$resultWebService = $cliente->PersonasEncuesta(array("encuestado" => true));
						
						if(is_soap_fault($resultWebService)){
						 $correcto = 0;
						}
						
						$returnJs['unidadesWs'] = json_decode($resultWebService->PersonasEncuestaResult);
					    
						 //OBTENER LAS UNIDADES DEL ULTIMO MES
						 
						$sql = "SELECT m.*,u.req_codigo,a.contestadoManual FROM mesEncuesta as m, aspirante as a, unidad as u WHERE m.fk_mes = {$returnJs['mes'][0]['pk_mes']} ".
						       "AND a.fk_mesEncuesta=m.pk_mesEncuesta AND u.pk_unidad=m.fk_unidad GROUP BY m.pk_mesEncuesta;";
						  
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()){
								$returnJs['unidadesMySQL'][]=$row;
							}
							
							if($returnJs['mes'][0]['mes'] == $returnJs['anio']){
								
								$returnJs['mesRegistrado'] = true;
							}
						}
												
						//OBTENER LAS UNIDADES CON COMENTERIO DEL ULTIMO MES
						$sql = "SELECT m.*,u.req_codigo FROM mesEncuesta as m, aspirante as a, unidad as u, aspirante as asp, contestado as c".
						" WHERE m.fk_mes = {$returnJs['mes'][0]['pk_mes']} AND a.fk_mesEncuesta=m.pk_mesEncuesta AND u.pk_unidad=m.fk_unidad ".
						"AND asp.fk_mesEncuesta=m.pk_mesEncuesta AND c.fk_aspirante=asp.pk_aspirante AND c.comentario is not null AND".
						" comentario !=''GROUP BY m.pk_mesEncuesta;";
						  
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()){
								$returnJs['unidadesComentarios'][]=$row;
							}
						}
						$result->free();
											 
						 echo json_encode($returnJs);
					    $conn->close();
	         
	}
?>
