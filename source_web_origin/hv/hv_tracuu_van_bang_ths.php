	<div style="width:100%;">
	
		<div style="padding-right:10px;">
			<form id="form_tracuuvbang" name="form_tracuuvbang" method="post" action="">
				<table style="width:420px" border='0' align=center cellspacing='0' cellpadding='5' class='ui-widget ui-widget-content ui-corner-top tableData'>
				<tr>
				<td colspan=2><b>Tra cứu theo:</b></td>
				</tr>
				<tr >
				<td align=right><label for=txtSoVB>Số hiệu bằng</label></td><td align=left><input style="width:150px; height:20px;" class="text ui-widget-content" type=textbox name=txtSoVB id=txtSoVB></td>
				</tr>
				<tr>
				<td colspan=2><i><b>hoặc</b></i></td>
				</tr>
				<tr>
				<td align=right><label for=txtMaHV>Mã số học viên</label></td><td align=left><input style="width:100px; height:20px;" class="text ui-widget-content" type=textbox name=txtMaHV id=txtMaHV></td>
				</tr>
				<tr>
				<td colspan=2><i><b>hoặc</b></i></td>
				</tr>
				<tr>
				<td align=right><label for=txtHoTen>Họ tên</label></td><td align=left><input style="width:200px; height:20px;" class="text ui-widget-content " type=textbox name=txtHoTen id=txtHoTen></td>
				</tr>
				
				<tr>
				<td align=right><label for=txtNgaySinh>& Ngày sinh</label></td><td align=left><input style="width:80px; height:20px;" class="text ui-widget-content " type=textbox name=txtNgaySinh id=txtNgaySinh> <span style="font-size:10px; color:blue">dd/mm/yyyy</span></td>
				</tr>
				<tr>
				<td colspan=2><div style="margin-top:5px; width:100%;  color:red;" id="tipOnTap" align=center></div></td>
				</tr>
				
				<tr>
				<td></td>
				<td ><div style="margin-bottom:5px; " align=left><a id="btnSubmit" >Tra cứu</a></div></td>
				</tr>
				</table>
				<input type=hidden name="print" id="print" value=''>
			</form>
		</div>
		
		<div id=tracuu_van_bang_detail style="margin-top:5px;" align=center></div>
	</div>
	
	<script>
			
	$(function() {
		$( "#btnSubmit, #btnaprint" ).button();
		$("#txtNgaySinh").mask("99/99/9999");
		$("#txtNgaySinh").datepicker({
				showOn: "button",
				showButtonPanel: false,
				dateFormat: "dd/mm/yy",
				yearRange: "1900:2000",
				changeMonth: true,
				changeYear: true,
				defaultDate: '01/01/1980'
		});

		function isValidDate(controlName, format){ //format = 'dd/mm/yyyy'
			var isValid = true;
			//alert(document.getElementById(controlName).value + ' ' + format);
			try{
				$.datepicker.parseDate(format, document.getElementById(controlName).value,null);
			}
			catch(error){
				isValid = false;
			}
			
			if (document.getElementById(controlName).value == '')
				isValid = false;
			//alert(isValid);
			return isValid;
		}
		
		// UpdateTips session = ttgv, detai, ctkh, sach, login
		function updateTips( t ) {
			tipsOnTap
					.text( t )
					.addClass( "ui-state-highlight" );
				setTimeout(function() {
					tipsOnTap.removeClass( "ui-state-highlight", 1500 );
				}, 1000 );
		}
		// Checklength
		function checkLength( o, n, min, max) {
			if (min==0 && (o.val().length==0))
			{	
				o.addClass( "ui-state-error1" );
				o.focus();	
				updateTips( "Thông tin " + n + " không được phép để trống." );
				
				return false;
			}else if (min==max && o.val().length<min){
				o.addClass( "ui-state-error1" );
				o.focus();	
				updateTips( "Thông tin " + n + " phải đủ " + min + " ký tự." );
			}else if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error1" );
				o.focus();		
				updateTips( "Chiều dài của " + n + " từ " +
							min + " đến " + max + " ký tự.");
				return false;
			} else {
				return true;
			}
		}
		
		// Check Regexp
		function checkRegexp( o, regexp, n ) {
			//alert('a');
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error1" );
				o.focus();
				updateTips( n );
				return false;
			} else {
				return true;
			
			}
		}
		
		// Check validate fields Login
		var jHoTen		= $("#txtHoTen"),
		jNgaySinh = $("#txtNgaySinh"),
		jSoVB = $("#txtSoVB"),
		jMaHV = $("#txtMaHV"),
		jRecapcha = $("#recaptcha_response_field"),
		allFieldsLogin = $([]).add(jSoVB).add(jHoTen).add(jNgaySinh).add(jMaHV).add(jRecapcha),
		tipsOnTap	= $("#tipOnTap");
		var dongydangky = false;
		var noidungprint = '';

		//$("#form_tracuuvbang").submit(function() {
		$("#btnSubmit").click(function(){
			var bValid = true;
			allFieldsLogin.removeClass( "ui-state-error1" );
			
			if (jSoVB.val()=='') {
				if (jMaHV.val()=='') { // Kiem tra ho ten ngay sinh
					if (jHoTen.val()=='' || jNgaySinh.val()=='') {
						
						bValid = false;
						updateTips('Vui lòng nhập Số văn bằng hoặc Mã học viên hoặc Họ tên & Ngày sinh');
					}else{
						// Co Ho Ten & Ngay Sinh
						// Kiem Tra ngay sinh dung hay sai
						if (bValid && !isValidDate('txtNgaySinh','dd/mm/yy'))
						{
							bValid = false;
							document.getElementById('txtNgaySinh').focus();
							jNgaySinh.addClass( "ui-state-error1" );
							updateTips('Ngày Sinh không chính xác.');
						}
					}
				}else{
					// Co Nhap MA HV
				}
			}else{
				// Co Nhap Ma van bang
			}
			
			
			//alert(isValidDate('txtNgaySinh','dd/mm/yy'));
			//alert (dongydangky);
					
			//bValid = bValid && checkLength( jRecapcha, "\"Recapcha\"", 0, 100);
			
			//alert (bValid);
			if (bValid) {

				$("#tracuu_van_bang_detail").html("<img border='0' src='images/ajax-loader.gif'/>");
				$("#btnSubmit" ).button( "disable" );
				
				DataString = 'w=tracuuvbang'
				  + '&h=' + jHoTen.val() + '&n=' + jNgaySinh.val() 
				  + '&s=' + jSoVB.val() + '&m=' + jMaHV.val()
				  + '&hisid=<?php echo $_REQUEST["hisid"]; ?>';
				
				xreq = $.ajax({
				  type: 'POST', data: DataString, dataType: "html", url: 'hv_tracuu_van_bang_ths_process.php',
				  success: function(data) {
					$("#tracuu_van_bang_detail").html(data);
					$("#btnSubmit" ).button( "enable" );
				  },
				  error: function(xhr, ajaxOptions, thrownError) {
					$("#tracuu_van_bang_detail").html(thrownError);
				  }
				});
				
			}
			
			return bValid;
			
		});	// end $("#btnSubmit")
		
		
	});
	</script>