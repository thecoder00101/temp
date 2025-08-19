<?php
/**
 * 用户签名插件卸载文件
 *
 * @create 2020-02-07
 * @author 西部主机论坛 https://www.westping.com
 */
!defined('DEBUG') AND exit('Forbidden');
$tablepre = $db->tablepre;
$sql = "ALTER TABLE {$tablepre}user DROP COLUMN signature";
$r = db_exec($sql);
kv_delete('user_signature');
message(0, '<p>客官，这就走了吗，伦家有点舍不得哎，有空再来玩啊。</p><a role="button" class="btn btn-secondary btn-block m-t-1" href="javascript:history.back();">返回</a>');
$r === FALSE AND message(-1, '删除表结构失败');
?>