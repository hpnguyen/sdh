<?php
/**
 * update_code_remove_unexpected_character
 */
class Migration_1393231231 {
	function __construct() {
		echo "Start migrate file 1393231231.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up: fix unexpected character\n";
		$model = new NckhPbNoiDungModel();
		$check = $model->getListToFixInvalidCharacter();
		$help = Helper::getHelper('functions/util');
		while ($check != null) {
			$row = $check[0];
			$row['a1_tam_quan_trong'] = $help->escapeSpecialCharToHtmlCode($row['a1_tam_quan_trong']);
			$row['a1_tam_quan_trong'] = $help->trimSlashSpecialChar($row['a1_tam_quan_trong']);
			
			$row['a2_chat_luong_nc'] =  $help->escapeSpecialCharToHtmlCode($row['a2_chat_luong_nc']);
			$row['a2_chat_luong_nc'] =  $help->trimSlashSpecialChar($row['a2_chat_luong_nc']);
			
			$row['a3_nlnc_csvc'] =  $help->escapeSpecialCharToHtmlCode($row['a3_nlnc_csvc']);
			$row['a3_nlnc_csvc'] =  $help->trimSlashSpecialChar($row['a3_nlnc_csvc']);
			
			$row['a4_kinh_phi_nx'] =  $help->escapeSpecialCharToHtmlCode($row['a4_kinh_phi_nx']);
			$row['a4_kinh_phi_nx'] =  $help->trimSlashSpecialChar($row['a4_kinh_phi_nx']);
			
			$row['c_ket_luan'] =  $help->escapeSpecialCharToHtmlCode($row['c_ket_luan']);
			$row['c_ket_luan'] =  $help->trimSlashSpecialChar($row['c_ket_luan']);
			
			$model->doCreateUpdate($row);
			$check = $model->getListToFixInvalidCharacter();
		}
	}
	
	public	function down(){
		echo "function down\n";
	}
}