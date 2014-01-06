<?
   // cat = detai, ttgv, ctkh, sach, hdlvts, hdlats
   // act = add, edit, del
error_reporting(0);

$sid = $_REQUEST["hisid"];

if ($sid!=""){
	session_id($sid);
	session_start();
}

$search = array('\\',"'",'"');
$replace = array('\\\\',"\'","&quot;"); 

$searchdb = array("\\\\","\\'",'\\"', "'");
$replacedb = array("\\","'", '"', "''");

if (isset($_SESSION["uidloginPortal"]) && isset($_SESSION["macb"])) 
{
		$macb = $_SESSION["macb"];
		//$macb = str_replace($temp,"",$macb);
	
	   include "../libs/connect.php";
	   $cat = $_POST['cat'];
	   $action = $_POST['act'];
	 
	if ($cat == "detai") {
		
		if ($action=="add" || $action=="edit") // Them de tai hay edit
		{
		
			$dtHuongDeTai = str_replace($searchdb, $replacedb,$_POST['dtHuongDeTai']);
			//$dtHuongDeTai = htmlspecialchars($_POST['dtHuongDeTai']);
			
			if ($action=="add") // Add de tai
			{
				$sqlstr = "insert into huong_de_tai(ma_de_tai, ten_de_tai, nam, ma_can_bo) values
						(get_ma_de_tai, '".$dtHuongDeTai."' ," .$_POST['dtNam']." ,'".$macb."')";
			}
			else // Edit de tai
				$sqlstr = "update huong_de_tai set ten_de_tai='".$dtHuongDeTai."', 
							nam = " .$_POST['dtNam']. 
							" where ma_de_tai ='".base64_decode($_POST["madtedit"])."'";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $dtHuongDeTai;
			
		} // end of ($act=="add")
		else if ($action=="del") // Delete de tai
		{
			
			$sqlstr = "select count(*) tongdt from huong_de_tai where ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $danhsachdetai);
			oci_free_statement($stmt);
			$n=$danhsachdetai["TONGDT"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["dtchk".$i]=="1")
					$detaidel = $detaidel.base64_decode($_POST["MaDeTai".$i]).",";
			$detaidel = substr($detaidel, 0, -1);
			$sqlstr = "delete huong_de_tai where ma_can_bo ='".$macb."' and ma_de_tai in (" .$detaidel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $sqlstr;
		} // end of ($act=="del")

	} // end of ($cat=="detai")
	
	// Xu ly cho Cong Trinh Khoa Hoc
	else if ($cat=="ctkh")
	{
		if ($action=="add" || $action=="edit") // add edit Cong trinh khoa hoc
		{
			$ctkhSoTapChi = str_replace($searchdb, $replacedb,$_POST['txtSoTapChi']);
			$ctkhNamXB = str_replace($searchdb, $replacedb,$_POST['txtNamXB']);
			$ctkhTrangDang = str_replace($searchdb, $replacedb,$_POST['txtTrangDangBaiBao']);
			$ctkhTenTapChi = str_replace($searchdb, $replacedb,$_POST['txtTenTapChi']);
			$ctkhTenBaiBao = str_replace($searchdb, $replacedb,$_POST['txtTenBaiBao']);
			$ctkhTenTacGia = str_replace($searchdb, $replacedb,$_POST['txtTenTacGia']);
			$txtTenTacGiaChinh = str_replace($searchdb, $replacedb,$_POST['txtTenTacGiaChinh']);
			$txtTenTacGiaVNU = str_replace($searchdb, $replacedb,$_POST['txtTenTacGiaVNU']);
			$txtThuocLinhVuc = str_replace($searchdb, $replacedb,$_POST['txtThuocLinhVuc']);
			$txtLinkBaibao = str_replace($searchdb, $replacedb,$_POST['txtLinkBaibao']);
			$ctkhLoaiCongTrinh = str_replace($searchdb, $replacedb,$_POST['txtLoaiCongTrinh']);
			$ctkhISBN = str_replace($searchdb, $replacedb,$_POST['txtISBN']);
			$ctkhThanhPho = str_replace($searchdb, $replacedb,$_POST['txtThanhPho']);
			$ctkhQuocGia = str_replace($searchdb, $replacedb,$_POST['txtQuocGia']);
			$txtMaSoDeTai = str_replace($searchdb, $replacedb,$_POST['txtMaSoDeTai']);
			$txtISI = str_replace($searchdb, $replacedb,$_POST['txtISI']);
			$txtIF = str_replace($searchdb, $replacedb,$_POST['txtIF']);
			$txtGhiChu = str_replace($searchdb, $replacedb,$_POST['txtGhiChu_baibao']);
			$txtLoaiTacGia = str_replace($searchdb, $replacedb,$_POST['txtLoaiTacGia']);
			
			if ($action=="add") // Add ctkh
				$sqlstr = "insert into cong_trinh_khoa_hoc(ma_cong_trinh, ma_can_bo,ten_tac_gia,
													   ten_bai_bao,ten_tap_chi,so_tap_chi,
													   trang_dang_bai_bao,nam_xuat_ban_tap_chi,
													   loai_cong_trinh,isbn,city,fk_quoc_gia,san_pham_ma_de_tai,
													   isi, diem_if, ghi_chu,fk_ma_loai_tac_gia, 
													   tac_gia_chinh, tac_gia_thuoc_vnu, thuoc_linh_vuc, link_bai_bao) values
		   					(get_ma_cong_trinh('$macb'), '$macb','$ctkhTenTacGia' ,'$ctkhTenBaiBao',
							'$ctkhTenTapChi','$ctkhSoTapChi','$ctkhTrangDang',$ctkhNamXB,
							'$ctkhLoaiCongTrinh','$ctkhISBN','$ctkhThanhPho','$ctkhQuocGia','$txtMaSoDeTai',
							'$txtISI','$txtIF','$txtGhiChu', '$txtLoaiTacGia',
							'$txtTenTacGiaChinh','$txtTenTacGiaVNU','$txtThuocLinhVuc', '$txtLinkBaibao')";
		    else	// Edit ctkh
		   		$sqlstr = "update cong_trinh_khoa_hoc set so_tap_chi='$ctkhSoTapChi',
				 nam_xuat_ban_tap_chi = $ctkhNamXB, trang_dang_bai_bao='$ctkhTrangDang',
				 ten_tap_chi='$ctkhTenTapChi', ten_bai_bao='$ctkhTenBaiBao', 
				 ten_tac_gia='$ctkhTenTacGia', loai_cong_trinh='$ctkhLoaiCongTrinh', 
				 isbn='$ctkhISBN', city='$ctkhThanhPho', fk_quoc_gia='$ctkhQuocGia', san_pham_ma_de_tai='$txtMaSoDeTai',
				 isi='$txtISI', diem_if='$txtIF', ghi_chu='$txtGhiChu', fk_ma_loai_tac_gia = '$txtLoaiTacGia',
				 tac_gia_chinh='$txtTenTacGiaChinh',tac_gia_thuoc_vnu='$txtTenTacGiaVNU',thuoc_linh_vuc='$txtThuocLinhVuc',link_bai_bao='$txtLinkBaibao'
				 where ma_cong_trinh ='".base64_decode($_POST["mactkhedit"])."' and ma_can_bo='$macb'";
		   
		   //echo $sqlstr;
		   
		   	$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
		} // end of ($act=="add")
		
		
		else if ($action=="del") // Xoa Cong trinh khoa hoc
		{
			
			$sqlstr = "select count(*) tongctkh from cong_trinh_khoa_hoc where ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $danhsach);
			oci_free_statement($stmt);
			$n=$danhsach["TONGCTKH"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["ctkhchk".$i]=="1")
					$ctkhdel = $ctkhdel.base64_decode($_POST["hiddenMaCTKH".$i]).",";
			$ctkhdel = substr($ctkhdel, 0, -1);
			
			$sqlstr = "delete cong_trinh_khoa_hoc where ma_can_bo ='".$macb."' and ma_cong_trinh in (" .$ctkhdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo $sqlstr;
		} // end of ($act=="del")
		
	}// end if $cat == "ctkh"
	else if ($cat=="ttgv"){
			$txtDiaChi = str_replace($searchdb, $replacedb,$_POST['txtDiaChi']);
			$txtDiaChiRieng = str_replace($searchdb, $replacedb,$_POST['txtDiaChiRieng']);
			$txtDienThoai = str_replace($searchdb, $replacedb,$_POST['txtDienThoai']);
			$txtDienThoai2 = str_replace($searchdb, $replacedb,$_POST['txtDienThoai2']);
			$txtFax = str_replace($searchdb, $replacedb,$_POST['txtFax']);
			$txtWebsite = str_replace($searchdb, $replacedb,$_POST['txtWebsite']);
			$txtEmail = str_replace($searchdb, $replacedb,$_POST['txtEmail']);
			$txtEmail2 = str_replace($searchdb, $replacedb,$_POST['txtEmail2']);
			$txtTenLanhDaoCQ = str_replace($searchdb, $replacedb,$_POST['txtTenLanhDaoCQ']);
			$txtDTLanhDaoCQ = str_replace($searchdb, $replacedb,$_POST['txtDTLanhDaoCQ']);
			$txtChucDanhNghienCuu = str_replace($searchdb, $replacedb,$_POST['txtChucDanhNghienCuu']);
			$txtChuyenNganh = str_replace($searchdb, $replacedb,$_POST['txtChuyenNganh']);
			$txtChuyenMon = str_replace($searchdb, $replacedb,$_POST['txtChuyenMon']);
			$txtChucVu = str_replace($searchdb, $replacedb,$_POST['txtChucVu']);
			$txtNamCongTac = str_replace($searchdb, $replacedb,$_POST['txtNamCongTac']);
			$txtNamNghiHuu = str_replace($searchdb, $replacedb,$_POST['txtNamNghiHuu']);
			$txtBoMonQL = str_replace($searchdb, $replacedb,$_POST['txtBoMonQL']);
				
			$txtNamPhongHH = str_replace($searchdb, $replacedb,$_POST['txtNamPhongHocHam']);
				
			// check unique email 1
			$sqlstr = "select ma_can_bo from can_bo_giang_day 
			where ma_can_bo <> '".$macb."' and email='".$txtEmail."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $resDM);
			
			
			if ($n>0) {
				echo '{"email_check":"Exist"}';
			}else {
				$sqlstr = "UPDATE can_bo_giang_day SET 
				dia_chi='$txtDiaChi', 
				dia_chi_rieng='$txtDiaChiRieng',
				dien_thoai = '$txtDienThoai', 
				dien_thoai_cn='$txtDienThoai2', 
				email='$txtEmail', 
				email_2 = '$txtEmail2', 
				chuyen_nganh='$txtChuyenNganh',
				nam_phong_hoc_ham='$txtNamPhongHH',
				chuyen_mon = '$txtChuyenMon',
				fk_chuc_vu = '$txtChucVu',
				nam_bd_cong_tac = '$txtNamCongTac',
				nam_nghi_huu = '$txtNamNghiHuu',
				ma_bo_mon_ql = '$txtBoMonQL',
				ten_nguoi_lanh_dao_cq = '$txtTenLanhDaoCQ',
				dien_thoai_lanh_dao_cq = '$txtDTLanhDaoCQ',
				chuc_danh_nghien_cuu = '$txtChucDanhNghienCuu',
				fax = '$txtFax',
				website_co_quan = '$txtWebsite'
				WHERE ma_can_bo='".$macb."'";
				
				$stmt = oci_parse($db_conn, $sqlstr);
				oci_execute($stmt);
				
				echo '{"email_check":"NotExist"}';
			}
			
			oci_free_statement($stmt);
			//echo $sqlstr;
			
			
	} // end if cat=ttgv
	
	else if ($cat=="sach"){
		if ($action=="add" || $action=="edit") // add edit Sach
		{
			$sachTenSach = str_replace($searchdb, $replacedb,$_POST['txtTenSach']);
			$sachNamXB = str_replace($searchdb, $replacedb,$_POST['txtNamXBSach']);
			$sachNhaXB = str_replace($searchdb, $replacedb,$_POST['txtNhaXBSach']);
			$butdanh = str_replace($searchdb, $replacedb,$_POST['txtButDanhSach']);
			$detaisp = str_replace($searchdb, $replacedb,$_POST['txtDeTaiSach']); 
			$nuocngoai = str_replace($searchdb, $replacedb,$_POST['txtNuocNgoai_sach']);
			
			if ($_POST['chkTacGiaChinh']==1)
				$sachTacGiaChinh = 1;
			else
				$sachTacGiaChinh = 0;
			
			if ($action=="add")
				$sqlstr = "insert into SACH(ma_sach, ma_can_bo, ten_sach, nha_xuat_ban,
							nam_xuat_ban, tac_gia, san_pham_ma_de_tai, nuoc_ngoai, but_danh) values
	   						(get_ma_sach('$macb'), '$macb','$sachTenSach' ,'$sachNhaXB','$sachNamXB','$sachTacGiaChinh',
							'$detaisp','$nuocngoai','$butdanh')";
		   else 
		   		$sqlstr = "update sach set ten_sach='$sachTenSach', nam_xuat_ban='$sachNamXB', nha_xuat_ban='$sachNhaXB', 
				tac_gia='$sachTacGiaChinh', san_pham_ma_de_tai='$detaisp', nuoc_ngoai='$nuocngoai', but_danh='$butdanh'  
				where ma_sach ='".base64_decode($_POST["masachedit"])."' and ma_can_bo='$macb'";
		
		   //echo $sqlstr;
		    $stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
		}
		else if ($action=="del") // Xoa Sach
		{
			$sqlstr = "select count(*) tongsach from sach where ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $danhsach);
			oci_free_statement($stmt);
			$n=$danhsach["TONGSACH"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["sachchk".$i]=="1")
					$sachdel = $sachdel.base64_decode($_POST["hiddenMaSACH".$i]).",";
			$sachdel = substr($sachdel, 0, -1);
			
			$sqlstr = "delete sach where ma_can_bo ='".$macb."' and ma_sach in (" .$sachdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
		}
		
	} // end if cat = sach
	
	else if ($cat=="nckh"){
		if ($action=="add" || $action=="edit") // add edit nghien cuu khoa hoc
		{
			//$txtTenNCKH = str_replace($searchdb, $replacedb,$_POST['txtTenNCKH']);
			$txtTenNCKH = str_replace($searchdb, $replacedb,$_POST['txtTenNCKH']);
			$txtNamNCKHbatdau = str_replace($searchdb, $replacedb,$_POST['txtNamNCKHbatdau']);
			$txtNamNCKHketthuc = str_replace($searchdb, $replacedb,$_POST['txtNamNCKHketthuc']);
			$txtChuongTrinhNCKH = str_replace($searchdb, $replacedb,$_POST['txtChuongTrinhNCKH']);
			$txtMaSoNCKH = str_replace($searchdb, $replacedb,$_POST['txtMaSoNCKH']);
			$txtCapDT = str_replace($searchdb, $replacedb,$_POST['txtCapDT']);
			($_POST['chkChuNhiemNCKH']==1)? $chkChuNhiemNCKH = 1 : $chkChuNhiemNCKH = 0;
			$txtKetQuaNCKH = $_POST['txtKetQuaNCKH'];
			$txtKinhPhiNCKH = str_replace($searchdb, $replacedb,$_POST['txtKinhPhiNCKH']);
			$txtKinhPhiNCKH = str_replace(",", "",$txtKinhPhiNCKH);		
			$txtNgayNghiemThu = str_replace($searchdb, $replacedb,$_POST['txtNghiemThuNCKH']);
			
			if ($action=="add")	
				$sqlstr = "insert into de_tai_nckh(	ma_de_tai, ma_can_bo,ten_de_tai,nam_bat_dau,
				nam_ket_thuc,ngay_nghiem_thu,chu_nhiem,ket_qua,thuoc_chuong_trinh, fk_cap_de_tai, ma_so_de_tai, kinh_phi)
				values(get_ma_de_tai_nckh('$macb'), '$macb','$txtTenNCKH' ,'$txtNamNCKHbatdau','$txtNamNCKHketthuc'
				,'$txtNgayNghiemThu','$chkChuNhiemNCKH','$txtKetQuaNCKH','$txtChuongTrinhNCKH','$txtCapDT', '$txtMaSoNCKH', '$txtKinhPhiNCKH')";
		   	else
				$sqlstr = "update de_tai_nckh set ten_de_tai='$txtTenNCKH', nam_bat_dau = '$txtNamNCKHbatdau', 
				nam_ket_thuc='$txtNamNCKHketthuc', thuoc_chuong_trinh='$txtChuongTrinhNCKH', chu_nhiem='$chkChuNhiemNCKH', 
				ngay_nghiem_thu='$txtNgayNghiemThu', ket_qua='$txtKetQuaNCKH', fk_cap_de_tai='$txtCapDT', ma_so_de_tai='$txtMaSoNCKH', kinh_phi='$txtKinhPhiNCKH'
				where ma_de_tai ='".base64_decode($_POST["manckhedit"])."' and ma_can_bo='$macb'";
				
			//file_put_contents("logs.txt", $sqlstr);
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//oci_close($db_conn);
		} // end of ($act=="add")
		else if ($action=="del") // delete nckh
		{
			$sqlstr = "select count(*) tongnckh from de_tai_nckh where ma_can_bo ='".$macb."'";
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			$n = oci_fetch_all($stmt, $danhsach);
			oci_free_statement($stmt);
			$n=$danhsach["TONGNCKH"][0];
			
			for ($i=0; $i<$n; $i++)
			   if ($_POST["nckhchk".$i]=="1")
					$nckhdel = $nckhdel.base64_decode($_POST["hiddenMaNCKH".$i]).",";
			$nckhdel = substr($nckhdel, 0, -1);
			
			$sqlstr = "delete de_tai_nckh where ma_can_bo ='".$macb."' and ma_de_tai in (" .$nckhdel.")";
			
			$stmt = oci_parse($db_conn, $sqlstr);
			oci_execute($stmt);
			oci_free_statement($stmt);
			
			//echo "Không có dữ liệu";
		}
	} // end if cat = nckh
	else if ($cat=="getInfoDeTai")
	{
		$sqlstr = "select ten_de_tai, nam from huong_de_tai 
		where ma_can_bo ='".$macb."' and ma_de_tai='".base64_decode($action)."'" ;
		$stmt = oci_parse($db_conn, $sqlstr);
		oci_execute($stmt);
		$n = oci_fetch_all($stmt, $resDM);
		oci_free_statement($stmt);
		
	 // Load du lieu cho de tai
		if ($n>0){
			echo '{"nam":"'.$resDM["NAM"][0].'", "huongdt":"'.str_replace($search, $replace,$resDM["TEN_DE_TAI"][0]).'"}'; //testing \' \"  str_replace($search, $replace,
		}
		
	}
	
}
?>



<? 
if (isset($_SESSION["uidloginPortal"]) && isset($_SESSION["macb"])) 
{
// Xuat danh sach de tai
if ($cat=="detai")
{
	$sqlstr = "select ma_de_tai, ten_de_tai, nam from huong_de_tai where ma_can_bo ='".$macb."' order by nam desc, ten_de_tai";
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
 // Load du lieu cho de tai
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
		echo "<tr align=\"left\" valign=\"top\" class=\"".$classAlt."\">"."<input name=\"MaDeTai".$i."\" type=\"hidden\" id=\"MaDeTai".$i."\" value=\"".base64_encode($resDM["MA_DE_TAI"][$i])."\"/>";
		echo "<td  class=\"fontcontent\" valign=\"top\" >" .($i+1).".</td>";
		echo "<td class=\"fontcontent\" valign=\"top\">".$resDM["NAM"][$i]."</td>";
		echo "<td class=\"fontcontent\" >".$resDM["TEN_DE_TAI"][$i]."</td>";
		echo "<td class=\"fontcontent ahref\" onclick=\"
		getDeTai(". ($i+1) .", '".base64_encode($resDM["MA_DE_TAI"][$i])."');\">Sửa</td>";
		echo "<td><input type=\"checkbox\" id=\"dtchk".$i."\" name=\"dtchk".$i. "\" value=\"1\" /></td>";
		echo "</tr>";
	}
}

// Xuat danh sach cong trinh khoa hoc
else if ($cat=="ctkh")
{
	$sqlstr="select c.*, q.ten_quoc_gia, decode(fk_ma_loai_tac_gia, '03','x', '') loai_tac_gia
			from cong_trinh_khoa_hoc c, quoc_gia q
			where ma_can_bo = '".$macb. "' 
			and c.fk_quoc_gia = q.ma_quoc_gia(+)
			order by c.loai_cong_trinh, c.nam_xuat_ban_tap_chi desc"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{	
		$tenbaibao=str_replace($search,$replace,$resDM["TEN_BAI_BAO"][$i]);
		$tentacgia = str_replace($search,$replace,$resDM["TEN_TAC_GIA"][$i]);
		$madetai=str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
		$issnisbn = str_replace($search,$replace,$resDM["ISBN"][$i]);
		$isi = str_replace($search,$replace,$resDM["ISI"][$i]);
		$diemif = str_replace($search,$replace,$resDM["DIEM_IF"][$i]);
		$tentapchi=str_replace($search,$replace,$resDM["TEN_TAP_CHI"][$i]);
		$thanhpho=str_replace($search,$replace,$resDM["CITY"][$i]);
		$quocgia=$resDM["FK_QUOC_GIA"][$i];
		$tenquocgia=$resDM["TEN_QUOC_GIA"][$i];
		$sotapchi=str_replace($search,$replace,$resDM["SO_TAP_CHI"][$i]);
		$trang=str_replace($search,$replace,$resDM["TRANG_DANG_BAI_BAO"][$i]);
		$nam = str_replace($search,$replace,$resDM["NAM_XUAT_BAN_TAP_CHI"][$i]);
		$ghichu=str_replace($search,$replace,$resDM["GHI_CHU"][$i]);
		$loaicongtrinh = $resDM["LOAI_CONG_TRINH"][$i];
		$macongtrinh=base64_encode($resDM["MA_CONG_TRINH"][$i]);
		$loaitacgia = str_replace($search,$replace,$resDM["LOAI_TAC_GIA"][$i]);
		$maloaitacgia = str_replace($search,$replace,$resDM["FK_MA_LOAI_TAC_GIA"][$i]);
		
		$tacgiachinh = str_replace($search,$replace,$resDM["TAC_GIA_CHINH"][$i]);
		$tacgiavnu=str_replace($search,$replace,$resDM["TAC_GIA_THUOC_VNU"][$i]);
		$thuoclinhvuc = str_replace($search,$replace,$resDM["THUOC_LINH_VUC"][$i]);
		$linkbaibao = str_replace($search,$replace,$resDM["LINK_BAI_BAO"][$i]);
			
  	    if ($loaicongtrinh=="BQ")
		{
			$loaitc = "Tạp chí quốc tế";
			$header = "<tr class='ui-widget-header heading' >
            <td ></td>
            <td align='left'><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></td>
			<td align='center'><em>Tác giả<br/>chính</em></td>
			<td align='center'><em>Sản phẩm của<br/>đề tài/dự án</em></td>
			<td align='center'><em>Số hiệu ISSN</em></td>
			<td align='center'><em>Điểm IF</em></td>
            <td width='45' >&nbsp;</td>
            <td width=36>&nbsp;</td>
			</tr>";
			$col1="$tentacgia, $tenbaibao, $tentapchi và $sotapchi, $trang, $nam";
			$col2="$madetai";
			$col3="$issnisbn";
			if ($isi != "")
				$col3.=" thuộc $isi";
			$col4="$diemif";
	    }
	    else if ($loaicongtrinh=="BT")
		{
			$loaitc = "Tạp chí trong nước";
			$header = "<tr class='ui-widget-header heading' >
            <td ></td>
            <td align='left'><em>Tên tác giả, tên bài viết, tên tạp chí và số của tạp chí, trang đăng bài viết, năm xuất bản</em></td>
			<td align='center'><em>Tác giả<br/>chính</em></td>
			<td align='center'><em>Sản phẩm của<br/>đề tài/dự án</em></td>
			<td align='center'><em>Số hiệu ISSN</em></td>
			<td align='center'><em>Ghi chú</em></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
			</tr>";
			$col1="$tentacgia, $tenbaibao, $tentapchi và $sotapchi, $trang, $nam";
			$col2="$madetai";
			$col3="$issnisbn";
			$col4="$ghichu";
	    }
		else if ($loaicongtrinh=="HQ")
		{
			$loaitc = "Hội nghị quốc tế";
			$header = "<tr class='ui-widget-header heading' >
            <td ></td>
            <td align='left'><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></td>
			<td align='center'><em>Tác giả<br/>chính</em></td>
			<td align='center'><em>Sản phẩm của<br/>đề tài/dự án</em></td>
			<td align='center'><em>Số hiệu ISBN</em></td>
			<td align='center'><em>Ghi chú</em></td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
			</tr>";
			$col1="$tentacgia, $tenbaibao, $tentapchi, $nam, $thanhpho - $tenquocgia";
			$col2="$madetai";
			$col3="$issnisbn";
			$col4="$ghichu";
		}
	    else if ($loaicongtrinh=="HT"){
			$loaitc = "Hội nghị trong nước";
			$header = "<tr class='ui-widget-header heading' >
            <td ></td>
            <td align='left'><em>Tên tác giả, tên bài viết, tên Hội nghị, thời gian tổ chức, nơi tổ chức</em></td>
			<td align='center'><em>Tác giả<br/>chính</em></td>
			<td align='center'><em>Sản phẩm của<br/>đề tài/dự án</em></td>
			<td align='center'><em>Số hiệu ISBN</em></td>
			<td align='center'><em>Ghi chú</em></td>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
			</tr>";
			$col1="$tentacgia, $tenbaibao, $tentapchi, $nam, $thanhpho - $tenquocgia";
			$col2="$madetai";
			$col3="$issnisbn";
			$col4="$ghichu";
		}
		//($i % 2) ? $classAlt="alt" : $classAlt="";
		
		if ($loaictT!=$loaitc)
		{
			echo "<tr class='fontcontent' style='background:#eeeeee'><td colspan=8 align=left><b>$loaitc</b></td></tr>";
			echo $header;
			$loaictT=$loaitc;
			$classAlt="alt";
		}

		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
		
				
		echo "<tr align=left valign=top class=\"fontcontent ".$classAlt."\" ><input type=\"hidden\" name=\"hiddenMaCTKH".$i."\" id=\"hiddenMaCTKH".$i."\" value=\"".base64_encode($resDM["MA_CONG_TRINH"][$i])."\" />";				
		echo "<td  valign=top width=\"20\">" .($i+1).".</td>";
		echo "<td  width=550>$col1</td>";
		echo "<td  align=center valign=top><b>$loaitacgia</b></td>";
		echo "<td  align=center valign=top>$col2</td>";
		echo "<td  align=center valign=top>$col3</td>";
		echo "<td  align=center valign=top>$col4</td>";
		echo "<td class=\"fontcontent ahref\" valign=top onClick=\"
						getCTKH('$sotapchi','$nam','$trang','$tentapchi','$tenbaibao',
						'$tentacgia','$loaicongtrinh','$issnisbn','$thanhpho','$quocgia',
						'$macongtrinh','$madetai','$isi','$diemif','$ghichu', '$maloaitacgia',
						'$tacgiachinh','$tacgiavnu','$thuoclinhvuc', '$linkbaibao');
						\">Sửa</td>";
		echo "<td valign=top><input type=\"checkbox\" id=\"ctkhchk".$i."\" name=\"ctkhchk".$i. "\" value=\"1\" /></td>";
		echo "</tr>";

	}
}
// Xuat danh sach SACH
else if ($cat=="sach")
{
	$sqlstr="select MA_SACH, TEN_SACH, NHA_XUAT_BAN, NAM_XUAT_BAN, TAC_GIA, DECODE(TAC_GIA,1,'tác giả','đồng tác giả') TAC_GIA_DESC ,
			DECODE(TAC_GIA,0,'x','') DONG_TAC_GIA, BUT_DANH, SAN_PHAM_MA_DE_TAI,NUOC_NGOAI,
			decode(NUOC_NGOAI, '1','nước ngoài','trong nước') NUOC_NGOAI_DESC 
			from sach where ma_can_bo = '".$macb. "' order by NUOC_NGOAI, nam_xuat_ban desc"; 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		$masach = str_replace($search,$replace,$resDM["MA_SACH"][$i]);
		$tensach = str_replace($search,$replace,$resDM["TEN_SACH"][$i]);
		$nxb = str_replace($search,$replace,$resDM["NHA_XUAT_BAN"][$i]);
		$namxuatban = str_replace($search,$replace,$resDM["NAM_XUAT_BAN"][$i]);
		$tacgia = str_replace($search,$replace,$resDM["TAC_GIA"][$i]);
		$tacgiadesc = str_replace($search,$replace,$resDM["TAC_GIA_DESC"][$i]);
		$tacgiadong = str_replace($search,$replace,$resDM["DONG_TAC_GIA"][$i]);
		$detaisp = str_replace($search,$replace,$resDM["SAN_PHAM_MA_DE_TAI"][$i]);
		$butdanh = str_replace($search,$replace,$resDM["BUT_DANH"][$i]);
		$nuocngoai = str_replace($search,$replace,$resDM["NUOC_NGOAI"][$i]);
		$nuocngoaidesc = str_replace($search,$replace,$resDM["NUOC_NGOAI_DESC"][$i]);
		
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
		echo "<tr align=\"left\" valign=\"top\" class=\"fontcontent ".$classAlt."\" ><input type=\"hidden\" name=\"hiddenMaSACH".$i."\" id=\"hiddenMaSACH".$i."\" value=\"".base64_encode($masach)."\" />";
		echo "<td >" .($i+1).".</td>";
		echo "<td >$tensach</td>";
		echo "<td align=center>$detaisp</td>";
		echo "<td align=left>$nxb</td>";
		echo "<td align=center>$namxuatban</td>";
		echo "<td align=center>$nuocngoaidesc</td>";
		echo "<td align=center>$tacgiadesc</td>";
		echo "<td align=center>$butdanh</td>";
		echo "<td align=center class=\"ahref\" onclick=\"
getSACH('$tensach','".base64_encode($masach)."','$nxb','$namxuatban','$tacgia','$detaisp','$butdanh','$nuocngoai');\">Sửa</td>";
		echo "<td ><input type=\"checkbox\" id=\"sachchk".$i."\" name=\"sachchk".$i. "\" value=\"1\"/></td>";
		   
		echo "</tr>";
	}
}
// Xuat danh sach NCKH
else if ($cat=="nckh")
{
	$sqlstr="select a.*, DECODE(a.CHU_NHIEM,1,'CN','TG') THAM_GIA, DECODE(a.NGHIEM_THU,1,'Đã NT','') TT_NGHIEM_THU, DECODE(a.KET_QUA,'X','Xuất sắc', 'T', 'Tốt', 'K','Khá','B', 'Trung Bình') TT_KET_QUA, b.ten_cap,a.kinh_phi
	from de_tai_nckh a, cap_de_tai b
	 where a.fk_cap_de_tai = b.ma_cap(+) and 
	 a.ma_can_bo = '".$macb. "' order by a.nam_bat_dau desc"; 
			 
	$stmt = oci_parse($db_conn, $sqlstr);
	oci_execute($stmt);
	$n = oci_fetch_all($stmt, $resDM);
	oci_free_statement($stmt);
	
	$classAlt="alt";
	for ($i = 0; $i < $n; $i++)
	{
		($classAlt=="alt") ? $classAlt="alt_" : $classAlt="alt";
		
		if ($resDM["MA_SO_DE_TAI"][$i]!="")
			$masotencap = "{$resDM["MA_SO_DE_TAI"][$i]}/{$resDM["TEN_CAP"][$i]}";
		else 
			$masotencap = $resDM["TEN_CAP"][$i];

		if ($resDM["NAM_KET_THUC"][$i]!="")
			$thoigian = $resDM["NAM_BAT_DAU"][$i]."-".$resDM["NAM_KET_THUC"][$i];
		else
			$thoigian = $resDM["NAM_BAT_DAU"][$i];
		$kinhphi = str_replace($search,$replace,$resDM["KINH_PHI"][$i]);
		$kinhphi = number_format($kinhphi, 0, '.', ',');
		if ($kinhphi == 0)
			$kinhphi = "";
		$thamgia = $resDM["THAM_GIA"][$i];
		$ngaynghiemthu = str_replace($search,$replace,$resDM["NGAY_NGHIEM_THU"][$i]);
		$ketqua = str_replace($search,$replace,$resDM["KET_QUA"][$i]);
		$thuocct = str_replace($search,$replace,$resDM["THUOC_CHUONG_TRINH"][$i]);
		echo "<tr class='fontcontent ".$classAlt."' align='left' valign='top'><input name=\"hiddenMaNCKH".$i."\" type='hidden' id='hiddenMaNCKH".$i."' value=\"".base64_encode($resDM["MA_DE_TAI"][$i])."\"/>";
		echo "<td >".($i+1).".</td>";
		echo "<td >".$resDM["TEN_DE_TAI"][$i]."</td>";
		echo "<td align=center>$masotencap</td>";
		echo "<td align=center >$thoigian</td>";
		echo "<td align=center>$kinhphi</td>";
		echo "<td align=center>$thamgia</td>";
		echo "<td >$thuocct</td>";
		echo "<td align=center >$ngaynghiemthu</td>";
		echo "<td >".$resDM["TT_KET_QUA"][$i]."</td>";
		echo "<td class='ahref' onclick=\"
getNCKH(".($i+1).", '".base64_encode($resDM["MA_DE_TAI"][$i])."',".$resDM["NAM_BAT_DAU"][$i].","
.$resDM["NAM_KET_THUC"][$i].",". $resDM["CHU_NHIEM"][$i] .",'$ngaynghiemthu', '".$resDM["KET_QUA"][$i]."','".$resDM["FK_CAP_DE_TAI"][$i]."','".$resDM["MA_SO_DE_TAI"][$i]."','$thuocct','$kinhphi');\">Sửa</td>";
		echo "<td ><input type=\"checkbox\" id=\"nckhchk".$i."\" name=\"nckhchk".$i. "\" value=\"1\" /></td>";
		echo "</tr>";
	} 


	
} // end of if isset($_SESSION['uidlogin']) && isset($_SESSION['macb'])
else
{
	//echo "Truy cập bất hợp pháp";
}
}
?>

<?php 
if (isset ($db_conn))
	oci_close($db_conn);
?>