<?php 
session_start();
?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>CiberEncuesta</title>
        <meta name="description" content="redlab">
        <meta name="author" content="redlab">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		
		<link rel="shortcut icon" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/ciberEncuesta.css">
		
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<script src="js/ciberEncuesta.js" type="text/javascript"></script>

</style>
    </head>
    <body>
	<div id="regresivo">
	
	</div>
            

        <div class="container centrado">
            <div class="starter-template">
			
			<div class="row"  style="width: 80%; margin: 0 auto;">
			  <div class="col-md-4" style="padding-left: 0px;"><img src="img/logo.jpg" height="150"></div>
			  <div class="col-md-8"><h2>Encuesta de percepci&oacute;n de la satisfaci&oacute;n del usuario <br>del mes de <span id="mes"></span></h2></div>
			</div>
			              
				<div class="centrado">
					<form id="form-send"  method="post" style="width: 80%; margin: 0 auto">
						
						<div style="text-align:left">
						
						<!--label>JEFE DE LABORATORIO:&nbsp;</label-->
						<label  id="jefeLab"></label>
						</div>
						
						<div style="text-align:left" >
						<!-- label>UNIDAD:&nbsp;</label -->
						<label id="unidad"></label>
						</div>
						
						<!--div style="text-align:left">
						<label>PRODUCTO:&nbsp;</label>
						<label  id="producto"></label>
						<div-->
					
						<label style="text-align:left">Para Cibern&eacute;tica de M&eacute;xico, es muy importante conocer la percepci&oacute;n de la satisfacci&oacute;n de nuestros productos y servicios, para lograr que se cumplan sus necesidades y expectativas.
						Por lo anterior, solicitamos dedique un momento de su valioso tiempo a completar esta peque&ntilde;a encuesta, la informaci&oacute;n que nos proporcione ser&aacute; utilizada para mejorar nuestro servicio. 
						Sus respuestas ser&aacute;n tratadas de forma confidencial y no ser&aacute;n utilizadas para ning&uacute;n prop&oacute;sito diferente al mencionado.</label>
						<!-- Identificador Manual 	-->		
						<input type="hidden" id="encuestaManual" name="encuestaManual" value="0"></input>	
						
						<div id="preguntas" style="text-align:left">
						
						</div>
						<div class="centrado">
						<button type="submit" class="btn btn-info" id="aceptar" style="width:30%;display:none">Mandar respuestas</button>
						</div>
						</form>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>