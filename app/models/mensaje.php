<?php
	class Mensaje extends ActiveRecord{
		public static function get($codigo){
			return Mensaje::consultar("codigo = '".$codigo."'") -> mensaje;
		}
	}
?>