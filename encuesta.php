<?php 
session_start();
?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Encuesta</title>
        <meta name="description" content="redlab">
        <meta name="author" content="redlab">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		
		<link rel="shortcut icon" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/encuesta.css">
		
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>
		<script src="js/encuesta.js" type="text/javascript"></script>

</style>
    </head>
    <body>
	<div id="regresivo">
	
	</div>
            

        <div class="container centrado">
            <div class="starter-template">
			<div class="centrado">
               <img src="img/logo.jpg" height="200">
			</div>

                
				<div class="centrado">
					<form id="form-send"  method="post" style="width: 100%; margin: 0 auto">
					<input type="hidden" name="indentificador" id="indentificador" value="">
					<input type="hidden" name="puesto" id="puesto" value="">
						
					<div id="preguntas" style="text-align:left">
					
					</div>				   
				    <button type="submit" class="btn btn-info centrado" id="aceptar" style="width:30%;">Mandar respuestas</button>
					</form>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>