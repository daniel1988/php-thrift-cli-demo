<?php
namespace OneLib;

use
    Thrift\ClassLoader\ThriftClassLoader,
    Thrift\Protocol\TBinaryProtocol,
    Thrift\Transport\TSocket,
    Thrift\Exception\TException,
    Thrift\Transport\TFramedTransport,
    proxy\security\SecurityServiceClient AS SecurityService;

class Security
{

    static $instance = null;
    var $loader             = null;
    var $security_obj       = null;

    var $host               = 'localhost' ;
    var $port               = 9090 ;
    var $timeout            = 10000 ;

    /**
     * 是否开启日志写入
     *
     * @var boolean
     */
    var $debug = true;

    /**
     * @return Security
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 初始化实例
     */
    public function initSecurity()
    {
        if ($this->security_obj !== null) {
            return $this->security_obj;
        }

        $this->initClass() ;
        $transport_obj = $this->initTransport() ;

        $this->security_obj = new SecurityService(
            new TBinaryProtocol($transport_obj)
        );
        return $this->security_obj;

    }

    /**
     * 初始化加载类
     */
    public function initClass()
    {
        $directory = __DIR__ . '/Thrift';
        $this->loader = new ThriftClassLoader();
        $this->loader->registerNamespace('Thrift', __DIR__ . '/');
        $this->loader->registerDefinition('proxy\security', $directory);
        $this->loader->register();
    }

    /**
     * initTransport
     */
    public function initTransport()
    {
        $socket_srv = new TSocket($this->host, $this->port);
        if ( $this->timeout !==null ) {
            $socket_srv->setSendTimeout( $this->timeout ) ;
            $socket_srv->setRecvTimeout( $this->timeout ) ;
        }
        $transport_obj = new TFramedTransport($socket_srv);
        $transport_obj->open();
        return $transport_obj;
    }

    /**
     * 添加server
     */
    public function setServer($host, $port, $timeout=10000)
    {
        $this->host = $host ;
        $this->port = $port ;
        $this->timeout = $timeout ;
        return true ;
    }
    /**
     * 设置debug
     */
    public function setDebug( $debug = false )
    {
        $this->debug = $debug;
    }

    /**
     * 加密字符串
     * @return 'encrypt_str' | false
     */
    public function encrypt($str)
    {
        try {
            $result = $this->initSecurity()->encrypt( $str ) ;
            if ( empty( $result ) || $result->err != 0 ) {
                $this->writeLog(sprintf('encrypt Error:%s', var_export($result,true))) ;
                return false ;
            }
            return $result->str;
        } catch(TException $e) {
            $this->writeLog(sprintf("encrypt TException:%s",$e->getMessage())) ;
            return false;
        }

        return $result->str;
    }
    /**
     * 解密字符串
     * @return 'decrypt_str' | false
     */
    public function decrypt($str)
    {
        try {
            $result = $this->initSecurity()->decrypt( $str ) ;
            if ( empty( $result ) || $result->err != 0 ) {
                $this->writeLog(sprintf('decrypt Error:%s', var_export($result,true))) ;
                return false ;
            }
            return $result->str ;
        } catch(TException $e) {
            $this->writeLog(sprintf("decrypt TException:%s",$e->getMessage())) ;
            return false ;
        }
    }
    /**
     * 批量处理
     * @param  [array] $data_list ['k1'=>'str1', 'k2'=>'str2']
     * @return [array] $encrypt_list ['k1'=>'encrypt_str1' , 'k2'=>'encrypt_str2']
     */
    public function batchEncrypt( $data_list )
    {
        try {
            $result = $this->initSecurity()->batchEncrypt( $data_list ) ;
            $encrypt_data_list = [] ;
            foreach( $result as $key => $obj ) {
                if ( empty( $obj ) ) {
                    continue;
                }
                $encrypt_data_list[$key] = $obj->str ;
            }
            return $encrypt_data_list ;
        } catch(TException $e) {
            $this->writeLog(sprintf("batchEncrypt TException:%s",$e->getMessage())) ;
            return [];
        }

    }
    /**
     * 批量解密
     * @param  [array] $encrypt_data_list ['encrypt_str1', 'encrypt_str2']
     * @return [array] $decrypt_data_list ['str1', 'str2']
     */
    public function batchDecrypt( $encrypt_data_list )
    {
        try {
            $result = $this->initSecurity()->batchDecrypt( $encrypt_data_list ) ;
            $decrypt_data_list = [] ;
            foreach( $result as $key => $obj ) {
                if ( empty( $obj ) ) {
                    continue;
                }
                $decrypt_data_list[$key] = $obj->str ;
            }
            return $decrypt_data_list ;
        } catch(TException $e) {
            $this->writeLog(sprintf("batchDecrypt TException:%s",$e->getMessage())) ;
            return [];
        }
    }
    /**
     *
     * @param  [string] $flag
     * @param  [array] $data_list ['str1','str2']
     * @return [array] $ecrypt_data_list ['str1', 'str2']
     */
    public function batchEncryptWithFlag( $flag, $data_list )
    {
        try {
            $result = $this->initSecurity()->batchEncryptWithFlag( $flag, $data_list ) ;
            if ( !isset( $result->BatchResultStruct ) ) {
                $this->writeLog(sprintf("batchDecryptWithFlag Error:%s", var_export($result,true))) ;
                return [] ;
            }
            $data_list = isset( $result->BatchResultStruct ) ? $result->BatchResultStruct : [] ;
            $encrypt_data_list = [] ;
            foreach( $data_list as $key => $obj ) {
                if ( empty($obj) ) {
                    continue;
                }
                $encrypt_data_list[$key] = $obj->str ;
            }
            return $encrypt_data_list;
        } catch(TException $e) {
            $this->writeLog(sprintf("batchEncryptWithFlag TException:%s",$e->getMessage())) ;
            return [];
        }
    }
    /**
     * @param  [string] $flag
     * @param  [array] $encrypt_data_list ['encrypt_str1', 'encrypt_str2']
     * @return [array] $decrypt_data_list ['str1', 'str2']
     */
    public function batchDecryptWithFlag( $flag, $encrypt_data_list )
    {
        try {
            $result = $this->initSecurity()->batchDecryptWithFlag( $flag, $encrypt_data_list ) ;
            if ( !isset( $result->BatchResultStruct ) ) {
                $this->writeLog(sprintf("batchDecryptWithFlag Error:%s", var_export($result,true))) ;
                return [] ;
            }
            $decrypt_data_list = [] ;
            foreach( $result->BatchResultStruct as $key => $obj ) {
                if ( empty( $obj ) ) {
                    continue;
                }
                $decrypt_data_list[$key] = $obj->str;
            }
            return $decrypt_data_list ;
        } catch(TException $e) {
            $this->writeLog(sprintf("batchDecryptWithFlag TException:%s",$e->getMessage())) ;
            return [];
        }
    }

    /**
     * 写入日志
     *
     * @param string $name
     * @param string $message
     */
    protected function writeLog($message)
    {
        if (!$this->debug) {
            return false;
        }
        $log_path = dirname(__FILE__) . '/../log/';
        if ( !is_dir( $log_path ) ) {
            mkdir($log_path);
        }
        $log_file = $log_path . 'api_security.log';
        echo $log_file ;
        file_put_contents($log_file, $message . "\n", FILE_APPEND) ;
    }
}
