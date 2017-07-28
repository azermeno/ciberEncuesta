<?php

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        
			require_once 'config.php';
			require_once 'respuesta_valor.php';
			session_start();
		
			$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
				$conn->set_charset("utf8");
				$conn->query("START TRANSACTION");	
				$correcto = 1;
				$fecha = date('Y-m-d H:i:s');
				$fk_seccion = 0;
				$respuestasTemp = json_encode($_POST);
			
					$returnJs = array();
					$pk_puesto = 0;
					$pk_aspirante = 0;
					$pk_respuesta_anterior = array();;
					//$ponderacionPregunta = 0;
					$numero_de_secciones = 0;
					$ponderacion_seccion = 0;
					$resultado_total_secciones = 0;
					$ponderacionPreguntaRespuesta = 0;
					$contador_preguntas = 0;
					$contetadoManualmente = isset($_POST['encuestaManual']) ? $_POST['encuestaManual'] + 0 : 0;
					$banPrimerRegistro =  true;
					$resultado_por_seccion = 0;
					$nombreSeccionAnterior = "";
					$detalle = "";					
				foreach($_POST as $pregunta => $resultado){
					
					//El primer registro trae el indentificador de encuesta manual o automático
					if($banPrimerRegistro ==  false){
						$respuestaAbierta = explode('/',$pregunta);
						
						//Obtengo la ponderación de la sección 
						if($respuestaAbierta[0] == 'ponderacion'){
							
							if ($ponderacionPreguntaRespuesta > 0){
									//$pregunta tiene el valor de la ponderación de la seccion(Nombre de encuesta) en sólo en este if
									//Obtenemos la calificación por sección
									$resultado_por_seccion = ($ponderacionPreguntaRespuesta / $contador_preguntas) * $ponderacion_seccion;
									$resultado_por_seccion = round($resultado_por_seccion*10);
									$resultado_total_secciones += $resultado_por_seccion ;
									// se va a agregar la calificación de la sección
									$sql = "UPDATE aspirante SET calificacion={$resultado_por_seccion} WHERE pk_aspirante={$pk_aspirante} ";
									//error_log($sql);	
									$conn->query($sql);
									if($conn->affected_rows != 1){
										$correcto = 0;							
									} 
									$detalle = $detalle."\n La seccion: ".$nombreSeccionAnterior." tiene de promedio = {$resultado_por_seccion} ";
									
									$resultado_por_seccion  = 0;
								}
							
							//Obtenemos el nombre de la sección anteriro
							$sql ="SELECT puesto FROM puesto WHERE pk_puesto={$respuestaAbierta[1]}";
				
							$result = $conn->query($sql);
							if($result->num_rows > 0){
								
								$row = $result->fetch_array();
								
								$nombreSeccionAnterior = $row[0];
							}
							
							//Si es 0 el valor de la ponderación no se va a tomar en cuenta para dividir 
							//el resultado final de todas las secciones
							if($resultado > 0){
								
								$numero_de_secciones ++;
								
							}
							$ponderacion_seccion = $resultado;
							$ponderacionPreguntaRespuesta = 0;
							$contador_preguntas = 0;
						} else {
							$respuestaPonderacionOrden = explode('/',$resultado);
														
							if(isset($respuestaPonderacionOrden[1])){
								
								//  $respuestaPonderacionOrden[0] trae pk_respuesta que se selecciono
								//  Si no es respuesta abierta $respuestaPonderacionOrden[1] trae la ponderacion de la pregunta
								//	Si no es respuesta abierta $respuestaPonderacionOrden[2] trae la ponderación de la respuesta																																
								//$ponderacionPregunta += $respuestaPonderacionOrden[1];
								$ponderacionPreguntaRespuesta += ($respuestaPonderacionOrden[1] * $respuestaPonderacionOrden[2]);// ponderación pregunta * respuesta
								$contador_preguntas += $respuestaPonderacionOrden[1] == 0 ? 0 : 1 ;
								
							}
							if($respuestaAbierta[0] == 'abierta'){
								$textoDeUsuario = $conn->real_escape_string($respuestaPonderacionOrden[0]);
								$sql = "UPDATE contestado SET comentario='{$textoDeUsuario}' WHERE fk_respuesta={$pk_respuesta_anterior[0]} ".
								"&& fk_aspirante={$pk_respuesta_anterior[1]} ";
								//error_log($sql);
															
								$conn->query($sql);
								if($conn->affected_rows != 1){
									$correcto = 0;							
								} 
								$detalle = $detalle."\n * ".$textoDeUsuario;
							} else {
								if($pk_puesto != $respuestaAbierta[0]){
									 $pk_puesto = $respuestaAbierta[0];
									 $sql = "INSERT INTO aspirante(email,nombre,fk_puesto,estatus_contestado,tiempo_inicio,fk_mesEncuesta,contestadoManual)".
									 "VALUES('','',{$pk_puesto},1,'{$fecha}',{$_SESSION['mesEncuesta']},{$contetadoManualmente})";
										
									$conn->query($sql);
									if($conn->affected_rows != 1){
										$correcto = 0;
										break;
									} else {
										
										$pk_aspirante = $conn->insert_id;
										
									}
									
								}
								$sql = "INSERT INTO contestado(fk_respuesta,fk_aspirante) VALUE ({$respuestaPonderacionOrden[0]},{$pk_aspirante})";
								//error_log($sql);
								$conn->query($sql);
								  //error_log('agregar preguntas  '.$correcto);
								if($conn->affected_rows == 1){
									$pk_respuesta_anterior[0] =  $respuestaPonderacionOrden[0];
									$pk_respuesta_anterior[1] = $pk_aspirante;
																
								} else {
									$correcto = 0;
									
								}
								
							}
						}
					} else {
						
						$banPrimerRegistro = false;
					}
					
				} 
							//Obtenemos el nombre de la sección anteriro
							
				if ($ponderacionPreguntaRespuesta > 0){
					//$pregunta tiene el valor de la ponderación de la seccion en sólo en este if
					//Obtenemos la calificación por sección
					$resultado_por_seccion = ($ponderacionPreguntaRespuesta / $contador_preguntas) * $ponderacion_seccion;
					$resultado_por_seccion = round($resultado_por_seccion*10);
					$resultado_total_secciones += $resultado_por_seccion ;
					// se va a agregar la calificación de la sección
					$sql = "UPDATE aspirante SET calificacion={$resultado_por_seccion} WHERE pk_aspirante={$pk_aspirante} ";
					//error_log($sql);
					
					$conn->query($sql);
					if($conn->affected_rows != 1){
						$correcto = 0;							
					} 
					$detalle = $detalle."\n La seccion: ".$nombreSeccionAnterior." tiene de promedio = {$resultado_por_seccion}";
					$resultado_por_seccion  = 0;
				}
			
				if ($correcto==1) {// si fue correcto la captura en mysql	
					//WEB SERVICE
					$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');
					
					//$indicadorNuevo = intval($total / $ponderacionTotal);
					 $indicadorNuevo = floatval($resultado_total_secciones / $numero_de_secciones);
					
					 $indicadorNuevo = round($indicadorNuevo);
					//error_log($indicadorNuevo);
					//$indicadorNuevo = 100;
					$resultWebService = array();
					$datos = array(
					"mesEncuesta" => "{$_SESSION['encuesta']}",
					"reqCodigo" => $_SESSION['unidad'],
					"indicadorNuevo" => $indicadorNuevo,
					"txtFirma" => "{$_SESSION['txtFirma']}"
					);
					
					$detalle = $detalle."\n El resultado total es de  = {$indicadorNuevo}";
											
					$resultWebService = $cliente->ActualizaContesta($datos);
					
					if(is_soap_fault($resultWebService)){
					 $correcto = false;
					}
					
				 } else {
					 
					 $correcto = 0;
				 }
					
		   if ($correcto==1) { // si fue correcto en mysql y mssql
			    //Mandamos a llamar el ws de NuevoTicketPorEncuesta
					/*if($indicadorNuevo < 75){
					$cliente = new SoapClient('http:192.168.0.211:700/servicio.asmx?wsdl');
							$datos = array(
						"unidad" => $_SESSION['unidad'],
						"calificacion" => $indicadorNuevo,
						"detalle" => $detalle
						);
						
						$resultWebService = $cliente->NuevoTicketPorEncuesta($datos);
						
						if(is_soap_fault($resultWebService)){
							$sql = "INSERT INTO log(accion) VALUES('La unidad {$_SESSION['txtNombre']} con el códogo".
							" {$_SESSION['unidad']} en el mes ".
							" {$_SESSION['encuesta']}, tiene una calificación menor a 85 y no se pudo crear su incidencia ');";
							$conn->query($sql);
						}
					}*/
					
				$tipoAcceso = $contetadoManualmente == 1 ? ' de forma manual' : ' de forma acutomatica';
			    $sql = "INSERT INTO log(accion) VALUES('La unidad {$_SESSION['txtNombre']} con el códogo".
				" {$_SESSION['unidad']} respondio exitosamente la ciber_encuesta del mes ".
				" {$_SESSION['encuesta']} {$tipoAcceso}, respuestas: {$respuestasTemp}');";
				$conn->query($sql);
								
				$conn->query("COMMIT");
				echo json_encode(array('msg' => 'Respuesta enviadas correctamente','status' => true));
				$conn->close();
			} else {
				
				$tipoAcceso = $contetadoManualmente == 1 ? ' de forma manual' : ' de forma acutomatica';
			    $sql = "INSERT INTO log(accion) VALUES('La unidad {$_SESSION['txtNombre']} con el códogo". 
				" {$_SESSION['unidad']}, fallo la captura de ciber_encuesta del mes {$_SESSION['encuesta']}  {$tipoAcceso}, respuestas: {$respuestasTemp}');";
				$conn->query($sql);
				$conn->query("ROLLBACK");
				echo json_encode(array('msg' => 'Por el momento no esta activa la funcionalidad, reporte el inconveniente a medicoNet','status' => false));
				$conn->close();
			}
	}
?>

