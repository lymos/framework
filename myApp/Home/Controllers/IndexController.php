<?php
/**
 * index controller
 */
namespace Home\Controllers;
//use \Controller\Controller;

class IndexController extends \Controller\Controller{

    public static function indexAction(){
        echo '<h2 style="text-align: center;">Hello</h2>';
    }
}

