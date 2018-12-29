
<?php 

	global $_GPC, $_W;
	
	include_once MODULE_ROOT.'/inc/mobile/common.php';
	$title = '快递出库';
	load()->func('file');
	if ($myinfo['m_level']==4)
	{
		$url=$this->createMobileUrl('employee');
		header("Location: $url");exit();
	
	}
	
	//走权限管理
	$page="express_outgoing";
	$is_allow = $member->is_allow_to_view($page,$myinfo['m_level']);
	if ($is_allow ==0)
	{
		header("Location: $refuse_url");exit();
	}
	//走权限管理
	
	if ($_W['isajax'])
		{
			$act=trim($_GPC['act']);
			if ($act == 'scan' || $act == 'search')
			{
				//权限判断，如果是加入者或店铺负责人，那就可以删除
					$barcode=trim($_GPC['barcode']);
					$barcode_where= ' where recoder_barcode =:recoder_barcode and uniacid=:uniacid';
					$barcode_params = array(
					'uniacid' => $_W['uniacid'],
					'recoder_barcode' =>$barcode,
					);  
					$barcode_info = pdo_fetch("SELECT *  FROM ".tablename($express_recode). $barcode_where,$barcode_params);
					if (!empty($barcode_info))
					{	

						$user_flag = pdo_fetch("SELECT m_flag FROM ".tablename($express_tel_list)." where m_tel=".$barcode_info['recoder_tel']);

						$b_info['m_flag']=$user_flag['m_flag'];
						$b_info['recoder_code']=$barcode_info['recoder_code'];
						$b_info['shelves']=$barcode_info['recoder_shelves'].'-'.$barcode_info['recoder_goods_num'];
						$b_info['recoder_express_name']=$barcode_info['recoder_express_name'];
						$b_info['recoder_create']=date("Y-m-d H:i",$barcode_info['recoder_create']);
						$b_info['recoder_senttime']=date("Y-m-d H:i",$barcode_info['recoder_senttime']);
						$b_info['recoder_tel']=$barcode_info['recoder_tel'];
						$b_info['recoder_get_status']=$barcode_info['recoder_get_status'];
						$b_info['id']=$barcode_info['id'];
						if ($myinfo['m_level']==1 || $myinfo['m_level']==2)
						$b_info['img']=tomedia($barcode_info['recoder_img']);
						result_back(1,$b_info);
					}
					result_back(0);
				

			}

			if ($act == 'done')
			{

				$id= $_GPC['id'];
				$done_res=$opt->Done_record($id,$openid);	

						if ($done_res)
						{	
							$opt->Stats_data_change($member->Get_shop_id($openid),$s_type=0,$s_flag=0);
							$res['rid']=$id;
							result_back(1,$res);	
						}

			}
			
			
			if ($act == 'down_pic')
	{
		
		
		$media_id=$_GPC['ser_id'];
		$express_id=(int)($_GPC['express_id']);
		if ($express_id && $media_id)
		{
			$account_api = WeAccount::create();
			$token = $account_api->getAccessToken();
			$src_path = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$token&media_id=".$media_id;  
			
			// $upload_path= MODULE_ROOT."/temp_images/";

			$datefolder =date("Ymd");
			$upload_path="yiheng/".$datefolder."/";
			
			$upload_name=date("YmdHis").rand(100000,999999);

			// $salt= 'nbyiheng';
			// $img_name=md5($upload_name.$salt).'.jpg';

			$img_name=$upload_path.$upload_name.'.jpg';	
			$down_img_data=file_get_contents($src_path);  
			file_write($img_name, $down_img_data);
			$imgdata = array(
				'recoder_img' => $img_name,
				); 
			$res = pdo_update($express_recode,$imgdata,array('id' => $express_id));

			$data = array(		 
				'uniacid' => $_W['uniacid'],
				'ra_recode_id' => $express_id,
				'ra_img' => $img_name,
				'ra_ctime' =>time(),
				);
			$result=pdo_insert($recode_attimg, $data);
			
			$resa['img']=tomedia($img_name);	
			$re=json_encode($resa);
			exit($re);	
	  }

	}


			
			
			
	
	
		}
		include $this->template('express_outgoing');  
		exit();	

	
	 
	   function result_back($flag,$params =null)
		  {
		  	if (!empty($params))
		  	{
		  		foreach ($params as $key => $value) {
		  			$res[$key]=$value;	
		  		}
		  	}

		  	$res['success']=$flag;			
			$re=json_encode($res);
			exit($re);
		  }



 
	 ?>






























