<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\Model\OrderModel;
class PayController extends Controller
{
    public $app_id;
    public $gate_way;
    public $notify_url;
    public $return_url;
    public $rsaPrivateKeyFilePath = './key/priv.key';
    public $aliPubKey = './key/ali_pub.key';
    //
    public function __construct(){
        $this->app_id=env('APP_ID');
        $this->gate_way = env('GATE_WAY');
        $this->notify_url = env('NOTIFY_URL');
        $this->return_url = env('RETURN_URL');
    }
    public function pay(){
        $url='http://word.tactshan.com';
        $client = new Client([
            'base_uri' => $url,
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET', '/order.php');
        echo $response->getBody();
    }
    public function test($order_id)
    {
        $where=[
            'order_id'=>$order_id
        ];
        $rel=OrderModel::where($where)->first()->toArray();
        if(!$rel){
            die('订单号不存在');
        }
        if($rel['is_pay']==1){
            die('订单已支付');
        }
        if($rel['order_status']==2){
            die('订单已取消');
        }
        $bizcont = [
            'subject'           => 'diingdan:'. mt_rand(1111,9999).str_random(6),
            'out_trade_no'      => $rel['order_number'],
            'total_amount'      => $rel['order_amount'],
            'product_code'      => 'QUICK_WAP_WAY',
        ];

        $data = [
            'app_id'   => $this->app_id,
            'method'   => 'alipay.trade.wap.pay',
            'format'   => 'JSON',
            'charset'   => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'   => '1.0',
            'return_url' => $this->return_url,
            'notify_url'   => $this->notify_url,
            'biz_content'   => json_encode($bizcont),
        ];

        $sign = $this->rsaSign($data);
        $data['sign'] = $sign;
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        $url = rtrim($param_str,'&');
        $url = $this->gate_way . $url;
        header("Location:".$url);
    }


    public function rsaSign($params) {
        return $this->sign($this->getSignContent($params));
    }

    protected function sign($data) {

        $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
        $res = openssl_get_privatekey($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);

        if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }


    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, 'UTF-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }


    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = 'UTF-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }


        return $data;
    }
    //验签
    function verify($params) {
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;

        //读取公钥文件
        $pubKey = file_get_contents($this->aliPubKey);
        $pubKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        //转换为openssl格式密钥

        $res = openssl_get_publickey($pubKey);
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值

        $result = (openssl_verify($this->getSignContent($params), base64_decode($sign), $res, OPENSSL_ALGO_SHA256)===1);
        openssl_free_key($res);

        return $result;
    }
    public function alinotify(){
        $data = json_encode($_POST);
        $log_str = '>>>> '.date('Y-m-d H:i:s') . $data . "<<<<\n\n";
        //记录日志
        file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);
        //验签
        $res = $this->verify($_POST);

        $log_str = '>>>> ' . date('Y-m-d H:i:s');
        if($res === false){
            //记录日志 验签失败
            $log_str .= " Sign Failed!<<<<< \n\n";
            file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);
        }else{
            $log_str .= " Sign OK!<<<<< \n\n";
            file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);
        }
        //修改订单信息
        if($_POST['trade_status']=='TRADE_SUCCESS'){
            //更新订单状态
            $oid = $_POST['out_trade_no'];     //商户订单号
            $info = [
                'order_status'  =>3,
                'is_pay'        => 1,       //支付状态  0未支付 1已支付
                'pay_amount'    => $_POST['total_amount'],    //支付金额
                'pay_time'      => strtotime($_POST['gmt_payment']), //支付时间
                'plat_number'   => $_POST['trade_no'],      //支付宝订单号
                'plat'          => 1,      //平台编号 1支付宝 2微信
            ];

            OrderModel::where(['order_number'=>$oid])->update($info);
        }
    }
    public function alireturn(){
        echo '<pre>';print_r($_GET);echo '</pre>';die;
    }
}
