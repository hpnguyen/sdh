<?php

/**
 * 
 */
class logfile {
	private $logFile;
	private $logFileName;
	private $defaultLogSize;
	
	function __construct($filename = "logfile") {
		$this->logFileName = $filename;
		$this->logFile = ROOT_DIR.'app/logs/files/'.$filename.'.log';
		$this->defaultLogSize = 1024 * 1024 * 1024; //1GB
		
		if (! file_exists($this->logFile)){
			$fp = fopen($this->logFile,"w"); 
			fwrite($fp,""); 
			fclose($fp);
			chmod($this->logFile, 0777);
		}
	}
	
	function write($the_string )
	{
		$fh = @fopen($this->logFile, "a+" );
		$msg = "[".date("d-m-Y H:i:s")."] ".$the_string."\r";
		fputs( $fh, $msg, strlen($msg));
		fclose( $fh );
		
		//create new log file if file is greater 1GB
		clearstatcache();
		$filesize = filesize($this->logFile);
		
		if ($filesize >= $this->defaultLogSize) {
			$old = $this->logFile;
			$new = ROOT_DIR.'app/logs/files/'.$this->logFileName."_".date("Ymd_His").'.txt';
			rename($old , $new);
			$this->__construct($this->logFileName);
		}
	}
}