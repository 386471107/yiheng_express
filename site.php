<?php

defined('IN_IA') or die('Access Denied');

Class Yiheng_expressModuleSite extends WeModuleSite

{

	

	// ===============================================

	

	

	// ===============================================

		

	// public function doWebUpgrade(){

		// global $_W, $_GPC;

		// include_once 'sys/upgrade.php';

		// echo 'upgraded';

	// }

	

	// 文件路径

	private function getLogic($_name, $type = "web", $auth = false) {

		global $_W, $_GPC;

		if ($type == 'web') {

			checkLogin ();

			include_once 'inc/web/' . strtolower ( substr ( $_name, 5 ) ) . '.php';

		} else if ($type == 'mobile') {

			 if ($auth) {

				  include_once 'inc/func/isauth.php';

			  }

			include_once 'inc/mobile/' . strtolower ( substr ( $_name, 8 ) ) . '.php';

		} else if ($type == 'func') {

			//checkAuth ();

			include_once 'inc/func/' . strtolower ( substr ( $_name, 8 ) ) . '.php';

		}

	}



	// ====================== Web =====================

	

	

	

	

	

	public function doWebStaff_manage() { 

	$this->getLogic ( __FUNCTION__, 'web' );  

	}

	public function doWebExpress_list() { 

	$this->getLogic ( __FUNCTION__, 'web' );  

	}

	public function doWebExpress_setting() { 

	$this->getLogic ( __FUNCTION__, 'web' );  

	}

	public function doWebBase_setting() { 

	$this->getLogic ( __FUNCTION__, 'web' );  

	}

	
public function doWebSlide_manage() { 

			$this->getLogic ( __FUNCTION__, 'web' );  

		}

	



public function doWebShop_manage() { 

			$this->getLogic ( __FUNCTION__, 'web' );  

		}

	



public function doWebAccess_setting() { 

			$this->getLogic ( __FUNCTION__, 'web' );  

		}

	













public function doMobileHome() { 


		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

public function doMobileStorage() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileSend_message() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	

	public function doMobileCheckin() { 
		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileRegister() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	

	public function doMobileRecord() { 
		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	

	public function doMobileLog() { 
		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileSetting() { 
		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	

	

	public function doMobileBind() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}


	public function doMobileApply() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileManage() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

		public function doMobileArea_default() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileMy_staff_list() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}


		public function doMobileMy_area() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileTel_list() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileEmployee() {  

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileSend_express() {  

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	

// *********************************mobile 端*************************************
	public function doMobileExpress_sendmsg() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileExpress_ordertraces() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileExpress_send() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileExpress_send_log() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileError() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}


	public function doMobileWait_pickup() { 

		$this->getLogic ( __FUNCTION__, 'mobile' );  

	}

	public function doMobileExpress_scan() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );  

		}
	

public function doMobileTest() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileComment_test() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileArticle_detail() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}



public function doMobileStock_list() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileRetention_list() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileStatistics() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}


public function doMobileMy() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileMy_wait() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}


public function doMobileMy_done() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileExpress_query() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}


public function doMobileShop_info() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}


public function doMobileAbout() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}


public function doMobileMy_sms() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}

public function doMobileSet_test() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}		


	public function doMobileSettinga() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}		

	
		public function doMobileExpress_outgoing() { 

			$this->getLogic ( __FUNCTION__, 'mobile' );   

		}		

		
		
		
		
//=======================================自定义PC端页面==========================	
		
		
		
	public function doWebPc_index() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}

	public function doWebPc_member_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}
	public function doWebPc_member_admin_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	
	public function doWebPc_member_manage_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	
	public function doWebPc_member_staff_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
		
	public function doWebPc_member_ban_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
			
			
		
	public function doWebPc_member_login_log() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
			
	public function doWebPc_member_edit() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
				
	public function doWebPc_shop_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}
	public function doWebPc_shop_edit() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_shop_info_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_collection_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_uncollection_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_retention_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_wait_receive_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
	public function doWebPc_wait_deliver_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}


	public function doWebPc_weixin_send_log() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
	public function doWebPc_msn_send_log() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
					
	public function doWebPc_api_client_list() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
					
		public function doWebPc_statistics() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
						
					
		public function doWebPc_weixin_tpl() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
		
		public function doWebPc_api() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
	
		public function doWebPc_api_log() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
			
		
		public function doWebPc_view_setting() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	
	public function doWebPc_m_view_setting() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}		
			
		public function doWebPc_change_pwd() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}			
		
		public function doWebPc_msn_tpl() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}		
		public function doWebIssue() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	

	public function doWebPc_common() 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	

	public function doWebPc_employee_setting () 
	{
		$this->getLogic ( __FUNCTION__, 'web' ); 
	}	

}