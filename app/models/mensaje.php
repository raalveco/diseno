<?php
	class Mensaje extends ActiveRecord{
		public static function get($codigo){
		    $mensaje = Mensaje::consultar("codigo = '".$codigo."'");
			return $mensaje ? $mensaje -> mensaje : "MENSAJE NO CONFIGURADO";
		}
	}
?>