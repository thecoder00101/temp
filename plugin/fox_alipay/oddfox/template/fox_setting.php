<?php !defined('DEBUG') AND exit('Access Denied.');include _include(ADMIN_PATH.'view/htm/header.inc.htm');?>
<div class="row">
    <div class="col-lg-12">
        <div class="btn-group mb-3" role="group">
            <?php echo admin_tab_active($menu['pay']['tab'], 'setting');?>
        </div>
        <div class="btn-group mb-3 hidden-sm float-right" role="group">
            <a class="btn btn-danger" target="_blank" href="https://bbs.oddfox.cn"><i class="icon-firefox"></i></a>
            <a class="btn btn-dark" href="javascript:void(0);" onclick="javascript:location.reload();"><i class="icon-refresh"></i></a>
            <a class="btn btn-dark" href="<?php echo url("plugin");?>"><i class="icon-times"></i></a>
        </div>
        <div class="w-100"></div>
        <div class="card">
            <div class="card-header"><i class="icon-cogs"></i> 插件设置</div>
            <div class="card-body">
                <form action="<?php echo url('plugin-setting-fox_alipay');?>" method="post" id="form" class="mb-0">
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">快捷选择充值XN币数：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_rmbstr'];?>
                            <div class="text-grey small mt-2">注：多个金额以竖线|隔开，如<b class="text-danger">1|5|10|20|30|50|100|200|300|500</b></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">最低充值多少XN币：</label>
                        <div class="col-sm-10" id="input_num">
                            <?php echo $input['pay_min'];?>
                        </div>
                    </div>                  
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">充值XN币积分比例：</label>
                        <div class="col-sm-10" id="input_num">
                            <?php echo $input['pay_ratio'];?>
                            <div class="text-grey small mt-2">注：比如填写2，那么充值一元XN币则到账2元</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">你的支付宝收款帐号：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_email'];?>
                            <div class="text-grey small mt-2">注：用于异步通知验证</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">支付宝APPID：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_appid'];?>
                            <div class="text-grey small mt-2">注：前往支付宝官网<a href="https://openhome.alipay.com" class="text-danger" target="_blank">获取APPID</a></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">开放平台支付宝公钥：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_publickey'];?>
                            <div class="text-grey small mt-2">注：<a href="https://opendocs.alipay.com" class="text-danger" target="_blank">获取支付宝公钥方法</a></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0 pt-sm-2">开放平台开发助手私钥：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_privatekey'];?>
                            <div class="text-grey small mt-2">注：<a href="https://opendocs.alipay.com" class="text-danger" target="_blank">获取商户私钥方法</a></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0">扫码之后限时完成付款：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_timeout'];?>
                            <div class="text-grey small">注：插件默认3分钟内完成扫码付款，超时则关闭交易；可自行修改，如：5分钟请填写<span class="text-danger">5m</span>，参数：<span class="text-danger">m</span>分钟，<span class="text-danger">h</span>小时，该参数数值<span class="text-danger">不接受小数点</span></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label text-sm-right pr-sm-0">是否开启支付调试模式：</label>
                        <div class="col-sm-10">
                            <?php echo $input['pay_debug'];?>
                            <div class="text-grey small">注：正式使用时请勿开启，调试模式固定支付<span class="text-danger">1分钱</span>。</div>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-sm-12">
                            <input type="hidden" name="act" value="setting" />
                            <button type="submit" class="btn btn-primary btn-block" id="submit" data-loading-text="<?php echo lang('submiting');?>..."><?php echo lang('confirm');?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include _include(ADMIN_PATH.'view/htm/footer.inc.htm');?>
<script>
var jform = $("#form");
var jsubmit = $("#submit");
jform.on('submit', function(){
    jform.reset();
    jsubmit.button('loading');
    var postdata = jform.serialize();
    $.xpost(jform.attr('action'), postdata, function(code, message) {
        if(code == 0) {
            $.alert(message, 1);
            setTimeout(function(){window.location.reload();jsubmit.button('reset');}, 1000); return false;
        } else {
            $.alert(message); jsubmit.button('reset'); return false;
        }
    });
    return false;
});
$('#input_num input').bind("input propertychange",function(event){
    if(this.value.length == 1) {
        this.value = this.value.replace(/[^1-9]/g, '1');
    } else {
        this.value = this.value.replace(/\D/g, '');
    }
});
$('#nav li.nav-item-pay').addClass('active');
</script>