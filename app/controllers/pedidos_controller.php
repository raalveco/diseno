<?php

	class PedidosController extends ApplicationController {
		
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
	}	

?>


