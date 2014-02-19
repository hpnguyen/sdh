<?php
/**
 * 
 */
class ModuleSystemControllerMail extends FrontController {
	
	function __construct() {
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
		
	public	function tbtkbAction(){
		$model = new GuiEmailModel();
		$result = $model->getListSendMailTkb();
		
		if(count($result) > 0){
			//Use log to write log
			$log = new logfile('cronjob_send_mail_thong_bao_tkb');
			
			//Get config data from file
			$config = Helper::getHelper('functions/util')->getDbFileConfig();
			//Mail subject
			$subject = $config['mail_tkb_title'];
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
			$template = new BaseTemplate("mail/tkb","default/index");
			$contentHTML = $template->contentTemplate();
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
	
	public	function tkbgetemailAction(){
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
		$id = $this->getParam('id',null);
		$model = new GuiEmailModel();
		//Set table gui_email status to success
		$model->updateSendMailStatusForTkb($id);
	}
}