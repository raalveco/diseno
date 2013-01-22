<?php

	class PedidosController extends ApplicationController {
		
		public function que_sigue(){
			Load::lib("mensajes");
			
			$this -> mensaje = Mensajes::consultar("MENSAJE_QUE_SIGUE");
		}
		
		public function reporte($mensaje) {
    		$this -> pedidos = Pedido::reporte();
			
			switch($mensaje){
				case "eliminado": $this -> mensaje = "El pedido ha sido eliminado correctamente."; break;
				case "registrado": $this -> mensaje = "El pedido ha sido registrado correctamente."; break;
				case "anticipo_registrado": $this -> mensaje = "La seña ha sido registrada correctamente."; break;
			}
			
			$this -> crm = false;
    	}
		
		public function pendientes($mensaje){
			$this -> render("reporte");
			
			switch($mensaje){
				case "actualizado": $this -> mensaje = "Los registros fueron actualizados con información del CRM."; break;
				case "enviado": $this -> mensaje = "El Mensaje ha sido enviado correctamente."; break;
			}
			
			$this -> pedidos = Pedido::reporte("enviado = 'NO'");
			
			$this -> crm = true;
		}
		 
		public function consulta($id, $mensaje) {
    		$this -> pedido = Pedido::consultar($id);
			
			switch($mensaje){
				case "modificado": $this -> mensaje = "El pedido ha sido modificado correctamente."; 
			}
    	}
		
		public function modificar() {
			$this -> render(null,null);
			
    		$pedido = Pedido::consultar($this -> post("id"));
			
			$pedido -> crm_numero = $this -> post("crm_numero");
			$pedido -> nombre = $this -> post("nombre");
			$pedido -> total = $this -> post("total");
			$pedido -> anticipo = $this -> post("anticipo");
			$pedido -> saldo = $this -> post("saldo");
			
			$pedido -> guardar();
			
			$this -> redirect("pedidos/consulta/".$pedido -> id."/modificado");
    	}
    	
		public function eliminar($id) {
    		$this -> render(null,null);
			
			$pedido = Pedido::consultar($id);
			
			$pedido -> eliminar();
			
			$this -> redirect("pedidos/reporte/eliminado");
    	}
    	
    	public function actualizarCRM(){
			$this -> render(null,null);
			
			Pedido::cargarPedidosCRM();
			
			$this -> redirect("pedidos/pendientes/actualizado");
		}
		
		public function generarCorreo($pedido){
			$this -> pedido = Pedido::consultarCorreo($pedido);
		}
		
		public function enviarCorreo($id){
			$pedido = Pedido::consultar($id);
			
			$pedido -> tipo_diseno = $this -> post("tipo");
			
			$pedido -> guardar();
			
			$this -> pedidoInfo = $pedido;				
		}
		
		public function confirmarCorreo($id){
			$this -> render(null, null);
			
			Load::lib("mensajes");
			
			$pedido = Pedido::consultar($id);
			
			$url = "http://www.amecasoft.com.mx/diseno/uploader/index/".$pedido -> crm_cifrado;
			$variables = array("CONTACTO" => $pedido -> nombre, "URL" => $url,"PEDIDO" => $pedido -> crm_numero);
			
			$correo = Mensajes::correo("CORREO_INICIAL", $variables);
			$correo -> enviarCorreo("raalveco@gmail.com");
			
			$pedido -> enviado = "SI";
			$pedido -> guardar();
			
			$this -> redirect("pedidos/pendientes/enviado");
		}
		
		public function anticipo($ov_cifrada){
			$this -> pedido = Pedido::consultar("crm_cifrado = '".$ov_cifrada."'");
		}
		
		public function registrarAnticipo($tipo){
			//$this -> render(null,null);
			
			$pedido = Pedido::consultar($this -> post("id_pedido"));
			
			Load::lib("formato");
			
			$anticipo = new Anticipo();
			
			$anticipo -> pedido_id= $this -> post("id_pedido");
			$anticipo -> sucursal = $this -> post("sucursal");
			$anticipo -> importe = Formato::noDinero($this -> post("importe"));
			$anticipo -> numero_transferencia = $this -> post("numero");		
			$anticipo -> tipo = strtoupper($tipo);							
			if($tipo == "deposito"){
				$anticipo -> fecha = Formato::FechaDB($this -> post("fechaDepo"));
			}
			if($tipo == "transferencia"){
				$anticipo -> fecha = Formato::FechaDB($this -> post("fechaTrans"));
			}
					
			$anticipo -> guardar();
			
			//ACTUALIZANDO TABLA DE PEDIDOS
			$pedido -> anticipo = ($pedido -> anticipo) + ($anticipo -> importe);
			$pedido -> saldo = ($pedido -> total) - ($pedido -> anticipo);
			
			if(!$pedido -> saldo){
				$pedido -> saldo = "0.00";
			}
			
			Load::lib("formato");
			
			Load::lib("mensajes");
			
			$variables = array(
									"PEDIDO" => $pedido -> crm_numero,
									"TIPO" => Formato::capital($tipo),
									"SUCURSAL" => Formato::capital($this -> post("sucursal")),
									"NUMERO" => $this -> post("numero"),
									"FECHA" => Formato::fecha($pedido -> crm_numero),
									"IMPORTE" => Formato::dinero(Formato::noDinero($this -> post("importe")))
							  );
			
			if($tipo == "deposito"){
				$correo = Mensajes::correo("CORREO_SENADIR_DEP", $variables);
			}
			else{
				$correo = Mensajes::correo("CORREO_SENADIR_TRA", $variables);
			}
			
			$correo -> enviarCorreo(CORREO_DIRECCION);
			
			$pedido -> guardarCRM();
			
			$variables = array(
									"CONTACTO" => $pedido -> nombre,
									"PEDIDO" => $pedido -> crm_numero,
									"CORREO_DIRECCION" => CORREO_DIRECCION,
									"MINIMO" => Formato::dinero($pedido -> anticipo_minimo)
							  );
			
			if($pedido -> anticipo < $pedido -> anticipo_minimo){
				//SEÑA INCOMPLETA
				
				$this -> mensaje = Mensajes::consultar("MENSAJE_SENA_MALA",$variables);
				
				$correo = Mensajes::correo("CORREO_SENA_MALA",array($variables));
				$correo -> enviarCorreo("raalveco@gmail.com");	
				
				return;
			}
			else{
				if(strtolower($pedido -> diseno_estado) == strtolower("Archivo recibido") || strtolower($pedido -> diseno_estado) == strtolower("Archivos recibidos") || strtolower($pedido -> diseno_estado) == strtolower("Archivo recibidos") || strtolower($pedido -> diseno_estado) == strtolower("Archivos recibido")){
					//SEÑA CORRECTA Y ARCHIVOS SUBIDOS
						
					$variables = array(
									"CONTACTO" => $pedido -> nombre,
									"PEDIDO" => $pedido -> crm_numero,
									"URL" => APLICACION_URL."pedidos/que_sigue"
							  );
						
					$this -> mensaje = Mensajes::consultar("MENSAJE_SENA_ARCHIVOS",$variables);
				
					$correo = Mensajes::correo("CORREO_SENA_ARCHIVOS",$variables);
					$correo -> enviarCorreo("raalveco@gmail.com");	
					
					return;
				}
				else{
					if(strtolower($pedido -> diseno_grafico) == strtolower("Cliente Envia") || strtolower($pedido -> diseno_grafico) == strtolower("Cliente Envía")){
						$variables = array(
									"CONTACTO" => $pedido -> nombre,
									"PEDIDO" => $pedido -> crm_numero,
									"URL" => APLICACION_URL,
									"ENCRIPTADO" => $pedido -> crm_encriptado
							  );
							  
						$this -> mensaje = Mensajes::consultar("MENSAJE_INICIAL_SENADO",$variables);
							  
						$correo = Mensajes::correo("CORREO_INICIAL_SENADO",$variables);
						$correo -> enviarCorreo("raalveco@gmail.com");
						
						return;
					}
					else{
						$variables = array(
									"CONTACTO" => $pedido -> nombre,
									"PEDIDO" => $pedido -> crm_numero,
									"TIPO" => strtoupper($pedido -> tipo_diseno),
									"ENCRIPTADO" => $pedido -> crm_encriptado
							  );
							  
						$this -> mensaje = Mensajes::consultar("MENSAJE_NUEVO_DISENO",$variables);
							  
						$correo = Mensajes::correo("CORREO_NUEVO_DISENO",$variables);
						$correo -> enviarCorreo(CORREO_DIRECCION);
						
						return;
					}
				}
			}
		}
	}	

?>


