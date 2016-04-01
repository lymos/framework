<?php
/**
 * String Helper Class
 * Usage: \Util\String::compressPhpCode();
 *
 */
namespace Util;

class String {

	/**
 	 * compress php code
  	 * @param string $context content or file
 	 * @param boolean $is_file
 	 * @return string
 	 */
	public static function compressPhpCode($context, $is_file = false){
		if($is_file){
			$content = file_get_contents($context);
		}else{
			$content = $context;
		}
		$content = str_replace("\r\n", '', $content);
		$content = preg_replace('/\?>\s*<\?php\s*/', '', $content);		/* ?><?php */
		$content = str_replace('<?php', '<?php ', $content);
		$content = preg_replace('/\/\*[^\*\/]*\*\//', '', $content);	// /* */
		$content = preg_replace('/\{\s*/', '{', $content);	 // {
		$content = preg_replace('/\(\s*/', '(', $content);	// (
		$content = preg_replace('/,\s*/', ',', $content);	// ,
		$content = preg_replace('/;\s*/', ';', $content);	// ;
		$content = preg_replace('/\s*\.\s*/', '.', $content);	// .
		$content = preg_replace('/\s*\=\s*/', '=', $content);	// =
		return $content;
	}
}
