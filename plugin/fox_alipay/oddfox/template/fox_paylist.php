<?php !defined('DEBUG') AND exit('Access Denied.');include _include(ADMIN_PATH.'view/htm/header.inc.htm');?>
<link rel="stylesheet" href="../plugin/fox_alipay/oddfox/static/css/pay.table.css">
<div class="row">
    <div class="col-lg-12">
        <div class="btn-group mb-3" role="group">
            <?php echo admin_tab_active($menu['pay']['tab'], 'paylist');?>
        </div>
        <div class="btn-group mb-3 hidden-sm float-right" role="group">
            <a class="btn btn-danger" target="_blank" href="https://bbs.oddfox.cn"><i class="icon-firefox"></i></a>
            <a class="btn btn-dark" href="javascript:void(0);" onclick="javascript:location.reload();"><i class="icon-refresh"></i></a>
            <a class="btn btn-dark" href="<?php echo url("plugin");?>"><i class="icon-times"></i></a>
        </div>
        <div class="w-100"></div>
        <div class="card">
          <div class="card-header"><i class="icon-cogs"></i> 支付列表 (总收入：<?php echo $totalmoney;?>元)</div>
          <div class="card-body pb-0">
            <div class="input-group">
                <input type="text" name="keyword" id="keyword" placeholder="订单号 / 支付宝订单号 / 用户ID" value="<?php echo $kwd;?>" class="form-control" />
                <div class="input-group-append"><button id="search" class="btn btn-dark px-5">点击搜索</button></div>
            </div>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th class="hidden-sm">ID</th>
                        <th>用户名</th>
                        <th>订单号</th>
                        <th class="hidden-sm">支付宝订单号</th>
                        <th class="text-lg-right">充值金额</th>
                        <?php if($fox_alipay_arr['pay_ratio'] > 1){?><th class="text-lg-right">到账金额</th><?php }?>
                        <th>充值时间</th>
                        <th>到账时间</th>
                        <th>状态</th>
                        <th class="text-lg-right">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($paylist as $val){?>
                <tr>
                    <td class="hidden-sm" data-label="ID"><?php echo $val['id'];?></td>
                    <td data-label="用户名"><a href="<?php echo url("/user-$val[uid]");?>" target="_blank"><?php echo $val['username'];?></a></td>
                    <td data-label="订单号"><?php echo $val['orderid'];?></td>
                    <td class="hidden-sm" data-label="支付宝订单号"><?php echo $val['trade_no_fmt'];?></td>
                    <td data-label="充值金额" class="text-lg-right"><?php echo $val['money'];?>元</td>
                    <?php if($fox_alipay_arr['pay_ratio'] > 1){?><td data-label="到账金额" class="text-lg-right"><?php echo $val['pay_rmbs_fmt']/100.0;?>元</td><?php }?>
                    <td data-label="充值时间"><?php echo $val['create_date_fmt'];?></td>
                    <td data-label="到账时间"><?php echo $val['pay_date_fmt'];?></td>
                    <td data-label="状态"><?php echo $val['status_fmt'];?></td>
                    <td class="text-lg-right" data-label="操作"><div class="btn-group"><a role="button" href="javascript:void(0)" class="btn btn-sm btn-danger fox_confirm <?php if($val['status'] != 2){?>disabled<?php }?>" data-confirm-text="确定要退款？要不要再想想？" data-url="<?php echo url('plugin-setting-fox_alipay-payrefund-'.$val['orderid']);?>">退款</a><a role="button" href="javascript:void(0);" class="btn btn-sm btn-info fox_confirm <?php if($val['status'] != 3){?>disabled<?php }?>" data-confirm-text="确定要关闭该交易？" data-url="<?php echo url('plugin-setting-fox_alipay-payclose-'.$val['orderid'])?>">关闭</a><a role="button" href='<?php echo url('user-update-'.$val['uid'])?>' target="_blank" class="btn btn-sm btn-success">编辑</a></div></td>
                </tr>
                <?php }?>
                </tbody>
            </table><?php if($pagination){?>
            <nav><ul class="pagination justify-content-center flex-wrap"><?php echo $pagination; ?></ul></nav><?php }?>

          </div>
        </div>
    </div>
</div>
<?php include _include(ADMIN_PATH.'view/htm/footer.inc.htm');?>
<script>
$('#nav li.nav-item-pay').addClass('active');
var jsearch = $("#search");
jsearch.on('click', function(){
    var keyword = $("#keyword").val();
    var url = xn.url('plugin-setting-fox_alipay-paylist')+"?kwd="+xn.urlencode(keyword);
    window.location = url;
});
$('a.fox_confirm').on('click', function(){
    var url = $(this).data('url');
    var amount = $(this).data('amount');
    var text = $(this).data('confirm-text');
    $.confirm(text, function(){
        $.xpost(url, function(code, message){
            if(code == 0){
                $.alert(message, 1);
                setTimeout(function(){window.location.reload();}, 1000); return false;
            }else{
                $.alert(message); return false;
            }
        });
    });
    return false;
});
</script>