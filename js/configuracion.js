    
			var codigo = '';
			var unidad = '';
			var ipGlobal = '';
			var mensajeError='';
			var banYaContesto = false;
			
	function getQueryVariable(variable) {
		
	   var query = window.location.search.substring(1);
	      
	      // query = window.atob(query);
	   var vars = query.split("&");
	   for (var i=0; i < vars.length; i++) {
		   vars[i] = vars[i].replace(/=/, "|");
		   var pair = vars[i].split("|",2);
		   if(pair[0] == variable) {
			   return pair[1];
		   }
	   }
	   return false;
	}		
	
	function Browser() {   
    // ---- public properties -----
    this.fullName = 'unknow'; // getName(false);
    this.name = 'unknow'; // getName(true);
    this.code = 'unknow'; // getCodeName(this.name);
    this.fullVersion = 'unknow'; // getVersion(this.name);
    this.version = 'unknow'; // getBasicVersion(this.fullVersion);
    this.mobile = false; // isMobile(navigator.userAgent);
    this.width = screen.width;
    this.height = screen.height;
    this.platform =  'unknow'; //getPlatform(navigator.userAgent);
    
    // ------- init -------    
    this.init = function() { //operative system, is an auxiliary var, for special-cases
        //the first var is the string that will be found in userAgent. the Second var is the common name
        // IMPORTANT NOTE: define new navigators BEFORE firefox, chrome and safari
        var navs = [
            { name:'Opera Mobi', fullName:'Opera Mobile', pre:'Version/' },
            { name:'Opera Mini', fullName:'Opera Mini', pre:'Version/' },
            { name:'Opera', fullName:'Opera', pre:'Version/' },
            { name:'MSIE', fullName:'Microsoft Internet Explorer', pre:'MSIE ' },  
            { name:'BlackBerry', fullName:'BlackBerry Navigator', pre:'/' }, 
            { name:'BrowserNG', fullName:'Nokia Navigator', pre:'BrowserNG/' }, 
            { name:'Midori', fullName:'Midori', pre:'Midori/' }, 
            { name:'Kazehakase', fullName:'Kazehakase', pre:'Kazehakase/' }, 
            { name:'Chromium', fullName:'Chromium', pre:'Chromium/' }, 
            { name:'Flock', fullName:'Flock', pre:'Flock/' }, 
            { name:'Galeon', fullName:'Galeon', pre:'Galeon/' }, 
            { name:'RockMelt', fullName:'RockMelt', pre:'RockMelt/' }, 
            { name:'Fennec', fullName:'Fennec', pre:'Fennec/' }, 
            { name:'Konqueror', fullName:'Konqueror', pre:'Konqueror/' }, 
            { name:'Arora', fullName:'Arora', pre:'Arora/' }, 
            { name:'Swiftfox', fullName:'Swiftfox', pre:'Firefox/' }, 
            { name:'Maxthon', fullName:'Maxthon', pre:'Maxthon/' },
            // { name:'', fullName:'', pre:'' } //add new broswers
            // { name:'', fullName:'', pre:'' }
            { name:'Firefox',fullName:'Mozilla Firefox', pre:'Firefox/' },
            { name:'Chrome', fullName:'Google Chrome', pre:'Chrome/' },
            { name:'Safari', fullName:'Apple Safari', pre:'Version/' }
        ];
    
        var agent = navigator.userAgent, pre;
        //set names
        for (i in navs) {
           if (agent.indexOf(navs[i].name)>-1) {
               pre = navs[i].pre;
               this.name = navs[i].name.toLowerCase(); //the code name is always lowercase
               this.fullName = navs[i].fullName; 
                if (this.name=='msie') this.name = 'iexplorer';
                if (this.name=='opera mobi') this.name = 'opera';
                if (this.name=='opera mini') this.name = 'opera';
                break; //when found it, stops reading
            }
        }//for
        
      //set version
        if ((idx=agent.indexOf(pre))>-1) {
            this.fullVersion = '';
            this.version = '';
            var nDots = 0;
            var len = agent.length;
            var indexVersion = idx + pre.length;
            for (j=indexVersion; j<len; j++) {
                var n = agent.charCodeAt(j); 
                if ((n>=48 && n<=57) || n==46) { //looking for numbers and dots
                    if (n==46) nDots++;
                    if (nDots<2) this.version += agent.charAt(j);
                    this.fullVersion += agent.charAt(j);
                }else j=len; //finish sub-cycle
            }//for
            this.version = parseInt(this.version);
        }
        
        // set Mobile
        var mobiles = ['mobi', 'mobile', 'mini', 'iphone', 'ipod', 'ipad', 'android', 'blackberry'];
        for (var i in mobiles) {
            if (agent.indexOf(mobiles[i])>-1) this.mobile = true;
        }
        if (this.width<700 || this.height<600) this.mobile = true;
        
        // set Platform        
        var plat = navigator.platform;
        if (plat=='Win32' || plat=='Win64') this.platform = 'Windows';
        if (agent.indexOf('NT 5.1') !=-1) this.platform = 'Windows XP';        
        if (agent.indexOf('NT 6') !=-1)  this.platform = 'Windows Vista';
        if (agent.indexOf('NT 6.1') !=-1) this.platform = 'Windows 7';
        if (agent.indexOf('Mac') !=-1) this.platform = 'Macintosh';
        if (agent.indexOf('Linux') !=-1) this.platform = 'Linux';
        if (agent.indexOf('iPhone') !=-1) this.platform = 'iOS iPhone';
        if (agent.indexOf('iPod') !=-1) this.platform = 'iOS iPod';
        if (agent.indexOf('iPad') !=-1) this.platform = 'iOS iPad';
        if (agent.indexOf('Android') !=-1) this.platform = 'Android';
        
        if (this.name!='unknow') {
            this.code = this.name+'';
            if (this.name=='opera') this.code = 'op';
            if (this.name=='firefox') this.code = 'ff';
            if (this.name=='chrome') this.code = 'ch';
            if (this.name=='safari') this.code = 'sf';
            if (this.name=='iexplorer') this.code = 'ie';
            if (this.name=='maxthon') this.code = 'mx';
        }
        
        //manual filter, when is so hard to define the navigator type
        if (this.name=='safari' && this.platform=='Linux') {
            this.name = 'unknow';
            this.fullName = 'unknow';
            this.code = 'unknow';
        }
        
    };//function
    
    this.init();

}//Browser class


//obtiene la direccion IP:
    function getIPs(callback){
        var ip_dups = {};
    
        //compatibilidad exclusiva de firefox y chrome, el usuario @guzgarcia compartio este enlace muy util: http://iswebrtcreadyyet.com/
        var RTCPeerConnection = window.RTCPeerConnection
            || window.mozRTCPeerConnection
            || window.webkitRTCPeerConnection;
        var useWebKit = !!window.webkitRTCPeerConnection;
    
        //bypass naive webrtc blocking using an iframe
        if(!RTCPeerConnection){
            //NOTE: necesitas tener un iframe in la pagina, exactamente arriba de la etiqueta script
            //
            //<iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
            //<script>... se llama a la funcion getIPs aqui...
            //
            var win = iframe.contentWindow;
            RTCPeerConnection = win.RTCPeerConnection
                || win.mozRTCPeerConnection
                || win.webkitRTCPeerConnection;
            useWebKit = !!win.webkitRTCPeerConnection;
        }
    
        //requisitos minimos para conexion de datos
        var mediaConstraints = {
            optional: [{RtpDataChannels: true}]
        };
    
        var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};
    
        //construccion de una nueva RTCPeerConnection
        var pc = new RTCPeerConnection(servers, mediaConstraints);
    
        function handleCandidate(candidate){
            // coincidimos con la direccion IP
            var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/
            var ip_addr = ip_regex.exec(candidate)[1];
    
            //eliminamos duplicados
            if(ip_dups[ip_addr] === undefined)
                callback(ip_addr);
    
            ip_dups[ip_addr] = true;
        }
    
        //escuchamos eventos candidatos
        pc.onicecandidate = function(ice){
    
            //dejamos de lado a los eventos que no son candidatos
            if(ice.candidate)
                handleCandidate(ice.candidate.candidate);
        };
    
        //creamos el canal de datos
        pc.createDataChannel("");
    
        //creamos un offer sdp
        pc.createOffer(function(result){
    
            //disparamos la peticion (request) al stun server (para entender mejor debemos ver la documentacion de WebRTC.
            pc.setLocalDescription(result, function(){}, function(){});
    
        }, function(){});
    
        //esperamos un rato para dejar que todo se complete:
        setTimeout(function(){
            //leemos la informacion del candidato desde la descripcion local
            var lines = pc.localDescription.sdp.split('\n');
    
            lines.forEach(function(line){
                if(line.indexOf('a=candidate:') === 0)
                    handleCandidate(line);
            });
        }, 1000);
    }
	
	 function validarSessionCodigo(codigoEnviado, unidadObtenida){
		
		 if(codigoEnviado != "" && unidadObtenida != ""){
			$.ajax({
			 url : "php/verificadorCiberEncuesta.php",
			 method : "POST",
			 dataType : "json",
			 async:false, 
             cache:false,
			 data : {codigo:codigoEnviado,unidad:unidadObtenida}
			
			}).done(function(respuesta){
				
				var resultado = "";
				if(respuesta.error == ""){
					resultado = respuesta.validacion.substring(1,respuesta.validacion.length-1).split("|");
					
					if(resultado.length == 3){
						if(resultado[0] == 0){
							
						   $("#mensaje").html("La sesi&oacute;n "+unidadObtenida+" con el c&oacute;digo "+codigoEnviado+" no debe de contestar por que ya contesto o por que no es tiempo de contestar, el rango de fecha es hasta el d&iacute;a 15 y con horario de 7am a 3pm.");
							$(".alert").show();
							banYaContesto = true;
							
						} else {
							
								 var datos = btoa("requiriente="+resultado[2]+"&encuesta="+resultado[1]);
								window.location.replace("/ciberEncuestaPHP/ciberEncuesta.php?"+datos);

						}
					} else {
							mensajeError += codigoEnviado+",";
					
					}
					
				} else {
					
					$("#mensaje").html(respuesta.error);
					
				}
			
			}).fail(function(error){
			
				console.log("Fallo");
				console.log(error);
				console.log(error.statusText);
			});
		} else {
			mensajeError += codigoEnviado+",";
			
		}
		 
	 }
    
	$(function(){
		
		codigo = getQueryVariable('codigo');
		unidad = getQueryVariable('unidad');
		getIPs(function(ip){
			 
			ipGlobal = ip;
			
			  var brw = new Browser();
			  			  
		  $.ajax({
				 url : "php/registro_actividad_mtd.php",
				 method : "POST",
				 dataType : "json",
				 data : {codigo:codigo,sesion:unidad,ip:ip,actividad:"Ingreso autom&aacute;tico por bat",navegador:brw.fullName,nVersion:brw.fullVersion,plataforma:brw.platform,movil:brw.mobile,resolucion: brw.width + 'x' + brw.height}
				
				}).done(function(respuesta){
					
				}).fail(function(error){
				
					console.log("Fallo");
					console.log(error);
					console.log(error.statusText);
				});
		
		});
		
		if(codigo != "" && unidad != ""){
			
			var codigos = codigo.split("-");
			
			for(var i =0; i < codigos.length; i++){
				
				validarSessionCodigo(codigos[i], unidad);
				
			};
			if(banYaContesto === false){
				$("#mensaje").html("La sesi&oacute;n "+unidad+" con c&oacute;digo(s) "+mensajeError+" no tiene los datos correcto.");
				$(".alert").show();
			}
		} else {
			
			$("#mensaje").html("La sesi&oacute;n "+unidad+" con el c&oacute;digo "+codigo+" no tiene los datos correcto.");
			
			
		}
		
		
	});
    
		