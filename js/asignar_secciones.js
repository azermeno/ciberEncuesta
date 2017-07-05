//Es para obtener los nombres de id de la columna estado y porder modificar su texto
var campoEstado = [];
var prioridad = [];
var banIdentificador = false; 
	function obtenerPuestos(){
		var selectExamen ='';
		var selectEncuesta ='';
		
		campoEstado = [];
		prioridad = [];
		$.ajax({
				  method: "POST",
				  url: "php/obtener_unidades_activas_mtd.php",
				  dataType: "json"
				}).done(function(data){
					
					  var unidadesMes ='';
					  var secciones ='';
					  data.unidadesWs.forEach(function(entry){
						  
						  unidadesMes += '<option value="'+entry.req_codigo+'">'+entry.txtNombre+'</option>';
							
					   });
						 
						$("#unidad").append(unidadesMes);
						$("#unidad").selectpicker('refresh');
					     llenarTabla(data.secciones);
						
						
					}).fail(function(error){
					  
						alert("Por el momento no esta disponible el servicio, intente m\u00E1s tarde");
					  
					});
	}
	
	function llenarTabla(secciones){
		var fila = "";
		 $("#secciones").empty();
		 campoEstado = [];
		 secciones.forEach(function(entry){
			 
				fila += '<tr class="warning"><td><b>'+entry.puesto+'</b></td><td><span id="'+entry.pk_puesto+'"></span></td><td id="prioridad'+entry.pk_puesto+'">'+entry.prioridad+'</td><td><button id="agregar'+entry.pk_puesto+'" type="button" class="btn btn-info actualizar" data-codigo="'+entry.pk_puesto+'" data-accion="1"   			style="width:45%;text-align:center"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button><button id="quitar'+entry.pk_puesto+'" type="button" class="btn btn-danger actualizar" data-codigo="'+entry.pk_puesto+'" data-accion="0" style="width:45%;text-align:center"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></td></tr>';
			 
				campoEstado.push(entry.pk_puesto);
			
			 prioridad['"'+entry.pk_puesto+'"'] = entry.prioridad;
			
		  });
						 
						 $("#secciones").append(fila);
						
						$(".actualizar").on("click",agregarQuitar);
						
	};
	
	function agregarQuitar(){
		//es pk_puesto
		 var seccion = $(this).attr("data-codigo");
		 var texto = $("#"+seccion);
		 //en accion; 0 se elimina, 1 de agrega
		 var accion = $(this).attr("data-accion");
		
		 var unidad = $("#unidad").val();
		 var estado = $('input[name=general]:checked').val();
		
		 if(estado == 0 && unidad == ''){
			 
			 alert("Debe seleccionar una unidad");
		 } else {
		 
		 $.ajax({
					method : "POST",
					url : "php/guardar_empaquetado_mtd.php",
					dataType : "json",
					data : {unidad:unidad,estado:estado,seccion:seccion,accion:accion}
				}).done(function(data){
					
					if(data.asignado == true){
						//alert(seccion);
						if(accion == 0){
							//alert("Se quito correctamente");	
							 if(estado == 0){
								 texto.css("color","red").html('Sin asignar');
								
								 $("#agregar"+seccion).show();
								 $("#quitar"+seccion).hide();
							    }  else {
								 
								   texto.css("color","red").html('Se quit&oacute; correctamente');
								   
								    setTimeout(function(){
										
									  texto.html('');
									
									},3000);
								}
							
						} else {
							
							 if(estado == 0){
								 texto.css("color","blue").html('Asignado');
								
								 $("#agregar"+seccion).hide();
								 $("#quitar"+seccion).show();
							 } else {
								 
								  texto.css("color","blue").html('Agregado correctamente');
								  setTimeout(function(){
										
									  texto.html('');
										
									},3000);
								}
							}
					 						
					} else if(data.asignado == false){
						
						if(estado == 1 ){
							
							texto.css("color","black").html('La acci&oacute;n ya se aplic&oacute;');
							  setTimeout(function(){
										
									  texto.html('');
									
									},3000);
						} else {
							
							alert("Error al asignar secci\u00F3n");					
							
						}
				
					}
				  					
				}).fail(function(){
					
					alert("Por el momento no se encuentra activa la funcionalidas solicitada, intente m\u00E1s tarde");
					
				});
		 }
				
	};
	
		
$(function () {
		
		obtenerPuestos();
		
	   $("#unidad").change(function(){
					
			 var unidad = $("#unidad").val();
			 var estado = $('input[name=general]:checked').val();
			
			 if(estado == 0 && unidad !=''){
				$.ajax({
					method : "POST",
					url : "php/obtener_empaquetado_mtd.php",
					dataType : "json",
					data : {unidad:unidad,estado:estado}
				}).done(function(data){
					
					var banderaEncontrado;
					campoEstado.forEach(function(entry){
					
						banderaEncontrado = false;
							
						data.empaquetado.forEach(function(dato){
							
							if(entry == dato.fk_puesto){
								banderaEncontrado = true;
							 if(dato.activo == 1){
							 $("#"+entry).css("color","blue");
							 $("#"+entry).html('Asignado');
							 $("#agregar"+entry).hide();
						     $("#quitar"+entry).show();
							 } else {
							 $("#"+entry).css("color","red");
							 $("#"+entry).html('Sin asignar');
							 $("#quitar"+entry).hide();
							 $("#agregar"+entry).show();
								 
							 }
							}
						});
							if(banderaEncontrado == false){
								
						  	 $("#"+entry).css("color","red");
							 $("#"+entry).html('Sin asignar');
							 $("#quitar"+entry).hide();
							 $("#agregar"+entry).show();
								
							}
				  
					});
					
				}).fail(function(){
					
					alert("Por la funcionalidad no esta disponible, intente en otro momento");
				});
			 } else {
				 
				 alert("Debe seleccionar una unidad");
			 }
		
		});
			
		$('input[name="general"]').change(function(){
			
			var general = $(this).val();
			$(".actualizar").show();
			if(general == 1){
				
				$("#actualiza").show();
				
				$("#unidad").val('');
				$("#unidad").selectpicker('refresh');
				$("#ocultar").hide();
			
				campoEstado.forEach(function(entry){
					
					$("#"+entry).html('');
				  
				});
			} else {
				
				banderaGuardarCamio = false;
				acomodarPrioridad(banderaGuardarCamio);
				$("#actualiza").hide();
				$("#guardarCambios").hide();
				campoEstado.forEach(function(entry){
					
					$("#"+entry).html('');
				  
				});
				$("#ocultar").show();
			}
		});
		
		$("#actualiza").on("click",habilitarSubmit);
		$("#cancelar").on("click",cancelar); 
		
		function acomodarPrioridad(editable){
			
			campoEstado.forEach(function(valor){
				
				if(editable){
					
					$("#prioridad"+valor).html('<input type="number" name="'+valor+'" style="text-align:center;width:55px;" value="'+prioridad['"'+valor+'"']+'">');
				} else {
					
					$("#prioridad"+valor).html(prioridad['"'+valor+'"']);
					
				}
					
		    });
			
		};
		function cancelar(){
			
			$("#actualiza").show();
			$("#guardarCambios").hide();
			$(".actualizar").show();
			acomodarPrioridad(false);
			
		};
		function habilitarSubmit(){
			     
				$("#actualiza").hide();
				$("#guardarCambios").show();
				$(".actualizar").hide();
				var indice = "";
				acomodarPrioridad(true);
			
		};
		
		$("#form-send").submit(function(event){
			
			event.preventDefault();
			var serializada = $(this).serialize();
			
			$.ajax({
				url: "php/update_prioridad_mtd.php",
				method : "POST",
				dataType : "json",
				data : serializada
				
			}).done(function(respuesta){
				
				 llenarTabla(respuesta.secciones);
			}).fail(function(){
				
				alert("Funcionalidad no desponible por el momento, intente m\u00E1s tarde");
				
			});
		});
		
	});
									
	