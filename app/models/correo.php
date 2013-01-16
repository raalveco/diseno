<?php
	class Correo extends ActiveRecord{
		public static function get($codigo){
		    $correo = Correo::consultar("codigo = '".$codigo."'");
			return $correo;
		}
		
		public function headers(){
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '. $this -> remitente . "\r\n" .
    					'Reply-To: '. $this -> remitente . "\r\n";
						
			return $headers;
		}
		
		public function enviarCorreo($correo){
			@mail($correo, $this -> asunto, $this -> mensaje, $this -> headers());
		}
	}
?>