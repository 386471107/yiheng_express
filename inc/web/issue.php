
<?php 

	global $_GPC, $_W;
	include_once MODULE_ROOT.'/inc/func/yh_web.func.php';

	$wopt = new yh_web_option();
	$nav = $wopt->page_info();
	$yiheng_express_issue = 'yiheng_express_issue';

	if(checksubmit('m_save'))
		{

         	$data=array(
				'issue_type'=>$_GPC['ctype'],
				'issue_content'=>trim($_GPC['content']),
				'issue_time'=>time(),
				
			);
			$result=pdo_insert($yiheng_express_issue, $data);
			message('新增成功！', $this->createWebUrl('issue'), 'success');
         }

      
    $list = pdo_fetchall("SELECT * FROM ".tablename($yiheng_express_issue)." order by issue_done ASC,issue_id DESC");   

	include $this->template('pc/issue');  
	
	
	
?>