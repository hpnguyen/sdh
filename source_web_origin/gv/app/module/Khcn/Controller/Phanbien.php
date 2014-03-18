<?php
/**
 * 
 */
class ModuleKhcnControllerPhanbien extends FrontController {
	
	function __construct() {
		if (! $this->checkLogin()){
			echo "Bạn phải login để sử dụng chức năng này.";
			die;
		}
	}
	
	private function checkEnableView($macb , $madetai){
		$model = new NckhPhanCongPhanBienModel();
		return $model->checkEnableView($madetai, $macb);
	}
	
	private function checkEnableUpdate($macb , $madetai){
		$model = new NckhThuyetMinhDeTaiModel();
		$modelNckhPhanCongPhanBien = new NckhPhanCongPhanBienModel();
		
		return $model->checkEnableSaveEdit($madetai, $macb) && $modelNckhPhanCongPhanBien->checkEnableSaveUpdate($madetai, $macb);
	}
	
	private function checkEnableDoYesOrNo($macb , $madetai){
		$model = new NckhThuyetMinhDeTaiModel();
		return $model->checkEnableSaveEdit($madetai, $macb);
	}
	
	public	function indexAction(){
		$template = new BaseTemplate("khcn/phanbien/index","default/index");
		$template->formKey = 'xemPhanBienDeTaiIndex';
		
		$tkb = new ThoiKhoaBieuModel();
		$template->listDotHoc =$tkb->getListHocKy();
		unset($tkb);
		
		$macb = $_SESSION['macb'];
		$year = date("Y");
		$modelNckhPhanCongPhanBien = new NckhPhanCongPhanBienModel();
		$template->listYear =$modelNckhPhanCongPhanBien->getListYear($macb,$year);
		unset($modelNckhPhanCongPhanBien);
		
		$config = new ConfigModel();
		$template->dothoc =$config->getPhanBoCbgdDotHoc();
		
		$template->nganh = false;
		
		$modelCapDeTai = new CapDeTaiModel();
		$template->listLimitTimes = $modelCapDeTai->getThoiHanPhanBienDeTai();
		
		$contentHTML = $template->contentTemplate();
		$template->renderLayout(array('title' => '','content' => $contentHTML));
	}
	
	public	function listAction(){
		$macb = $_SESSION['macb'];
		$dothoc = $this->getGet('d',null);
		$makhoa = base64_decode($_SESSION['makhoa']);
		
		// $template = new BaseTemplate("khcn/phanbien/list","default/index");
		$template = new BaseTemplate("khcn/phanbien/list_page","default/index");
		$template->formKey = 'xemPhanBienDeTaiList';
		$template->hk = $this->getGet('h');
		$template->macb = $macb;
		$template->dothoc = $dothoc;
		
		$model = new NckhThuyetMinhDeTaiModel();
		$template->listItems = $model->getList($macb,$dothoc);
		
		$contentHTML = $template->contentTemplate();
		$template->renderLayout(array('title' => '','content' => $contentHTML));
	}
	
	public	function ajaxdialogAction(){
		$macb = $_SESSION['macb'];
		$madetai = $this->getGet('madetai');
		
		if (! $this->checkEnableView($macb, $madetai)){
			echo "Bạn không được phép xem phản biện đề tài này.";
			die;
		}
		// $template = new BaseTemplate("khcn/phanbien/ajaxdialog_list","default/index");
		$template = new BaseTemplate("khcn/phanbien/ajaxdialog_list_page","default/index");
		$template->formKey = 'xemPhanBienDeTaiList';
		
		$dothoc = $this->getGet('d',null);
		$makhoa = base64_decode($_SESSION['makhoa']);
		$template->hk = $this->getGet('h');
		$template->madetai = $madetai;
		$template->macb = $macb;
		
		$model = new NckhThuyetMinhDeTaiModel();
		$template->listItems = $model->getListByMaDeTai($macb,$template->madetai,$dothoc);
		
		$contentHTML = $template->contentTemplate();
		$template->renderLayout(array('title' => '','content' => $contentHTML));
	}
	
	public	function saveAction(){
		$indexTabActiveSendData = $this->getPost('tabActiveIndex', null);
		if ($indexTabActiveSendData != null){
			$macb = $this->getPost('fk_ma_can_bo', null);
			$maDeTai = $this->getPost('ma_thuyet_minh_dt', null);
			
			if (! $this->checkEnableUpdate($macb, $maDeTai)){
				$this->renderJSON(array('status' => 0, 'message' => 'Bạn không thể cập nhật do hết hạn hoặc bạn chưa đồng ý phản biện đề tài này.'));
				die;
			}
			
			try {
				$data = array('fk_ma_can_bo' => $macb, 'ma_thuyet_minh_dt' => $maDeTai);
				
				//Save data for Tab A1, A2, A3, C
				$dataGroup1 = $this->getPost('data_group_1', null);
				
				foreach ($dataGroup1 as $key => $value) {
					//Replace special character
					$dataGroup1[$key] = Helper::getHelper('functions/util')->escapeSpecialCharToHtmlCode($dataGroup1[$key]);
				}
				
				$data = array_merge($data, $dataGroup1);
				$model = new NckhPbNoiDungModel();
				
				$model->doCreateUpdate($data);
				
				$data_group_2 = $this->getPost('data_group_2', null);
				if ($data_group_2 != null){
					$noidungPbKinhPhiModel = new NckhPbNoiDungKinhPhiModel();
					$data2 = array('fk_ma_can_bo' => $macb, 'ma_thuyet_minh_dt' => $maDeTai);
					$kinhPhiArray = $data_group_2['a4_kinh_phi_A4_input'];
					$radio = array();
					if(isset($data_group_2['a4_kinh_phi_A4_radio'])){
						$radio = $data_group_2['a4_kinh_phi_A4_radio'];
					}
					
					$idOrderByCount = 0;
					$maxID = count($noidungPbKinhPhiModel->getAll());
					foreach ($kinhPhiArray as $k => $v) {
						$valueNhanXet = null;
						if (isset($radio[$k])){
							$valueNhanXet = (int) $radio[$k];
						}
						$dataAddOrUpdate = $data2;
						$dataAddOrUpdate['ma_nd'] = $k;
						$dataAddOrUpdate['nhan_xet'] = $valueNhanXet;
						$idOrderByCount++;
						$maxID  = $maxID + 1;
						$dataAddOrUpdate['id'] = $maxID;
						$dataAddOrUpdate['stt'] = $idOrderByCount;
						$dataAddOrUpdate['id_order_by'] = $idOrderByCount;
						
						if ($v !=''){
							$v = str_replace('.', '', $v);
							$v = str_replace(',', '.', $v);
							$dataAddOrUpdate['kinh_phi_de_nghi'] = floatval($v);
						}
						// var_dump($data_group_2['a4_kinh_phi_A4_input'],$dataAddOrUpdate);
						$noidungPbKinhPhiModel->doCreateUpdate($dataAddOrUpdate);
					}
				}
				
				$data_group_3 = $this->getPost('data_group_3', null);
				if ($data_group_3 != null){
					$noidungPbDanhGiaModel = new NckhPbNoiDungDanhGiaModel();
					foreach ($data_group_3['b_danh_gia_input'] as $k3 => $v3) {
						$dataUpdate = array('fk_ma_can_bo' => $macb, 'ma_thuyet_minh_dt' => $maDeTai);
						$dataUpdate['id'] = $k3;
						$dataUpdate['diem'] = $v3 == '' ? null :(int) $v3;
						
						$noidungPbDanhGiaModel->doUpdate($dataUpdate);
					}
				}
				
				$this->renderJSON(array('status' => 1, 'message' => 'Dữ liệu đã được cập nhật thành công.'));
			} catch (Exception $e) {
				$this->renderJSON(array('status' => 0, 'message' => 'lỗi xử lý : '.$e->getMessage()));
			}
				
		}
	}
	
	public	function yesAction(){
		$macb = $_SESSION['macb'];
		$maDeTai = $this->getPost('ma_thuyet_minh_dt', null);
		
		if (! $this->checkEnableDoYesOrNo($macb, $maDeTai)){
			$this->renderJSON(array('status' => 0, 'message' => 'Đã hết hạn phản biện đề tài.'));
			die;
		}
		try {
			$model = new NckhPhanCongPhanBienModel();
			$model->updateYes($maDeTai, $macb);
			$this->renderJSON(array('status' => 1, 'message' => 'Bạn đã được phép phản biện đề tài này.'));
		} catch (Exception $e) {
			$this->renderJSON(array('status' => 0, 'message' => 'lỗi xử lý : '.$e->getMessage()));
		}
	}
	
	public	function noAction(){
		$macb = $_SESSION['macb'];
		$maDeTai = $this->getPost('ma_thuyet_minh_dt', null);
		
		if (! $this->checkEnableDoYesOrNo($macb, $maDeTai)){
			$this->renderJSON(array('status' => 0, 'message' => 'Đã hết hạn phản biện đề tài.'));
			die;
		}
		try {
			$model = new NckhPhanCongPhanBienModel();
			$model->updateNo($maDeTai, $macb);
			$this->renderJSON(array('status' => 1, 'message' => 'Bạn đã từ chối phản biện thành công.'));
		} catch (Exception $e) {
			$this->renderJSON(array('status' => 0, 'message' => 'lỗi xử lý : '.$e->getMessage()));
		}
	}
	
	public	function printpdfbm01Action(){
		if (isset($_GET["hisid"])){
			session_id($_GET["hisid"]);
			session_start();
		}
		
		if (!isset($_SESSION['uidloginPortal'])){
			die('Đã hết phiên làm việc');
		}
		
		$macb = $_GET['m'];
		$a = $_GET['a'];
		$key = $_GET["k"];
		$madetai = $_GET["mdt"];
		
		if ($macb == '') {
			$macb = $_SESSION['macb'];
		}
		$template = new BaseTemplate("khcn/print/khcn_print_danh_gia_tmdt_m01_pdf","default/index");
		$template->macb = $macb;
		$template->a = $a;
		$template->key = $key;
		
		$modelCbgd = new CanBoGiangDayModel();
		$template->detailCbgd = $modelCbgd->getDetailForThuyetMinhDeTai($macb);
		
		$modelTmdt = new NckhThuyetMinhDeTaiModel();
		$macb = $_SESSION['macb'];
		$template->detailTmdt = $modelTmdt->getDetailForPrintPdf($madetai,$macb);
		$templateContent = $template->contentTemplate();
		
		$mpdf=new mPDF('utf-8','A4');
		$mpdf->SetTitle('Biểu Mẫu 01');
		$mpdf->SetAutoFont();
		$mpdf->forcePortraitHeaders = true;
		$mpdf->WriteHTML($templateContent);
		$mpdf->Output();
		exit;
		die;
		$template->renderLayout(array('title' => '','content' => $templateContent));
	}
	
	public	function printpdfbm06Action(){
		if (isset($_GET["hisid"])){
			session_id($_GET["hisid"]);
			session_start();
		}
		
		if (!isset($_SESSION['uidloginPortal'])){
			die('Đã hết phiên làm việc');
		}
		
		$macb = $_GET['m'];
		$a = $_GET['a'];
		$key = $_GET["k"];
		$madetai = $_GET["mdt"];
		
		if ($macb == '') {
			$macb = $_SESSION['macb'];
		}
		$template = new BaseTemplate("khcn/print/khcn_print_danh_gia_tmdt_m06_pdf","default/index");
		$template->macb = $macb;
		$template->a = $a;
		$template->key = $key;
		
		$modelCbgd = new CanBoGiangDayModel();
		$template->detailCbgd = $modelCbgd->getDetailForThuyetMinhDeTai($macb);
		
		$modelTmdt = new NckhThuyetMinhDeTaiModel();
		$macb = $_SESSION['macb'];
		$template->detailTmdt = $modelTmdt->getDetailForPrintPdf($madetai,$macb);
		$templateContent = $template->contentTemplate();
		
		$mpdf=new mPDF('utf-8','A4');
		$mpdf->SetTitle('Biểu Mẫu 06');
		$mpdf->SetAutoFont();
		$mpdf->forcePortraitHeaders = true;
		$mpdf->WriteHTML($templateContent);
		$mpdf->Output();
		exit;
		die;
		$template->renderLayout(array('title' => '','content' => $templateContent));
	}
}
