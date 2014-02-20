<?php
/**
 * 
 */
class ModuleSystemControllerMail extends FrontController {
	
	function __construct() {
		
	}
	
	private function checkValidIP()
	{
		$userip = ($_SERVER['X_FORWARDED_FOR']) ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$validIpArray = array('127.0.0.1');
		
		//Get config data from file
		$config = Helper::getHelper('functions/util')->getDbFileConfig();
		$temp = explode(",", $config['mail_valid_ip_array']);
		foreach ($temp as $key => $value) {
			$validIpArray[] = $value;
		}
		if (! in_array($userip, $validIpArray)){
			die('Only request from local') ;
		}
	}
	// public	function tbtkbAction(){
		// $model = new GuiEmailModel();
		// $result = $model->getListSendMailTkb();
// 		
		// if(count($result) > 0){
			// //Use log to write log
			// $log = new logfile('cronjob_send_mail_thong_bao_tkb');
// 			
			// //Get config data from file
			// $config = Helper::getHelper('functions/util')->getDbFileConfig();
			// //Mail subject
			// $subject = $config['mail_tkb_title'];
			// //CC email
			// $ccList = array(array($config['mail_tkb_cc']));
// 			
			// //Get list email send to
			// $recipients = array();
			// $firstRow = $result[0];
			// $rowID = $firstRow['id'];
			// $email = $firstRow['email'];
// 			
			// $temp = explode(",", $email);
			// foreach ($temp as $k => $v) {
				// $recipients[] = array($v);
			// }
			// //Ready attachment
			// $attach = null;
// 			
			// //Get email template
			// $template = new BaseTemplate("mail/tkb","default/index");
			// $contentHTML = $template->contentTemplate();
			// echo "[id,email] = [".$rowID.",".$email."]\n";
			// $log->write("[id,email] = [".$rowID.",".$email."]");
			// //Send mail
			// $ret = Helper::getHelper('functions/mail')->sendMail($subject, $contentHTML, $recipients, $ccList, null, $attach, null, 0, false);
// 			
			// //Echo the result
			// echo $ret['message'];
			// if ($ret['status']) {
				// $log->write("Send mail success");
				// //Set table gui_email status to success
				// $model->updateSendMailStatusForTkb($rowID);
			// }else{
				// $log->write("Send mail unsuccess");
			// }
		// }else{
			// echo "No have email to send";
		// }
	// }
	
	public	function tbtkbAction(){
		$this->checkValidIP();
		
		$model = new GuiEmailModel();
		$result = $model->getListSendMailTkb();
		
		if(count($result) > 0){
			$emailTemplateModel = new EmailTemplateModel();
			$ret = $emailTemplateModel->getMailTemplate('gui_thong_bao_tkb');
			//Use log to write log
			$log = new logfile('cronjob_send_mail_thong_bao_tkb');
			
			//Get config data from file
			$config = Helper::getHelper('functions/util')->getDbFileConfig();
			//Mail subject
			$subject = $ret['title'];
			//CC email
			$ccList = array(array($config['mail_tkb_cc']));
			
			//Get list email send to
			$recipients = array();
			$firstRow = $result[0];
			$rowID = $firstRow['id'];
			$email = $firstRow['email'];
			
			$temp = explode(",", $email);
			foreach ($temp as $k => $v) {
				$recipients[] = array($v);
			}
			//Ready attachment
			$attach = null;
			
			//Get email template
			$contentHTML = $ret['content'];
			echo "[id,email] = [".$rowID.",".$email."]\n";
			$log->write("[id,email] = [".$rowID.",".$email."]");
			//Send mail
			$ret = Helper::getHelper('functions/mail')->sendMail($subject, $contentHTML, $recipients, $ccList, null, $attach, null, 0, false);
			
			//Echo the result
			echo $ret['message'];
			if ($ret['status']) {
				$log->write("Send mail success");
				//Set table gui_email status to success
				$model->updateSendMailStatusForTkb($rowID);
			}else{
				$log->write("Send mail unsuccess");
			}
		}else{
			echo "No have email to send";
		}
	}
	
	public function templateAction()
	{
		$model = new NhanSuModel();
		if ($model->checkRoleCanUpdateEmailTemplate()) {
			$template = new BaseTemplate("system/listemailtemplate","default/index");
			$modelEmailTempalate = new EmailTemplateModel();
			$template->listItems = $modelEmailTempalate->listAll();
			unset($modelEmailTempalate);
			$template->renderTemplate();
		}else{
			unset($model);
			echo "Bạn không có quyền truy cập trang này.";
		}
	}
	
	public function changeAction()
	{
		$model = new NhanSuModel();
		if ($model->checkRoleCanUpdateEmailTemplate()) {
			unset($model);
			try {
				$modelEmailTemplate = new EmailTemplateModel();
				$data = $this->getPost('data');
				$modelEmailTemplate->checkTemplateThongBaoTkb($data);
				$ret = $modelEmailTemplate->getMailTemplate($data['id']);
				
				$this->renderJSON(array('status' => 1, 
					'message' => 'Change data successful.',
					'updated_at' => $ret['t_updated_at']
				));
			} catch (Exception $e) {
				$this->renderJSON(array('status' => 0, 'message' => $e->getMessage()));
			}
		}else{
			unset($model);
			$this->renderJSON(array('status' => 0, 'message' => "Bạn không có quyền cập nhật email."));
		}
	}
	
	public	function tkbgetemailAction(){
		$this->checkValidIP();
		
		$model = new GuiEmailModel();
		$result = $model->getListSendMailTkb();
		if(count($result) > 0){
			//Get list email send to
			$firstRow = $result[0];
			$rowID = $firstRow['id'];
			$email = $firstRow['email'];
			echo $rowID.",".$email;
		}else{
			echo "";
		}
	}
	
	public	function tkbsendmaildoneAction(){
		$this->checkValidIP();
		
		$id = $this->getParam('id',null);
		$model = new GuiEmailModel();
		//Set table gui_email status to success
		$model->updateSendMailStatusForTkb($id);
	}
}
