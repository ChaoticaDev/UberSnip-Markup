<?php

	class UObject{
		var $attributes;
		var $data;
		
		function attribute($attribute, $update = false){
			if ( $update === false ){
				return $this->attribute[$attribute];
			}else{
				$this->attribute[$attribute] = $update;	
			}
		}
		
		
	}
?>