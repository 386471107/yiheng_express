<?php 

global $_GPC, $_W;


$id = intval($_GPC['id']);;
$title = '客户中心-我的待件';

$express_member='yiheng_express_member';
$express_recode='yiheng_express_recode';
$express_reg='yiheng_express_reg';

$express_wait_pickup='yiheng_express_wait_pickup';
$express_tpl='yiheng_express_tpl';

$uniacid=$_W['uniacid'];


include_once MODULE_ROOT.'/inc/func/nbyiheng.func.php';
$NbyihengFunc = new Yiheng_express();

	
	if (empty($_W['member']))
	{
			$appid	 =$_W['oauth_account']['key'];
			$secret	 =$_W['oauth_account']['secret'];
			$snsapi  = 'snsapi_userinfo'; 
			$expired = '1200';
			
			$wxuser = $_COOKIE[$_W['config']['cookie']['pre'] . $appid];
			if ($wxuser === NULL) {

				$code = ((isset($_GET['code']) ? $_GET['code'] : ''));

				if (!($code)) {
					$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
					header('Location: ' . $oauth_url);
					exit();
				}

				load()->func('communication');
				$getOauthAccessToken = ihttp_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code');
				$json = json_decode($getOauthAccessToken['content'], true);

				if (!(empty($json['errcode'])) && (($json['errcode'] == '40029') || ($json['errcode'] == '40163'))) {
					$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ((strpos($_SERVER['REQUEST_URI'], '?') ? '' : '?'));
					$parse = parse_url($url);

					if (isset($parse['query'])) {
						parse_str($parse['query'], $params);
						unset($params['code'], $params['state']);
						$url = 'http://' . $_SERVER['HTTP_HOST'] . $parse['path'] . '?' . http_build_query($params);
					}


					$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
					header('Location: ' . $oauth_url);
					exit();
				}


				if ($snsapi == 'snsapi_userinfo') {
					$userinfo = ihttp_get('https://api.weixin.qq.com/sns/userinfo?access_token=' . $json['access_token'] . '&openid=' . $json['openid'] . '&lang=zh_CN');
					$userinfo = $userinfo['content'];
				}
				 else if ($snsapi == 'snsapi_base') {
					$userinfo = array();
					$userinfo['openid'] = $json['openid'];
				}
				$userinfostr = json_encode($userinfo);
				isetcookie($appid, $userinfostr, $expired);
			}
			else
			{
				$userinfo= json_decode($wxuser, true);
			}

	}
 


if ($_W['isajax'])
		{

		$act=trim($_GPC['act']);

			if ($act=='del')
			{

				$delid=(int)$_GPC['id'];
				$result = pdo_delete($express_wait_pickup, array('wait_id' => $delid,'uniacid' =>$uniacid,'wait_openid' => $_W['openid']));
				$res['success']=1;			
				$re=json_encode($res);
				exit($re);

			}



	    if ($act=='addnew')
				{
					
						$express_no=trim($_GPC['express_no']);

						if (strlen($express_no)>9 && strlen($express_no)<16 && is_numeric($express_no))
					
						{
							

							//搜索记录中是否存在等待收件列表
							$where=' WHERE wait_openid =:wait_openid and wait_express_no = :wait_express_no and wait_status=:wait_status and uniacid = :uniacid';		
							$params = array(

							'wait_openid' => $_W['openid'],		
							'wait_express_no' => $express_no,	
							'wait_status' => 1,
							'uniacid' => $_W['uniacid'],
							); 
							
							$item = pdo_fetch("select * FROM ".tablename($express_wait_pickup). $where,$params);

							if ($item)
							{
								$res['success']=3;
								$re=json_encode($res);
								exit($re); 

							}
							//搜索记录中是否存在等待收件列表


							//搜索记录中是否存在，有存在则直接返回记录

							$exist_where=' WHERE recoder_express_id = :recoder_express_id and recoder_send_status=:recoder_send_status and uniacid = :uniacid';		
							$exist_params = array(
							'recoder_express_id' => $express_no,	
							'recoder_send_status' => 0,
							'uniacid' => $_W['uniacid'],
							); 
							
							$exist_item = pdo_fetch("select * FROM ".tablename($express_recode). $exist_where,$exist_params);

							if ($exist_item)
							{
								$data = array(
									'wait_openid' => $_W['openid'],
									'wait_express_no' => $express_no,
									'wait_exsit' => 1,
									'wait_addtime' => time(),
									'wait_sendtime' => time(),
									'wait_sendstatus' => 1,
									'wait_status' => 1,  

									'uniacid' => $_W['uniacid'],
								);
								 
								$result = pdo_insert($express_wait_pickup, $data);

								
									$tplwhere=" where uniacid =:uniacid and tpl_status =:tpl_status";	
											$tplparams = array(
											'uniacid' => $_W['uniacid'],
											'tpl_status' =>1,

											
											);  
										 $tplinfo = pdo_fetch("SELECT *  FROM ".tablename($express_tpl). $tplwhere,$tplparams);
										
										 if (!empty($tplinfo))
										 {
											$datainfo= array(
														'first' => $tplinfo['tpl_title']."\n",
														'keyword1' => $tplinfo['tpl_kw1'],
														'keyword2' => $exist_item['recoder_code'],
														'remark' => "\n".$tplinfo['tpl_remark']."\n"."货架号为".$exist_item['recoder_shelves'].'-'.$exist_item['recoder_goods_num'],
													); 
 											$tplid=$tplinfo['tpl_id'];
											$NbyihengFunc->sendmessage_inputa($_W['openid'],$tplid,$datainfo,'wait_pickup');
												}
								$res['success']=2;
								$re=json_encode($res);
								exit($re); 

							}
							//搜索记录中是否存在，有存在则直接返回记录




							
							//查询记录条数，一般最多为只能存30条记录
								$where=' WHERE wait_status = :wait_status and wait_openid=:wait_openid and uniacid = :uniacid';		
								$params = array(
								'wait_status' => 1,	
								'wait_openid' => $_W['openid'],
								'uniacid' => $_W['uniacid'],
								); 
								
								$item_count = pdo_fetch("SELECT count(*) as total  FROM ".tablename($express_wait_pickup). $where,$params);

								if ($item_count['total']>30)
								{
									$res['success']=0;
									// $res['total']=$item_count['total'];
									$re=json_encode($res);
									exit($re); 
								}
								//查询记录条数，一般最多为只能存30条记录

								$data = array(
									'wait_openid' => $_W['openid'],
									'wait_express_no' => $express_no,
									'wait_addtime' => time(),
									'uniacid' => $_W['uniacid'],
								);
								 $result = pdo_insert($express_wait_pickup, $data);
								

								if ($result) 
				 				{	

				 					$res['resultid']=pdo_insertid();
			 					    $res['success']=1;
									$re=json_encode($res);
									exit($re); 
				 				}
				 				 
				  			

						}


				}

		}

	

if ($_GPC['act']=='check')
{


	$id=$_GPC['id'];
	
	$data = array(
	'wait_get_status' => 1,					
	);
			pdo_update($express_wait_pickup, $data, array('wait_id' => $id,'uniacid' =>$_W['uniacid'],'wait_openid' =>$_W['openid'])); 
 
 }
 
//wait_exsit==1
//wait_status==1	

$orderby=' order by '.tablename($express_wait_pickup).'.`wait_get_status` ASC , '.tablename($express_wait_pickup).'.`wait_sendtime` ASC, '.tablename($express_wait_pickup).'.`wait_exsit` DESC';
$limit =" limit 0,30";
// $list = pdo_fetchall("SELECT * FROM ".tablename($express_wait_pickup). $where.$orderby.$limit ,$params);

 $sql="SELECT * FROM ".tablename($express_wait_pickup)." LEFT JOIN ".tablename($express_recode)." ON ".tablename($express_wait_pickup).".`wait_express_no` =".tablename($express_recode).".`recoder_express_id` WHERE ".tablename($express_wait_pickup).".`wait_exsit`=1 and ".tablename($express_wait_pickup).".`wait_status`=1 and ".tablename($express_wait_pickup).".`wait_openid`='".$_W['openid']."' and ".tablename($express_wait_pickup).".`uniacid`=  ".$_W['uniacid'].$orderby.$limit;

$list = pdo_fetchall($sql);


$wait_params=array(
	":uniacid" => $_W['uniacid'],
	":wait_openid" => $_W['openid'],
	":wait_exsit" => 0,
	
);	 

$wait_where=' WHERE uniacid = :uniacid and wait_openid=:wait_openid and wait_exsit =:wait_exsit';
$wait_orderby=' order by `wait_status` DESC , `wait_addtime` ASC';
$wait_limit =" limit 0,30";
$wait_list = pdo_fetchall("SELECT * FROM ".tablename($express_wait_pickup). $wait_where.$wait_orderby.$wait_limit ,$wait_params);


include $this->template('wait_pickup');  


 ?>
