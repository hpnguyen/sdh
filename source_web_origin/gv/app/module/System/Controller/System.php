<?php
/**
 * 
 */
class  ModuleSystemControllerSystem extends FrontController {
	
	function __construct() {
		
	}
	
	private function initCheck() {
		if (! $this->checkLogin()) {
			die("Bạn chưa đăng nhập.");
		}
	}
	
	public function listuserAction()
	{
		$this->initCheck();
		$model = new NhanSuModel();
		if ($model->checkRoleCanDoResetUserPassword()) {
			$template = new BaseTemplate("system/listuser","default/blank");
			$template->listItems = $model->getListNhanSu();
			
			unset($model);
			$template->renderTemplate();
		}else{
			unset($model);
			echo "Bạn không có quyền truy cập trang này.";
		}
	}
	
	public function changeAction()
	{
		$this->initCheck();
		$model = new NhanSuModel();
		$check = $model->checkRoleCanDoResetUserPassword();
		unset($model);
		if (! $check) {
			$this->renderJSON(array('status' => 0, 'message' => 'Bạn không có quyền sử dụng chức năng này.'));
		}else{
			$model = new NhanSuModel();
			$username = $this->getPost('p',null);
			$checkMessage = $model->resetPassword($username);
			
			if ($checkMessage == null){
				$this->renderJSON(array('status' => 1, 'message' => 'Reset password thành công.'));
			}else{
				$this->renderJSON(array('status' => 0, 'message' => $checkMessage));
			}
		}
	}
}
