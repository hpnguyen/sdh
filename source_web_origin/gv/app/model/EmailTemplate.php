<?php
/** 
* Table email_template model class
*
* @param  id varchar2(100) not null primary key
* @param  title varchar2(200)
* @param  content long varchar
* @param  created_at timestamp default current_timestamp
* @param  updated_at timestamp default current_timestamp
*/
class EmailTemplateModel extends BaseTable {
	public $emailTemplateFilePathOfThongBao = '';
	
	function __construct() {
		$this->emailTemplateFilePathOfThongBao = ROOT_DIR.'app/logs/files/thong-bao.pdf';
		parent::init("email_template");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function migrateUp()
	{
		$primaryKeys = array('id');
		$fieldsData = array(
			'id' => array('varchar2(100)', null),
			'title'=> array('varchar2(200)',null),
			'content' => array('long varchar', null),
			'general_comment' => array('varchar2(100)', null),
			'created_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP'),
			'updated_at' => array('timestamp', 'DEFAULT CURRENT_TIMESTAMP')
		);
		return $this->getCreate($primaryKeys,$fieldsData)->execute(true, array());
		
	}
	
	public function migrateDown()
	{
		return $this->getDrop()->execute(true, array());
	}
	
	public function checkTemplateThongBaoTkb($data)
	{
		if (is_array($data) && isset($data['id'])){
			$check = $this->getSelect('*')
			->where("id = '".$data['id']."'")
			->execute(false, array());
			
			if ($check->itemsCount < 1){
				$this->getInsert($data)->execute(true, array());
			}else{
				if(! isset($data['updated_at'])){
					$data['updated_at'] = 'CURRENT_TIMESTAMP';
				}
				$this->getUpdate($data)->where("id = '".$data['id']."'")->execute(true, array());
			}
			$check = $this->getSelect($this->tableName.".*, to_char(updated_at,'dd-mm-yyyy') as t_updated_at")
			->where("id = '".$data['id']."'")
			->execute(false, array());
			//Create PDF file attachment
			$row = $check->result[0];
			$mpdf=new mPDF('utf-8','A4'); 
			$mpdf->SetAutoFont();
			$mpdf->forcePortraitHeaders = true;
			$mpdf->WriteHTML($data['content']);
			$replace = '-'.$row['t_updated_at'].'.pdf';
			$filePDF = str_replace('.pdf', $replace, $this->emailTemplateFilePathOfThongBao);
			$mpdf->Output($filePDF,'F');
			
			return true;
		}else{
			return false;
		}
	}
	
	public function deleteTemplate($id)
	{
		$this->getDelete("id = '".$id."'")->execute(true, array());
	}
	
	public function getMailTemplate($id)
	{
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, to_char(updated_at,'dd-mm-yyyy') as pdf_updated_at , email_template.*")
		->where("id = '".$id."'")
		->execute(false, array());
		
		if($check->itemsCount > 0){
			return $check->result[0];
		}else{
			return null;
		}
	}
	
	public function listAll()
	{
		$check = $this->getSelect("TO_CHAR(created_at ,'DD-MM-YYYY HH24:MI:SS') t_created_at,TO_CHAR(updated_at ,'DD-MM-YYYY HH24:MI:SS') t_updated_at, email_template.*")
		->execute(false, array());
		
		return $check->result;
	}
}
