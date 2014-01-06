<?php
interface FrontControllerInterface {
	public function setController($controller);
	public function setAction($action);
	public function setParams(array $params);
	public function run();
	public function init($params);
	public function initSession();
	public function setSession($name,$value = null);
	public function getSession($name,$value = null);	
	public function checkLogin();
	public function renderJSON($params);
}