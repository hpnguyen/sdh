<?php
/**
 * 
 */
class NckhThuyetMinhDeTaiModel extends BaseTable {
	
	private $kqPhanHoi = array('Không đồng ý','Đồng ý phản biện','Chưa trả lời');
	
	
	function __construct() {
		parent::init("nckh_thuyet_minh_de_tai","_khcn");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getList($macb, $year = '')
	{
		$rightJoin = "(+)";
		// $rightJoin = "";
		$sqlstr="SELECT a.*, 
		check_het_han_phan_bien(a.ma_thuyet_minh_dt , a.fk_ma_can_bo) as het_han_phan_bien,
		b.kq_phan_hoi, c.ten_tinh_trang ,
		(CASE 
			WHEN b.kq_phan_hoi = 0 THEN '".$this->kqPhanHoi[0]."' 
			WHEN b.kq_phan_hoi = 1 THEN '".$this->kqPhanHoi[1]."'
			ELSE '".$this->kqPhanHoi[2]."' 
		END ) AS text_kq_phan_hoi,
		d.a1_tam_quan_trong, 
		d.a2_chat_luong_nc, 
		d.a3_nlnc_csvc, 
		d.a4_kinh_phi_nx, 
		d.c_ket_luan
		FROM 	nckh_thuyet_minh_de_tai a, 
				nckh_phan_cong_phan_bien b, 
				nckh_dm_tinh_trang c ,
				nckh_pb_noi_dung d
		WHERE a.ma_thuyet_minh_dt = b.ma_thuyet_minh_dt
		AND a.fk_tinh_trang = c.ma_tinh_trang ".$rightJoin."
		AND b.ma_thuyet_minh_dt =  d.ma_thuyet_minh_dt ".$rightJoin."
		AND b.fk_ma_can_bo = d.fk_ma_can_bo ".$rightJoin."
		AND b.fk_ma_can_bo = :macb  
		AND TO_CHAR(b.ngay_phan_cong ,'YYYY') = '".$year."'
		ORDER BY het_han_phan_bien asc, b.kq_phan_hoi ,a.ma_thuyet_minh_dt asc";
		
		$check = $this->getQuery($sqlstr)->bindExecute(false, array(':macb' => $macb));
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
	}

	public function getListByMaDeTai($macb, $madetai, $year = '')
	{
		$rightJoin = "(+)";
		// $rightJoin = "";
		$sqlstr="SELECT a.*,
		check_het_han_phan_bien(a.ma_thuyet_minh_dt , a.fk_ma_can_bo) as het_han_phan_bien ,
		b.kq_phan_hoi, c.ten_tinh_trang ,
		(CASE 
			WHEN b.kq_phan_hoi = 0 THEN '".$this->kqPhanHoi[0]."' 
			WHEN b.kq_phan_hoi = 1 THEN '".$this->kqPhanHoi[1]."'
			ELSE '".$this->kqPhanHoi[2]."' 
		END ) AS text_kq_phan_hoi,
		d.a1_tam_quan_trong, 
		d.a2_chat_luong_nc, 
		d.a3_nlnc_csvc, 
		d.a4_kinh_phi_nx, 
		d.c_ket_luan
		FROM 	nckh_thuyet_minh_de_tai a, 
				nckh_phan_cong_phan_bien b, 
				nckh_dm_tinh_trang c ,
				nckh_pb_noi_dung d
		WHERE a.ma_thuyet_minh_dt = b.ma_thuyet_minh_dt
		AND a.fk_tinh_trang = c.ma_tinh_trang ".$rightJoin."
		AND b.ma_thuyet_minh_dt =  d.ma_thuyet_minh_dt ".$rightJoin."
		AND a.ma_thuyet_minh_dt = '".$madetai."'
		AND b.fk_ma_can_bo = d.fk_ma_can_bo ".$rightJoin."
		AND b.fk_ma_can_bo = :macb  
		AND TO_CHAR(b.ngay_phan_cong ,'YYYY') = '".$year."'
		ORDER BY het_han_phan_bien asc, b.kq_phan_hoi, a.ma_thuyet_minh_dt asc";
		
		$check = $this->getQuery($sqlstr)->bindExecute(false, array(':macb' => $macb));
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
			
			$model = new NckhPbDmNoiDungModel;
			$listValues = $model->getListTabA4KinhPhiPhanBien();
			$listValuesArray = array();
			foreach ($listValues as $item) {
				$listValuesArray[] = "'".$item['ma_nd']."'";
			}
			
			if (count($listValuesArray) > 0){
				foreach ($ret as $k => $row) {
					//Get data from table nckh_pb_noi_dung_kinh_phi, ready to append data to Tab A4 
					$madetai = $row['ma_thuyet_minh_dt'];
					$sqlDmKinhPhi = "SELECT a.ma_nd , a.noi_dung,
					b.nhan_xet, b.kinh_phi_de_nghi
					FROM 	nckh_pb_dm_noi_dung a,
					nckh_pb_noi_dung_kinh_phi b
					WHERE a.ma_nd =  b.ma_nd
					AND b.ma_thuyet_minh_dt =  '".$madetai."'
					AND b.fk_ma_can_bo = '".$macb."'
					ORDER BY b.id_order_by asc, a.ma_nd asc";
					$rowsDmKinhPhi = $this->getQuery($sqlDmKinhPhi)
					->execute(false, array());
					//var_dump($rowsDmKinhPhi->result);
					$temp = array('nckh_pb_noi_dung_kinh_phi' => $rowsDmKinhPhi->result);
					
					//Get data from table nckh_pb_noi_dung_kinh_phi, ready to append data to Tab A4
					$sqlDmDanhGia = "SELECT b.* , a.noi_dung
					FROM 	nckh_pb_dm_noi_dung a,
					nckh_pb_noi_dung_danh_gia b
					WHERE a.ma_nd =  b.ma_nd
					AND b.ma_thuyet_minh_dt =  '".$madetai."'
					AND b.fk_ma_can_bo = '".$macb."'
					ORDER BY b.id_order_by asc, b.ma_nd asc";
					// echo $sqlDmDanhGia;
					$rowsDmDanhGia = $this->getQuery($sqlDmDanhGia)
					->execute(false, array());
					$temp['nckh_pb_noi_dung_danh_gia'] = $rowsDmDanhGia->result;
					// var_dump($temp['nckh_pb_noi_dung_danh_gia']);
					$ret[$k]['join_tables'] = $temp;
				}
			}
		}
		return $ret;
	}
	
	public function checkEnableSaveEdit($madetai, $macb) {
		$sql ="select check_het_han_phan_bien('".$madetai."', '".$macb."') as het_han_phan_bien from dual";
		$check = $this->getQuery($sql)->execute(false, array());
		
		return (int) $check->result[0]['het_han_phan_bien'] == 0;
	}
}
