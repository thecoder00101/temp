<?php
class AlipayService{
    protected $appId;
    protected $notifyUrl;
    protected $charset;
    protected $alipayPublicKey;  //支付宝公钥
    protected $rsaPrivateKey;    //商户私钥
    protected $totalFee;
    protected $outTradeNo;
    protected $orderName;
    protected $tradeNo;
    protected $refundAmount;

    public function __construct($alipayPublicKey = false, $timeout_express = false){
        $this->charset = 'utf-8';
        !empty($alipayPublicKey) AND $this->alipayPublicKey = $alipayPublicKey;
        $this->timeout_express = '3m';
        !empty($timeout_express) AND $this->timeout_express = $timeout_express;
    }
    public function setAppid($appid){
        $this->appId = $appid;
    }
    public function setNotifyUrl($notifyUrl){
        $this->notifyUrl = $notifyUrl;
    }
    public function setRsaPrivateKey($saPrivateKey){
        $this->rsaPrivateKey = $saPrivateKey;
    }
    public function setTotalFee($payAmount){
        $this->totalFee = $payAmount;
    }
    public function setOutTradeNo($outTradeNo){
        $this->outTradeNo = $outTradeNo;
    }
    public function setOrderName($orderName){
        $this->orderName = $orderName;
    }
    public function setTradeNo($tradeNo){
        $this->tradeNo = $tradeNo;
    }
    public function setRefundAmount($refundAmount){
        $this->refundAmount = $refundAmount;
    }
    public function doPay(){
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'total_amount'=>$this->totalFee,
            'subject'=>$this->orderName,
            'timeout_express'=>$this->timeout_express
        );
        $commonConfigs = array(
            'app_id' => $this->appId,
            'method' => 'alipay.trade.precreate',
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset, $commonConfigs);
        return json_decode($result,true);
    }
    public function doQuery(){
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'trade_no'=>$this->tradeNo,
        );
        $commonConfigs = array(
            'app_id' => $this->appId,
            'method' => 'alipay.trade.query',
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset, $commonConfigs, true);
        return json_decode($result,true);
    }
    public function doClose(){
        $requestConfigs = array(
            'trade_no'=>$this->tradeNo,
            'out_trade_no'=>$this->outTradeNo,
        );
        $commonConfigs = array(
            'app_id' => $this->appId,
            'method' => 'alipay.trade.close',
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset, $commonConfigs);
        $resultArr = json_decode($result,true);
        if(empty($resultArr)){
            $result = iconv('GBK','UTF-8//IGNORE',$result);
            return json_decode($result,true);
        }
        return $resultArr;
    }
    public function doRefund(){
        $requestConfigs = array(
            'trade_no'=>$this->tradeNo,
            'out_trade_no'=>$this->outTradeNo,
            'refund_amount'=>$this->refundAmount,
        );
        $commonConfigs = array(
            'app_id' => $this->appId,
            'method' => 'alipay.trade.refund',
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset, $commonConfigs);
        $resultArr = json_decode($result,true);
        return $resultArr;
    }
    public function generateSign($params, $signType = "RSA"){
        return $this->sign($this->getSignContent($params), $signType);
    }
    protected function sign($data, $signType = "RSA"){
        $priKey=$this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if("RSA2" == $signType){
            //openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        }else{
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    public function rsaCheck($params){
        if(empty($params['sign'])) return FALSE;
        if(empty($params['sign_type'])) return FALSE;
        
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign']);
        unset($params['sign_type']);
        $str_data = $this->getSignContent($params);
        return $this->verify($str_data, $sign, $signType);
    }
    protected function verify($data, $sign, $signType = 'RSA'){
        $pubKey = $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if("RSA2" == $signType){
            //$result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        }else{
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }
    protected function checkEmpty($value){
        if(!isset($value))
            return true;
        if($value === null)
            return true;
        if(trim($value) === "")
            return true;
        return false;
    }
    public function getSignContent($params){
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach($params as $k => $v){
            if(false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)){
                $v = $this->characet($v, $this->charset);
                if($i == 0){
                    $stringToBeSigned .= "$k" . "=" . "$v";
                }else{
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }
    protected function characet($data, $targetCharset){
        if(!empty($data)){
            $fileType = $this->charset;
            if(strcasecmp($fileType, $targetCharset) != 0){
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
    public function curlPost($url = '', $postData = '', $query = false, $options = array()){
        if(is_array($postData)){
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if(!empty($query)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/x-www-form-urlencoded;charset=' . $this->charset));
        }
        if(!empty($options)){
            curl_setopt_array($ch, $options);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}?>