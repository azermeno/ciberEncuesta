<?php

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
 
		$returnJs = [];
		$returnJs['error'] = "";
		//WEB SERVICE PARA OBTENER LOS DATOS DE LA BASE PRICIPAL DE SIBER-ENCUESTA (SQL)
		$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');
		$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : false ;
		$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : false ;
		$resultWebService = [];
		
		if($unidad && $codigo){
			//Al mandar true nos regresa sólo las unidades encuestadas activas
		
			$resultWebService = $cliente->YaContestoActualizado(array("codigo" => $codigo,"unidad"=> $unidad));
			
			if(is_soap_fault($resultWebService)){
			 $correcto = 0;
			}
			
			$returnJs['validacion'] = $resultWebService->YaContestoActualizadoResult;
			
		} else {
			
			$returnJs['error'] = "Algunos de los siguientes datos son incorrectos favor de verificar: codigo= ".$codigo." ,unidad= ".$unidad;
			
		}
		echo json_encode($returnJs);
}			    

?>

