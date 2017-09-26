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
			$returnJs['unidades'] = array();
			$returnJs['unidadesComentarios'] = array();
			$mes = isset($_POST['mes']) ? $_POST['mes'] : 0;   
						 
			//OBTENER LAS UNIDADES DEL ULTIMO MES
						
			$sql = "SELECT m.*,u.req_codigo,a.contestadoManual FROM mesEncuesta as m, aspirante as a, unidad as u WHERE m.fk_mes ={$mes} AND a.fk_mesEncuesta=m.pk_mesEncuesta AND u.pk_unidad=m.fk_unidad GROUP BY m.pk_mesEncuesta;";
						  
						
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()){
					$returnJs['unidades'][]=$row;
				}
				$result->free();
			}
			
			//OBTENER LAS UNIDADES CON COMENTERIO DEL ULTIMO MES
			$sql = "SELECT m.*,u.req_codigo FROM mesEncuesta as m, aspirante as a, unidad as u, aspirante as asp, contestado as c WHERE m.fk_mes = {$mes} AND a.fk_mesEncuesta=m.pk_mesEncuesta AND u.pk_unidad=m.fk_unidad AND asp.fk_mesEncuesta=m.pk_mesEncuesta AND c.fk_aspirante=asp.pk_aspirante AND c.comentario is not null AND comentario !=''GROUP BY m.pk_mesEncuesta;";
			
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()){
					$returnJs['unidadesComentarios'][]=$row;
				}
				$result->free();
			}
			 
			 echo json_encode($returnJs);
			$conn->close();
	         
	}
?>
