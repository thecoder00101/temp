
<?php
!defined('DEBUG') AND exit('Access Denied.');
$king_player_kv = kv_cache_get('king_player');
?>
<div>
</div>
<a href="javascript:void(0);" role="button" class="btn btn-sm btn-primary mb-3 mr-2" data-toggle="modal" data-target="#king_player_modal">隐藏内容</a>
<div class="modal fade" id="king_player_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="max-width:600px!important; margin-top:150px!important;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">阅读权限学院修改版：</h4>
			</div>
		
			<div class="modal-body">
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">登录可见：</span>
                    </div>
                    <input type="text" name="vod_url" id="vod_url" placeholder="请输入需要登录可见的内容" class="form-control" />
                </div>
                
           
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">回复可见：</span>
                    </div>
                    <input type="text" name="http_url" id="http_url" placeholder="请输入需要回复可见的内容" class="form-control" />
                </div>
			
			
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">友情提醒：</span>
                    </div>
                    <div class="form-control text-danger">学院提醒您：按照你的需求选择！</div>
                </div>            
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-primary" onclick="king_player_submit_modal()">确定</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
function king_player_submit_modal(){
	$("#king_player_modal").modal("hide");  
	var editor = window.parent.window.tinymce.activeEditor.insertContent['message'];;
	var vod_url = $('#vod_url').val();
	<?php if( !empty($king_player_kv['api_url']) ){?>
	var http_url = $('#http_url').val();<?php }?>
	if(vod_url){
		tinymce.activeEditor.insertContent('[ttlogin]'+vod_url+'[/ttlogin]', 1);
		$('#vod_url').val('');
	}
	<?php if( !empty($king_player_kv['api_url']) ){?>

	if(http_url){
		tinymce.activeEditor.insertContent('[ttreply]'+http_url+'[/ttreply]', 1);
		$('#http_url').val('');
	}<?php }?>
}
</script>
<script type="text/javascript" src="/plugin/tt_read/hook/xiunocntt.js"></script>