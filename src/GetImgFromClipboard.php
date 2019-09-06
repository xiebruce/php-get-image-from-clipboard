<?php
/**
 * Created by PhpStorm.
 * User: Bruce Xie
 * Date: 2019-09-04
 * Time: 09:24
 */

namespace GetImgFromClipboard;

class GetImgFromClipboard {
	private $cmd;

	public function __construct(){
		switch (PHP_OS){
			case 'Darwin':
				$pngpaste1 = '/usr/local/bin/pngpaste';
				$pngpaste2 = '/usr/bin/pngpaste';
				$this->cmd = is_file($pngpaste1) ? $pngpaste1 : (is_file($pngpaste2) ? $pngpaste2 : false);
				if(!$this->cmd){
					exit('pngpaste is required, please install: brew install pngpaste');
				}
				break;
			case 'WINNT':
				if(!$this->isPowershell51()){
					exit('Powershell need to update to Powershell 5.1. Download "Win7AndW2K8R2-KB3191566-x64.zip" in https://www.microsoft.com/en-us/download/details.aspx?id=54616 if you are using Windows 7.');
				}
				$this->cmd = 'powershell';
				break;
			case 'Linux':
			default:
				$xclip1 = '/usr/bin/xclip';
				$xclip2 = '/usr/local/bin/xclip';
				$this->cmd = is_file($xclip1) ? $xclip1 : (is_file($xclip2) ? $xclip2 : false);
				if(!$this->cmd){
					exit('xclip is required, please install: apt install xclip / yum install xclip / pacman -S xclip');
				}
				break;
		}
	}
	
	/**
	 * Save
	 * @param $saveFileFullPath
	 *
	 * @return bool|string
	 */
	public function save($saveFileFullPath){
		$ext = $this->getFileExt($saveFileFullPath);
		
		$imgTypeMime = [
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
		];
		
		if(!array_key_exists($ext, $imgTypeMime)){
			return 'Only jpg / png image is supported!';
		}
		
		$noImgMsg = 'No image data found on the clipboard!';
		
		switch (PHP_OS){
			// While on macOS, pngpaste is required(install: brew install pngpaste)
			case 'Darwin':
				// pngpaste support input type: PNG, PDF, GIF, TIF, JPEG, and ouput type:PNG, GIF, JPEG, TIFF.
				$command = $this->cmd . ' ' . $saveFileFullPath;
				// $output = shell_exec($command);
				$descriptorspec = array(
					0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
					1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
					2 => array("pipe", "w") // stderr is a file to write to
				);
				$process = proc_open($command, $descriptorspec, $pipes, null, null);
				$output = trim(stream_get_contents($pipes[2], 2048), PHP_EOL);
				proc_close($process);
				if($output && $output == 'pngpaste: No image data found on the clipboard, or could not convert!'){
					return $noImgMsg;
				}
				break;
			// While on Windows7 Powershell 5.1 is required(Download "Win7AndW2K8R2-KB3191566-x64.zip" from here: https://www.microsoft.com/en-us/download/details.aspx?id=54616), Windows 10 is supported, Windows 8 is not tested but it should be work is it's Powershell version is above 5.1.
			case 'WINNT':
				$powershell = __DIR__ . '\img-clipboard-dump\dump-clipboard-'.$ext.'.ps1';
				$command = $this->cmd . " -ExecutionPolicy Unrestricted {$powershell}";
				$imgPath = trim(shell_exec($command));
				if($imgPath == 'false'){
					return $noImgMsg;
				}
				// in here, rename means move file to a new place
				is_file($imgPath) && @rename($imgPath, $saveFileFullPath);
				break;
			//While on Linux, xclip is required(e.g. Ubuntu: apt install xclip)
			case 'Linux':
			default:
				$mime = $imgTypeMime[$ext];
				$clipboardContentTypeCmd = $this->cmd . ' -selection clipboard -t TARGETS -o';
				$contentTypes = shell_exec($clipboardContentTypeCmd);
				if(strpos($contentTypes, $mime) === false){
					return $noImgMsg;
				}
				$command = $this->cmd . ' -selection clipboard -t ' . $mime . ' -o > ' . $saveFileFullPath;
				// if the command run success, $output will be NULL(nothing output)
				$output = shell_exec($command);
		}
		
		if(is_file($saveFileFullPath)){
			return $saveFileFullPath;
		}
		return false;
	}
	
	/**
	 * Get file extension
	 * @param $filePath
	 *
	 * @return mixed
	 */
	private function getFileExt($filePath){
		$pathinfo = pathinfo($filePath);
		$ext = isset($pathinfo['extension']) ? strtolower($pathinfo['extension']) : '';
		$ext && $ext = str_replace('jpeg', 'jpg', $ext);
		return $ext;
	}
	
	/**
	 * Check if Powershell version is greater than 5.1
	 * @return bool
	 */
	private function isPowershell51(){
		$output = shell_exec('powershell $PSVersionTable');
		$arr = explode("\n", $output);
		$arr2 = explode(' ', preg_replace("/\s(?=\s)/","\\1",trim($arr[3])));
		$powershellVersion = isset($arr2['1']) ? $arr2['1'] : '';
		if(preg_match('/^5\.1\..*?$/', $powershellVersion)){
			return true;
		}
		return false;
	}
}