<?php
return [
	'app_path' => realpath(dirname(dirname(__FILE__))) . '/myApp',
	'app_model_dirname' => 'Models',
	'app_controller_dirname' => 'Controllers',
	'app_view_dirname' => 'Views',
	'app_default_module' => 'Home',
	'app_data_dirname' => 'Data',
	'app_web_dirname' => 'Web',
	'db' => [
		'db_type' => 'mysql',
		'dsn' => 'mysql: dbname=test; host = 127.0.0.1',
		'user' => 'root',
		'pwd' => 'root'
	]

];
?>
