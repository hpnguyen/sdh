<?
 
function js_blur($v){?>
onFocus	="if(this.value=='<?=$v?>')	{this.value=''}"
onBlur	="if(this.value=='')		{this.value='<?=$v?>'}"
<?}
 
function js_value($v){?><script type="text/javascript">document.getElementById('<?=$v?>').value = '<?=$_POST[$v] ? $_POST[$v] : ''?>'</script><?}
 
function processing(){
 
$from		= $_POST['email'];
$to		= 'your@email.com';
$name		= $_POST['name'];
$surname	= $_POST['surname'];
$mobile		= $_POST['mobile'];
$subject	= $_POST['subject'];
$message	= $_POST['message'];
 
function preg_email($email)	{ return preg_match('/^[a-z0-9\-\_\.]{1,50}@[a-z0-9][a-z0-9\-]{0,50}[a-z0-9]\.[a-z\.]{1,50}$/',$email) ? true : false; }
function textarea($a)		{ return strlen($a) < 5 || strlen($a) > 2000 ? false : true; }
 
if(!preg_email($from))					return 'Field "email"	is empty or contains an invalid character.';
if(!preg_match('/^[A-Za-z]{3,50}$/'	,$name))	return 'Field "name"	is empty or contains an invalid character.';
if(!preg_match('/^[A-Za-z]{3,50}$/'	,$surname))	return 'Field "surname"	is empty or contains an invalid character.';
if(!preg_match('/^[0-9]{6,15}$/'	,$mobile))	return 'Field "Mobile"	is empty or contains an invalid character.';
if(!preg_match('/^[A-Za-z ]{3,20}$/'	,$subject))	return 'Field "Subject"	is empty or contains an invalid character.';
if(!textarea($message))					return 'Field "Message" must be at least 5 characters long and no longer than 2000.';
 
if($_FILES['upload']['error'] > 0){
switch($_FILES['upload']['error']){
case 1: return 'File exceeded upload_max_filesize.';
case 2: return 'File exceeded max_file_size.';
case 3: return 'File only partially uploaded.';
case 4: return 'No file uploaded.';
case 6: return 'No temp directory specified.';
case 7: return 'Can\'t write to disk.';
}
}
 
$randname_path	= 'uploads/'.mt_rand();
 
if(is_uploaded_file($_FILES['upload']['tmp_name'])){
 
	if(!move_uploaded_file($_FILES['upload']['tmp_name'],$randname_path))	return 'Can\'t move uploaded file.';
 
}
else	return 'Possible file upload attack.';
 
$base = basename($_FILES['upload']['name']);
$file = fopen($randname_path,'rb');
$size = filesize($randname_path);
$data = fread($file,$size);
fclose($file);
$data = chunk_split(base64_encode($data));
 
//boundary
$div = "==Multipart_Boundary_x".md5(time())."x";
//headers
$head =	"From: $from\n".
	"MIME-Version: 1.0\n".
	"Content-Type: multipart/mixed;\n".
	" boundary=\"$div\"";
//message
$mess =	"--$div\n".
	"Content-Type: text/plain; charset=\"iso-8859-1\"\n".
	"Content-Transfer-Encoding: 7bit\n\n".
	"$message\n\n".
	"--$div\n".
	"Content-Type: application/octet-stream; name=\"$base\"\n".
	"Content-Description: $base\n".
	"Content-Disposition: attachment;\n".
	" filename=\"$base\"; size=$size;\n".
	"Content-Transfer-Encoding: base64\n\n".
	"$data\n\n".
	"--$div\n";
$return = "-f$from";
 
if(@mail($to,$subject,$mess,$head,$return))	return 'Your email has been sent successfully.';
else						return 'Your email could not been sent at this time.';
 
}
 
if($_POST['submit']) echo processing();
 
?>
<form action="email2.php" method="post" enctype="multipart/form-data">
<div>Name	</div>
<div><input type="text" name="name"	value="<?=$_POST['name']	? $_POST['name']	: 'Name'?>"	<?js_blur('Name')?>	/></div>
<div>Surname	</div>
<div><input type="text" name="surname"	value="<?=$_POST['surname']	? $_POST['surname']	: 'Surname'?>"	<?js_blur('Surname')?>	/></div>
<div>Email	</div>
<div><input type="text" name="email"	value="<?=$_POST['email']	? $_POST['email']	: 'Email'?>"	<?js_blur('Email')?>	/></div>
<div>Mobile	</div>
<div><input type="text" name="mobile"	value="<?=$_POST['mobile']	? $_POST['mobile']	: 'Mobile'?>"	<?js_blur('Mobile')?>	/></div>
<div>Subject	</div>
<div>
<select name="subject" id="subject">
<option value="">Select</option>
<option value="General enquiries">	General enquiries</option>
<option value="Collaboration">		Collaboration</option>
<option value="Advertising">		Advertising</option>
</select>
<?js_value('subject')?>
</div>
<div>File	</div>
<div>
<input type="hidden"	name="MAX_FILE_SIZE" value="30000" />
<input type="file"	name="upload"  />
</div>
<div>Message	</div>
<div><textarea name="message" cols="20" rows="2"><?=$_POST['message'] ? $_POST['message'] : ''?></textarea></div>
<div><input type="submit" name="submit" value="Submit" /></div>
</form>