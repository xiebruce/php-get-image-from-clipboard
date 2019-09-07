<?php
	/**
	 * Created by PhpStorm.
	 * User: Bruce Xie
	 * Date: 2019-09-07
	 * Time: 15:21
	 */
	
	require '../vendor/autoload.php';
	
	use GetImgFromClipboard\GetImgFromClipboard;
	
	$obj = new GetImgFromClipboard();
	$savedImgPath = $obj->save('/Users/bruce/Downloads/ScreenShot.png');
	echo $savedImgPath . "\n";