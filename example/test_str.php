<?php
include dirname(__DIR__) . '/vendor/autoload.php';
include dirname(__DIR__) . '/src/Security.php';


$security_obj = OneLib\Security::instance() ;

$host = '127.0.0.1' ;
$port = '9044' ;
$security_obj->setServer($host, $port);
// $security_obj->initSecurity();

$str = 'xxxxx' ;
$encrypt_ret = $security_obj->encrypt($str) ;
var_dump( $str , $encrypt_ret ) ;

// $decrypt_ret = $security_obj->decrypt( $encrypt_ret->str ) ;

// var_dump( $encrypt_ret->str, $decrypt_ret ) ;
