<?php
/**
 * Add_email_template_tkb
 */
class Migration_1389324082 {
	const EMAIL_TEMPLATE_TKB = "gui_thong_bao_tkb";
	
	function __construct() {
		echo "Start migrate file 1389324082.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up\n";
		$model = new EmailTemplateModel();
		if ($model->checkTableExist()){
			//Get config data from file
			$config = Helper::getHelper('functions/util')->getDbFileConfig();
			//Mail subject
			$subject = $config['mail_tkb_title'];
			//Mail content
			$template = new BaseTemplate("mail/tkb","default/index");
			$contentHTML = $template->contentTemplate();
			$comment = "Email gửi thông báo";
			
			$data = array('id' => self::EMAIL_TEMPLATE_TKB, 'title' => $subject, 'content' => $contentHTML, 'general_comment' => $comment);
			$model->checkTemplateThongBaoTkb($data);
		}
	}
	
	public	function down(){
		echo "function down\n";
		$model = new EmailTemplateModel();
		if ($model->checkTableExist()){
			$model->deleteTemplate(self::EMAIL_TEMPLATE_TKB);
		}
	}
}