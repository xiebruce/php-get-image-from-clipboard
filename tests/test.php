<?php
	/**
	 * Created by PhpStorm.
	 * User: Bruce Xie
	 * Date: 2019-09-07
	 * Time: 15:21
	 */
	
	// require '../vendor/autoload.php';
	require '../../../../vendor/autoload.php';
	
	use GetImgFromClipboard\GetImgFromClipboard;
	
	$obj = new GetImgFromClipboard();
	switch (PHP_OS){
		case 'Darwin':
			// For macOS, save screenshot to Desktop
			# /Users/youusername/Desktop/ScreenShot.jpg
			# /Users/youusername/Desktop/ScreenShot.jpg
			$savedImgPath = '/Users/bruce/Desktop/ScreenShot.jpg';
			break;
		case 'WINNT':
			// For Windows, save screenshot to Desktop
			# C:\Users\youusername\Desktop\ScreenShot.jpg
			# C:\Users\youusername\Desktop\ScreenShot.png
			$savedImgPath = 'C:\Users\youusername\Desktop\ScreenShot.png';
		case 'Linux':
			// For Linux, save screenshot to Desktop
			# /home/yourusername/Desktop/ScreenShot.jpg
			# /home/yourusername/Desktop/ScreenShot.png
			$savedImgPath = '/home/yourusername/Desktop/ScreenShot.jpg';
	}
	$savedImgPath = $obj->save($savedImgPath);
	echo $savedImgPath . "\n";