<?php
	class CorreosController extends ApplicationController{
		public function generar($mensaje = false){
			if($mensaje){
				$this -> mensaje = "El mensaje ha sido enviado correctamente";
			}				
		}
		
		public function enviar(){
			if(!Pedido::existe("crm_numero = '".$this -> post("crm_numero")."'")){
				$pedido = Pedido::cargarArchivo($this -> post("crm_numero") ,APP_PATH."/public/files/saleorder.xml");
			}
			else{
				$pedido = Pedido::consultar(("crm_numero = '".$this -> post("crm_numero")."'"));
			}
			$pedido -> tipo_diseno = $this -> post("tipo");
			$pedido -> guardar();
			$this -> pedidoInfo = $pedido;				
		}
		
		public function confirmar($id){
			$this -> render(null, null);
			$pedido = Pedido::consultar($id);
			$titulo = "Configurar Pedido";
			$mensaje = 'Hola '.$pedido -> nombre.'. <br><br>Por favor entra aquí para subir tu diseño: <br><br><a href="http://127.0.0.1/diseno/uploader/index/'.$pedido -> crm_cifrado.'">Enviar diseño</a><br><br>Numero de pedido: '.$pedido -> crm_numero.'<br><br>Saludos,<br><br>Raul<br>Responsable de ventas';                                       
			$headers = 'From: Ramiro <raalveco@gmail.com>' . "\r\n" .
    					'Reply-To: lizaolaa@gmail.com' . "\r\n";
			
			mail($pedido -> correo, $titulo, $mensaje, $headers);
			$this -> redirect("correos/generar/generado");
					
		}
	}
?>
