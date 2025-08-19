
<?php
!defined('DEBUG') AND exit('Access Denied.');
$text_f_kv = kv_cache_get('text_f');
?>
<a href="javascript:void(0);" role="button" class="btn btn-primary"  data-toggle="modal" data-target="#text_f_modal" style="float:right;">付费内容</a>
<div class="modal fade" id="text_f_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:600px!important; margin-top:150px!important;">
  <div class="modal-content">
   <div class="modal-header">
    <h4 class="modal-title" id="myModalLabel">付费功能</h4>
   </div>
  
   <div class="modal-body">
 <div class="form-group input-group">
                    <textarea style="min-height:160px;" type="text" name="text_f" id="text_f" placeholder="请输入付费内容" class="form-control"></textarea>
                </div>
   </div>
      <?php if($isfirst&&$group['allowsell']=="1") {?>

   <div style="margin-top:-20px;" class="modal-body">

    <div style="border:1px solid #ccc;border-radius:3px;color:#ddd;padding:0 10px;margin-top:5px;">
    <input maxlength="10" type="text" name="content_num" placeholder="请输入金额" id="content_number" value="<?php echo $content_num;?>" style="display: inline-block;width:70%;height:30px;border:none;padding:0 10px;">| <font color="#666">个</font> | 
		<select name="content_type" id="content_type" style="height:30px;width:20%;border:none;background:none;">
			<option checked="checked" value="<?php echo lang('credits2');?>"><?php echo lang('credits2');?></option>
			<option value="<?php echo lang('credits3');?>"><?php echo lang('credits3');?></option></select>
<!--{hook post_message_credits_after.htm}-->
</div>

</div>
       <span style="float:left;position:relative;bottom:-25px;left:13px;"><input type="checkbox" name="content_num_status" value="1" /></span><span style="position:relative;bottom:-5px;left:30px;float:left;">是否付费</span>
   <div style="margin-top:-30px;" class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="button" class="btn btn-primary" onclick="text_f_submit_modal()">确定</button><?php }?>
   </div>
  </div>
 </div>
</div>

<script type="text/javascript">
function text_f_submit_modal(){
 $("#text_f_modal").modal("hide");  
 var editor = window.parent.window.tinymce.activeEditor.insertContent['message'];;
var text_f = $('#text_f').val();
 <?php if( !empty($cc_wymusic_kv['api_url']) ){?>
var http_url = $('#http_url').val();<?php }?>
 if(text_f){
  tinymce.activeEditor.insertContent('[ttPay]'+text_f+'[/ttPay]', 1);
  $('#text_f').val('');
 }
}
</script>