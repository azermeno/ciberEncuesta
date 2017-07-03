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
		<link rel="stylesheet" href="css/TimeCircles.css">
		<script src="js/TimeCircles.js" type="text/javascript"></script>

       
        <script type="text/javascript">
		function pulsar(e) { 
		  tecla = (document.all) ? e.keyCode :e.which; 
		  return (tecla!=13); 
		} 
		function iniciarTiempo(){
			  $("#CountDownTimer").TimeCircles({ time: { 
	   Days: { show: false }, 
	   Hours: { text : "HORAS" },
		Minutes: { text:"MINUTOS" },
		Seconds: { text: "SEGUNDOS" }
		},
	   count_past_zero: true,
	   animation: "ticks",
	   });
	   $("#CountDownTimer").css({
		    position: 'fixed',
			botom: '0',
			left: '0'
		   
	   });
		// Start and stop are methods applied on the public TimeCircles instance
           
		}
		$(function(){
			 var  puesto= <?php $puesto = isset($_POST['puesto']) ? $_POST['puesto'] + 0 : 0;
		echo $puesto; ?>;

		 var mail = decodeURIComponent('<?php
		$mail = isset($_POST['mail']) ? urlencode($_POST['mail']) : '';
		echo $mail;
		?>');
		var name = decodeURIComponent('<?php
		$nombre = isset($_POST['nombre']) ? urlencode($_POST['nombre']) : '';
		echo $nombre;
		?>');
		
		if(puesto !=0 && mail !='' && name !=''){
			//alert("Puesto: "+puesto+ " mail: "+mail+" Nombre: "+name);
			$.ajax({
				method:"POST",
				url: "php/obtener_preguntas_mtd.php",
				dataType:"json",
				data:{puesto:puesto,mail:mail,name:name}
				
			}).done(function(data){
				
				if(typeof(data.msg)=='string'){
					alert(data.msg);
					$("#form-send").submit();
					
				} else {
					var cuestionario = '';
					
					if(data.preguntas.length > 0){
						var numero = 1;
						var segundos = data.limite.limite_minutos * 60;
						var milisegundos = segundos * 1000;
						
						$("#regresivo").html(
						'<div id="CountDownTimer" data-timer="'+segundos+'" style="width: 300px; height: 75px;"></div>'
						);
						iniciarTiempo();
						setTimeout(function(){
						//	alert("Mandar");
						  $("#form-send").submit();
						}, milisegundos);
						data.preguntas.forEach(function(entry){
							
							if(typeof(entry['fk_area']) != 'undefined'){ //es pregunta
									
									if(cuestionario==''){
										cuestionario += '<div class="well">';
										
									} else {
										
										cuestionario += '</div><div class="well">';
									}
								
								cuestionario += '<h3>'+numero+'.- '+entry['pregunta']+'</h3>';
								numero++;
							} else { //es respuesta
								cuestionario += '<div class="radio">'+
								'<h4><label onkeypress="return pulsar(event)" for="'+entry['pk_respuesta']+'">'+
								'<input type="radio" name="'+entry['fk_pregunta']+'" id="'+
								 entry['pk_respuesta']+'" value="'+entry['pk_respuesta']+'">'+
								 entry['respuesta']+'<label></h4>'+
								'</div>';
								
								
							}
						});
							cuestionario += "</div>";
						
						$("#preguntas").append(cuestionario);
						
					} else {
						
						alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente.)");
				
						window.location.replace("iniciar.php");
					}
				
				}
			}).fail(function(){
				
				alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente..)");
			
			    window.location.replace("iniciar.php");
				
			});
			
			
			
			
		} else {
			
			alert("No han sido registrados sus datos, intentelo de la forma correcta \\(¬¬)/ ");
			
			 window.location.replace("iniciar.php");
		}
		
		$('#form-send').submit(function(event) {
        event.preventDefault();
        $('#error-message').hide();
        
			var serialdata = $(this).serialize();
			
			  $.ajax({
			  method: "POST",
			  url: "php/guardar_resultados_mtd.php",
			  dataType: "json",
			  data: serialdata
			  }).done(function(data){
			  
			  if(data.status === true) {
				alert(data.msg);
				window.location.replace("index.php");
			  } else {
				alert(data.msg);
				window.location.replace("index.php");
			  }
			  console.log( "success" );          
			}).fail(function() {
			  console.log( "error" );
			});
		       
      });
	  
		});
        </script>

</style>
    </head>
    <body>
	<div id="regresivo">
	
	</div>
            

        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">


                
				<div class="centrado">
					<form id="form-send"  method="post" style="width: 80%; margin: 0 auto">
						<h3></span>PREGUNTAS:</h3>
						
				  <div id="preguntas" style="text-align:left">
					
				</div>				   
				   <button type="submit" class="btn btn-info" id="aceptar" style="width:30%;">Mandar respuestas</button>
					</form>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>