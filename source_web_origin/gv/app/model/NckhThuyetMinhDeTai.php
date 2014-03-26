<?php
/**
 * 
 */
class NckhThuyetMinhDeTaiModel extends BaseTable {
	
	private $kqPhanHoi = array('Không đồng ý','Đồng ý phản biện','Chưa trả lời');
	private $loaiHinhNghienCuu = array(null,'NCCB','NCUD','NCTK');
	
	
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
		d.c_ket_luan,
		(CASE 
			WHEN a.fk_loai_hinh_nc = 1 
			THEN '".$this->loaiHinhNghienCuu[1]."' 
			WHEN a.fk_loai_hinh_nc = 2 
			THEN '".$this->loaiHinhNghienCuu[2]."'
			WHEN a.fk_loai_hinh_nc = 3 
			THEN '".$this->loaiHinhNghienCuu[3]."'
			ELSE '' 
		END ) AS text_loai_hinh_nc
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
		/*
		 * (CASE 
			WHEN a.fk_cap_de_tai >= 31 and a.fk_cap_de_tai <= 32 and a.fk_loai_hinh_nc = 1 
			THEN '".$this->loaiHinhNghienCuu[1]."' 
			WHEN a.fk_cap_de_tai >= 31 and a.fk_cap_de_tai <= 32 and a.fk_loai_hinh_nc = 2 
			THEN '".$this->loaiHinhNghienCuu[2]."'
			WHEN a.fk_cap_de_tai >= 31 and a.fk_cap_de_tai <= 32 and a.fk_loai_hinh_nc = 3 
			THEN '".$this->loaiHinhNghienCuu[3]."'
			ELSE '' 
		END )
		 */
		$check = $this->getQuery($sqlstr)->bindExecute(false, array(':macb' => $macb));
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result;
			$modelCapDeTai = new CapDeTaiModel();
			foreach ($ret as $k => $item) {
				$maCapDeTai = $item['fk_cap_de_tai'];
				$detail =$modelCapDeTai->getByMaCap($maCapDeTai);
				$item['ten_cap_de_tai'] = null;
				if ($detail != null){
					$item['ten_cap_de_tai'] = $detail['ten_cap'];
				}
				$ret[$k] = $item;
			}
			 
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

	public function getDetailForPrintPdf($madetai,$macb)
	{
		$sqlstr="SELECT tm.*,
			check_het_han_phan_bien(tm.ma_thuyet_minh_dt , tm.fk_ma_can_bo) as het_han_phan_bien,
			to_char(tm.cndt_ngay_sinh,'dd/mm/yyyy') t_cndt_ngay_sinh, 
			to_char(tm.cndt_ngay_cap,'dd/mm/yyyy') t_cndt_ngay_cap, 
			to_char(tm.dcndt_ngay_sinh,'dd/mm/yyyy') t_dcndt_ngay_sinh, 
			to_char(tm.dcndt_ngay_cap,'dd/mm/yyyy') t_dcndt_ngay_cap,
			to_char(tm.ngay_dang_ky, 'yyyy') t_nam_dang_ky,
			lhnc.ten_loai_hinh_nc,
			d.a1_tam_quan_trong, 
			d.a2_chat_luong_nc, 
			d.a3_nlnc_csvc, 
			d.a4_kinh_phi_nx, 
			d.c_ket_luan
		FROM	nckh_thuyet_minh_de_tai tm,
				nckh_pb_noi_dung d,
				nckh_loai_hinh_nc lhnc
		WHERE tm.ma_thuyet_minh_dt = '".$madetai."' 
		and d.fk_ma_can_bo = '".$macb."'
		and tm.ma_thuyet_minh_dt =  d.ma_thuyet_minh_dt(+)
		and tm.fk_loai_hinh_nc = lhnc.ma_loai_hinh_nc(+)";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			//******************************************************************************************
			//Trim unexpected character
			//******************************************************************************************
			$help = Helper::getHelper('functions/util');
			$ret['a1_tam_quan_trong'] = $help->trimSlashSpecialChar($ret['a1_tam_quan_trong']);
			$ret['a2_chat_luong_nc'] = $help->trimSlashSpecialChar($ret['a2_chat_luong_nc']);
			$ret['a3_nlnc_csvc'] = $help->trimSlashSpecialChar($ret['a3_nlnc_csvc']);
			$ret['a4_kinh_phi_nx'] = $help->trimSlashSpecialChar($ret['a4_kinh_phi_nx']);
			$ret['c_ket_luan'] = $help->trimSlashSpecialChar($ret['c_ket_luan']);
			//End trim
			//******************************************************************************************

			$ret['ten_cap'] = '';
			$ret['username'] = '';
			$macap = $ret['fk_cap_de_tai'];
			
			//Get detail for cap_de_tai 
			$modelCapDeTai = new CapDeTaiModel();
			$retTenCap = $modelCapDeTai->getByMaCap($macap);
			if($retTenCap != null){
				$ret['ten_cap'] = $retTenCap['ten_cap'];
			}
			
			$modelNhanSu = new NhanSuModel();
			$retUsername = $modelNhanSu->getByFkMaCanBo($fk_ma_can_bo);
			if($retUsername != null) {
				$ret['username'] = $retUsername['username'];
			}
			
			//Get data from table nckh_pb_noi_dung_kinh_phi, ready to append data to Tab A4 
			$madetai = $ret['ma_thuyet_minh_dt'];
			
			$modelNckhPbDm = new NckhPbDmNoiDungModel();
			$temp = array('nckh_pb_noi_dung_kinh_phi' => $modelNckhPbDm->getDetailNckhPbDmNoiDung($madetai, $macb));
			
			//Get data from table nckh_pb_noi_dung_kinh_phi, ready to append data to Tab A4
			$sqlDmDanhGia = "SELECT b.* , a.noi_dung,
			get_nckh_pb_nd_dg_diem_text(b.id, b.id_cha, b.ma_nd, b.ma_thuyet_minh_dt, b.diem) diem_text
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
			$ret['join_tables'] = $temp;
		}
		return $ret;
	}
}
