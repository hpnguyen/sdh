<?php
/**
 * 
 */
class CanBoGiangDayModel extends BaseTable {
	
	function __construct() {
		parent::init("nhan_su");
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	public function getCanBo()
	{
		$sqlstr="SELECT ma_can_bo, shcc, ten_bo_mon, ten_khoa, get_thanh_vien(c.ma_can_bo) cbgd
				FROM can_bo_giang_day c, bo_mon b, khoa k
				WHERE 	c.ma_bo_mon = b.ma_bo_mon
						and b.ma_khoa = k.ma_khoa
						and ma_hoc_vi in ('TS', 'TSK') 
						and c.trang_thai = 1 
				ORDER BY	ten_khoa, ten_bo_mon, ten_eng, ho_eng";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = array();
		if($check->itemsCount > 0){
			$ret = $check->result;
		}
		return $ret;
		
	}
	
	public function getUserLoginMaBoMon()
	{
		$user = base64_decode($_SESSION["uidloginPortal"]);
		$sqlstr="SELECT c.ma_bo_mon 
				FROM can_bo_giang_day c, nhan_su n 
				WHERE 	c.ma_can_bo = n.fk_ma_can_bo
				and upper(n.username)=upper('".$user."')";
		$check = $this->getQuery($sqlstr)
		->execute(false, array());
		$ret = null;
		
		if($check->itemsCount > 0){
			$ret = $check->result[0]['ma_bo_mon'];
		}
		
		return $ret;
	}
	
	public function getDetailForThuyetMinhDeTai($macb)
	{
		$sqlstr="SELECT cb.*, to_char(cb.NGAY_SINH,'dd-mm-yyyy') ngay_sinh, 
			decode(phai, 'M', 'Nam', 'F', 'Nữ') phai_desc, 
			k.ten_khoa, bm.ten_bo_mon,	v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, 
			qghv.ten_quoc_gia ten_nuoc_hv, hv.ten ten_hv, cb.chuyen_mon_bc_bo_gddt,
			decode(ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham, 
			get_thanh_vien(cb.ma_can_bo) hotencb,
			get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		FROM can_bo_giang_day cb, 
			bo_mon bm, khoa k, 
			dm_chuc_vu v, 
			bo_mon bmql, 
			quoc_gia qghv, 
			dm_hoc_vi hv
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) 
			and bm.ma_khoa = k.ma_khoa (+)
			and cb.fk_chuc_vu = v.ma_chuc_vu (+)
			and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
			and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
			and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
			and cb.ma_can_bo='".$macb."'";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
		}
		
		return $ret;
	}
	
	public function getDetailForLlkh($macb)	{
		$sqlstr="SELECT cb.*, to_char(cb.ngay_sinh,'dd-mm-yyyy') ngay_sinh, 
		decode(phai, 'M', 'Nam', 'F', 'Nữ') phai_desc, k.ten_khoa, bm.ten_bo_mon,
		v.ten_chuc_vu, bmql.ten_bo_mon ten_bo_mon_ql, qghv.ten_quoc_gia ten_nuoc_hv, hv.ten ten_hv, 
		cb.chuyen_mon_bc_bo_gddt, decode(ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham, 
		get_thanh_vien(cb.ma_can_bo) hotencb, get_nam_dat_hv_cao_nhat(cb.ma_can_bo, cb.ma_hoc_vi) nam_dat_hv_cao_nhat
		FROM can_bo_giang_day cb, 
			bo_mon bm, 
			khoa k, 
			dm_chuc_vu v, 
			bo_mon bmql, 
			quoc_gia qghv, 
			dm_hoc_vi hv
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) 
		and bm.ma_khoa = k.ma_khoa (+)
		and cb.fk_chuc_vu = v.ma_chuc_vu (+)
		and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
		and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
		and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
		and cb.ma_can_bo in ('".$macb."')";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			
			//Get qua trinh cong tac
			$sqlstrQtct="SELECT n.fk_chuc_vu, c.ten_chuc_vu, n.thoi_gian_kt, n.thoi_gian_bd, n.noi_cong_tac,
			n.chuyen_mon, n.dia_chi_co_quan, n.ma_qt_cong_tac
			FROM nckh_qua_trinh_cong_tac n, dm_chuc_vu c
			WHERE n.fk_chuc_vu=c.ma_chuc_vu (+) and fk_ma_can_bo='".$macb."'
			ORDER BY n.thoi_gian_bd desc"; 
			$ret['dm_qua_trinh_cong_tac'] = array();
			
			$retQtct = $this->getQuery($sqlstrQtct)->execute(false, array());
			if($retQtct->itemsCount > 0){
				$ret['dm_qua_trinh_cong_tac'] = $retQtct->result;
			}
			//Get bac dao tao
			$sqlstrBacDt="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, hdt.ten_he_dao_tao
			FROM NCKH_QUA_TRINH_DAO_TAO q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
			WHERE FK_MA_CAN_BO = '".$macb. "' and q.BAC_DAO_TAO = b.MA_BAC (+) and q.FK_NGANH = n.MA_NGANH (+)
				and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
			ORDER BY THOI_GIAN_TN";
			$retBacDt = $this->getQuery($sqlstrBacDt)->execute(false, array());
			$ret['dm_qua_trinh_dao_tao'] = array();
			if($retBacDt->itemsCount > 0){
				$ret['dm_qua_trinh_dao_tao'] = $retBacDt->result;
			}
			
			//Get danh muc linh vuc nghien cuu
			$sqlstrDmLvnc="SELECT q.TEN_LVNC, q.MA_LVNC, l.LVNC_KHAC 
			FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q 
			WHERE l.FK_MA_CAN_BO = '".$macb."' and l.FK_MA_LVNC = q.MA_LVNC";
			$retDmLvnc = $this->getQuery($sqlstrDmLvnc)->execute(false, array());
			$ret['dm_linh_vuc_nghien_cuu'] = array();
			if($retDmLvnc->itemsCount > 0){
				$ret['dm_linh_vuc_nghien_cuu'] = $retDmLvnc->result;
			}
			
			//Get danh muc huong de tai
			$sqlstrDmHuongDt="SELECT ma_de_tai, ten_de_tai, nam 
			FROM huong_de_tai 
			WHERE ma_can_bo ='".$macb."' 
			ORDER BY nam desc, ten_de_tai";
			$retDmHuongDt = $this->getQuery($sqlstrDmHuongDt)->execute(false, array());
			$ret['dm_huong_de_tai'] = array();
			if($retDmHuongDt->itemsCount > 0){
				$ret['dm_huong_de_tai'] = $retDmHuongDt->result;
			}
			
			//Get de tai nghien cuu khoa hoc
			$sqlstrDtNckh="SELECT a.*, 
			DECODE(a.CHU_NHIEM,1,'Chủ nhiệm','Tham gia') THAM_GIA, 
			DECODE(a.NGHIEM_THU,1,'x','') TT_NGHIEM_THU, 
			DECODE(a.KET_QUA,'X','Xuất sắc', 'T', 'Tốt', 'K','Khá','B', 'Trung Bình') TT_KET_QUA, b.ten_cap
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) 
			and a.ma_can_bo = '".$macb."' 
			ORDER BY a.nam_bat_dau desc"; 
			$retDtNckh = $this->getQuery($sqlstrDtNckh)->execute(false, array());
			$ret['dm_de_tai_nckh'] = array();
			if($retDtNckh->itemsCount > 0){
				$ret['dm_de_tai_nckh'] = $retDtNckh->result;
			}
			
			//Get danh sach hoc vien, sinh vien, nghien cuu sinh dang huong dan
			$sqlstrDsHuongDanHocVien="SELECT MA_HD_LUAN_AN, lower(HO_TEN_SV) ho_ten_sv, BAC_DAO_TAO, TEN_BAC, 
				MA_HOC_VIEN, NAM_TOT_NGHIEP, SAN_PHAM_MA_DE_TAI, TEN_LUAN_AN
			FROM NCKH_HD_LUAN_AN h, BAC_DAO_TAO b
			WHERE FK_MA_CAN_BO = '".$macb."' AND h.BAC_DAO_TAO = b.MA_BAC
			ORDER BY BAC_DAO_TAO, HO_TEN_SV";
			$retDsHuongDanHocVien = $this->getQuery($sqlstrDsHuongDanHocVien)->execute(false, array());
			$ret['ds_huong_dan_luan_an'] = array();
			if($retDsHuongDanHocVien->itemsCount > 0){
				$ret['ds_huong_dan_luan_an'] = $retDsHuongDanHocVien->result;
			}
			
			//Get danh sach sach da xuat ban quoc te
			$sqlstrDmSach ="SELECT MA_SACH, TEN_SACH, NHA_XUAT_BAN, NAM_XUAT_BAN, 
				DECODE(TAC_GIA,1,'tác giả','đồng tác giả') TAC_GIA_DESC,BUT_DANH, SAN_PHAM_MA_DE_TAI
			FROM sach 
			WHERE NUOC_NGOAI=1 and ma_can_bo = '".$macb."' 
			ORDER BY nam_xuat_ban desc";
			$retDmSach = $this->getQuery($sqlstrDmSach)->execute(false, array());
			$ret['dm_sach_xuat_ban_quoc_te'] = array();
			if($retDmSach->itemsCount > 0){
				$ret['dm_sach_xuat_ban_quoc_te'] = $retDmSach->result;
			}
			
			//Get danh sach sach da xuat ban trong nuoc
			$sqlstrDmSachTn ="SELECT MA_SACH, TEN_SACH, NHA_XUAT_BAN, NAM_XUAT_BAN, 
				DECODE(TAC_GIA,1,'tác giả','đồng tác giả') TAC_GIA_DESC,BUT_DANH, SAN_PHAM_MA_DE_TAI
			FROM SACH 
			WHERE (NUOC_NGOAI=0 or NUOC_NGOAI is null) and MA_CAN_BO = '".$macb."' 
			ORDER BY NAM_XUAT_BAN desc";
			
			$retDmSachTn = $this->getQuery($sqlstrDmSachTn)->execute(false, array());
			$ret['dm_sach_xuat_ban_trong_nuoc'] = array();
			if($retDmSachTn->itemsCount > 0){
				$ret['dm_sach_xuat_ban_trong_nuoc'] = $retDmSachTn->result;
			}
			
			//Get tap chi quoc te
			 $sqlstrTapChiQuocTe="SELECT c.*, q.ten_quoc_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q
			WHERE ma_can_bo = '".$macb."' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BQ'
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc";
			$retTapChiQuocTe = $this->getQuery($sqlstrTapChiQuocTe)->execute(false, array());
			$ret['tap_chi_quoc_te'] = array();
			if($retTapChiQuocTe->itemsCount > 0){
				$ret['tap_chi_quoc_te'] = $retTapChiQuocTe->result;
			}
			
			//Get tap chi trong nuoc
			$sqlstrTapChiTrongNuoc="SELECT c.*, q.ten_quoc_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q
			WHERE ma_can_bo = '".$macb."' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BT'
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc";
			$retTapChiTrongNuoc = $this->getQuery($sqlstrTapChiTrongNuoc)->execute(false, array());
			$ret['tap_chi_trong_nuoc'] = array();
			if($retTapChiTrongNuoc->itemsCount > 0){
				$ret['tap_chi_trong_nuoc'] = $retTapChiTrongNuoc->result;
			}
			
			//Get ky yeu hoi nghi quoc te
			$sqlstrKyYeuHoiNghiQuocTe="SELECT c.*, q.ten_quoc_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q
			WHERE ma_can_bo = '".$macb."' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='HQ'
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc";
			$retKyYeuHoiNghiQuocTe = $this->getQuery($sqlstrKyYeuHoiNghiQuocTe)->execute(false, array());
			$ret['ky_yeu_hnqt'] = array();
			if($retKyYeuHoiNghiQuocTe->itemsCount > 0){
				$ret['ky_yeu_hnqt'] = $retKyYeuHoiNghiQuocTe->result;
			}
			
			//Get ky yeu trong nuoc
			$sqlstrKyYeuTrongNuoc="SELECT c.*, q.ten_quoc_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q
			WHERE ma_can_bo = '".$macb."' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='HT'
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc";
			$retKyYeuTrongNuoc = $this->getQuery($sqlstrKyYeuTrongNuoc)->execute(false, array());
			$ret['ky_yeu_trong_nuoc'] = array();
			if($retKyYeuTrongNuoc->itemsCount > 0){
				$ret['ky_yeu_trong_nuoc'] = $retKyYeuTrongNuoc->result;
			}
			
			//Get giai thuong
			$sqlstrGiaiThuong ="SELECT n.MA_GIAI_THUONG_KHCN, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
			n.NOI_DUNG_GIAI_THUONG, n.NUOC_CAP, n.TEN_GIAI_THUONG, n.FK_MA_CAN_BO
			FROM NCKH_GIAI_THUONG_KHCN n, QUOC_GIA c 
			WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+) AND FK_MA_CAN_BO='".$macb."'
			ORDER BY n.NAM_CAP desc";
			$retGiaiThuong = $this->getQuery($sqlstrGiaiThuong)->execute(false, array());
			$ret['giai_thuong'] = array();
			if($retGiaiThuong->itemsCount > 0){
				$ret['giai_thuong'] = $retGiaiThuong->result;
			}
			
			//Get phat minh sang che
			$sqlstrPhatMinhSangChe="SELECT n.MA_BANG_SANG_CHE, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
			n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
			decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh
			FROM NCKH_BANG_SANG_CHE n, QUOC_GIA c 
			WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+) AND FK_MA_CAN_BO='".$macb."'
			ORDER BY n.NAM_CAP desc";
			$retPhatMinhSangChe = $this->getQuery($sqlstrPhatMinhSangChe)->execute(false, array());
			$ret['phat_minh_sang_che'] = array();
			if($retPhatMinhSangChe->itemsCount > 0){
				$ret['phat_minh_sang_che'] = $retPhatMinhSangChe->result;
			}
			
			//Get bang giai phap
			$sqlstrBangGiaiPhap ="SELECT n.MA_BANG_GP_HUU_ICH, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
			n.NUOC_CAP, n.TEN_BANG, n.FK_MA_CAN_BO, n.SAN_PHAM_MA_DE_TAI, n.SO_HIEU_BANG,
			decode(n.TAC_GIA, '1','tác giả chính', 'đồng tác giả') Tac_gia_chinh, n.TAC_GIA 
			FROM NCKH_BANG_GP_HUU_ICH n, QUOC_GIA c 
			WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+) AND FK_MA_CAN_BO='".$macb."'
			ORDER BY n.NAM_CAP desc";
			$retBangGiaiPhap = $this->getQuery($sqlstrBangGiaiPhap)->execute(false, array());
			$ret['bang_giai_phap'] = array();
			if($retBangGiaiPhap->itemsCount > 0){
				$ret['bang_giai_phap'] = $retBangGiaiPhap->result;
			}
			
			//Get Ung dung
			$sqlstrUngDung="SELECT MA_UD_THUC_TIEN, TEN_CONG_NGHE_GP_HU , HINH_THUC, 
			QUY_MO, DIA_CHI_AP_DUNG, FK_MA_CAN_BO, THOI_GIAN_CG, SAN_PHAM_MA_DE_TAI
			FROM NCKH_UD_THUC_TIEN
			WHERE FK_MA_CAN_BO='".$macb."'";
			$retUngDung = $this->getQuery($sqlstrUngDung)->execute(false, array());
			$ret['ung_dung_thuc_tien'] = array();
			if($retUngDung->itemsCount > 0){
				$ret['ung_dung_thuc_tien'] = $retUngDung->result;
			}
			
			//Get nckh tham gia chuong trinh
			$sqlstrNckhThamGiaChuongTrinh="SELECT FK_MA_CAN_BO,MA_TG_CHUONG_TRINH,TEN_CHUONG_TRINH,
				CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT,NUOC_NGOAI, 
				decode(NUOC_NGOAI, '1','ngoài nước', '0','trong nước') nuoc_ngoai_desc
			FROM NCKH_THAM_GIA_CHUONG_TRINH n
			WHERE FK_MA_CAN_BO='".$macb."'
			ORDER BY THOI_GIAN_BD desc";
			$retNckhThamGiaChuongTrinh = $this->getQuery($sqlstrNckhThamGiaChuongTrinh)->execute(false, array());
			$ret['nckh_tham_gia_ct'] = array();
			if($retNckhThamGiaChuongTrinh->itemsCount > 0){
				$ret['nckh_tham_gia_ct'] = $retNckhThamGiaChuongTrinh->result;
			}
			
			//Get tham gia hiep hoi, khoa hoc ky thuat
			$sqlstrThamGiaHiepHoi="SELECT FK_MA_CAN_BO,MA_TG_HH_TC_HN,TEN_HH_TC_HN,CHUC_DANH,THOI_GIAN_BD,THOI_GIAN_KT, decode(LOAI, 'H','Hiệp hội khoa học', 'T','Tạp chí khoa học', 'HN','Hội nghị khoa học công nghệ') loai_desc, loai
			FROM NCKH_THAM_GIA_HH_TC_HN n
			WHERE FK_MA_CAN_BO='".$macb."'
			ORDER BY THOI_GIAN_BD desc";
			$retThamGiaHiepHoi = $this->getQuery($sqlstrThamGiaHiepHoi)->execute(false, array());
			$ret['tham_gia_hiep_hoi'] = array();
			if($retThamGiaHiepHoi->itemsCount > 0){
				$ret['tham_gia_hiep_hoi'] = $retThamGiaHiepHoi->result;
			}
			
			//Get ngoai ngu
			$sqlstrDmNgoaiNgu="SELECT FK_MA_CAN_BO, FK_MA_NGOAI_NGU,a.TEN_NGOAI_NGU,KY_NANG_NGHE,KY_NANG_NOI,
				KY_NANG_DOC,KY_NANG_VIET,GHI_CHU
			FROM NCKH_QT_NGOAI_NGU n, 
				DM_NGOAI_NGU a
			WHERE FK_MA_CAN_BO='".$macb."' 
				and n.FK_MA_NGOAI_NGU = a.MA_NGOAI_NGU
			ORDER BY a.TEN_NGOAI_NGU";
			
			$ret['dm_ngoai_ngu'] = array();
			
			$retNgoaiNgu = $this->getQuery($sqlstrDmNgoaiNgu)->execute(false, array());
			if($retNgoaiNgu->itemsCount > 0){
				$ret['dm_ngoai_ngu'] = $retNgoaiNgu->result;
			}
			//Get tham gia nghien cuu khoa hoc truong vien
			$sqlstrNckhTruongVien="SELECT FK_MA_CAN_BO,MA_TG_TRUONG_VIEN,TEN_TRUONG_VIEN,NOI_DUNG_THAM_GIA,THOI_GIAN_BD,THOI_GIAN_KT
			FROM NCKH_THAM_GIA_TRUONG_VIEN n
			WHERE FK_MA_CAN_BO='".$macb."'
			ORDER BY THOI_GIAN_BD desc";
			
			$ret['dm_nckh_truong_vien'] = array();
			$retNckhTruongVien = $this->getQuery($sqlstrNckhTruongVien)->execute(false, array());
			if($retNckhTruongVien->itemsCount > 0){
				$ret['dm_nckh_truong_vien'] = $retNckhTruongVien->result;
			}
		}
		
		return $ret;
	}
	
	public function getDetailForTtkh($macb)	{
		$sqlstr="SELECT cb.*, 
		to_char(cb.ngay_sinh,'dd-mm-yyyy') ngay_sinh, 
		k.ten_khoa, bm.ten_bo_mon, get_thanh_vien(cb.ma_can_bo) hotencb,
		decode(cb.ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham,
		q.ten_quoc_gia as ten_quoc_gia_hv,
		nk.ten_nganh as nk_ten_nganh
		FROM can_bo_giang_day cb, 
			bo_mon bm, 
			khoa k ,
			quoc_gia q,
			nckh_nganh_dt nk
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) 
			and bm.ma_khoa = k.ma_khoa (+) 
			and cb.qg_dat_hoc_vi = q.ma_quoc_gia (+) 
			and cb.fk_nganh = nk.ma_nganh (+)
			and length(nk.ma_nganh) = 8 
			and nk.bac_dao_tao = 'TS'
			and cb.ma_can_bo = '".$macb."'";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			
			//Get huong de tai nghien cuu
			$sqlstr="select * from huong_de_tai where ma_can_bo = '$macb' order by nam desc, ten_de_tai";
			
			$ret['dm_huong_de_tai'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_huong_de_tai'] = $retDm->result;
			}
			
			//Get so luong luan an thanh cong/ that bai
			$sqlstr="SELECT
			(	select count(l.ma_hoc_vien)
				from luan_an_tien_sy l 
				where (l.huong_dan_1 = '".$macb."' or l.huong_dan_2 = '".$macb."' or l.huong_dan_3 = '".$macb."') 
					and l.dot_cap_bang is null
			) dang_huong_dan,
			(	select count(l.ma_hoc_vien)
				from luan_an_tien_sy l 
				where (l.huong_dan_1 = '".$macb."' or l.huong_dan_2 = '".$macb."' or l.huong_dan_3 = '".$macb."') 
					and l.dot_cap_bang is not null
			) thanh_cong
			FROM dual";
			
			$ret['la_dang_huong_dan'] = null;
			$ret['la_thanh_cong'] = null;
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$item = $retDm->result[0];
				$ret['la_dang_huong_dan'] = $item['dang_huong_dan']; 
				$ret['la_thanh_cong'] = $item['thanh_cong'];
			}
			
			//Get so luong huong dan luan van thac sy
			$sqlstr="SELECT count(l.ma_hoc_vien) huong_dan_th 
			FROM luan_van_thac_sy l, hoc_vien h
			WHERE diem_luan_van(l.ma_hoc_vien)>=5 
			and (huong_dan_chinh = '".$macb."' or huong_dan_phu = '".$macb."')
			and h.ma_hoc_vien = l.ma_hoc_vien 
			and dot_nhan_lv = dot_nhan_lv(h.ma_hoc_vien)";
			$ret['huong_dan_th'] = null;
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$item = $retDm->result[0];
				$ret['huong_dan_th'] = $item['huong_dan_th'];
			}
			
			//Get ten mon hoc va cac nganh cua mon
			$sqlstr="SELECT DISTINCT titlecase(m.TEN) ten_mon_hoc 
			FROM THOI_KHOA_BIEU t, MON_HOC m 
			WHERE t.MA_MH = m.MA_MH 
				AND (t.ma_can_bo='".$macb."' OR t.ma_can_bo_phu='".$macb."')
			ORDER BY ten_mon_hoc";
			$ret['mon_giang_day'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['mon_giang_day'] = $retDm->result;
				
				$tempArray = array();
				foreach ($ret['mon_giang_day'] as $i => $row) {
					$sqlstrTemp = "SELECT distinct titlecase(ten_nganh) ten_nganh_title
					FROM thoi_khoa_bieu t1, nganh n
					WHERE t1.ma_nganh_in = n.ma_nganh
					and (t1.ma_can_bo = '".$macb."' OR t1.ma_can_bo_phu='".$macb."')
					and TitleCase(t1.ten_mh) = '".$row["ten_mon_hoc"]."'";
					
					$temp = $this->getQuery($sqlstrTemp)->execute(false, array());
					$tempString = '';
					
					if($temp->itemsCount > 0){
						foreach ($temp->result as $k => $v) {
							$tempString .= (($tempString != '') ? ', ' : '' ).$v['ten_nganh_title'];
						}
					}
					
					$row['mon_giang_day_mon_nganh'] = $tempString;
					$tempArray[] = $row;
				}
				$ret['mon_giang_day'] = $tempArray;
			}
			
			//Get de tai khoa hoc
			$sqlstr="SELECT a.*, DECODE(a.CHU_NHIEM,1,'Chủ nhiệm','Tham gia') tham_gia, 
			decode(a.ngay_nghiem_thu, null,'','x') tt_nghiem_thu, 
			decode(a.ket_qua,'X','Xuất sắc', 'T', 'Tốt', 'K','Khá','B', 'Trung Bình') tt_ket_qua, b.ten_cap
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) and 
			a.ma_can_bo = '".$macb. "' 
			ORDER BY a.nam_bat_dau desc";
			$ret['dm_de_tai_khoa_hoc'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_de_tai_khoa_hoc'] = $retDm->result;
			}
			
			//Get bai bao, nghien cuu khoa hoc, tap chi khoa hoc
			$sqlstr="SELECT * 
			FROM cong_trinh_khoa_hoc where ma_can_bo = '".$macb. "' 
			ORDER BY loai_cong_trinh, nam_xuat_ban_tap_chi desc";
			$ret['dm_bb_nckh_tckh'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_bb_nckh_tckh'] = $retDm->result;
			} 
		}
		
		return $ret;
	}
	
	public function getDetailForLlkhCndt($macb)	{
		$sqlstr="SELECT cb.*, to_char(cb.ngay_sinh,'dd-mm-yyyy') ngay_sinh,
			decode(cb.phai, 'M', 'Ông', 'F', 'Bà') title, 
			decode(cb.phai, 'M', 'Nam', 'F', 'Nữ') phai_desc, 
			k.ten_khoa, bm.ten_bo_mon,
			v.ten_chuc_vu, 
			bmql.ten_bo_mon ten_bo_mon_ql, 
			qghv.ten_quoc_gia ten_nuoc_hv, 
			hv.ten ten_hv, cb.chuyen_mon_bc_bo_gddt,
			decode(cb.ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham, 
			get_thanh_vien(cb.ma_can_bo) hotencb
		FROM can_bo_giang_day cb, 
			bo_mon bm, 
			khoa k, dm_chuc_vu v, bo_mon bmql, quoc_gia qghv, dm_hoc_vi hv
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
			and cb.fk_chuc_vu = v.ma_chuc_vu (+)
			and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
			and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
			and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
			and cb.ma_can_bo='".$macb."'";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			
			//Get qua trinh dao tao
			$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, hdt.ten_he_dao_tao
			FROM nckh_qua_trinh_dao_tao q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
			WHERE fk_ma_can_bo = '".$macb. "' 
				and q.bac_dao_tao = b.MA_BAC (+) 
				and q.FK_NGANH = n.MA_NGANH (+)
				and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA 
				and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
			ORDER BY thoi_gian_tn";
			$ret['dm_qua_trinh_dao_tao'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_dao_tao'] = $retDm->result;
			}
			
			//Get qua trinh cong tac
			$sqlstr="SELECT n.fk_chuc_vu, c.ten_chuc_vu, n.thoi_gian_kt, n.thoi_gian_bd, n.noi_cong_tac, 
				n.chuyen_mon, n.dia_chi_co_quan, n.ma_qt_cong_tac
			FROM nckh_qua_trinh_cong_tac n, dm_chuc_vu c 
			WHERE n.fk_chuc_vu=c.ma_chuc_vu (+) and fk_ma_can_bo = '".$macb."'
			ORDER BY n.thoi_gian_bd desc";
			$ret['dm_qua_trinh_cong_tac'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_cong_tac'] = $retDm->result;
			}
			
			//Get cong trinh
			$sqlstr="SELECT c.*, q.ten_quoc_gia, l.ten_loai_tac_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q, loai_tac_gia l
			WHERE ma_can_bo = '".$macb."' and c.fk_quoc_gia = q.ma_quoc_gia(+) 
			and (c.loai_cong_trinh='BQ' or c.loai_cong_trinh='BT') and c.fk_ma_loai_tac_gia = l.ma_loai_tac_gia (+)
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc";
			$ret['dm_qua_trinh_cong_trinh'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_cong_trinh'] = $retDm->result;
			}
			
			//Get danh muc bang sang che
			$sqlstr="SELECT n.MA_BANG_SANG_CHE, n.NAM_CAP, n.TEN_BANG, n.FK_MA_CAN_BO
			FROM NCKH_BANG_SANG_CHE n 
			WHERE FK_MA_CAN_BO='".$macb."' 
			ORDER BY n.NAM_CAP desc";
			$ret['dm_bang_sang_che'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_bang_sang_che'] = $retDm->result;
			}
			
			//Get danh muc nghien cuu ung dung thuc tien
			$sqlstr="SELECT * 
			FROM NCKH_UD_THUC_TIEN
			WHERE FK_MA_CAN_BO='".$macb."' 
			ORDER BY thoi_gian_bd desc";
			$ret['dm_ncud'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_ncud'] = $retDm->result;
			}
			
			//Get danh muc de tai de an
			$sqlstr="SELECT a.*, 
				decode (a.chu_nhiem, 1,'CN','TG') tham_gia, 
				decode(a.ngay_nghiem_thu, null,'chưa nghiệm thu', 'đã nghiệm thu') tt_nghiem_thu, 
				b.ten_cap,a.kinh_phi
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '".$macb."' 
				and a.CHU_NHIEM = 1 
			ORDER BY a.nam_bat_dau desc";
			$ret['dm_de_an_de_tai'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_de_an_de_tai'] = $retDm->result;
			}
			
			//Get danh muc de tai de an 2
			$sqlstr="SELECT a.*, 
				decode (a.chu_nhiem, 1,'CN','TG') tham_gia, 
				decode(a.ngay_nghiem_thu, null,'chưa nghiệm thu', 'đã nghiệm thu') tt_nghiem_thu, 
				b.ten_cap,a.kinh_phi
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '".$macb."' and a.CHU_NHIEM <> 1 
			ORDER BY a.nam_bat_dau desc";
			$ret['dm_de_an_de_tai2'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_de_an_de_tai2'] = $retDm->result;
			}
			
			//Get danh muc giai thuong
			$sqlstr="SELECT n.MA_GIAI_THUONG_KHCN, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
				n.NOI_DUNG_GIAI_THUONG, n.NUOC_CAP, n.TEN_GIAI_THUONG, n.FK_MA_CAN_BO
			FROM NCKH_GIAI_THUONG_KHCN n, QUOC_GIA c 
			WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
			AND FK_MA_CAN_BO='".$macb."'
			ORDER BY n.NAM_CAP desc";
			$ret['dm_giai_thuong'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_giai_thuong'] = $retDm->result;
			}
			
			//Get danh muc thanh tuu khcn
			$sqlstr="SELECT FK_MA_CAN_BO, MA_THANH_TUU_KHCN, THANH_TUU_KHCN, NAM
			FROM NCKH_THANH_TUU_KHCN
			WHERE fk_ma_can_bo='".$macb."'
			ORDER BY NAM desc";
			$ret['dm_thanh_tuu_khcn'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_thanh_tuu_khcn'] = $retDm->result;
			}
			
			//Get linh vuc nghien cuu
			$sqlstr="SELECT lower(q.TEN_LVNC) TEN_LVNC, q.MA_LVNC, substr(q.MA_LVNC, 1,1) MA_LVNC_C1, lower(q1.ten_lvnc) ten_lvnc_c1, lower(l.LVNC_KHAC) LVNC_KHAC, l.NAM
			FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q, NCKH_LVNC_KHCN q1
			WHERE l.FK_MA_CAN_BO = '".$macb."' and l.FK_MA_LVNC = q.MA_LVNC (+)
			AND substr(q.MA_LVNC, 1,1) = q1.ma_lvnc (+)
			AND to_number(to_char(sysdate,'yyyy'))-l.nam <= 5";
			$ret['dm_linh_vuc_nghien_cuu'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_linh_vuc_nghien_cuu'] = $retDm->result;
			}
			
			//Get danh muc ngoai ngu
			$sqlstr="SELECT FK_MA_CAN_BO,FK_MA_NGOAI_NGU,a.TEN_NGOAI_NGU,
				KY_NANG_NGHE,KY_NANG_NOI,KY_NANG_DOC,KY_NANG_VIET,GHI_CHU
			FROM NCKH_QT_NGOAI_NGU n, DM_NGOAI_NGU a
			WHERE FK_MA_CAN_BO='".$macb."' and n.FK_MA_NGOAI_NGU = a.MA_NGOAI_NGU
			ORDER BY a.TEN_NGOAI_NGU";
			$ret['dm_ngoai_ngu'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_ngoai_ngu'] = $retDm->result;
			}
			
			//Get danh muc kinh nghiem
			$sqlstr="SELECT FK_MA_CAN_BO,MA_KINH_NGHIEM_QLDG,HINH_THUC_HOI_DONG,NAM,GHI_CHU
			FROM NCKH_KINH_NGHIEM_QLDG n
			WHERE FK_MA_CAN_BO='".$macb."' AND (TO_NUMBER(TO_CHAR(SYSDATE, 'yyyy'))-NAM) <= 5
			ORDER BY NAM desc";
			$ret['dm_kinh_nghiem'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_kinh_nghiem'] = $retDm->result;
			}
			
			//Get danh muc chuyen gia cung nganh
			$sqlstr="SELECT FK_MA_CAN_BO,MA_GT_CHUYEN_GIA,HO_TEN,NOI_CONG_TAC,DIA_CHI_LIEN_LAC,DIEN_THOAI,EMAIL
			FROM NCKH_GT_CHUYEN_GIA n
			WHERE FK_MA_CAN_BO='".$macb."'
			ORDER BY HO_TEN";
			$ret['dm_chuyen_gia_cung_nganh'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_chuyen_gia_cung_nganh'] = $retDm->result;
			}
		}
		
		return $ret;
	}
	
	public function getDetailForLlkhCgkhcn($macb)	{
		$sqlstr="SELECT cb.*, to_char(cb.ngay_sinh,'dd-mm-yyyy') ngay_sinh,
			decode(cb.phai, 'M', 'Ông', 'F', 'Bà') title, 
			decode(cb.phai, 'M', 'Nam', 'F', 'Nữ') phai_desc, 
			k.ten_khoa, bm.ten_bo_mon,
			v.ten_chuc_vu, 
			bmql.ten_bo_mon ten_bo_mon_ql, 
			qghv.ten_quoc_gia ten_nuoc_hv, 
			hv.ten ten_hv, cb.chuyen_mon_bc_bo_gddt,
			decode(cb.ma_hoc_ham, 'GS','Giáo sư', 'PGS','Phó giáo sư', '') ten_hoc_ham, 
			get_thanh_vien(cb.ma_can_bo) hotencb
		FROM can_bo_giang_day cb, 
			bo_mon bm, 
			khoa k, dm_chuc_vu v, bo_mon bmql, quoc_gia qghv, dm_hoc_vi hv
		WHERE cb.ma_bo_mon = bm.ma_bo_mon (+) and bm.ma_khoa = k.ma_khoa (+)
			and cb.fk_chuc_vu = v.ma_chuc_vu (+)
			and cb.ma_bo_mon_ql = bmql.ma_bo_mon (+)
			and cb.qg_dat_hoc_vi = qghv.ma_quoc_gia (+)
			and cb.ma_hoc_vi = hv.ma_hoc_vi (+)
			and cb.ma_can_bo='".$macb."'";
		
		$check = $this->getQuery($sqlstr)->execute(false, array());
		$ret = array();
		
		if($check->itemsCount > 0){
			$ret = $check->result[0];
			
			//Get qua trinh dao tao
			$sqlstr="SELECT q.*, b.TEN_BAC, n.TEN_NGANH, g.TEN_QUOC_GIA, hdt.ten_he_dao_tao
			FROM nckh_qua_trinh_dao_tao q, bac_dao_tao b, nckh_nganh_dt n, quoc_gia g, dm_he_dao_tao hdt
			WHERE fk_ma_can_bo = '".$macb. "' 
				and q.bac_dao_tao = b.MA_BAC (+) 
				and q.FK_NGANH = n.MA_NGANH (+)
				and q.QG_DAT_HOC_VI = g.MA_QUOC_GIA 
				and q.fk_he_dao_tao = hdt.ma_he_dao_tao (+)
			ORDER BY thoi_gian_tn";
			$ret['dm_qua_trinh_dao_tao'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_dao_tao'] = $retDm->result;
			}
			
			//Get qua trinh cong tac
			$sqlstr="SELECT n.fk_chuc_vu, c.ten_chuc_vu, n.thoi_gian_kt, n.thoi_gian_bd, n.noi_cong_tac, 
				n.chuyen_mon, n.dia_chi_co_quan, n.ma_qt_cong_tac
			FROM nckh_qua_trinh_cong_tac n, dm_chuc_vu c 
			WHERE n.fk_chuc_vu=c.ma_chuc_vu (+) and fk_ma_can_bo = '".$macb."'
			ORDER BY n.thoi_gian_bd desc";
			$ret['dm_qua_trinh_cong_tac'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_cong_tac'] = $retDm->result;
			}
			
			//Get cong trinh
			$sqlstr="select c.*, q.ten_quoc_gia, l.ten_loai_tac_gia
			FROM cong_trinh_khoa_hoc c, quoc_gia q, loai_tac_gia l
			WHERE ma_can_bo = '$macb' and c.fk_quoc_gia = q.ma_quoc_gia(+) and c.loai_cong_trinh='BQ'
			and c.fk_ma_loai_tac_gia = l.ma_loai_tac_gia (+)
			ORDER BY c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
			$ret['dm_qua_trinh_cong_trinh'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_qua_trinh_cong_trinh'] = $retDm->result;
			}
			
			//Get danh muc bang sang che
			$sqlstr="SELECT n.MA_BANG_SANG_CHE, n.NAM_CAP, n.TEN_BANG, n.FK_MA_CAN_BO
			FROM NCKH_BANG_SANG_CHE n 
			WHERE FK_MA_CAN_BO='".$macb."' 
			ORDER BY n.NAM_CAP desc";
			$ret['dm_bang_sang_che'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_bang_sang_che'] = $retDm->result;
			}
			
			//Get danh muc nghien cuu ung dung thuc tien
			$sqlstr="SELECT * 
			FROM NCKH_UD_THUC_TIEN
			WHERE FK_MA_CAN_BO='".$macb."' 
			ORDER BY thoi_gian_bd desc";
			$ret['dm_ncud'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_ncud'] = $retDm->result;
			}
			
			//Get danh muc de tai de an
			$sqlstr="SELECT a.*, 
				decode (a.chu_nhiem, 1,'CN','TG') tham_gia, 
				decode(a.ngay_nghiem_thu, null,'chưa nghiệm thu', 'đã nghiệm thu') tt_nghiem_thu, 
				b.ten_cap,a.kinh_phi
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '".$macb."' 
				and a.CHU_NHIEM = 1 
			ORDER BY a.nam_bat_dau desc";
			$ret['dm_de_an_de_tai'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_de_an_de_tai'] = $retDm->result;
			}
			
			//Get danh muc de tai de an 2
			$sqlstr="SELECT a.*, 
				decode (a.chu_nhiem, 1,'CN','TG') tham_gia, 
				decode(a.ngay_nghiem_thu, null,'chưa nghiệm thu', 'đã nghiệm thu') tt_nghiem_thu, 
				b.ten_cap,a.kinh_phi
			FROM de_tai_nckh a, cap_de_tai b
			WHERE a.fk_cap_de_tai = b.ma_cap(+) and a.ma_can_bo = '".$macb."' and a.CHU_NHIEM <> 1 
			ORDER BY a.nam_bat_dau desc";
			$ret['dm_de_an_de_tai2'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_de_an_de_tai2'] = $retDm->result;
			}
			
			//Get danh muc giai thuong
			$sqlstr="SELECT n.MA_GIAI_THUONG_KHCN, c.TEN_QUOC_GIA TEN_NUOC_CAP, n.NAM_CAP, n.NOI_CAP, 
				n.NOI_DUNG_GIAI_THUONG, n.NUOC_CAP, n.TEN_GIAI_THUONG, n.FK_MA_CAN_BO
			FROM NCKH_GIAI_THUONG_KHCN n, QUOC_GIA c 
			WHERE n.NUOC_CAP=c.MA_QUOC_GIA (+)
			AND FK_MA_CAN_BO='".$macb."'
			ORDER BY n.NAM_CAP desc";
			$ret['dm_giai_thuong'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_giai_thuong'] = $retDm->result;
			}
			
			//Get danh muc thanh tuu khcn
			$sqlstr="SELECT FK_MA_CAN_BO, MA_THANH_TUU_KHCN, THANH_TUU_KHCN, NAM
			FROM NCKH_THANH_TUU_KHCN
			WHERE fk_ma_can_bo='".$macb."'
			ORDER BY NAM desc";
			$ret['dm_thanh_tuu_khcn'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_thanh_tuu_khcn'] = $retDm->result;
			}
			
			//Get linh vuc nghien cuu
			$sqlstr="SELECT lower(q.TEN_LVNC) TEN_LVNC, q.MA_LVNC, substr(q.MA_LVNC, 1,1) MA_LVNC_C1, lower(q1.ten_lvnc) ten_lvnc_c1, lower(l.LVNC_KHAC) LVNC_KHAC, l.NAM
			FROM NCKH_LVNC_KHCN_CBGD l, NCKH_LVNC_KHCN q, NCKH_LVNC_KHCN q1
			WHERE l.FK_MA_CAN_BO = '".$macb."' and l.FK_MA_LVNC = q.MA_LVNC (+)
			AND substr(q.MA_LVNC, 1,1) = q1.ma_lvnc (+)
			AND to_number(to_char(sysdate,'yyyy'))-l.nam <= 5";
			$ret['dm_linh_vuc_nghien_cuu'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_linh_vuc_nghien_cuu'] = $retDm->result;
			}
			
			//Get danh muc ngoai ngu
			$sqlstr="SELECT FK_MA_CAN_BO,FK_MA_NGOAI_NGU,a.TEN_NGOAI_NGU,
				KY_NANG_NGHE,KY_NANG_NOI,KY_NANG_DOC,KY_NANG_VIET,GHI_CHU
			FROM NCKH_QT_NGOAI_NGU n, DM_NGOAI_NGU a
			WHERE FK_MA_CAN_BO='".$macb."' and n.FK_MA_NGOAI_NGU = a.MA_NGOAI_NGU
			ORDER BY a.TEN_NGOAI_NGU";
			$ret['dm_ngoai_ngu'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_ngoai_ngu'] = $retDm->result;
			}
			
			//Get danh muc kinh nghiem
			$sqlstr="SELECT FK_MA_CAN_BO,MA_KINH_NGHIEM_QLDG,HINH_THUC_HOI_DONG,NAM,GHI_CHU
			FROM NCKH_KINH_NGHIEM_QLDG n
			WHERE FK_MA_CAN_BO='".$macb."' AND (TO_NUMBER(TO_CHAR(SYSDATE, 'yyyy'))-NAM) <= 5
			ORDER BY NAM desc";
			$ret['dm_kinh_nghiem'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_kinh_nghiem'] = $retDm->result;
			}
			
			//Get danh muc chuyen gia cung nganh
			$sqlstr="SELECT FK_MA_CAN_BO,MA_GT_CHUYEN_GIA,HO_TEN,NOI_CONG_TAC,DIA_CHI_LIEN_LAC,DIEN_THOAI,EMAIL
			FROM NCKH_GT_CHUYEN_GIA n
			WHERE FK_MA_CAN_BO='".$macb."'
			ORDER BY HO_TEN";
			$ret['dm_chuyen_gia_cung_nganh'] = array();
			$retDm = $this->getQuery($sqlstr)->execute(false, array());
			if($retDm->itemsCount > 0){
				$ret['dm_chuyen_gia_cung_nganh'] = $retDm->result;
			}
		}
		
		return $ret;
	}
}