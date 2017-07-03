<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
		require_once 'config.php';
        
        
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$conn->set_charset("utf8");
			$nombre = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
			$examen = isset($_POST['filtro']) ? $conn->real_escape_string($_POST['filtro']) : '';
			$condicion = '';
			if($nombre != ''){
				
				
				$condicion = $examen == 0 ? ' && a.Nombre LIKE "%'.$nombre.'%" ' : ' && p.puesto LIKE "%'.$nombre.'%" ';
			} 
				
			$returnJs = array();
			$aspirantes = array();
			$temporal = array();
			
			$sql = "SELECT a.pk_aspirante,a.Nombre,a.email,a.tiempo_inicio,p.puesto,p.pk_puesto ".
			"FROM aspirante as a,puesto as p ".
			"WHERE a.fk_puesto=p.pk_puesto ".$condicion." && (Nombre <> '' || email <> '') ORDER BY a.pk_aspirante DESC";
			
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
					while($row = $result->fetch_assoc()) {
						$aspirantes[]=$row;
					}
						
					foreach($aspirantes as $aspirante){
						$temporal = array();
						$sql = "SELECT a.area,a.pk_area ,COUNT(p.pk_pregunta) as total FROM area as a,pregunta as p WHERE  a.fk_puesto={$aspirante['pk_puesto']} && p.fk_area=a.pk_area GROUP by a.area; ";
						$result = $conn->query($sql);
						
						
						if ($result->num_rows > 0) {
							// output data of each row
									while($row = $result->fetch_assoc()) {
										
										$sql = "SELECT count(r.pk_respuesta)  as correctas FROM contestado as c, respuesta as r, pregunta as p WHERE p.fk_area={$row['pk_area']} && r.fk_pregunta=p.pk_pregunta && r.correcta=1 && c.fk_respuesta=r.pk_respuesta && c.fk_aspirante={$aspirante['pk_aspirante']}";
										
										$result1 = $conn->query($sql);
										if ($result1->num_rows > 0) {
										// output data of each row
											while($row1 = $result1->fetch_assoc()) {
												
												$temporal[]= array_merge($row,$row1);
											}
										} else {
											
											$temporal[]= array_merge($row,array($row['area'] =>'0'));
										}
										
									}
									$returnJs[]= array_merge(array('aspirante'=>$aspirante),array('areas'=>$temporal));
						}
						
					}
			}
						

		$result->free();
		
		$conn->close();
        echo json_encode($returnJs);
	}
?>

