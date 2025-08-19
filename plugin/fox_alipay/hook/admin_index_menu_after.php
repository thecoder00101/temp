<?php exit;
$pay_menu = array(
        'pay' => array(
        'url'=>url('plugin-setting-fox_alipay'),
        'text'=>'支付', 
        'icon'=>'icon-cny', 
        'tab'=> array (
            'setting'=>array('url'=>url('plugin-setting-fox_alipay'), 'text'=>'支付设置'),
            'paylist'=>array('url'=>url('plugin-setting-fox_alipay-paylist'), 'text'=>'支付列表'),
        )
    ));
$menu += $pay_menu;
?>