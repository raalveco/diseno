<?php
	class Deadline extends ActiveRecord{
		public static function vencimiento(){
			$vencimiento = Deadline::buscar("deadline >= '".date("Y-m-d")."' AND vencimiento > '".date("Y-m-d")."'","vencimiento ASC");
			
			return $vencimiento -> vencimiento;
		}
	}
?>