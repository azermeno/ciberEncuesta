<?php

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    // Start the session
    session_start();

   // $cliente = new SoapClient('http://cibernetica.customercare.com.mx/servicio.asmx?wsdl');
    $cliente = new SoapClient('http://localhost:82/wsActEncuesta.asmx?wsdl');

    $result = array();

    $result = $cliente->detalleCliente();
    if (is_soap_fault($result)) {
        echo json_encode(array('error' => "SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})"));
    } else {

        //$resultado = utf8_encode($result ["ObtieneUsuariosResult"]);
        echo $result->detalleClienteResult;
    }
}


