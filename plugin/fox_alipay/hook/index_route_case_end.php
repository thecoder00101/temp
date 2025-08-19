<?php exit;
case 'pay': include _include(APP_PATH.'plugin/fox_alipay/oddfox/route/alipay.php'); break;
case 'tyqr': include _include(APP_PATH.'plugin/fox_alipay/oddfox/route/qr_code.php'); break;
?>