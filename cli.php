<?php
	/**
	 * Created by PhpStorm.
	 * User: Bruce Xie
	 * Date: 2019-09-04
	 * Time: 12:36
	 */
	
	require 'src/GetImgFromClipboard.php';
	
	use GetImgFromClipboard\GetImgFromClipboard;
	$argv = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : false;
	if(!$argv || in_array($argv, ['-h', 'help', '--help', '?'])){
		if(PHP_OS == 'WINNT'){
			$helpMsg = "\nUsage: php.exe cli.php C:\\path\\to\\ScreenShot.png\n\nThis will save the image on the clipboard to file C:\\path\\to\\ScreenShot.png\n";
		}else{
			$helpMsg = "\nUsage: php cli.php /path/to/ScreenShot.png\n\nThis will save the image on the clipboard to file /path/to/ScreenShot.png\n";
		}
		exit($helpMsg);
	}
	if(!preg_match('/:?(\/?.*?)+/', $argv)){
		exit('Destination file is not a file path, you can use "php cli.php -h" to get help.' . "\n");
	}
	
	$imgPath = $argv;
	if(!$imgPath){
		$filename = 'screenshot.jpg';
		$screenshotPaths = [
			'WINNT' => 'C:\Users\bruce\Desktop\\' . $filename,
			'Darwin' => '/Users/bruce/Desktop/' . $filename,
			'Linux' => '/home/bruce/Desktop/' . $filename,
		];
		$filePath = isset($screenshotPaths[PHP_OS]) ? $screenshotPaths[PHP_OS] : '/home/bruce/Desktop/' . $filename;
		exit('Please input destination file path, for example: ' . $filePath . "\n");
	}
	
	$obj = new GetImgFromClipboard();
	// just for test
	// $imgPath = '/Users/bruce/Downloads/ScreenShot.jpg';
	$output = $obj->save($imgPath);
	if(is_file($output)){
		$output = 'Image saved to ' . $output;
	}
	
	echo $output . "\n";