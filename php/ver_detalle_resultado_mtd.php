<?php

$idUsuario = isset($_POST['idAspirante']) ? $_POST['idAspirante'] : 0;
if($idUsuario ==0){
	echo '<script language="javascript">alert("Por el momento no se encuentra disponible el sitio");</script>'; 
	echo '<script language="javascript">window.history.back();</script>'; 
} else {
	require_once 'php/config.php';
        
        
		$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				echo '<script language="javascript">alert("Por el momento no se encuentra disponible el sitio");</script>'; 
	            echo '<script language="javascript">window.history.back();</script>'; 
				die("Connection failed: " . $conn->connect_error);
			} else {
				$conn->set_charset("utf8");
					
				$returnJs = array();
				$aspirantes = array();
			
				
				$sql = "SELECT a.pk_aspirante,a.Nombre,a.email,a.tiempo_inicio,p.puesto,p.pk_puesto FROM aspirante as a,puesto as p WHERE a.pk_aspirante={$idUsuario} && p.pk_puesto=a.fk_puesto";
				
				
				$result = $conn->query($sql);
				
				if ($result->num_rows > 0) {
					// output data of each row
							while($row = $result->fetch_assoc()) {
								$aspirantes[]=$row;
							}
							
							$totalPreguntas=0;
							$totalCorrectas=0;
							$porcentaje=0;
							$resultado='';
							foreach($aspirantes as $aspirante){
								$temporal = array();
								
								$returnJs = array();
								$sql = "SELECT a.area,a.pk_area ,COUNT(p.pk_pregunta) as total FROM area as a,pregunta as p WHERE  a.fk_puesto={$aspirante['pk_puesto']} && p.fk_area=a.pk_area GROUP by a.area; ";
								
								$result = $conn->query($sql);
								
								
								if ($result->num_rows > 0) {
									// output data of each row
											while($row = $result->fetch_assoc()) {
												$totalPreguntas+=$row['total'];
												$returnJs['preguntas'][]=$row;											
												$sql = "SELECT count(r.pk_respuesta)  as correctas FROM contestado as c, respuesta as r, pregunta as p WHERE p.fk_area={$row['pk_area']} && r.fk_pregunta=p.pk_pregunta && r.correcta=1 && c.fk_respuesta=r.pk_respuesta && c.fk_aspirante={$aspirante['pk_aspirante']}";
												
												$result1 = $conn->query($sql);
												if ($result1->num_rows > 0) {
												// output data of each row
													while($row1 = $result1->fetch_assoc()) {
														$totalCorrectas+=$row1['correctas'];
														$temporal[]= array_merge($row,$row1);
													}
												} else {
													
													$temporal[]= array_merge($row,array($row['area'] =>'0'));
												}
												$sql = "SELECT * FROM pregunta WHERE fk_area={$row['pk_area']}";
												$result2 = $conn->query($sql);
												if ($result2->num_rows > 0) {
														
														// output data of each row
																while($temp = $result2->fetch_assoc()) {
																
															$respuestas = array();
															$sql = "SELECT pk_respuesta,fk_pregunta,respuesta,correcta FROM respuesta WHERE fk_pregunta={$temp['pk_pregunta']} ORDER BY respuesta";
															//error_log($sql);
															 $result3 = $conn->query($sql);
													
															if ($result3->num_rows > 0) {
																// output data of each row
																	  
																	   $returnJs['preguntas'][]=$temp;
																		while($row = $result3->fetch_assoc()) {
																			$sql = "SELECT * FROM contestado WHERE fk_aspirante={$idUsuario} && fk_respuesta={$row['pk_respuesta']}";
																			//error_log($sql);
																			$result4 = $conn->query($sql);
																			//error_log($result4->num_rows);
																			if ($result4->num_rows > 0) {
																			//	error_log('entro a correcto');
																			   $row=array_merge($row,array('contestado'=>'1'));
																			} 
																				
																			$returnJs['respuestas'][]=$row;
																			
																				
																		}
																	
															}
															
														}
													}
												
											}
											$returnJs[]= array_merge(array('aspirante'=>$aspirante),array('areas'=>$temporal));
											//error_log(print_R($returnJs,true));
								}
								
							}
							
						$porcentaje = intval($totalCorrectas * 100 / $totalPreguntas);
						$resultado	= $totalCorrectas.' de '.$totalPreguntas.'<br>'.$porcentaje.'%';				
				}
					
			}
			
			$result->free();
			$result1->free();
			$result2->free();
			$result3->free();
			$result4->free();
		
		$conn->close();
}
?>