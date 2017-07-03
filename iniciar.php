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
		function validaDatos(){
			
			if($("#puesto").val() == 0){
				
				alert("Debe seleccionar un \u00E1rea");
				$("#puesto").focus();
				return false;
			} 
			var val = $("#mail").val();
			if(val != ''){
				expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if (!expr.test(val)) {
					alert("\n La direcci\u00F3n de correo " + val + " es incorrecta");
					return false;
				}
			} else {
				
				alert("Debe ingresar un correo electr\u00F3nico");
				$("#mail").focus();
				return false;
			}
			
			if($("#nombre").val() == ''){
				
				alert("Debe ingresar su nombre");
				$("#nombre").focus();
				return false;
				
			}
			var idPuesto = $("#puesto").val();
			$.ajax({
				async: true,
				method:'POST',
				url:'php/verificar_registrado_mtd.php',
				dataType:'json',
				data:{
					nombre:$("#nombre").val(),
					mail:$("#mail").val(),
					puesto:$("#puesto").val()
					}
			}).done(function(data){
				
				if(data.permite==false){// ya esta registrado por lo que ya hizo el examen
					alert(data.msg);
					window.location.replace("index.php");
				} else {
					
					
					$("#formulario").submit();
					
				}
				
				
			}).fail(function(){
				alert("Por el momento no esta disponible la funcionalidad del test, intente mas tarde sin problema");
				return false;
				
			});
			
			
			
		}
		function pulsar(e) { 
		  tecla = (document.all) ? e.keyCode :e.which; 
		  return (tecla!=13); 
		} 
		var informacion = []; 
            $(function () {
				
				$.ajax({
					  method: "POST",
					  url: "php/obtener_puesto_mtd.php",
					  dataType: "json"
					  }).done(function(data){
						  
						  
						  data.forEach(function(entry){
							
							  informacion[entry['pk_puesto']] = entry['Preguntas'] +'.'+ entry['limite_minutos'] ;
							
							  $("#puesto").append(
							  '<option value="'+entry['pk_puesto']+'">'+entry['puesto']+'</option>'
							  );
							  
						  });
						  
						 
						
					  }).fail(function(){
						  
						  alert("Por el momento no esta disponible el servicio, intente m\u00E1s tarde");
						  
					  });
               
					   $("#puesto").change(function(){
						  if($("#puesto").val()!=0){
							  var datos = informacion[$(this).val()].split('.');
							  $("#numero").html(datos[0]);
							  $("#tiempo").html(datos[1]);
							 
							  $("#informacion").show();
						  } else {
							  $("#numero").html('');
							  $("#tiempo").html('');
							  $("#informacion").hide();
						  }
				   
			   });
            });
        </script>

</style>
    </head>
    <body>

     

        <div class="container centrado">
            <div class="starter-template">
               <img src="img/logo.jpg" height="200" id="logo">


                
				<div class="centrado">
					<form id="formulario" action="cuestionario.php" method="post" style="width: 50%; margin: 0 auto">
						<h3></span>Seleccione el &aacute;rea que aspira</h3>
						<select class="form-control" id="puesto" name="puesto">
                        <option value="0">&Aacutereas a seleccionar</option>
                        
						</select>
				  <div id="informacion" style="display:none">
							  <h3>
								  El test consiste de <span id="numero"></span> preguntas, seleccione de entre las opciones mostradas en cada pregunta.  
								</h3>
								<br>
								<h3>
									Para que el resultado pueda ser considerado como v&aacute;lido, debe resolver el test en un tiempo m&aacute;ximo de <span id="tiempo"></span> minutos.
								</h3>
								<br>
								
						<div class="row">
                    <div class="col-md-6">
							
							<input type="text" onkeypress="return pulsar(event)" class="form-control" id="mail" name="mail" placeholder="E-mail"  required>
							</div><div class="col-md-6">
							<input type="text" onkeypress="return pulsar(event)" class="form-control" id="nombre" name="nombre" placeholder="Nombre" requiere>
                  
                    </div>
                   </div>
				   <br>
				   <h3>
									EL TEST LO PODR&Aacute;S REALIZAR UNA &Uacute;NICA VEZ, AS&Iacute; QUE CUANDO ESTES LISTO DA CLICK EN EL SIGUIENTE BOT&Oacute;N.
								</h3>
								<br>
				   <button type="button" class="btn btn-info" id="aceptar" onclick="validaDatos()" style="width:30%;">Comenzar</button>
				</div>				   
					</form>
				</div>

            </div>

        </div><!-- /.container -->

    </body>
</html>