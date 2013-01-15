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
			$pedido = Pedido::consultar($id);
			$titulo = "Configurar Pedido";
			$mensaje = 'Hola '.$pedido -> nombre.'. <br><br>Por favor entra aquí para subir tu diseño: <br><br><a href="http://127.0.0.1/diseno/uploader/index/'.$pedido -> crm_cifrado.'">Enviar diseño</a><br><br>Numero de pedido: '.$pedido -> crm_numero.'<br><br>Saludos,<br><br>Raul<br>Responsable de ventas';                                       
			$headers = 'From: Ramiro <raalveco@gmail.com>' . "\r\n" .
    					'Reply-To: lizaolaa@gmail.com' . "\r\n";
			
			@mail($pedido -> correo, $titulo, $mensaje, $headers);
			
			$pedido -> enviado = "SI";
			$pedido -> guardar();
			
			$this -> redirect("pedidos/pendientes/enviado");
		}
	}	

?>


