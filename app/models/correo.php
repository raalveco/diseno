<?php
	class Correo extends ActiveRecord{
		public static function get($codigo){
		    $correo = Correo::consultar("codigo = '".$codigo."'");
			return $correo;
		}
	}
?>