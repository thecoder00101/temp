<?php !defined('DEBUG') AND exit('Access Denied.');?>
<link rel="stylesheet" href="plugin/fox_alipay/oddfox/static/css/my.pay.css">
<template include="./plugin/tt_credits/view/htm/my_credits.template.htm">
	<slot name="my_body">
<div class="border rounded p-3">
  <div class="alert alert-info" role="alert">
      <i class="icon-bell"></i> <?php echo $fox_tips;?>
      <!--{hook fox_pay_my_pay_tips_after.php}-->
 </div>

  <form action="<?php echo url('my-pay');?>" method="post" id="formpay">
      <div class="foxpay-box w-100 mb-2">
          <label class="form-control-label"> 
         <div class="w-100 mb-2 text-muted"><b>快捷金额：</b></div>
          <div class="clearfix"> 
              <?php foreach($pay_rmbstr_arr as $k=>$v){?>
              <span class="payamount btn btn-sm btn-outline-orange mr-1 mb-2 <?php if(empty($k)){?>active<?php }?>" payamount="<?php echo $v;?>"><?php echo $v;?>元</span> 
              <?php }?>
              <span class="payamount btn btn-sm btn-outline-orange mr-1 mb-2 other" payamount="">其它</span>
          </div>
          </label>
      </div>
      <div class="foxpay-box w-100 mb-2 other-box">
          <label class="form-control-label"> 
          <div class="w-100 mb-2 text-muted"><b>充值金额：</b></div>




          <div class="input-group">
                <input type="text" name="amount" id="amount" value="<?php echo $pay_rmbstr_arr[0];?>" placeholder="请输入金额" value="<?php echo $kwd;?>" maxlength="4" 
 class="form-control" />
                <div class="input-group-append"><button id="submit" class="btn btn-dark px-5" data-loading-text="请稍候……">立即充值</button></div>





   




          </label>
      </div>  
      <div id="picbox" class="foxpay-box w-100 mb-2 collapse">
          <div class="w-100 mb-2 text-muted"><b>扫码付款：</b></div>
          <div class="clearfix">
              <div class="mb-3"><img id="piccode" src="plugin/fox_alipay/oddfox/static/img/wait_qr.png" class="border" width="300" /></div>
          </div>
      </div>
      <div class="queryfeedback mb-3" style="display:none"></div>
      
     <button type="button" id="payquery" class="btn btn-outline-orange active" data-loading-text="正在查询支付状态..." data-orderid="0" style="display:none">已完成付款</button>
  </form>
 </div>
</slot>
</template>
<script>$('a[data-active="my-pay"]').addClass('active');var pay_default = <?php echo $fox_alipay_arr['pay_min'];?>;</script>
<script src="plugin/fox_alipay/oddfox/static/js/fox_paycheck.js"></script>