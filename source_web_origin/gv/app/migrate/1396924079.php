<?php
/**
 * alter_nckh_pb_noi_dung_clob
 */
class Migration_1396924079 {
	function __construct() {
		echo "Start migrate file 1396924079.php\n";
	}
					
	function __destruct() {
		echo "***************************************************************\n";
	}
	
	public	function up(){
		echo "function up: Alter table nckh_pb_noi_dung to clob\n";
		$model = new NckhPbNoiDungModel();
		
		echo "Create temporary CLOB column\n";
		$sql ="ALTER TABLE nckh_pb_noi_dung ADD (
			clob_a1_tam_quan_trong CLOB null,
			clob_a2_chat_luong_nc CLOB null,
			clob_a3_nlnc_csvc CLOB null,
			clob_a4_kinh_phi_nx CLOB null,
			clob_c_ket_luan CLOB null
		)";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Backup data to temporary CLOB column\n";
		$sql = "UPDATE nckh_pb_noi_dung SET 
			clob_a1_tam_quan_trong = a1_tam_quan_trong ,
			clob_a2_chat_luong_nc = a2_chat_luong_nc ,
			clob_a3_nlnc_csvc = a3_nlnc_csvc ,
			clob_a4_kinh_phi_nx = a4_kinh_phi_nx ,
			clob_c_ket_luan = c_ket_luan";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Drop old column\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung DROP (
			a1_tam_quan_trong,
			a2_chat_luong_nc,
			a3_nlnc_csvc,
			a4_kinh_phi_nx,
			c_ket_luan
		)";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Change name of temporary  column a1_tam_quan_trong\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung RENAME COLUMN clob_a1_tam_quan_trong TO a1_tam_quan_trong";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Change name of temporary  column a2_chat_luong_nc\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung RENAME COLUMN clob_a2_chat_luong_nc TO a2_chat_luong_nc";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Change name of temporary  column a3_nlnc_csvc\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung RENAME COLUMN clob_a3_nlnc_csvc TO a3_nlnc_csvc";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Change name of temporary  column a4_kinh_phi_nx\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung RENAME COLUMN clob_a4_kinh_phi_nx TO a4_kinh_phi_nx";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		
		echo "Change name of temporary column c_ket_luan\n";
		$sql = "ALTER TABLE nckh_pb_noi_dung RENAME COLUMN clob_c_ket_luan TO c_ket_luan";
		$model->getQuery($sql)->execute(true, array());
		sleep(1);
		echo "End all\n";
	}
	
	public	function down(){
		echo "function down: This version cannot revert\n";
	}
}