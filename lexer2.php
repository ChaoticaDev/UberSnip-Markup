<?php
	//LEXER: PHP -- HTML UberSnipMarkup
	//CREATED AT: https://ubersnip.com/
	//GITHUB: https://github.com/ubersnip/UberSnip-PHPMarkup
	
	require("UObject.php");
	
	if(isset($_GET['t'])) header("Content-type: text");
	
	//An Array that stores defined variables.
	//$VARIABLE_DATA_HOLDER;
	
	
	$file = fopen("script.ubersnip.html", "r");
	
	$CODE = "";
	while(!feof($file)){
		$line = fgets($file);
		$CODE.= parse_lexer_syntax(trim(preg_replace('/\t+/', '', $line)));
		echo parse_lexer_syntax(trim(preg_replace('/\t+/', '', $line))) . "\n";
	}
	
	eval ( $CODE );
	
	//var_dump( $Users );
	
	function parse_lexer_syntax($val){
			include("preg_match_def.php");
		$EVAL_RES = "";
		//CHECK IF IS FUNCTION
		
		if ( strpos ( $val, "<function" ) === 0 ){
			
			//CHECK FOR FUNCTION NAME
			if (preg_match($MATCH_NAME, $val, $F_NAME)) {
				//echo $F_NAME[1]."<br />";  
				$EVAL_RES .= "function " . $F_NAME[1] . "(";
				
			} else {
				die("ERROR: NO FUNCTION NAME SUPPLIED!");
			}
			
			//CHECK FOR FUNCTION PARAMETERS
			if (preg_match($MATCH_PARAMS, $val, $m)) {
				//echo $m[1]."<br />";   
				
				//SEPARATE PARAMS
				preg_match_all('~(["\'])([^"\']+)\1~', $m[1], $F_PARAMS);
				
				//VARIABLE TO TEMPORARILY STORE PARAMETER
				$TMP_PARAM = "";
				for ( $i = 0; $i < count ( $F_PARAMS[0] ); $i++ )
				{
					//DETERMINE WHETHER PARAM REFERENCES ANOTHER VARIABLE, OR A STRING
					if ( strpos($F_PARAMS[0][$i], "$", 0 ) === 1 || strpos($F_PARAMS[0][$i], "!", 0 ) === 1 || strpos($F_PARAMS[0][$i], "#", 0 ) === 1 ){
						
						//REPLACE #!' WITH $ FOR VARIABLE REFERENCE
						$TMP_PARAM .= str_replace( "!", "$", str_replace( "#", "$", str_replace( "'", "", $F_PARAMS[0][$i] . ($i == count ( $F_PARAMS[0] )-1 ? "" : ", "))));
					}else{
						
						//PARAM VALUE IS STRING, NOT VARIABLE REFERENCE
						$TMP_PARAM .= $F_PARAMS[0][$i] . ($i == count ( $F_PARAMS[0] )-1 ? "" : ", ");
						
					}
				}
					$EVAL_RES .= $TMP_PARAM . "){\n";
			} else {
			   //preg_match returns the number of matches found, 
			   //so if here didn't match pattern
			   $EVAL_RES .= "){\n";
			}
			
		}
		
		//DEFINES A GLOBAL VARIABLE
		else if ( strpos ( $val, "<define" ) === 0 ){
			
			if (preg_match($MATCH_NAME, $val, $VAR_NAME)) {
				$EVAL_RES .= "global $" . $VAR_NAME[1] . ";";
			}
		}
		
		//IF VARIABLE TAG FOUND
		else if ( strpos ( $val, "<var" ) === 0 ){
			if (preg_match($MATCH_NAME, $val, $VAR_NAME)) {
				//echo $VAR_NAME[0]."<br />";  
				if (preg_match($MATCH_RAW_VALUE, $val, $VAR_VALUE)) {
					$EVAL_RES .= "$" . $VAR_NAME[1] . " = " . str_replace('\$', "$", $VAR_VALUE[1]) . ";\n";
				}else if (preg_match($MATCH_VALUE, $val, $VAR_VALUE)) {
					//echo $VAR_VALUE[0]."<br />"; 
					if ( strpos($VAR_VALUE[1], "$", 0 ) === 0 || strpos($VAR_VALUE[1], "!", 0 ) === 0 || strpos($VAR_VALUE[1], "#", 0 ) === 0 ){
						$EVAL_RES .= "$" . $VAR_NAME[1] . " = " . str_replace("'", "", $VAR_VALUE[1]) .";";
					}else{
						$EVAL_RES .= "$" . $VAR_NAME[1] . " = '" . str_replace('\$', "$", $VAR_VALUE[1]) . "';";
					}
					
				}
			} else {
				die("ERROR: NO FUNCTION NAME SUPPLIED!");
			}
		}
		
		//IF END_FUNCTION TAG FOUND
		else if ( strpos ( $val, "</function>" ) === 0 ){
			$EVAL_RES .= "}";
		}
		
		//IF ARRAY TAG FOUND
		else if ( strpos ( $val, "<array" ) === 0 ){
			$NAME_SET;
			$NAMES = array();
			$VALUES = array();
			
			if (preg_match($MATCH_NAME, $val, $VAR_NAME)) {
				$NAMES = explode(",",$VAR_NAME[1]);
			}
			if (preg_match($MATCH_VALUE, $val, $VAR_VALUES)) {
				$VALUES = explode(",", str_replace(", ", "", str_replace(" ,", ",", $VAR_VALUES[1])));
			}
			
			//echo var_dump($VAR_NAME[1]) . "--" . count ( $NAMES );;
			//echo var_dump($VAR_VALUES[1]) . "--" . count ( $VALUES );
			if ( count ( $NAMES ) === count ( $VALUES ) ){
				for ( $i = 0; $i < count ( $NAMES ); $i++ ){
					$EVAL_RES .= $NAMES[$i] . " = " . $VALUES[$i] . ";\n";
				}
			}else{
				//echo "MISMATCH";	
			}
			
		}
		
		//IF CALL TAG FOUND -- EXECUTE A FUNCTION
		else if ( strpos ( $val, "<call" ) === 0 ){
			preg_match($MATCH_PARAMS, $val, $VAR_NAME);
			preg_match($MATCH_FUNCTION, $val, $VAR_FUNCTION);
			
			//IF PARAMS ATTRIBUTE FOUND
			if (preg_match($MATCH_PARAMS, $val, $VAR_PARAMS)) {
				//echo $VAR_PARAMS[1];
				preg_match_all('~(["\'])([^"\']+)\1~', $VAR_PARAMS[1], $F_PARAMS);
			
				//VARIABLE TO TEMPORARILY STORE PARAMETER
				$TMP_PARAM = "";
				$func_params = explode(",", $VAR_PARAMS[1]);
				//var_dump($func_params);die();
				for ( $i = 0; $i < count ( $func_params ); $i++ )
				{
					//DETERMINE WHETHER PARAM REFERENCES ANOTHER VARIABLE, OR A STRING
					if ( strpos($func_params[$i], "$", 0 ) === 1 || strpos($func_params[$i], "!", 0 ) === 1 || strpos($func_params[$i], "#", 0 ) === 1 ){
						
						//REPLACE #!' WITH $ FOR VARIABLE REFERENCE
						$TMP_PARAM .= str_replace( "!", "$", str_replace( "#", "$", str_replace( "'", "", $func_params[$i] . ($i == count ( $func_params )-1 ? "'" : "', "))));
					}else{
						//PARAM VALUE IS STRING, NOT VARIABLE REFERENCE
						$TMP_PARAM .= "".$func_params[$i] . ($i == count ( $func_params )-1 ? "" : ", ");
						
					}
				}
				
				//echo "$". $VAR_VALUES[1] ." = ". $VAR_FUNCTION[1] . "(" . $TMP_PARAM . ");\n";
				
			}
			
			if (preg_match($MATCH_INIT_VAR, $val, $VAR_VALUES)) {
				$EVAL_RES .= "$". $VAR_VALUES[1] ." = ". $VAR_FUNCTION[1] . "(" . $TMP_PARAM . ");\n";
			}else{
				$EVAL_RES .= $VAR_FUNCTION[1] . "(" . $TMP_PARAM . ");\n";	
			}
			
		}else if ( strpos ( $val, "</function>" ) === 0 ){
			$EVAL_RES .= "}";
		}else if ( strpos ( $val, "<repeat" ) === 0 ){
			if (preg_match($MATCH_CONDITION, $val, $VAR_VALUES)) {
				$EVAL_RES .= "while ( " . $VAR_VALUES[1] . " ){\n";
			}
		}else if ( strpos ( $val, "</repeat>" ) === 0 ){
			$EVAL_RES .= "}";
		}
		
		
		
		
		
		
		
		return $EVAL_RES;
		
		
		/*
		else if ( strpos ( $val, "<if" ) === 0 ){
			$EVAL_RES .= "if (";
			//OTHER CODE TO SUPPORT IF SOON
		}else if ( strpos ( $val, "<endif>" ) === 0 ){
			$EVAL_RES .= "}";
		}
	
		/*if (preg_match($MATCH_PARAMS, $val, $m)) {
			echo $m[1]."<br />";   
		} else {
		   //preg_match returns the number of matches found, 
		   //so if here didn't match pattern
		}*/	
	}
	
	
?>