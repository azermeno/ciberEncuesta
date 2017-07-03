<?php 
session_start();
?>
<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">

        <title>Cuestionario</title>
        <meta name="description" content="redlab">
        <meta name="author" content="redlab">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		
		<link rel="shortcut icon" href="img/favicon.png">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/examen.css">
		
        <script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
        <script src="js/bootstrap.min.js"></script>

       
        <script type="text/javascript">
            
			$(function () {

                $(document).on('change', '.btn-file :file', function () {
                    var input = $(this),
                            numFiles = input.get(0).files ? input.get(0).files.length : 1,
                            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    input.trigger('fileselect', [numFiles, label]);
                });

                $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

                    var input = $(this).parents('.input-group').find(':text'),
					
                       log = numFiles > 1 ? numFiles + ' files selected' : label;

                    if (input.length) {
                        input.val(log);
                    } else {
                        if (log)
                            alert(log);
                    }

                });

              				
				$("#ejemplo").on('click',function(e){
					e.preventDefault();
					
					var posicion = $(this).position();
					
					//console.log( posicion.left);
					//console.log($("#tip").height());
					$("#tip").css("display", "none");
					$("#tip").css("left", 100);// this.offsetLeft // e.pageX // position.left
					$("#tip").css("top", posicion.top - $("#tip").height()-140);// this.offsetTop //  e.pageY -
					$("#tip").css("display", "block");
					//$("body").css("background-color",'rgba(127,127,127,50)')
					
					$("#ocultar").hide();
				});
				
				
				$("#ejemploExterno").on('click',function(e){
					e.preventDefault();
					
					var posicion = $(this).position();
					
					//console.log( posicion.left);
					//console.log($("#tip").height());
					$("#tip2").css("display", "none");
					$("#tip2").css("left", 100);// this.offsetLeft // e.pageX // position.left
					$("#tip2").css("top", posicion.top - $("#tip").height()-160);// this.offsetTop //  e.pageY -
					$("#tip2").css("display", "block");
					//$("body").css("background-color",'rgba(127,127,127,50)')
					
					$("#ocultar").hide();
				});
				$("#ejemploEncuesta").on('click',function(e){
					e.preventDefault();
					
					var posicion = $(this).position();
					
					//console.log( posicion.left);
					//console.log($("#tip").height());
					$("#tip3").css("display", "none");
					$("#tip3").css("left", 100);// this.offsetLeft // e.pageX // position.left
					$("#tip3").css("top", posicion.top - $("#tip3").height()+20);// this.offsetTop //  e.pageY -
					$("#tip3").css("display", "block");
					//$("body").css("background-color",'rgba(127,127,127,50)')
					
					$("#ocultar1").hide();
				});

				$("#tip").on('click', function (e) {
					e.preventDefault();
					$("#tip").css("display", "none");
					//$("body").css("background-color",'rgb(0,0,0)')
					$("#ocultar").show();
				});
				$("#tip2").on('click', function (e) {
					e.preventDefault();
					$("#tip2").css("display", "none");
					//$("body").css("background-color",'rgb(0,0,0)')
					$("#ocultar").show();
				});
				$("#tip3").on('click', function (e) {
					e.preventDefault();
					$("#tip3").css("display", "none");
					//$("body").css("background-color",'rgb(0,0,0)')
					$("#ocultar1").show();
				});
				$(".nav").on('click',function(){
					$("#tip").css("display", "none");
					$("#tip2").css("display", "none");
					$("#tip3").css("display", "none");
					$("#ocultar").show();
					$("#ocultar1").show();
				});
				
                <?php if (isset($_GET['ok'])) { ?>
				
				
				$("#ok-message-divmsg").html("<?php echo $_GET['ok']; ?>");
					$("#ok-message").show();
					
					setTimeout(function(){
						
						$("#ok-message").hide();
						
					},9000);
                <?php } ?>

				 <?php if (isset($_GET['error'])) { ?>
				
					 $("#error-message-divmsg").html("<?php echo $_GET['error']; ?>");
					 $("#error-message").show();
					
					setTimeout(function(){
						
						$("#error-message").hide();
						
					},9000);
					 
                <?php } ?>
            });
        </script>

</style>
    </head>
    <body>

      <?php include_once 'nav_menu.php'; ?>

        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">
                <h2><span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Cargar archivo excel</h2>
				<br>
						<div id="error-message" class="alert alert-danger" role="alert" style="display:none">
							 <div id="error-message-divmsg" ></div>
						</div>

						<div id="ok-message" class="alert alert-success" role="alert" style="display:none">
							  <div id="ok-message-divmsg" ></div>
						</div>
				<br>		
			    <ul class="nav nav-tabs">
				  <li class="active">
				  <a data-toggle="tab" href="#promedio"><h3>Ex&aacute;menes con evaluaci&oacute;n <span class="glyphicon glyphicon-hand-down" aria-hidden="true"></span></h3></a>
				  </li>
				  <li>
				  <a data-toggle="tab" href="#encuesta"><h3>Encuestas sin evaluaci&oacute;n <span class="glyphicon glyphicon-hand-down" aria-hidden="true"></span></h3></a>
				  </li>
				</ul>
				<div class="tab-content">
				<div id="promedio" class="tab-pane fade in active">
				   <div class="tip" id="tip"><img src="img/ejemploExcel.jpg"></div>
				   <div class="tip" id="tip2"><img src="img/ejemploExcelExterno.jpg"></div>

					<div class="centrado" style="margin-bottom: 40px" id="ocultar">
						<form action="php/cargar_excel_mtd.php" method="post" enctype="multipart/form-data" style="width: 50%; margin: 0 auto">
													
					
						<div class="alert alert-danger" role="alert" style="color:black; text-align:left"><b>IMPORTANTE:</b> el archivo debe tener la siguiente estructura:<br>
						Paso 1. <br>
						El nombre del archivo es el nombre que se le va a dar al ex&aacute;men 
						<br>(Ejemplo: <b>soporte avanzado.xlsx</b>).<br><br>
						Paso 2. <br>
						<u>En la celda <i>A1,</i> </u>si es un examen <b>interno (</b>Evaluaci&oacute;n al personal<b>)</b> se pone <b>I:</b> seguido de la duraci&oacute;n del el examen en minutos <b>(</b>Ejemplo: <strong>I: 90)</strong>, si es un examen <b>externo (</b>Evaluaci&oacute;n a candidatos a trabajo<b>)</b> se pone <b>E:</b> seguido de la duraci&oacute;n del el examen en minutos <b>(</b>Ejemplo: <strong>E: 90)</strong><br><br>
						Paso 3. <br>
						<u>En la siguiente fila, en la celda <i>A</i></u> debe ir <b>A:</b> seguido del nombre del <b>&aacute;rea</b><br>
						<b>(</b>ejemplo: <strong>A: Redes)</strong>.<br><br>
						Paso 4.<br>
						<u>En la siguiente fila, en la celdas <i>A</i></u> debe ir <b>P:</b> seguido de la <b>pregunta</b><br> 
						<b>(</b>Ejemplo: <b>P: Pregunta)</b>.<br>
						<u>En la celda <i>'B'</i> correspondiente a la pregunta</u>, se coloca la <b>respuesta correcta</b>, <u>en las celdas siguientes <i><b>(</b>C,D,E,F...etc<b>)</b></i></u> las <b>respuesta incorrectas</b>.<br>
						Todas las preguntas <b>(P:)</b> siguientes se asignan a la misma &aacute;rea <b>(A:)</b> hasta que se especifique otra.<br><br>
						
						Se repite el paso <b>3 y 4 </b> hasta finalizar el examen.<br><br>
						
						Para ver un ejemplo selecciona algunas de las opciones de abajo:<br>
						<a href="" id="ejemplo">EJEMPLO <b>INTERNO</b> PRESIONA AQU&Iacute;</a>
						<br>
						<a href="" id="ejemploExterno">EJEMPLO <b>EXTERNO</b> PRESIONA AQU&Iacute;</a>
						</div>
						
				   
							<div class="row">
								<div class="col-lg-12 col-sm-12">
									<h4>Buscar archivo</h4>
									<div class="input-group centrado">
										<span class="input-group-btn">
											<span class="btn btn-info btn-file">
												Explorar&hellip; <input type="file" name="fileToUpload" id="fileToUpload">
											</span>
										</span>
										<input type="text" class="form-control" readonly>
									</div>
									<span class="help-block">
										Selecciona el archivo excel que contenga la informaci&oacute;n que deseas procesar en el sistema.
									</span>				
									<input id="loadButton" type="submit" value="Cargar y procesar" name="submit" class="btn-primary">
								</div>
							</div>
						</form>
					</div>
				
				</div>
				 <div id="encuesta" class="tab-pane fade">
					 <div class="tip" id="tip3"><img src="img/ejemploExcelEncuesta.jpg"></div>
				   
					<div class="centrado" style="margin-bottom: 40px" id="ocultar1">
						<form action="php/cargar_excel_encuesta_mtd.php" method="post" enctype="multipart/form-data" style="width: 50%; margin: 0 auto">
													
					
						<div class="alert alert-danger" role="alert" style="color:black; text-align:left"><b>IMPORTANTE:</b> el archivo debe tener la siguiente estructura:<br>
						Paso 1. <br>
						El nombre del archivo es el nombre que se le va a dar a la encuesta 
						<br>(Ejemplo: <b>calidad.xlsx</b>).<br>
						Si la encuesta lleva ponderaci&oacute;n <b>(0 a 10)</b> se coloca antes del nombre seguido por un <b>(-)</b>
						<br>(Ejemplo: <b>9-calidad.xlsx</b>).<br>
						<br>
						Paso 2.
						
						<br>
						<u>En la celdas <i>A</i></u> debe ir la <b>pregunta.</b><br>
						<br>
						Si la pregunta lleva pornderaci&oacute;n <b>(0 a 10)</b> debe ir antes de la pregunta seguida por un <b>(-)</b> 
						<b>(</b>Ejemplo: <b>8-</b>Pregunta<b>)</b>.<br>
						<br>
						<u>En la celda <i>'B'</i> correspondiente a la pregunta</u>, se coloca una <u><b>C</b></u>, si lleva comentarios, de lo contrario <u><b>N</b></u>.
						<br>
						<br>
						<u>En las celdas siguientes <i><b>(</b>C,D,E,F...etc<b>)</b></i></u> van las <b>opciones de la pregunta</b>, en el orden que se acomoden son como van a aparecer,
						la celda 'C' ser&aacute; la opci&oacute;n uno, la celda 'D' la opci&oacute;n dos, etc...<br><br>
						Si se va a realizar una pregunta s&oacute;lo con comentario en la celda <b>'C'</b> se coloca un punto 
						<b>(</b>.<b>)</b> como muestra la pregunta 16 del ejemplo que se muestra al final de las instrucciones.<br><br>
						Se repite el paso <b>2 </b> hasta finalizar el examen.<br><br>
						
						<b>Nota: el orden en que se acomoden las pregunas es como aparecer&aacute;n en la encuesta</b>
						<br>
						<br>
						<a href="" id="ejemploEncuesta">PARA VER UN EJEMPLO PRESIONA AQU&Iacute;</a>
						
						</div>
						
							<div class="row">
								<div class="col-lg-12 col-sm-12">
									<h4>Buscar archivo</h4>
									<div class="input-group centrado">
										<span class="input-group-btn">
											<span class="btn btn-info btn-file">
												Explorar&hellip; <input type="file" name="fileToUpload" id="fileToUpload">
											</span>
										</span>
										<input type="text" class="form-control" readonly>
									</div>
									<span class="help-block">
										Selecciona el archivo excel que contenga la informaci&oacute;n que deseas procesar en el sistema.
									</span>				
									<input id="loadButton1" type="submit" value="Cargar y procesar" name="submit" class="btn-primary">
								</div>
							</div>
						</form>
					</div>
					
				  </div>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>