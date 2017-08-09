function resultados(name){
	
			var filtro = $('input:radio[name=filtro]:checked').val();
		   
			$.ajax({
			method:"POST",
			url: "php/obtener_resultados_mtd.php",
			dataType:"json",
			data:{name:name, filtro:filtro}
			
		}).done(function(data){
			
			var area,preguntas,respuestas,preguntasInt,aciertosInt,total,color,porcentaje;
			var contador =1;
				$("#tabla-contenido").empty();
				$("#tabla-contenido").append( 
				 '<tr class="warning">'+
					'<th style="width: 12%;" class="info centrado">Nombre</th>'+
					'<th style="width: 11%;" class="info centrado">Correo</th>'+
					'<th style="width: 11%;" class="info centrado">Puesto solicitado</th>'+
					'<th style="width: 9%;" class="info centrado">Realizado</th>'+
					'<th style="width: 38%;" class="info centrado">&Aacute;rea</th>'+
					'<th style="width: 5%;" class="info centrado">Preguntas</th>'+
					'<th style="width: 5%;" class="info centrado">Aciertos</th>'+
					'<th style="width: 5%;" class="info centrado">Resultado</th>'+
					'<th style="width: 4%;" class="info centrado">Ver</th>'+
											
				'</tr>');
				
			data.forEach(function(entry){
				if(contador % 2 == 0){
					
					color = 'class="warning"';
				} else {
					
					color = '';
				}
				area=preguntas=respuestas=total=''
				preguntasInt = aciertosInt = 0;
				
				entry.areas.forEach(function(entry2){
					
					
					area +=entry2.area+'<br>';
					preguntas +=entry2.total+'<br>';
					respuestas +=entry2.correctas+'<br>';
					preguntasInt +=parseInt(entry2.total);
					aciertosInt +=parseInt(entry2.correctas);
				});
					porcentaje = parseInt((aciertosInt * 100 )/preguntasInt);
				total = aciertosInt+' de '+preguntasInt+ '<br>' + porcentaje + '%';
				$("#tabla-contenido").append( 
				'<tr '+color+'>'+
					'<th class="centrado">'+entry.aspirante.Nombre+'</th>'+
					'<th class="centrado">'+entry.aspirante.email+'</th>'+
					'<th class="centrado">'+entry.aspirante.puesto+'</th>'+
					'<th class="centrado">'+entry.aspirante.tiempo_inicio+'</th>'+
					'<th class="centrado">'+area+'</th>'+
					'<th class="centrado">'+preguntas+'</th>'+
					'<th class="centrado">'+respuestas+'</th>'+
					'<th class="centrado">'+total+'</th>'+
					'<th class="centrado"><a href="#" data-id="'+entry.aspirante.pk_aspirante+'" class="detalle btn btn-info btn-xs" style="color:black">' +
				  '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></th>' +
											
				'</tr>');
				contador++;
			});
							
			$(".detalle").on('click',verDetalle);
		}).fail(function(){
			
			alert("Por el momento no esta disponible el apartado de resultados, intente mas tarde (Puedes intentarlo nuevamente..)");
		
			//window.location.replace("iniciar.php");
			
		});
		
		
	}
	function imprimir(){
		
		$("#form-send").hide();
		window.print();
		setTimeout(function(){
			
			$("#form-send").show();
			
		},500);
		
	}
	function verDetalle(){
		
					// Creamos el formulario auxiliar
		var form = document.createElement( "form" );

		// Le añadimos atributos como el name, action y el method
		form.setAttribute( "name", "formulario" );
		form.setAttribute( "action", "ver_detalle.php" );
		form.setAttribute( "method", "post" );

		// Creamos un input para enviar el valor
		var input = document.createElement( "input" );

		// Le añadimos atributos como el name, type y el value
		input.setAttribute( "name", "idAspirante" );
		input.setAttribute( "type", "hidden" );
		input.setAttribute( "value", $(this).attr('data-id') );

		// Añadimos el input al formulario
		form.appendChild( input );

		// Añadimos el formulario al documento
		document.getElementsByTagName( "body" )[0].appendChild( form );

		// Hacemos submit
		document.formulario.submit();
	}
		$(function () {
			
			
		resultados('');
		$("#imprimir").on('click',imprimir);
		
		$("#form-send").submit(function(event){
			
			 event.preventDefault();
			 //alert("En construcci\u00F3n");
			 var nombre = $("#nombre").val();
			 nombre = nombre.trim();
			
				
				resultados(nombre);
				
			
			
		});
			
		});