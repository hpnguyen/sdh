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
		AND a.thung_rac is null
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
		AND a.thung_rac is null
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
		and tm.fk_loai_hinh_nc = lhnc.ma_loai_hinh_nc(+)
		and tm.thung_rac is null";
		
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
	
	public function getDetailTmdtForPrintPdf($madetai)
	{
		$sqlstr="
		SELECT tm.*, 
			to_char(tm.cndt_ngay_sinh,'dd/mm/yyyy') cndt_ngay_sinh, 
			to_char(tm.cndt_ngay_cap,'dd/mm/yyyy') cndt_ngay_cap, 
			cdt.ten_cap, 
			lhnc.ten_loai_hinh_nc, 
			to_char(tm.dcndt_ngay_sinh,'dd/mm/yyyy') dcndt_ngay_sinh, 
			to_char(tm.dcndt_ngay_cap,'dd/mm/yyyy') dcndt_ngay_cap, 
			tp1.ten_tinh_tp cndt_ten_noi_cap, 
			tp2.ten_tinh_tp dcndt_ten_noi_cap, 
			decode(tm.cndt_phai, 'F', 'Nữ', 'M', 'Nam', '') cndt_ten_phai, 
			decode(tm.dcndt_phai, 'F', 'Nữ', 'M', 'Nam', '') dcndt_ten_phai, 
			num2str(tm.tong_kinh_phi*1000000) || ' đồng' chu_tong_kinh_phi, 
			num2str(tm.kinh_phi_tu_dhqg*1000000) || ' đồng' chu_kinh_phi_tu_dhqg, 
			num2str(tm.kinh_phi_huy_dong*1000000) || ' đồng' chu_kinh_phi_huy_dong, 
			num2str(tm.hd_von_tu_co*1000000) || ' đồng' chu_hd_von_tu_co, 
			num2str(tm.hd_khac*1000000) || ' đồng' chu_hd_khac, 
			n.username 
		FROM nckh_thuyet_minh_de_tai tm, 
			cap_de_tai cdt, 
			nckh_loai_hinh_nc lhnc, 
			dm_tinh_tp tp1, 
			dm_tinh_tp tp2, 
			nhan_su n 
		WHERE ma_thuyet_minh_dt='".$madetai."' 
			and fk_cap_de_tai = cdt.ma_cap(+) 
			and fk_loai_hinh_nc = lhnc.ma_loai_hinh_nc(+) 
			and cndt_noi_cap = tp1.ma_tinh_tp(+) 
			and dcndt_noi_cap = tp2.ma_tinh_tp(+)
			and tm.fk_ma_can_bo=n.fk_ma_can_bo(+)
			and tm.thung_rac is null";
		//var_dump($madetai);
		//echo $sqlstr;
		$check = $this->getQuery($sqlstr)->execute(false, array());
		
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			//Check CLOB or BLOB data need to load
			foreach ($ret as $key => $item) {
				if (is_object($ret[$key])){
					$t = $ret[$key];
					$text = $t->load();
					if (! empty($text)){
						$ret[$key] = Helper::getHelper('functions/util')->reverse_escape($text);
					}else{
						$ret[$key] = null;
					}
				}
			}
			
			$sqlstr_nhomnganh ="SELECT a.fk_ma_nhom_nganh, a.ten_nhom_nganh_khac, b.ten_nhom_nganh 
			FROM nckh_nhom_nganh_tmdt a, nckh_nhom_nganh b
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."' and a.fk_ma_nhom_nganh = b.ma_nhom_nganh(+)";
			
			$query_nhomnganh = $this->getQuery($sqlstr_nhomnganh)->execute(false, array());
			
			$ret["nhomnganh"] = array();
			if($query_nhomnganh->itemsCount > 0){
				$ret ["nhomnganh"] = $query_nhomnganh->result;
			}
			
			// Du lieu A9
			$sqlstr_nhanluc_cbgd ="SELECT * 
			FROM nckh_nhan_luc_tmdt_cbgd 
			WHERE fk_ma_thuyet_minh_dt = '".$madetai."' 
			ORDER BY ma_nhan_luc_tmdt_cbgd";
			
			$query_nhanluc_cbgd = $this->getQuery($sqlstr_nhanluc_cbgd)->execute(false, array());
			
			$ret["nhanluc_cbgd"] = array();
			if($query_nhanluc_cbgd->itemsCount > 0){
				$ret ["nhanluc_cbgd"] = $query_nhanluc_cbgd->result;
			}
			
			$sqlstr_nhanluc_sv ="SELECT * 
			FROM nckh_nhan_luc_tmdt_sv 
			WHERE fk_ma_thuyet_minh_dt = '".$madetai."' 
			ORDER BY ma_nhan_luc_tmdt_sv";
			
			$query_nhanluc_sv = $this->getQuery($sqlstr_nhanluc_sv)->execute(false, array());
			
			$ret["nhanluc_sv"] = array();
			if($query_nhanluc_sv->itemsCount > 0){
				$ret ["nhanluc_sv"] = $query_nhanluc_sv->result;
			}
			
			// Du lieu B4
			$sqlstrChuyenGiaC="SELECT * 
			FROM nckh_chuyen_gia_tmdt 
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."'";
			$queryChuyenGiaC = $this->getQuery($sqlstrChuyenGiaC)->execute(false, array());
			
			$ret["chuyengianc"] = array();
			if($queryChuyenGiaC->itemsCount > 0){
				$ret ["chuyengianc"] = $queryChuyenGiaC->result;
			}
			
			// Du lieu B6.1
			$sqlstrAnPhamKhoaHoc="SELECT a.*,b.ten_an_pham_kh  
			FROM nckh_an_pham_kh_tmdt a, nckh_dm_an_pham_kh b 
			WHERE a.fk_ma_thuyet_minh_dt ='".$madetai."' and a.fk_ma_an_pham_kh = b.ma_an_pham_kh(+)";
			$queryAnPhamKhoaHoc = $this->getQuery($sqlstrAnPhamKhoaHoc)->execute(false, array());
			
			$ret["anphamkhoahoc"] = array();
			if($queryAnPhamKhoaHoc->itemsCount > 0){
				$ret ["anphamkhoahoc"] = $queryAnPhamKhoaHoc->result;
			}
			
			// Du lieu B6.2
			$sqlstr_sohuutritue="SELECT a.*, 
			b.ten_so_huu_tri_tue as ten_hinh_thuc 
			FROM nckh_so_huu_tri_tue a, nckh_dm_so_huu_tri_tue b 
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."' 
			and a.fk_ma_so_huu_tri_tue = b.ma_so_huu_tri_tue(+)";
			$query_sohuutritue = $this->getQuery($sqlstr_sohuutritue)->execute(false, array());
			
			$ret["sohuutritue"] = array();
			if($query_sohuutritue->itemsCount > 0){
				$ret ["sohuutritue"] = $query_sohuutritue->result;
			}
			
			$sqlstr_sanphammem="SELECT * 
			FROM nckh_san_pham_mem 
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."'";
			
			$query_sanphammem = $this->getQuery($sqlstr_sanphammem)->execute(false, array());
			
			$ret["sanphammem"] = array();
			if($query_sanphammem->itemsCount > 0){
				$ret ["sanphammem"] = $query_sanphammem->result;
			}
			
			
			
			$sqlstr_sanphamcung ="SELECT a.*, 
			a.ten_san_pham_chi_tieu as ten_san_pham,
			a.mau_tt_trong_nuoc as trong_nuoc,
			a.MAU_TT_THE_GIOI as the_gioi,
			a.dk_sl_quy_mo_sp as so_luong_quy_mo
			FROM nckh_san_pham_cung a  
			WHERE a.fk_ma_thuyet_minh_dt ='".$madetai."'";
			
			$query_sanphamcung = $this->getQuery($sqlstr_sanphamcung)->execute(false, array());
			$ret['sanphamcung'] = array();
			if($query_sanphamcung->itemsCount > 0){
				$ret ["sanphamcung"] = $query_sanphamcung->result;
			}
			
			// Du lieu B6.3
			$sqlstr_ketquadaotao="SELECT a.*, b.ten_bac as ten_capdt
			FROM nckh_kq_dao_tao_tmdt a, bac_dao_tao b 
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."' and a.fk_bac_dao_tao =b.ma_bac(+)";
			
			$query_ketquadaotao = $this->getQuery($sqlstr_ketquadaotao)->execute(false, array());
			$ret['ketquadaotao'] = array();
			if($query_ketquadaotao->itemsCount > 0){
				$ret ["ketquadaotao"] = $query_ketquadaotao->result;
			}
			
			// Du lieu B8
			$sqlstr_khoanchiphi="SELECT a.fk_ma_khoan_chi_phi, 
				b.ten_khoan_chi_phi, 
				nvl(a.kinh_phi,0) kinh_phi, 
				nvl(a.khoan_chi,0) khoan_chi, 
				nvl(a.phan_tram,0) phan_tram
			FROM nckh_tong_hop_kinh_phi a, nckh_dm_khoan_chi_phi b
			WHERE fk_ma_thuyet_minh_dt ='".$madetai."' and a.fk_ma_khoan_chi_phi = b.ma_khoan_chi_phi(+) 
			ORDER BY fk_ma_khoan_chi_phi";
			
			$query_khoanchiphi = $this->getQuery($sqlstr_khoanchiphi)->execute(false, array());
			
			$ret['khoanchiphi'] = array();
			if($query_khoanchiphi->itemsCount > 0){
				$ret ["khoanchiphi"] = $query_khoanchiphi->result;
			}
		}
		
		return $ret;
	}
}
