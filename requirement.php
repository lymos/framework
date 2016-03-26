<?php
/**
* requirement for php extensition such as pdo_mysql and so on
* @time 2015
*/
$requirement_info = [];

if(extension_loaded('imagick')){
	$imagick = new Imagick();
	$formats = $imagick->queryFormats();
	if(! in_array('PNG', $formats)){
		$requirement_info['imagick png'] = 'not installed';
	}else{
		$requirement_info['imagick png'] = 'installed';
	}
}

if(extension_loaded('gd')){
	$gdinfo = gd_info();
	if(! $gdinfo['FreeType Support']){
		$requirement_info['gd freetype'] = 'not installed';
	}else{
		$requirement_info['gd freetype'] = 'installed';
	}
}

// php extensions is loaded or not
$extensions = [
	[
		'name' => 'PDO Extension',
		'condition' => extension_loaded('pdo'),
		'memo' => '<a target="_blank" href="http://php.net">PDO Extension</a>'
	],
	[
		'name' => 'PDO Mysql',
		'condition' => extension_loaded('pdo_mysql'),
		'memo' => '<a target="_blank" href="http://php.net">PDO Mysql</a>'
	],
	[
		'name' => 'PDO Sqlite',
		'condition' => extension_loaded('pdo_sqlite'),
		'memo' => '<a target="_blank" href="http://php.net">PDO Sqlite</a>'
	],
	[
		'name' => 'Mbstring',
		'condition' => extension_loaded('mbstring'),
		'memo' => '<a target="_blank" href="http://php.net">Mbstring</a>'
	],
	[
		'name' => 'FileInfo',
		'condition' => extension_loaded('fileinfo'),
		'memo' => '<a target="_blank" href="http://php.net">FileInfo</a>'
	],
	[
		'name' => 'Mysqli',
		'condition' => extension_loaded('mysqli'),
		'memo' => '<a target="_blank" href="http://php.net">Mysqli</a>'
	]
];

print_r($extensions);
?>
