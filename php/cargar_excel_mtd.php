<?php

// Start the session
session_start();

require_once 'config.php';

error_reporting(E_ALL ^ E_WARNING);
error_log("Ver donde buscar este error");
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
	$conn->query("INSERT INTO puesto(puesto,limite_minutos) VALUE('{$nombreArchivo[0]}',60)");
	if($conn->affected_rows==1){
		$id_puesto = $conn->insert_id;
		$id_area=0;
		$correcto=true;
		$bandera_tiempo = false;//es para indicar que se va a guardar en la primer celda el tiempo del examen en minutos
		$celdas = array('B','C','D','E','F','G','H','I','J','K','L');
	   foreach ($sheetData as $file) {
		if($bandera_tiempo==false && (strpos($file['A'], 'I:')!==FALSE || strpos($file['A'], 'E:')!==FALSE)){
			$tiempo = explode(':', $file['A'],2);
			//error_log( print_R($tiempo,TRUE),2);
			  $tiempo[1] = trim($tiempo[1]);
			if(preg_match('/^[1-9]([0-9]+)?$/', $tiempo[1])) {
			      $interno = '';
				  if(strpos($file['A'], 'I:')!==FALSE){
					  
					  $interno = ' , interno=1 ';
				  } 
				  $conn->query("UPDATE puesto SET limite_minutos={$tiempo[1]} $interno WHERE pk_puesto={$id_puesto}");
				if($conn->affected_rows != 1){
					//error_log("No se guardo el tiempo del excel".$nombreArchivo[0].", quedo por defecto 60");
				}
			} else {
				if(strpos($file['A'], 'I:')!==FALSE){
					  
					  $interno = ' , interno=1 ';
				  }
				//error_log("No se guardo el tiempo ".$tiempo[1]." del excel".$nombreArchivo[0]." por que no es de un formato valido, quedo por defecto 60");	
			}
							
		
			$candera_tiempo=true;
		} else {
			if ($file['A'] != NULL || $file['A'] !="") {
				
				$pos = stripos($file['A'], 'P:'); //regresa la posición de donde encontro la ocurrencia
				
				if ($pos !== false && $id_area != '') {//P: (Es una pregunta)
					$quitarPuntoYcoma = explode(':', $file['A'],2);
				
					$pregunta = $quitarPuntoYcoma[1];
					
					$conn->query("INSERT INTO pregunta(pregunta,fk_area) VALUE('{$pregunta}',{$id_area})");
					if($conn->affected_rows==1){
						$id_pregunta=$conn->insert_id;
						$correcta = 0;
						foreach($celdas as $celda){
							if($file[$celda]=='0' || $file[$celda]!=NULL ){
								$correcta = 0;
								if($celda == 'B'){
									$correcta = 1;
								} 
								
								$respuestaProvicional = strval($file[$celda]);
								if($file[$celda] === false){
									
									$respuestaProvicional = "Falso";
								}
								
								if($file[$celda] === true){
									
									$respuestaProvicional = "Verdadero";
									
								}
									
							$conn->query("INSERT INTO respuesta(respuesta,correcta,fk_pregunta)VALUE('{$respuestaProvicional}',{$correcta},{$id_pregunta})");
									if($conn->affected_rows!=1){
										$correcto=false;
										$conn->query("ROLLBACK");
										$conn->close();									
										break 2;
									} 
								
							} else {
								break;
								
							}
							
						}
						
					} else {
						//error_log("error al guardar la pregunta  ".$file[$celda]);
						$correcto=false;
						$conn->query("ROLLBACK");
						$conn->close();
						break;
						
					}
					
					
				} else if(stripos($file['A'], 'A:')!==FALSE){// Es una área
				   
					$quitarPunto = explode(':', $file['A'],2);
					
					$area = $quitarPunto[1];
					$sql = "INSERT INTO area (area,fk_puesto)VALUE('{$area}',{$id_puesto})";
					$conn->query($sql);
					if($conn->affected_rows==1){
						$id_area=$conn->insert_id;
						
					} else {//No se pudo guardar el área de las siguientes preguntas y se cansela todo
						$correcto=false;
					
					//	error_log("136 error al guardar el area  ".$file['A'].' id_puesto '.$id_puesto);
						$conn->query("ROLLBACK");
						$conn->close();
						break;
					}
				} else {
					
					$correcto = false;
					//error_log("144 error al guardar el area  ".$file['A'].' id_puesto '.$id_puesto);
					$conn->query("ROLLBACK");
					$conn->close();
					break;
				}
				
			}
		}
	 }
		if($correcto===true){
			$conn->query("COMMIT");
			header("Location: ../cargar_excel.php?ok=Archivo procesado correctamente"); /* Redirect browser */
			$conn->close();
		} else {
			$conn->query("ROLLBACK");
			$conn->close();
			header("Location: ../cargar_excel.php?error=Por el momento no es posible procesar el archivo, cheque que cumpla con las indicaciones arriba mencionadas e intente nuevamente(1)."); /* Redirect browser */
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