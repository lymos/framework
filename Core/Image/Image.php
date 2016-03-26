<?php
namespace Core\Image;

class Image{
	public static $im;
	public static $init = false;
	public static $font_size = 16;
	public static $font_file = 'd:/nginx/html/framework/Core/Image/font/STLITI.ttf';
	public static $width;
	public static $height;
	public static $text_length = 4;
	public static $text_type = 'en';
	public static $zh_text = [
							'数', '是', '怎', '么', '洗', '关', '户', '死', '三', '好',
							'哦', '中', '点', '句', '啊', '这', '行', '无', '和', '旧',
							'过', '你', '月', '事', '许', '因', '镜', '要', '就', '果',
							'很', '哎', '爱', '他', '也', '哈'
							];
	public static $en_text = [
							'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', '1', '2', '3',
							'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', '4', '5', '6',
							'w', 'x', 'y', 'z', '7', '8', '9', '0'
							];
	public function __construct(){

	}

	public static function init(){
		self::$init = true;
	}

	/**
	 * create auth code
	 * md5 value
	 */
	public static function createCaptcha($name = 'captcha', $img_type = 'png', $text_type = 'en', $length = 4, $width = 100, $height = 40){
		if(! self::$init){
			self::init();
		}
		self::$text_type = $text_type;
		self::$width = $width;
		self::$height = $height;
		self::$text_length = $length;
		self::$im = imagecreate(self::$width, self::$height);
		$background_color = imagecolorallocate(self::$im, 255, 255, 255);
		$string = self::drewString();
		$_SESSION[$name] = md5($string);
		self::drewPixel();
		self::drewLine();
		switch($img_type){
			case 'jpg':
				imagejpeg(self::$im);
				break;
			case 'gif':
				imagegif(self::$im);
				break;
			case 'png':
				imagepng(self::$im);
				break;
			default:
				imagepng(self::$im);
				break;
		}
		imagedestroy(self::$im);
	}
		// $font = @imageloadfont('d:/nginx/html/framework/Core/Image/font/3.ttf');
		// imagechar(self::$im, 5, 3, 0, $text, $text_color);
		// imagepstext(self::$im, $text, $font = 5, $size = 12, $text_color, $background_color, 10, 10); // php7 not supported
		// imagefilledarc(self::$im, 20, 20, 40, 40, 45, 360, $edarc_color, IMG_ARC_PIE);

	public static function drewString(){
		$angle = 0;
		$string = '';
		if(self::$text_type == 'zh'){
			$text = self::$zh_text;
			$font_size = self::$font_size;
		}else{
			$text = self::$en_text;
			$font_size = self::$font_size + 4;
		}
		$font_height = imagefontheight($font_size);
		$font_width = imagefontwidth($font_size) * 2;
		for($i = 0; $i < self::$text_length; $i++){
			$text_color = imagecolorallocate(self::$im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagefttext(self::$im, $font_size, $angle, 12 + $font_width * $i, (self::$height + $font_height) / 2 + mt_rand(-8, 8), $text_color, self::$font_file, $str = $text[mt_rand(0, 35)]);
			$string .= $str;
		}
		return $string;
	}

	public static function drewPixel(){
		for($i = 0; $i < 120; $i++){
			$pixel_color = imagecolorallocate(self::$im, 30 + $i, 20 + $i / 2, $i);
			imagesetpixel(self::$im, mt_rand(0, self::$width), mt_rand(0, self::$height), $pixel_color);
		}
	}

	public static function drewLine($line_num = 5){
		for($i = 0; $i < $line_num; $i++){
			$line_color = imagecolorallocate(self::$im, 10 + $i * 4, 100 + $i * 0.5, (30 + $i) * 2);
			imageline(self::$im, mt_rand(0, self::$width), mt_rand(0, self::$height), mt_rand(0, self::$height), mt_rand(0, self::$width), $line_color);
		}
	}

	/**
	 * reset image size
	 * @param string $src_file
	 * @param string $dst_file
	 * @return mixed
	 */
	public static function resizeImage($src_file, $dst_file = null, $width = 0, $height = 0, $quality = 50, $type = 'jpg'){
	    if(! is_file($src_file)){
	        return false;
	    }
	    $image_info = getimagesize($src_file);
	    $img_type = $image_info[2];
	    $src_width = $image_info[0];
	    $src_height = $image_info[1];
	    switch($img_type){
	        case 1:
	            $src_im = imagecreatefromgif($src_file);
	            break;
	        case 2:
	            $src_im = imagecreatefromjpeg($src_file);
	            break;
	        case 3:
	            $src_im = imagecreatefrompng($src_file);
	            break;
	    }

	    if(! $width){
	        $width = $src_width;
	    }
	    if(! $height){
	        $height = $src_height;
	    }
	    $dst_im = imagecreatetruecolor($width, $height);
	    $bg_color = imagecolorallocate($dst_im, 255, 255, 255); // while
	    imagefill($dst_im, 0, 0, $bg_color);

	    imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
	    switch ($type) {
	        case 'png':
	            imagepng($dst_im, $dst_file, $quality);
	            break;
	        
	        default:
	            imagejpeg($dst_im, $dst_file, $quality);
	            break;
	    }
	    return $dst_file;
	}
}
/*
Usage:
	header('Content-Type: image/jpeg');
	Image::createCaptcha(); // and so on
*/
?>