function resultados(name){
	
			var filtro = $('input:radio[name=filtro]:checked').val();
			var inputEncuesta = $("input:checkbox").is(':checked') ? 1 : 0;
		   
			$.ajax({
			method:"POST",
			url: "php/obtener_resultados_mtd.php",
			dataType:"json",
			data:{name:name, filtro:filtro, banEncuesta:inputEncuesta}
			
		}).done(function(data){
			
			var area,preguntas,respuestas,preguntasInt,aciertosInt,total,color,porcentaje;
			var contador =1;
			var nombre = "";
			var correo = "";
				$("#tabla-contenido").empty();
				var nombreOidentificador = inputEncuesta == 1 ? "Identificador" : "Nombre";
				
				if(inputEncuesta == 0 ){
					
					$("#tabla-contenido").append( 
					 '<tr class="warning">'+
						'<th style="width: 12%;" class="info centrado">'+nombreOidentificador+'</th>'+
						'<th style="width: 11%;" class="info centrado">Correo</th>'+
						'<th style="width: 11%;" class="info centrado">Ex&aacute;men</th>'+
						'<th style="width: 9%;" class="info centrado">Realizado</th>'+
						'<th style="width: 38%;" class="info centrado">&Aacute;rea</th>'+
						'<th style="width: 5%;" class="info centrado">Preguntas</th>'+
						'<th style="width: 5%;" class="info centrado">Aciertos</th>'+
						'<th style="width: 5%;" class="info centrado">Resultado</th>'+
						'<th style="width: 4%;" class="info centrado">Ver</th>'+
					'</tr>');
					
				} else {
					
					$("#tabla-contenido").append( 
					 '<tr class="warning">'+
						'<th style="width: 38%;" class="info centrado">'+nombreOidentificador+'</th>'+
						'<th style="width: 31%;" class="info centrado">Encuesta</th>'+
						'<th style="width: 9%;" class="info centrado">Realizado</th>'+
						'<th style="width: 5%;" class="info centrado">Preguntas</th>'+
						'<th style="width: 5%;" class="info centrado">Resultado</th>'+
						'<th style="width: 4%;" class="info centrado">Ver</th>'+
					'</tr>');
					
				}
				
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
					if(inputEncuesta == 0 ){
						
						nombre = entry.aspirante.Nombre;
						correo = entry.aspirante.email;
						total = aciertosInt+' de '+preguntasInt+ '<br>' + porcentaje + '%';
						$("#tabla-contenido").append( 
						'<tr '+color+'>'+
							'<th class="centrado">'+nombre+'</th>'+
							'<th class="centrado">'+correo+'</th>'+
							'<th class="centrado">'+entry.aspirante.puesto+'</th>'+
							'<th class="centrado">'+entry.aspirante.tiempo_inicio+'</th>'+
							'<th class="centrado">'+area+'</th>'+
							'<th class="centrado">'+preguntas+'</th>'+
							'<th class="centrado">'+respuestas+'</th>'+
							'<th class="centrado">'+total+'</th>'+
							'<th class="centrado"><a href="#" data-id="'+entry.aspirante.pk_aspirante+'" class="detalle btn btn-info btn-xs" style="color:black">' +
						  '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></th>' +
													
						'</tr>');
					} else {
						total = preguntasInt + " preguntas en total";
						respuestas = "N/A"
						nombre = entry.aspirante.seccion;
						correo = "No aplica";
						$("#tabla-contenido").append( 
						'<tr '+color+'>'+
							'<th class="centrado">'+nombre+'</th>'+
							'<th class="centrado">'+entry.aspirante.puesto+'</th>'+
							'<th class="centrado">'+entry.aspirante.tiempo_inicio+'</th>'+
							'<th class="centrado">'+preguntas+'</th>'+
							'<th class="centrado">'+total+'</th>'+
							'<th class="centrado"><a href="#" data-id="'+entry.aspirante.pk_aspirante+'" class="detalle btn btn-info btn-xs" style="color:black">' +
						  '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></th>' +
													
						'</tr>');
					}
					
				
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
		
	};
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
	};
		
		function mesYexamen(){
			
			$("#fecha").empty();
		    $("#examen").empty();
			var comboPuesto = '<option value="0">seleccione un ex&aacute;men</option>';
			var comboFecha ='<option value="0">Seleccione una fecha</option>';
			var inputEncuesta = $("input:checkbox").is(':checked') ? 1 : 0;
			$.ajax({
				url : "php/obtener_mes_examen_mtd.php",
				method: "POST",
				dataType : "json",
				data : {banEncuesta : inputEncuesta}
								
			}).done(function(entrada){

				
				
				if(entrada.fecha.length > 0){
					entrada.fecha.forEach(function(fecha){
						
						comboFecha += '<option value="'+fecha.fecha+'">'+fecha.fecha+'</option>';
						
					});
				} else {
					
					comboFecha = '<option value="0">No hay fechas registradas</option>';
				}
				if(entrada.puesto.length > 0){
					entrada.puesto.forEach(function(puesto){
						
						comboPuesto += '<option value="'+puesto.pk_puesto+'">'+puesto.puesto+'</option>';
						
					});
				} else {
					
					comboPuesto = '<option value="0">No hay ex&aacute;menes registrados</option>'
				}
					
				$("#fecha").append(comboFecha);
				$("#examen").append(comboPuesto);
			}).fail(function(error){
				console.log(error);
			});
		};
		
		function reporte(){
			
			var fecha = $("#fecha");
			var examen = $("#examen");
			var banEncuesta = $("input:checkbox").is(':checked') ? 1 : 0;
			
			if(examen.val() != 0){
				if(fecha.val() != 0 ){
					
					window.open('php/reporte_excel_examenes.php?examen='+examen.val()+'&fecha='+fecha.val()+'&banEncuesta='+banEncuesta, '_blank');
					ocultar();
				} else {
					
					alert("Debe seleccionar una fecha")
					fecha.focus();
				}
				
			} else {
					
				alert("Debe seleccionar un examen")
				examen.focus();
			}
		};
		
		function mostrar(){
			
			$("#mostrarReporte").hide();
			$("#ocultarReporte").show();
			$("#bloqueExcel").show();
		};
		function ocultar(){
			
			$("#fecha").val(0);
			$("#examen").val(0);
			$("#mostrarReporte").show();
			$("#ocultarReporte").hide();
			$("#bloqueExcel").hide();
		};
		
		$(function () {
			
			$("input:checkbox").on( 'change', function() {
				/*if( $(this).is(':checked') ) {
					// Hacer algo si el checkbox ha sido seleccionado
					alert("El checkbox con valor " + $(this).val() + " ha sido seleccionado");
				} else {
					// Hacer algo si el checkbox ha sido deseleccionado
					alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
				}*/
				var nombre = $("#nombre").val();
				nombre = nombre.trim();
				resultados(nombre);
				mesYexamen();
			});
			$("#mostrarReporte").on("click",mostrar);
			$("#ocultarReporte").on("click",ocultar);
      	    $("#reporte").on("click",reporte);	
			  mesYexamen();
			  resultados('');
			$("#imprimir").on('click',imprimir);
			
			$("#form-send").submit(function(event){
				
				event.preventDefault();
				var nombre = $("#nombre").val();
				nombre = nombre.trim();
				resultados(nombre);
					
			});
				
		});