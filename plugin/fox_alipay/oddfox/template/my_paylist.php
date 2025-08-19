<?php !defined('DEBUG') AND exit('Access Denied.');?>
<link rel="stylesheet" href="plugin/fox_alipay/oddfox/static/css/my.pay.css">
<template include="./plugin/tt_credits/view/htm/my_credits.template.htm">
	<slot name="my_body">
<table class="table table-hover mb-0">
     <div class="card-body">
        <?php foreach($paylist as $val){?>  
<span class="btn btn-outline-info btn-sm"><?php echo $val['pay_status_fmt'];?></span><?php echo $val['pay_date_fmt'];?>， 充值金额：<?php echo $val['money'];?> 元，<?php if($fox_alipay_arr['pay_ratio'] > 1){?>应到账金额<?php }?>：<?php if($fox_alipay_arr['pay_ratio'] > 1){?><?php echo $val['pay_rmbs_fmt']/100.0;?>元<?php }?><br>订单号：<?php echo $val['orderid'];?><br>支付宝订单号：<?php echo $val['pay_trade_no_fmt'];?>
<hr>
<?php }?>
     </div>
</table>
<?php if($pagination){?>
<nav class="border-top pt-3"><ul class="pagination justify-content-center flex-wrap mb-0"><?php echo $pagination;?></ul></nav>
<?php }?>
</slot>
</template>
<script>$('a[data-active="my-paylist"]').addClass('active');</script>