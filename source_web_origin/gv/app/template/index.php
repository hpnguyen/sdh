<?php
/**
 * 
 */
class BaseTemplate  {
	protected $template = null;
	protected $templateLayout = null;
	
	protected $variables = array();
	 
	public function __construct($template,$layout = null)
	{
		$this->template = dirname(__FILE__).'/view/'.$template.'.php';
		if ($layout != null){
			$this->templateLayout = dirname(__FILE__).'/layout/'.$layout.'.php';
		}
	}
	 
	public function __get($key)
	{
		return $this->variables[$key];
	}
	 
	public function __set($key, $value)
	{
		$this->variables[$key] = $value;
	}
	
	public function template($template) {
		$this->template = dirname(__FILE__).'/view/'.$template.'.php';
		return $this;
	}
	
	public function layout($layout) {
		$this->templateLayout = dirname(__FILE__).'/layout/'.$layout.'.php';
		return $this;
	}
	
	public function contentTemplate(){
		$temp = $this->variables;
		
		extract($temp);
		chdir(dirname($this->template));
		ob_start();
		 
		include basename($this->template);
		
		return ob_get_clean();
	}
	
	public function renderTemplate()
	{
		echo $this->contentTemplate();
	}
	
	public function contentLayout($variables){
		extract($variables);
		chdir(dirname($this->templateLayout));
		ob_start();
		 
		include basename($this->templateLayout);
		
		return ob_get_clean();
	}
	
	public function renderLayout($variables)
	{
		echo $this->contentLayout($variables);
	}
}
