<?php

/**
*   Validate Form Class
*   Author: Daniel P. Gilfoy
*   Description: Validate's a value or an array of data.  
*		   
*/
class Validation{
	
	protected $validate_data = array();
	protected $no_errors;
	
	public function __construct( ){
		$this->no_errors = true;
		$this->errors = array();
	}
	
	/*
	*   Iterates through a passed array (see format) and validates each imput, returning an array with "error field" added
	*   Format: array(
	*			   "Name_of_field" => array(
	*			   "value" => "field_value_to_validate",
	*			   "rules" => "rule_here" <- such as date or matches[value_to_match]
	*			   "label" => "label of field"
	*		   ) );
	*/
	public function validate( $fields, $values ){
		foreach( $fields as $field_name=>$field ){
			$rules = array_reverse(explode( '|', $field['rules']));
			foreach( $rules as $rule ){
				$label = ( isset( $field['label'] ) ) ? $field['label'] : ucwords( preg_replace( '`-_`', ' ', $field_name ) ) ;
				if( preg_match( '`(\w*)\[(.*?)\]`is', $rule, $rule_segments ) ){
					$valid_rule = 'valid_' . $rule_segments[1];
					if( method_exists( $this, $valid_rule ) ) self::$valid_rule( $field_name, $label, $values[$field_name], $rule_segments[2] );
				}else{
					$valid_rule = 'valid_' . $rule;
					if( method_exists( $this, $valid_rule ) ) self::$valid_rule( $field_name, $label, $values[$field_name] );
				}
			}
		}
		return $this->errors;
	}

	/*
	*   Validates a single field returning true or false (you can get the error message if you need it through get_validate_data())
	*/
	public function validate_field( $value, $rule, $field_name = 'validated_field', $field_label = false ){
		$valid_rule = 'valid' . $rule;
		$label = ( $field_label ) ? $field_label : ucwords( preg_replace( '`-_`', ' ', $field_name ) ) ;
		if( method_exists( $this, $valid_rule ) ) self::$valid_rule( $field_name, $label, $value );
		return $this->no_errors;
	}

	/*
	*   returns the array of validated data
	*/
	public function get_validate_data(){
		return $this->validate_data;
	}
	
	/*
	*   returns the status of errors for the object - for validate_array, let's you know if any element in the array has an error. 
	*/
	public function has_errors(){
		return $this->no_errors;
	}
	
	/*
	*   set's the error message and the error flag
	*/
	protected function set_error( $label, $msg ){
		$this->errors[$label] = $msg;
		$this->no_errors = false;
	}

	/*
	*   Make's certain that the field, if required, is not empty.
	*/
	protected function valid_required( $name, $label, $value ){
		if( strlen( trim( $value ) ) < 1 ) self::set_error( $name, $label . ' alanı zorunludur.' );
	}
	
	/*
	*   Is a valid date ( dd.mm.yyyy - hh:ii )
	*   Note: will add ability to pass your own date format eventually - change this manually if your needs are different
	*/
	protected function valid_date( $name, $label, $value ){
		if( !preg_match( '/^\d{2}\.\d{2}\.\d{4} - \d{2}:\d{2}$/', $value ) )
			self::set_error( $name, $label . ' geçersiz.'); 
	}
	
	/*
	*   is an integer
	*/
	 protected function valid_integer( $name, $label, $value ){
		if (! ctype_digit( $value ) )
			self::set_error( $name, $label . ' tamsayı olmalıdır.'); 
	}
	
	/*
	*   is valid phone number
	*/
	protected function valid_phone( $name, $label, $value ){
		$value = ltrim(trim($value), '0');
		if (strlen($value) != 10 || !ctype_digit($value))
			self::set_error( $name, $label . ' numarası geçersiz.'); 
	}
	
	/*
	*   has a certain length: length[5] for a string that is 5 characters long, or for between a range: length[5|10]
	*/
	protected function valid_length( $name, $label, $value, $segments ){
		$data_length = strlen( $value );
		if (preg_match('`(\d*):(\d*)`is', $segments, $length_rules)) {
		   if( $length_rules[1] > $data_length  || $data_length > $length_rules[2] )
				self::set_error( $name, $label . ' must be at least ' . $length_rules[1]. ' and no more than '.$length_rules[2].' characters.' );
		}else{
			if ( $data_length != $segments )
				self::set_error( $name, $label . ' must contain at least '.$segments.' characters.' ); 
		}
	}
	
	/*
	*   is valid tc kimlik no
	*/
	protected function valid_id_number( $name, $label, $value ){
		// http://yandexer.com/konu-php-tc-kimlik-no-dogrulama.html
		$valid = true;
		if(strlen($value) != 11){ $valid = false; } 
		else {
			if($value[0] == '0'){ $valid = false; } 
			$toplam = ($value[0] + $value[2] + $value[4] + $value[6] + $value[8]) * 7; 
			$cikar  = $toplam - ($value[1] + $value[3] + $value[5] + $value[7]); 
			$mod = $cikar % 10; 
			if($mod != $value[9]){ $valid = false; } 
			$hanelertoplami = ''; 
			for($i = 0 ; $i < 10 ; $i++){ $hanelertoplami += $value[$i];  } 
			if($hanelertoplami % 10 != $value[10]){ $valid = false; } 
		}
		if (!$valid) self::set_error( $name, $label . ' geçersiz.' );
	}

}