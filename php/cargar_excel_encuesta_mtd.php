<?php
// Start the session
session_start();

require_once 'config.php';

set_include_path(get_include_path() . PATH_SEPARATOR . '/PHPExcel-1.8/Classes/');
require('PHPExcel-1.8/Classes/PHPExcel/IOFactory.php');

if (!empty($_FILES["fileToUpload"]["tmp_name"])) {

     $nombreArchivo = explode(".", $_FILES["fileToUpload"]["name"]);

	 if ($nombreArchivo[1] === 'xlsm' || $nombreArchivo[1] === 'xlsx') {
		
			$inputFileName = $_FILES["fileToUpload"]["tmp_name"];
			
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		   /*
			$sheetDataSecundaria = $objPHPExcel->getSheetByName('SECUNDARIA')->toArray(null, true, true, true);*/
			
			$conn = new mysqli($mysql_config['host'], $mysql_config['user'], $mysql_config['pass'], $mysql_config['db']);
			
			if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
			} 
					$conn->set_charset("utf8");
					
			$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
			
			$fechaCaptura = date("Y-m-d H:i:s");
			
			/*INICIAMOS TRANSACCION*/
			$conn->query("START TRANSACTION");
			$ciberEncuesta = strpos($nombreArchivo[0],'@');
			$ponderacionEncuesta = 1;
			$tempNombreArchivo = strpos($nombreArchivo[0],'-');
						
			if($tempNombreArchivo < 5 && $tempNombreArchivo !== false){
				$divideNombre = explode('-',$nombreArchivo[0]);
				$ponderacionEncuesta = $ciberEncuesta === 0 ? substr($divideNombre[0], 1) : $divideNombre[0];
				$nombreArchivo[0] = $divideNombre[1];
			}
			$sql = "INSERT INTO puesto(puesto,limite_minutos,conPromedio,puestoPonderacion)".
			" VALUE('{$nombreArchivo[0]}',60,0,{$ponderacionEncuesta});";
			$conn->query($sql);
			if($conn->affected_rows==1){
				
					$id_area=0;
					$correcto = true;
					$celdas = array('C','D','E','F','G','H','I','J');

					$id_puesto = $conn->insert_id;
					
					if($ciberEncuesta === 0){
					  
						$sql = "SELECT pk_unidad FROM unidad;";
						$result = $conn->query($sql);
						
						if($result->num_rows > 0){
							$unidad = array();
							while($row = $result->fetch_array()) {
								$sql = "INSERT INTO empaquetado(fk_unidad,fk_puesto,activo)".
								" VALUES ({$row[0]},{$id_puesto},1);";
								
									$conn->query($sql);
								
								if($conn->affected_rows != 1){
									
									$correcto = false;
									break;
								}
							}
							
						}
					}
					
					$sql = "INSERT INTO area (area,fk_puesto)VALUE('',{$id_puesto})";
					$conn->query($sql);
					if($conn->affected_rows==1){
					 $id_area=$conn->insert_id;
					 $ordenPregunta = 1;
					 $ordenRespuesta = 1;
					    foreach ($sheetData as $file) {
					        $temporal= trim($file['B']);
							$temporal = $temporal != NULL || $temporal !="" ? strtoupper($temporal) : '';
							$banComentario = $temporal == 'C' ? 1 : 0;
					        if ($id_area != 0 && ($file['A'] != NULL || $file['A'] !="")) {
								$ponderacionPregunta = 0;
								$pregunta = $file['A'];
								$temp = strpos($file['A'],'-');
								if($temp < 4 && $temp !== false){
									
											$dividePregunta = explode('-',$file['A']);
											$ponderacionPregunta = $dividePregunta[0];
											$pregunta = $dividePregunta[1];
								}
								$sql = "INSERT INTO pregunta(pregunta,fk_area,preguntaOrden,banComentario,preguntaPonderacion)". 
							  " VALUE('{$pregunta}',{$id_area},{$ordenPregunta},{$banComentario},{$ponderacionPregunta});";
							 
								$conn->query($sql);
								$ordenPregunta++;
							    if($conn->affected_rows == 1){
							
							      $id_pregunta=$conn->insert_id;
								  $ordenRespuesta=1;
							    	foreach($celdas as $celda){
										
										if(isset($file[$celda])){
											$respuestaProvicional = strval($file[$celda]);
											if($file[$celda] === false){
												
												$respuestaProvicional = "Falso";
											}
											
											if($file[$celda] === true){
												
												$respuestaProvicional = "Verdadero";
												
											}
											
											if(isset($file[$celda]) && ($file[$celda]=='0' || $file[$celda]!=NULL )){

												$conn->query("INSERT INTO respuesta(respuesta,correcta,fk_pregunta,respuestaOrden)
												VALUE('{$respuestaProvicional}',0,{$id_pregunta},{$ordenRespuesta})");
												if($conn->affected_rows!=1){
													
													$correcto=false;
													$conn->query("ROLLBACK");
													$conn->close();									
													break 2;
												} 
												$ordenRespuesta ++;
													
											} else {
											  break;
											  }
										}else {
											
											break;
										}

									}//fin foreach celda

							    } else {
									
									$correcto=false;
									$conn->query("ROLLBACK");
									$conn->close();
									break;
								}
							} 
							
						}//fin foreach fila
		   
						if($correcto==true){
							$conn->query("COMMIT");
							header("Location: ../cargar_excel.php?ok=Archivo procesado correctamente"); /* Redirect browser */
							$conn->close();
						} else {
							$conn->query("ROLLBACK");
							$conn->close();
							header("Location: ../cargar_excel.php?error=Por el momento no es posible procesar el archivo(2), cheque que cumpla con las indicaciones arriba mencionadas e intente nuevamente(1)."); /* Redirect browser */
						}
				
					} else {
						$conn->query("ROLLBACK");
						$conn->close();
						header("Location: ../cargar_excel.php?error=Por el momento no es posible procesar el archivo (1), intente nuevamente(2)."); /* Redirect browser */
					}
						
				} else {
					$conn->query("ROLLBACK");
						$conn->close();
						header("Location: ../cargar_excel.php?error=Por el momento no es posible procesar el archivo, intente nuevamente(2)."); /* Redirect browser */
					}
					
		} else {
			header("Location: ../cargar_excel.php?error=El archivo no es del formato requerido.(ejemplo Mi_archivo.xlsx)"); /* Redirect browser */
			exit();
		}
		
	} else {
		header("Location: ../cargar_excel.php?error=Debe seleccionar un archivo."); /* Redirect browser */
		exit();
	}
?>