var informacion = [];
var ponderaciones = [];
var banIdentificador = false;
var idPreguntaRespesta = ''; 

	function obtenerPuestos(){
		var selectExamen ='';
		var selectEncuesta ='';
		$.ajax({
				  method: "POST",
				  url: "php/obtener_todosLosPuestos_mtd.php",
				  dataType: "json"
				  }).done(function(data){
					
					  limpiarPuesto()
					  var fondo ='';
					  var interno ='';
					  data.puesto.forEach(function(entry){
						  if(entry['activo']== 0 ){ 
							  
							  fondo='style="background: rgb(217,83,79); color:black;"';
							} else {
							  
							  fondo='style="background: white; color:black;"';
							}
							if(entry['interno']==0){
							  
							  interno = ' -->  Examen Externo.';
							} else {
							  
							  interno = ' -->  Examen Interno.';
							}
						  informacion[entry['pk_puesto']] = entry['Preguntas'] +'.'+ entry['limite_minutos']+'.'+ entry['activo'] ;
							if(entry['conPromedio']==1){
								selectExamen += '<option '+ fondo+' value="'+entry['pk_puesto']+'" id="p'+entry['pk_puesto']+'">'+entry['puesto']+interno+'</option>'
							} else {
								selectEncuesta += '<option '+ fondo+' value="'+entry['pk_puesto']+'" id="p'+entry['pk_puesto']+'">'+entry['puesto']+'</option>'
							}
						  
					  });
					  if(banIdentificador == false){
						  data.seccion.forEach(function(entry1){
							  
							  $("#identificadoresExistenetes").append('<option value="'+entry1.seccion+'">'+entry1.seccion+'</option>');
						  });
						  banIdentificador = true;
					  }
						  $("#puestoExamen").append(selectExamen);
						 
						  $("#puestoEncuesta").append(selectEncuesta);
					 
					
				  }).fail(function(){
					  
					  alert("Por el momento no esta disponible el servicio, intente m\u00E1s tarde");
					  
				  });
	}
	function limpiarPuesto(){
		
		$("#puestoExamen").empty();
		$("#puestoExamen").append(
		'<option value="0" style="background: white; color:black;">&Aacutereas a seleccionar</option>'
		);
		$("#puestoEncuesta").empty();
		$("#puestoEncuesta").append(
		'<option value="0" style="background: white; color:black;">Encuesta a seleccionar</option>'
		);
		
		
	}
	function verEstado(encuesta){
		$("#switch-state").bootstrapSwitch();
		$("#verEstado"+encuesta).show();
		$('#switch-state').on('switchChange.bootstrapSwitch', function(event, state) {
			$.blockUI({ 
			message: 'Actualizando estado...',	
			css: {
				border: 'none', 
				padding: '15px', 
				backgroundColor: '#000', 
				'-webkit-border-radius': '10px', 
				'-moz-border-radius': '10px', 
				opacity: .5, 
				color: '#fff' 
			} 
		}); 
		//console.log(state); // true | false
				  var id = $(this).attr('data-id');
				  $.ajax({
				  method: "POST",
				  url: "php/actualizar_estado_mtd.php",
				  dataType: "json",
				  data:{id:id,estado:state}
				  }).done(function(data){
										  
					
					if(data.status==true){
						
						
						setTimeout(function(){
						$("#puestoExamen").css("background","white");
						$("#puestoExamen").css("color","black");
						$("#verEstado").hide();
						$("#preguntas").html('');
						$("#estado").html('');
						$("#puestoEncuesta").css("background","white");
						$("#puestoEncuesta").css("color","black");
						$("#verEstadoEncuesta").hide();
						$("#preguntasEncuesta").html('');
						$("#estadoEncuesta").html('');
						$.unblockUI();
						obtenerPuestos();	
							
						alert(data.msg);
						}, 1500);
						
					} else {
						$("#verEstado").hide();
						$("#preguntas").html('');
						$("#estado").html('');
						$("#verEstadoEncuesta").hide();
						$("#preguntasEncuesta").html('');
						$("#estadoEncuesta").html('');
						$.unblockUI();
						alert("Error al cambiar de estado el puesto");
					}
					 
					
				  }).fail(function(){
					  
					  alert("Por el momento no esta disponible el servicio, intente m\u00E1s tarde");
					  $.unblockUI();
				  });
		
		});
	}
			
		function copiarAlPortapapeles(id_elemento) {
		
					  var aux = document.createElement("input");
					  aux.setAttribute("value", document.getElementById("urlParaCopiar").value + document.getElementById(id_elemento).innerHTML);
					  document.body.appendChild(aux);
					  aux.select();
					  document.execCommand("copy");
					  document.body.removeChild(aux);
					  
		}
		
		function modificarPreguntaRespuesta(){
			
			console.log($(this).attr("id"));
			
			idPreguntaRespesta = $(this).attr("id").substring(1);
			console.log(idPreguntaRespesta);
			console.log($("#"+idPreguntaRespesta).html());
			console.log($("#"+idPreguntaRespesta).text());
			
			//$("#editor1").html($("#"+id).html());
			CKEDITOR.instances['editor1'].setData($("#"+idPreguntaRespesta).html());
			console.log($("#editor1").html());
			//inicializaCKeditor();
			showLightbox();
			
		}
		
		function guardarPreguntaRespuesta(){
			
			alert("guardar");
			var elementoHTML = document.getElementById(idPreguntaRespesta);
			elementoHTML.innerHTML = CKEDITOR.instances['editor1'].getData();
			console.log(CKEDITOR.instances['editor1'].getData());
			//elementoHTML.focus();
			//elementoHTML.scrollIntoView();
			
			$.ajax({
				method:"POST",
				url:"php/editar_pregunta_respuesta_mtd.php",
				dataType:"json",
				data: {editado:CKEDITOR.instances['editor1'].getData(),idPreguntaRespesta:idPreguntaRespesta}
			}).done(function(entry){
				
				
			}).fail(function(){
				
				
				
			});
			
			
			hideLightbox();
			idPreguntaRespesta = "";
		}
		
		function showLightbox() {
			document.getElementById('over').style.display='block';
			document.getElementById('fade').style.display='block';
		}
		function hideLightbox() {
			document.getElementById('over').style.display='none';
			document.getElementById('fade').style.display='none';
		}
		
		function mostrarEditarPreguntaRespuesta(){
			
			$(".editarPyR").show();
			$("#cancelarPreguntaRespuesta").show();
			$("#actualiza").hide();
			$("#actualizaPreguntaRespuesta").hide();
		}
		function cancelarPreguntaRespuesta(){
			
			$(".editarPyR").hide();
			$("#cancelarPreguntaRespuesta").hide();
			$("#actualiza").show();
			$("#actualizaPreguntaRespuesta").show();
		}
		
	$(function () {
		
		//CKEDITOR
		CKEDITOR.replace( 'editor1', {
		// Define the toolbar: http://docs.ckeditor.com/#!/guide/dev_toolbar
		// The standard preset from CDN which we used as a base provides more features than we need.
		// Also by default it comes with a 2-line toolbar. Here we put all buttons in a single row.
		toolbar: [
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'styles', items: [ 'Styles', 'Format' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'insert', items: [ 'Image', 'EmbedSemantic', 'Table' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'editing', items: [ 'Scayt' ] }
		],
		
		// Since we define all configuration options here, let's instruct CKEditor to not load config.js which it does by default.
		// One HTTP request less will result in a faster startup time.
		// For more information check http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-customConfig
		customConfig: '',
		// Enabling extra plugins, available in the standard-all preset: http://ckeditor.com/presets-all
		extraPlugins: 'autoembed,embedsemantic,image2,uploadimage,uploadfile',
		/*********************** File management support ***********************/
		// In order to turn on support for file uploads, CKEditor has to be configured to use some server side
		// solution with file upload/management capabilities, like for example CKFinder.
		// For more information see http://docs.ckeditor.com/#!/guide/dev_ckfinder_integration
		// Uncomment and correct these lines after you setup your local CKFinder instance.
		filebrowserBrowseUrl: 'visorImagenes.php',
		filebrowserUploadUrl: 'php/subirFoto.php',
		
		/*********************** File management support ***********************/
		// Remove the default image plugin because image2, which offers captions for images, was enabled above.
		removePlugins: 'image',
		// Make the editing area bigger than default.
		height: 461,
		// An array of stylesheets to style the WYSIWYG area.
		// Note: it is recommended to keep your own styles in a separate file in order to make future updates painless.
		//contentsCss: [ 'https://cdn.ckeditor.com/4.6.1/standard-all/contents.css', 'mystyles.css' ],
		// This is optional, but will let us define multiple different styles for multiple editors using the same CSS file.
		//bodyClass: 'article-editor',
		// Reduce the list of block elements listed in the Format dropdown to the most commonly used.
		//format_tags: 'p;h1;h2;h3;pre',
		// Simplify the Image and Link dialog windows. The "Advanced" tab is not needed in most cases.
		//removeDialogTabs: 'image:advanced;link:advanced',
		// Define the list of styles which should be available in the Styles dropdown list.
		// If the "class" attribute is used to style an element, make sure to define the style for the class in "mystyles.css"
		// (and on your website so that it rendered in the same way).
		// Note: by default CKEditor looks for styles.js file. Defining stylesSet inline (as below) stops CKEditor from loading
		// that file, which means one HTTP request less (and a faster startup).
		// For more information see http://docs.ckeditor.com/#!/guide/dev_styles
		
	} );
	//Termina CDEDITOR
		
			obtenerPuestos();
			
		$("#actualizaPreguntaRespuesta").on("click",mostrarEditarPreguntaRespuesta);	
		$("#cancelarPreguntaRespuesta").on("click",cancelarPreguntaRespuesta);	
	
	   $("#form-send").submit(function(event){
		
			 event.preventDefault();
			 //alert("En construcci\u00F3n");
			 var puestoExamen = $("#puestoExamen").val();
			if(puestoExamen == 0){
				
				alert("Debe seleccionar un \u00E1rea");
				$("#puestoExamen").focus();
				return false;
			} else {
					$.ajax({
						method:"POST",
						url: "php/obtener_todasLasPreguntas_mtd.php",
						dataType:"json",
						data:{puesto:puestoExamen}
					
					}).done(function(data){
				
						var cuestionario = '';
						
						if(data.preguntas.length > 0){
							$("#preguntas").html('');
							
									 verEstado('');
									 
							var numero = 1;
							var correcta = '';
							data.preguntas.forEach(function(entry){
								if(typeof(entry['area']) != 'undefined'){
										if(cuestionario==''){
											cuestionario += '<div class="well well-sm" style="text-align:center">';
											
										} else {
											
											cuestionario += '</div><div class="well well-sm" style="text-align:center">';
										}
									cuestionario += '<h2>'+ entry['area'] +'</h2>';
								} else if(typeof(entry['fk_area']) != 'undefined'){ //es pregunta
										
									cuestionario += '</div><div class="well well-sm">';
									cuestionario += '<h3>'+numero+'.- '+entry['pregunta']+'</h3>';
									numero++;
								} else { //es respuesta
									if(entry['correcta']==1){
										
										correcta = '<span style="color:green" class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
									} else {
										
										correcta = '<span style="color:red" class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
									}
									cuestionario += '<h4><label for="'+entry['pk_respuesta']+'">'+ correcta +'&nbsp;&nbsp;'+entry['respuesta']+'</label></h4>';
									
									
								}
							});
								cuestionario += "</div>";
							
							$("#preguntas").append(cuestionario);
							$("#preguntas").show();
							
						} else {
							
							alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente.)");
					
							
						}
			
		
					}).fail(function(){
							
							alert("Por el momento no esta disponible el apartado de preguntas, intente mas tarde (Puedes intentarlo nuevamente..)");
							
						});
				}
		
		
		});
		
	  $("#form-send1").submit(function(event){
		
			 event.preventDefault();
			 //alert("En construcci\u00F3n");
			 var puestoEncuesta = $("#puestoEncuesta").val();
			if(puestoEncuesta == 0){
				
				alert("Debe seleccionar una encuesta");
				$("#puestoEncuesta").focus();
				return false;
			} else {
				$.ajax({
					method:"POST",
					url: "php/obtener_todasLasPreguntas_mtd.php",
					dataType:"json",
					data:{puesto:puestoEncuesta}
					
				}).done(function(data){
					
				    var cuestionario = '';
					
					if(data.preguntas.length > 0){
						$("#preguntasEncuesta").html('');
						
						verEstado('Encuesta');
								 
						var numero = 1;
						
						var respuesta = '';
						ponderaciones = [];
						data.preguntas.forEach(function(entry){
							if(typeof(entry['area']) != 'undefined'){
								if(cuestionario==''){
									
									cuestionario += '<div class="well well-sm ponderacion" style="text-align:center"><h3><b>Ponderaci&oacute;n de la secci&oacute;n:&nbsp; <span id="A'+entry.fk_puesto+'">'+entry.puestoPonderacion+'</span></b></h3>';
									ponderaciones['A'+entry.fk_puesto] = entry.puestoPonderacion;
									
								} else {
									
									cuestionario += '</div><div class="well well-sm" style="text-align:center">';
									
								}
								cuestionario += '<h2>'+ entry['area'] +'</h2>';
								
							} else if(typeof(entry['fk_area']) != 'undefined'){ //es pregunta
								
								if(numero > 1){
									cuestionario += '</table></div>';
									
								}
								cuestionario += '</div><div class="well well-sm">';
								cuestionario += '<h3><button type="button" class="btn btn-warning editarPyR" id="PPP'+entry.pk_pregunta+'" style="display:none"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;&nbsp;&nbsp;'+numero+'.- <span id="PP'+entry.pk_pregunta+'">' + entry['pregunta'] + '&nbsp;&nbsp;&nbsp;&nbsp;</span><label class="ponderacion">Ponderaci&oacute;n: &nbsp;<span id="P'+entry.pk_pregunta+'">'+entry.preguntaPonderacion+'</span></label></h3>';
								cuestionario += '<div class="table-responsive"><table class="table">';
								numero++;
								ponderaciones['P'+entry.pk_pregunta] = entry.preguntaPonderacion;
								
							} else { //es respuesta
								
								respuesta = entry.respuesta == '.' ? "(SE PERMITE COMENTARIO)" : entry.respuesta;
									
								cuestionario += '<tr><th><h5><label for="'+entry['pk_respuesta']+'"><button type="button" class="btn btn-warning editarPyR" id="RRR'+entry.pk_respuesta+'" style="display:none"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;&nbsp;&nbsp;'+
								'<span style="color:blue" class="glyphicon glyphicon-unchecked" aria-hidden="true"></span>&nbsp;<span id="RR'+entry.pk_respuesta+'">'+respuesta+'</span></label></h5></th><th class="ponderacion" style="weight:15%">Ponderaci&oacute;n: &nbsp;<span id="R'+entry.pk_respuesta+'">'+entry.respuestaPonderacion+'</span></th></tr>';
								
								ponderaciones['R'+entry.pk_respuesta] = entry.respuestaPonderacion;
								
							}
						});
							cuestionario += "</table></div>";
						
						$("#preguntasEncuesta").append(cuestionario);
						$("#preguntasEncuesta").show();
					} else {
						
						alert("Por el momento no esta disponible el apartado de encuestas, intente m\u00E1s tarde (Puedes intentarlo nuevamente.)");
					
					}
		
					$(".editarPyR").on("click",modificarPreguntaRespuesta);
				}).fail(function(){
						
						alert("Por el momento no esta disponible el apartado de encuestas, intente m\u00E1s tarde (Puedes intentarlo nuevamente..)");
					
				    });
			}
				
				
		});
		
		$("#actualiza").on("click",habilitarSubmit);
		
		function habilitarSubmit(){
			     
				$("#actualiza").hide();
				$("#actualizaPreguntaRespuesta").hide();
				$("#guardarCambios").show();
				 acomodarPrioridad(true);
			
		};
		
		$("#cancelar").on("click",cancelar); 
		
		function cancelar(){
			
			$("#actualiza").show();
			$("#actualizaPreguntaRespuesta").show();
			$("#guardarCambios").hide();
			acomodarPrioridad(false);
			 			
		};
		
		$("#guarda").on("click",mandarPonderaciones);
		
		function mandarPonderaciones(){
			
		 $("#form-ponderacion").submit();	
			
		};
		
		$("#form-ponderacion").submit(function(event){
			
			event.preventDefault();
			var ponderacion = $(this).serialize();
						
			$.ajax({
				method : "POST",
				url : "php/actualizar_ponderacion_mtd.php",
				dataType : "json",
				data : ponderacion
			}).done(function(data){
				
				ponderaciones = data.ponderacion;
				alert("Se realizo el cambio correctamente");
				cancelar();
				
			}).fail(function(error){
				
				console.log(error);
				alert("Por el momento no esta activa esta funcionalidad, intente m\u00E1s tarde");
				
			});
			
			
		});
		
		function acomodarPrioridad(editable){
		
			var valor = '';			
			for(var index in ponderaciones) { 
				if (ponderaciones.hasOwnProperty(index)) {
					
					valor = ponderaciones[index];
					
					if(editable){

						$("#"+index).html('<input type="number" name="'+index+'" style="text-align:center;width:55px;" value="'+valor+'">');
					} else {

						$("#"+index).html(valor);

					}
				}
			} 
			
		};
		
				 $("#puestoExamen").change(function(){
					    
					    $("#puestoEncuesta").val(0);
						$("#verEstado").hide();
						$("#preguntas").html('');
						$("#verEstadoEncuesta").hide();
						$("#preguntasEncuesta").html('');
						$("#estadoEncuesta").html('');
						$("#estadoEncuestaBoton").html('');
						  if($("#puestoExamen").val()!=0){
								  var id = $(this).val();
								  var datos = informacion[id].split('.');
								  if(datos[2]==0){
									 
								  $("#puestoExamen").css("background","rgb(217,83,79)");
								  $("#puestoExamen").css("color","black");
								  $("#estado").html('Tiempo : '+datos[1]+' minutos');
								  $("#estadoBoton").html('Estado:   <input data-id="'+id+
								  '" id="switch-state" type="checkbox" data-on-text="Activo" data-off-text="Inactivo" data-on-color="info" data-off-color="danger">');
								  } else {
									  
									$("#puestoExamen").css("background","white");
									$("#estado").html('Tiempo : '+datos[1]+' minutos ');
									$("#estadoBoton").html('Estado   <input data-id="'+id+
									'" id="switch-state" type="checkbox" data-on-text="Activo" data-off-text="Inactivo" data-on-color="info" data-off-color="danger" checked>');
								  }
							} else {
								
							   $("#puestoExamen").css("background","white");
								$("#estado").html('');
						  }
					
				});
				
								
				function getAbsolutePath() {
					var loc = window.location;
					var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
					return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
				}
				
				
								
				$("#puestoEncuesta").change(function(){
				
						$("#actualiza").show();
						$("#guardarCambios").hide();
						$("#identificadoresExistenetes").val('0');
						$("#inputText").val('');
						$("#puestoExamen").val(0);
						$("#verEstadoEncuesta").hide();
						$("#preguntasEncuesta").html('');
						$("#verEstado").hide();
						$("#preguntas").html('');
						$("#estado").html('');
						$("#estadoBoton").html('');
					  if($("#puestoEncuesta").val()!=0){
							  var id = $(this).val();
							  var datos = informacion[id].split('.');
							  var urlAbsoluta = getAbsolutePath();
							  
							  if(datos[2]==0){
								 
							  $("#puestoEncuesta").css("background","rgb(217,83,79)");
							  $("#puestoEncuesta").css("color","black");
							  $("#estadoEncuesta").html('URL :<br><del>'+urlAbsoluta+'<br>encuesta.php?info='+btoa(id)+
								'</del>');
								$("#estadoEncuestaBoton").html('Estado:   <input data-id="'+id+
							  '" id="switch-state" type="checkbox" data-on-text="Activo" data-off-text="Inactivo" data-on-color="info" data-off-color="danger">');
							  $("#urlParaCopiar").val(urlAbsoluta+'encuesta.php?info='+btoa(id)+
								'&identificador=');
							  } else {
								  
								$("#puestoEncuesta").css("background","white");
								$("#estadoEncuesta").html('URL :<br>'+urlAbsoluta+'<br>encuesta.php?info='+btoa(id)+
								'<br>&identificador=<span id="identificador"></span>');
								$("#estadoEncuestaBoton").html('Estado   <input data-id="'+id+
								'" id="switch-state" type="checkbox" data-on-text="Activo" data-off-text="Inactivo" data-on-color="info" data-off-color="danger" checked>');
								$("#urlParaCopiar").val(urlAbsoluta+'encuesta.php?info='+btoa(id)+
								'&identificador=');
							  }
							  							 
						} else {
							
						   $("#puestoEncuesta").css("background","white");
							$("#estadoEncuesta").html('');
					    }
		
				});
				$("#identificadoresExistenetes").change(function(){
						
					 // $("#identificadoresExistenetes").val('0');
					  $("#inputText").val('');
							 
					  if($("#identificadoresExistenetes").val()!=0){
							  var textoSelect = $(this).val();
							 
							$("#identificador").text(btoa(textoSelect));
						} else {
							  
							$("#identificador").text("");
						  
					    }
		
				});
				$('input').keypress(function(e){
				if(e.which == 13){
				  return false;
				}
				});
				function identificador(){
					$("#identificadoresExistenetes").val(0)
					var value = $(this).val().trim().replace(/(\s\s*)/g,'_');
					
					if(value == ''){
					 $("#identificador").text(btoa(value));
					} else {
					 $("#identificador").text(btoa(value));
					}
					
				}
				
				$("#inputText").on('keyup', identificador);
				
				
	});