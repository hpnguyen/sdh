<?php
class PhpStringParser
{
	protected $variables;

	public function __construct($variables = array())
	{
		$this->variables = $variables;
	}
	
	protected function eval_block($matches)
	{
		if( is_array($this->variables) && count($this->variables) ) {
			foreach($this->variables as $var_name => $var_value) {
				$$var_name = $var_value;
			}
		}
		
		$eval_end = '';
		
		if( $matches[1] == '<?=' || $matches[1] == '<?php=' || $matches[1] == '<?php echo') {
			if( $matches[2][count($matches[2]-1)] !== ';' ) {
				$eval_end = ';';
			}
		}
		
		$return_block = '';
		eval('$return_block = ' . $matches[2] . $eval_end);
		
		return $return_block;
	}
	
	public function parse($string)
	{
		$temp = explode("<?php", $string);
		$string2 = "";
		
		$listKey = array();
		foreach ($temp as $key => $value) {
			$temp2 = explode("echo ", $value);
			
			$ttt = str_replace(" ", "", $temp2[0]);;
			if ($ttt == ''){
				$parseString = array();
				$string3 = "";
				unset($temp2[0]);
				
				foreach ($temp2 as $key2 => $value2) {
					$temp3 = str_replace(" ", "", $value2);
					
					if($temp3 != ""){
						$parseString[] = $value2;
					}
				}
				
				foreach ($parseString as $key3 => $value3) {
					$string3 .= "<?php echo ".$value3;
				}
				
				$string2 .= $string3;
			}else{
				$string2 .= $value;
			}
		}
		//var_dump($string2);
		//return preg_replace_callback('/(\<\?=|\<\?php=|\<\?php echo|\<\?php)(.*?)\?\>/', array(&$this, 'eval_block'), $string);
		return preg_replace_callback('/(\<\?=|\<\?php=|\<\?php echo|\<\?php)(.*?)\?\>/', array(&$this, 'eval_block'), $string2);
	}
}