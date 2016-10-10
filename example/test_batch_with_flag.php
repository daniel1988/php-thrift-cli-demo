<?php
include dirname(__DIR__) . '/vendor/autoload.php';
include dirname(__DIR__) . '/src/Security.php';


$security_obj = OneLib\Security::instance() ;


$host = '127.0.0.1' ;
$port = '9044' ;
$security_obj->setServer($host, $port);
// $security_obj->initSecurity();

$flag = 'xxoo';
$data_list = [
    'aaaaa','bbbbb','ccccc'
];

$batch_encrypt_ret = $security_obj->batchEncryptWithFlag($flag,$data_list) ;
var_dump( $data_list , $batch_encrypt_ret ) ;

$encrypt_arr = [] ;
foreach( $batch_encrypt_ret as $obj ) {
    $encrypt_arr[] = $obj->str;
}

$result = $security_obj->batchDecryptWithFlag( $flag, $encrypt_arr ) ;

var_dump( $encrypt_arr, $result ) ;
