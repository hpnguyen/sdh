<?php
	if (isset($_REQUEST['l']) && isset($_REQUEST['t']))
	{
		$l = $_REQUEST['l'];
		$t = $_REQUEST['t'];
		
		if ($t=='images')
		{
			echo "
			<div>
				<div align=center style='margin:10px 0 10px 0;'><img src='$l' border=0></div>
			</div>
			<div class=clearfloat></div>
			";
		}
		else if ($t=='pdf')
		{
			echo "
			<div>
			<div align=center style='margin:5px 0 15px 0;'><b>Để In và Lưu tài liệu vui lòng bấm nút <img src='icons/gview.png' border=0></b></div>
			<div align=center><iframe src='http://docs.google.com/gview?url=$l&embedded=true' style='width:800px; height:700px;' frameborder='0'></iframe></div>
			</div>
			";
		}
	}
?>