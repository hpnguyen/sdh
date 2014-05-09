<?php
/**
 * 
 */
class ModuleThoikhoabieuControllerPhancong extends FrontController {
	
	function __construct() {
		
	}
	
	public	function dslopAction(){
		if (! $this->checkLogin()){
			echo 'Chưa đăng nhập';
		}else{
			$objPHPExcel = new PHPExcel();
					  
			$dothoc = $this->getParam('dothoc');
			$mamh =  $this->getParam('mamh');
			$lop = $this->getParam('lop'); 
			$usr = base64_decode($_SESSION['uidloginPortal']);
			
			if(! date('d-m-Y', strtotime($dothoc)) == $dothoc){
				echo 'Ngày không đúng chuẩn';
			}else{
				$dothoc = date('d-M-Y', strtotime($dothoc));
				$modelMonHoc = new MonHocModel();
				$monHoc = $modelMonHoc->getByMaMH($mamh);
				
				$modelDHHK = new DotHocNamHocKyModel();
				$hocky = $modelDHHK->getNamHocHocKy($dothoc);
				$hockyString = "";
				
				if($hocky != null){
					$hockyString = $hocky['nam_hoc_tu'].'-'.$hocky['nam_hoc_den'].'/HK '.$hocky['hoc_ky'];
				}
				
				$today =date("Ymd");
				$time = date("His");
				$rootPath = Helper::getHelper('functions/util')->getRootPath();
				$fileName = $usr."_dslopDOT_".$dothoc.'_MH_'.$mamh.'_LOP_'.$lop.'.xlsx';
				$pathfile = $rootPath."download/dslop/".$fileName;
				$fileURL = Helper::getHelper('functions/util')->baseURL()."/download/dslop/".$fileName;
				
				if(file_exists($pathfile)){
					unlink($pathfile);
				}
				
				// Set document properties
				$objPHPExcel->getProperties()
				->setCreator($usr)
				->setLastModifiedBy($usr)
				->setTitle("DANH SÁCH LỚP ".$lop." - DOT ".$dothoc." - MON HOC ".$monhoc)
				->setSubject("DANH SÁCH LỚP ".$lop." - DOT ".$dothoc." - MON HOC ".$monhoc)
				->setDescription("")
				->setKeywords("")
				->setCategory("Danh sách lớp");
				// Set default font
				$objPHPExcel->getDefaultStyle()
				->getFont()
				->setName('Arial')
				->setSize(10);
				
				$objPHPExcel->getActiveSheet()
				->setCellValue('A1', "Danh sách lớp ".$monHoc["ten"].", Lớp ".$lop.", Khóa ".$hockyString);
				$objPHPExcel->getActiveSheet()
				->setCellValue('A2', 'STT')
				->setCellValue('B2', 'Mã HV')
				->setCellValue('C2', 'Họ')
				->setCellValue('D2', 'Tên')
				->setCellValue('E2', 'Phái')
				->setCellValue('F2', 'Khóa')
				->setCellValue('G2', 'Email');
				
				$objPHPExcel->getActiveSheet()
				->getStyle('A2:G2')
				->getFont()
				->setBold(true);
				
				$objPHPExcel->getActiveSheet()
				->getStyle('A1')
				->getFont()
				->setBold(true);
				
				$modelDangKyMonHoc = new DangKyMonHocModel();
				$list = $modelDangKyMonHoc->getDanhSach($dothoc,$mamh,$lop);
				
				foreach ($list as $i => $resDM) {
					$j=$i+3;
					$objPHPExcel->getActiveSheet()
					->setCellValue("A".$j, "".($j-2))
					->setCellValue("B".$j, $resDM["ma_hoc_vien"])
					->setCellValue("C".$j, $resDM["ho"])
					->setCellValue("D".$j, $resDM["ten"])
					->setCellValue("E".$j, $resDM["phai"])
					->setCellValue("F".$j, $resDM["khoa"])
					->setCellValue("G".$j, $resDM["email"]);
					
					$objPHPExcel->getActiveSheet()
					->getStyle("B".$j)
					->getNumberFormat()
					->setFormatCode('00000000');
				}
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
				
				$objPHPExcel->getActiveSheet()->setTitle('Danh sách lớp');
				$objPHPExcel->setActiveSheetIndex(0);
				
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				$objWriter->save($pathfile);
				echo '<script>window.location="'.$fileURL.'";</script>';
			}
		}
	}
}
