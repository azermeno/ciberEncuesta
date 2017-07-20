    
			var codigo = '6';
			var unidad = '045JEFATURAGDL';
		
		$(function(){
		
			console.log(codigo+' -- '+unidad+"--- ");
			$.ajax({
			 url : "php/verificadorCiberEncuesta.php",
			 method : "POST",
			 dataType : "json",
			 data : {codigo:codigo,unidad:unidad}
			
			}).done(function(respuesta){
				
				console.log(respuesta);
				var resultado = "";
				if(respuesta.error == ""){
				    resultado = respuesta.validacion.substring(1,respuesta.validacion.length-1).split("|");
					console.log(resultado.length);
					console.log(respuesta.validacion.substring(1,respuesta.validacion.length-1).split("|"));
					if(resultado.length == 3){
						if(resultado[0] == 0){
							
						   $("#mensaje").html("La sesi&oacute;n "+unidad+" con el c&oacute;digo "+codigo+" no debe de contestar por que ya contesto o por que no es tiempo de contestar, el rango de fecha es hasta el d&iacute;a 15 y con horario de 7am a 3pm.");
							$(".alert").show();
							 var datos = btoa("requiriente="+resultado[2]+"&encuesta="+resultado[1]);
								window.location.replace("/ciberEncuestaPHP/ciberEncuesta.php?"+datos);
							
						} else {
							
								 var datos = btoa("requiriente="+resultado[2]+"&encuesta="+resultado[1]);
								window.location.replace("/ciberEncuestaPHP/ciberEncuesta.php?"+datos);
	
						}
					} else {
						
						
						 $("#mensaje").html("La sesi&oacute;n "+unidad+" con el c&oacute;digo "+codigo+" no tiene los datos correcto.");
							$(".alert").show();
					}
					
				} else {
					
					$("#mensaje").html(respuesta.error);
					
				}
			
			}).fail(function(error){
			
				console.log("Fallo");
				console.log(error);
				console.log(error.statusText);
			});
			
		});
    
		