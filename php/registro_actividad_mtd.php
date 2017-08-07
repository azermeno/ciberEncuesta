<?php

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
 
		$returnJs = [];
		$returnJs['error'] = "";
		//WEB SERVICE PARA OBTENER LOS DATOS DE LA BASE PRICIPAL DE SIBER-ENCUESTA (SQL)
		$cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');
		$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : false ;
		$sesion = isset($_POST['sesion']) ? $_POST['sesion'] : false ;
		$ip = isset($_POST['ip']) ? $_POST['ip'] : false ;
		$actividad = isset($_POST['actividad']) ? $_POST['actividad'] : false ;
		$navegador = isset($_POST['navegador']) ? $_POST['navegador'] : false ;
		$nVersion = isset($_POST['nVersion']) ? $_POST['nVersion'] : false ;
		$plataforma = isset($_POST['plataforma']) ? $_POST['plataforma'] : false ;
		$movil = isset($_POST['movil']) ? $_POST['movil'] : false ;
		$resolucion = isset($_POST['resolucion']) ? $_POST['resolucion'] : false ;
		
		$resultWebService = [];
		
			//Al mandar true nos regresa sólo las unidades encuestadas activas
		$datos = array(
			"codigo" => $codigo,
			"sesion"=> $sesion,
			"ip" => $ip,
			"actividad"=> $actividad,
			"navegador" => $navegador,
			"nVersion"=> $nVersion,
			"plataforma" => $plataforma,
			"movil" => $movil,
			"resolucion"=> $resolucion
		
			);
			//error_log(print_r($datos,true));
			$resultWebService = $cliente->registroActividad($datos);
			
			if(is_soap_fault($resultWebService)){
			 $correcto = 0;
			}
			
			$returnJs['validacion'] = $resultWebService->registroActividadResult;
			
	
		echo json_encode($returnJs);
}			    

?>
