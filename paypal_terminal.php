<?php
		/*
		Plugin Name: Sterling Property Management Tenant Payments
		Plugin URI: http://www.iwantmywebsitenow.com
		Description: Paypal Plugin to accept payments online from tenants
		Author: Melissa Redd
		Version: 2.0
		Release Date: May 6 2011
		Author URI: http://www.iwantmywebsitenow.com
		*/
		
		#################################### INSTALL & UNINSTALL PART #########################################################################
		//installation function
		
		global $wp_url;
		$wp_url = get_bloginfo('siteurl');
		
		function pppt_install(){
    		global $wpdb;
			//let's create transaction table.
   			$table = $wpdb->prefix."pppt_transactions";
    		$structure = "CREATE TABLE IF NOT EXISTS `$table` (
						  `pppt_id` int(20) NOT NULL auto_increment,
						  `pppt_dateCreated` datetime default '0000-00-00 00:00:00',
						  `pppt_amount` double NOT NULL,
						  `pppt_payer_email` varchar(255) default NULL,
						  `pppt_comment` longtext,
						  `pppt_transaction_id` varchar(255) default NULL,
						  `pppt_status` tinyint(5) default '1',
						  `pppt_payer_name` varchar(255) NOT NULL,
						  `pppt_propertyID` int(20) NOT NULL default '0',
						  UNIQUE KEY `pppt_id` (`pppt_id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    		$wpdb->query($structure);
			
			//now create services table
			$table = $wpdb->prefix."pppt_property";
    		$structure = "CREATE TABLE IF NOT EXISTS $table (
        				pppt_property_id INT(20) NOT NULL AUTO_INCREMENT,
						pppt_property_title VARCHAR(255) NOT NULL,
						pppt_property_unit DOUBLE NOT NULL,
						pppt_property_paymentamount MEDIUMTEXT NULL,
						UNIQUE KEY pppt_property_id (pppt_property_id)
    		);";
    		$wpdb->query($structure);
			
			//now create all options of the script.
			update_option('pppt_paypal_email',"mypaypal@email.com");
			update_option('pppt_paypal_currency',"USD");
			update_option('pppt_ty_title',"Thank You!");
			update_option('pppt_ty_text',"<p>Thank you for your payment! We will email you a confirmation number.</p>");
			update_option('pppt_cancel_title',"Payment Canceled!");
			//$wp_url = get_bloginfo('siteurl');
			update_option('pppt_cancel_text',"<p><a href='".$wp_url."'>Click Here</a> to go to homepage</p>");
			update_option('pppt_admin_email',"changeme@email.com");
			update_option('pppt_admin_send',"1");
			update_option('pppt_show_comment_field',"1");
			update_option('pppt_license',"");
			update_option('pppt_button_text',"Pay!");
			update_option('pppt_show_am_text',"1");
			update_option('pppt_show_dd_text',"2"); //show drop down with services 1 or show text box for input 2	
		}
		
		function pppt_uninstall(){
			//in case somebody want's to remove the script.
			//we are leaving intact transactions and services table - for history and in case client will want to re-instantiate the script.
			delete_option('pppt_paypal_email');
			delete_option('pppt_paypal_currency');
			delete_option('pppt_ty_title');
			delete_option('pppt_ty_text');
			delete_option('pppt_cancel_title');
			delete_option('pppt_button_text');
			delete_option('pppt_cancel_text');
			delete_option('pppt_admin_email');
			delete_option('pppt_admin_send');
			delete_option('pppt_show_comment_field');
			delete_option('pppt_show_dd_text');
			delete_option('pppt_button_text');
			delete_option('pppt_license');
			delete_option('pppt_show_am_text');
		}
		##############################################################################################################################################
		
				
		//Creating our menu in WP admin.
		function pppt_admin_actions() {
			global $wp_url;
			add_menu_page( "SPM Tenant Payments", "SPM Tenant Payments", "administrator", basename(__file__), "pppt_admin_overview", $wp_url."/wp-content/plugins/spmtenantpayments/resources/images/spmlogo.png");
			//add_menu_page( "SPM Tenant Payment", "SPM Tenant Payment", "administrator", basename(__file__), "pppt_display_admin_menu", "icon URL");
			//add_options_page("SPM Settings", "SPM Settings", 1, "SPM Terminal Settings", "pppt_admin");
			add_submenu_page( basename(__file__), 'SPM Payment Settings', 'Settings', 'administrator', 'pppt_admin_settings','pppt_admin_settings');
			add_submenu_page( basename(__file__), 'SPM Payment Services', 'Services', 'administrator', 'pppt_admin_services','pppt_admin_services');
			add_submenu_page( basename(__file__), 'SPM Payment Transactions', 'Transactions', 'administrator', 'pppt_admin_transactions','pppt_admin_transactions');
			add_submenu_page( basename(__file__), 'SPM Property Edit', '', 'administrator', 'pppt_admin_services_edit','pppt_admin_services_edit');

		}
		   
   	
		//function for including needed page into wp admin upon request from menu
		function pppt_admin_overview() { /* plugin overview page */
			include('spm_payment_overview.php');
		}

		function pppt_admin_settings() { /* settings page */ 
			include('spm_payment_settings.php');
		}
		function pppt_admin_services() { /* services page */
			include('spm_payment_services.php');
		}
		function pppt_admin_services_edit() { /* services editing page */
			include('spm_payment_services_edit.php');
		}
		function pppt_admin_transactions() { /* transactions page */
			include('spm_payment_transactions.php');
		}
		
		

		function pppt_tinymce()
		{
		  wp_enqueue_script('common');
		  wp_enqueue_script('jquery-color');
		  wp_admin_css('thickbox');
		  wp_print_scripts('post');
		  wp_print_scripts('media-upload');
		  wp_print_scripts('jquery');
		  wp_print_scripts('jquery-ui-core');
		  wp_print_scripts('jquery-ui-tabs');
		  wp_print_scripts('tiny_mce');
		  wp_print_scripts('editor');
		  wp_print_scripts('editor-functions');
		  add_thickbox();
		  wp_tiny_mce();
		  wp_admin_css();
		  wp_enqueue_script('utils');
		  do_action("admin_print_styles-post-php");
		  do_action('admin_print_styles');
		  remove_all_filters('mce_external_plugins');
		}
		
		function pppt_add_widget(){
			register_widget( 'pppt_widget' );
		}
		
		function init_jquery() {
			wp_enqueue_script('jquery');  
			wp_enqueue_script('jquery-ui-core'); 
		}    
		
		


		
		

		//add the hooks for install/uninstall and menu.
		register_activation_hook( __FILE__, 'pppt_install' );
		register_deactivation_hook(__FILE__, 'pppt_uninstall');
		add_action('admin_menu', 'pppt_admin_actions');
		add_filter('admin_head','pppt_tinymce');
		add_action('widgets_init', 'pppt_add_widget');
		add_action('init', 'init_jquery');
		

		/*add_action('init', 'pppt_button');
		########################################## BEGIN BUTTON FUNCTION ######################################
		function pppt_button() {
		   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
			 return;
		   }
		 
		   if ( get_user_option('rich_editing') == 'true' ) {
			 add_filter( 'mce_external_plugins', 'add_plugin' );
			 add_filter( 'mce_buttons', 'register_button' );
		   }
		 
		}
		function register_button( $buttons ) {
		 array_push( $buttons, "|", "pppt" );
		 return $buttons;
		}
		function add_plugin( $plugin_array ) {
		   $plugin_array['pppt'] = $wp_url.'/wp-content/plugins/spmtenantpayment/resources/js/button.js';
		   return $plugin_array;
		}*/
		
		########################################## BEGIN SHORTCODE FUNCTION ######################################
		
		/* allowed shortcodes [pppt_paypalform], with id of service [pppt_paypalform sid="service-id"], with name of service [pppt_paypalform sid="service-name"]*/
		
		add_shortcode('pppt_paypalform', 'pppt_paypalform_display'); //NEW IN v2
		
		function pppt_paypalform_display($atts) {
		  	$serviceData = $copyright_img = ""; //powered by Melissa Dufore.
			
					global $wp_url;
					global $wpdb;
					
					extract( shortcode_atts( array(
								'sid' => ''
							), $atts ) );
							
					/* Chech service id */		
					
					if(!empty($sid)){
						if(is_numeric($sid)){
							$query="SELECT * FROM ".$wpdb->prefix."pppt_property WHERE pppt_property_id='".intval($sid)."'";
						}else{
							$query="SELECT * FROM ".$wpdb->prefix."pppt_property WHERE pppt_property_title='".$sid."'";
						}
						$result=mysql_query($query) or die(mysql_error());
						if(mysql_num_rows($result)>0){
							$serviceData = mysql_fetch_assoc($result);
						}else{
							$sid='';
						}
					}
					
					$pppt_msg = "";	
					$pppt_show_form = 1;
					$pppt_continue = false;	
					$pppt_submit_paypal = false;

					
					/* Paypal processing if form submitted. */
					$process_pppt_post = (!empty($_REQUEST["process_pppt_post"]))?addslashes(htmlentities(strip_tags($_REQUEST["process_pppt_post"]))):'';
					$pppt_comments = (!empty($_REQUEST["pppt_comments"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_comments"]))):'';
					$pppt_amount = (!empty($_REQUEST["pppt_amount"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_amount"]))):'';
					$pppt_serviceID = (!empty($_REQUEST["pppt_propertyID"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_propertyID"]))):'';
					$pppt_ptype = (!empty($_REQUEST["pppt_ptype"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_ptype"]))):'';
					
					
					
					
					/*
						THANK YOU / CANCEL / IPN PROCESSING PART ==================
					*/
					$pppt_action = (!empty($_REQUEST["pppt_action"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_action"]))):'';
					if(!empty($pppt_action)){
						switch($pppt_action){
							case "cancel":
								$pppt_show_form = 1;
								$pppt_cancel_text = get_option('pppt_cancel_text');
								$pppt_msg = "<div class='pppt_error'>".(__($pppt_cancel_text))."</div>";
							break;
							case "success":
								$pppt_show_form = 0;
								$pppt_cancel_text = get_option('pppt_ty_text');
								$pppt_msg = "<div class='pppt_success'>".(__($pppt_ty_text))."</div>";
							break;
							case "ipn":
								require_once("paypal.ipn.php"); 
							break;
						}
					}
					/* 
						============================================================
					*/
					
					if(!empty($process_pppt_post) && $process_pppt_post=="yes"){
						/* 1. do all validations */
						if($pppt_ptype=="amount"){
							if(!empty($pppt_amount) && is_numeric($pppt_amount)){ $pppt_continue = true; } else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! Amount field cannot be empty and must contain numbers only."))."</div>";
							}
						} else if($pppt_ptype=="service"){
							if(!empty($pppt_serviceID) && is_numeric($pppt_serviceID)){ 
								$query="SELECT * FROM ".$wpdb->prefix."pppt_services WHERE pppt_services_id='".$pppt_serviceID."'";
								$result=mysql_query($query) or die(mysql_error());
								if(mysql_num_rows($result)>0){
									$r=mysql_fetch_assoc($result);
									$pppt_amount = number_format($r["pppt_services_price"],2);
									$pppt_continue = true;
								} else {
									$pppt_msg = "<div class='pppt_error'>".(__("Error! Service doesn't exist!"))."</div>";
								}
							} else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! No service selected!"))."</div>";
							}
						} 
						
						/* 2. if everything ok, lets insert transaction record with status unPAID*/
						if($pppt_continue){
						//pppt_services_title	pppt_services_price	pppt_services_descr
							$query="INSERT INTO ".$wpdb->prefix."pppt_transactions (pppt_dateCreated, pppt_amount, pppt_comment,pppt_serviceID ) VALUES (NOW(), '".$pppt_amount."', '".$pppt_comments."', '".$pppt_serviceID."')";
							if(mysql_query($query)){							
							/* 3. show thank you message and redirect to paypal, we'll need to add hook here so that on body load form would get processed? or document.form.submit()? */				
							
								$orderID=mysql_insert_id();
								//$blog_url = get_bloginfo('wpurl');
								$blog_url = "http://".(str_replace("http://","",$_SERVER['HTTP_HOST'])).$_SERVER['REQUEST_URI'];
								
								$pppt_paypal_email = get_option('pppt_paypal_email');
								$pppt_paypal_currency = get_option('pppt_paypal_currency');
								
								require_once("paypal.class.php"); 
								$paypal = new paypal_class;
								
								$paypal->add_field('business', $pppt_paypal_email);
								$paypal->add_field('return', $blog_url.(stristr($blog_url,"?")?"&":"?").'pppt_action=success');
								$paypal->add_field('cancel_return', $blog_url.(stristr($blog_url,"?")?"&":"?").'pppt_action=cancel');
								$paypal->add_field('notify_url', $blog_url.(stristr($blog_url,"?")?"&":"?").'pppt_action=ipn');
								$paypal->add_field('item_name_1', $_SERVER['HTTP_HOST']." payment");
								$paypal->add_field('amount_1', $pppt_amount);
								$paypal->add_field('item_number_1', "00001");
								$paypal->add_field('quantity_1', '1');
								$paypal->add_field('custom', $orderID);
								$paypal->add_field('upload', 1);
								$paypal->add_field('cmd', '_cart'); 
								$paypal->add_field('txn_type', 'cart'); 
								$paypal->add_field('num_cart_items', 1);
								$paypal->add_field('payment_gross', $pppt_amount);
								$paypal->add_field('currency_code', $pppt_paypal_currency);
								$pppt_show_form = 0;
								$pppt_submit_paypal = true;
							
								//$pppt_msg = "<div class='pppt_success'>".(_e("Thank you! You will be redirected to paypal.com for payment within 5 seconds."))."</div>";
							} else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! Something went wrong, Please double check your input"))."</div>";
							}
						} 
						
					}
			
					$pppt_wform = "";
		
					if($pppt_show_form==1){
					
						$pppt_wform .= "Pay by <img src='".$wp_url."/wp-content/plugins/spmtenantpayment/resources/images/paypal_logo.png"."' width='70'>";
					}
			
					$pppt_wform .= '<link rel="stylesheet" type="text/css" media="all" href="'.$wp_url.'/wp-content/plugins/spmtenantpayment/resources/css/pppt-widget.css" />';
					
					$pppt_wform .= $pppt_msg;
					
					if($pppt_show_form==1){
						$pppt_wform .= '<div class="pppt_widget_form">';
						if(stristr($_SERVER["REQUEST_URI"],"pppt_action=")){
							$ppptUrl = substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],"pppt_action="));
						} else { 
							$ppptUrl = "";
						}
						$pppt_wform .= '<form name="pppt_form_post" method="post" action="'.$ppptUrl.'" enctype="multipart/form-data">';
						$pppt_wform .= '<input type="hidden" value="yes" name="process_pppt_post" />';
						$pppt_wform .= '<ul id="pppt_widget_form_list">';
						
						//get services display setting:
						$pppt_show_dd_text = get_option('pppt_show_dd_text');
						$pppt_show_am_text = get_option('pppt_show_am_text');
						
						//show amount field
						if($pppt_show_am_text=="1"){
							$pppt_wform .= '<li class="ppptTitle">Amount:</li><li><input type="text" name="pppt_amount" id="pppt_amount" value="" /><input type="hidden" value="amount" name="pppt_ptype" /></li>';
						}
						
						//show services field
						if($pppt_show_dd_text==1){
						$pppt_paypal_currency = get_option('pppt_paypal_currency');
							if(!empty($sid)){
										if($pppt_show_am_text=="1"){
											$pppt_wform .= '<li class="ppptTitle">Pay Rent: '.$serviceData["pppt_property_title"].'</li>';
										} else { 
											$pppt_wform .= '<li class="ppptTitle">Pay Rent: '.$serviceData["pppt_property_title"].' - '.number_format($serviceData["pppt_services_price"],2)."&nbsp;".$pppt_paypal_currency.'</li>';
										}
										$pppt_wform .= '<input type="hidden" value="'.$serviceData["pppt_property_id"].'" name="pppt_propertyID" />';
										if($pppt_show_am_text!="1"){ $pppt_wform .= '<input type="hidden" value="property" name="pppt_ptype" />'; }
							
							}else{
								//lets get all services from database
								$query="SELECT * FROM ".$wpdb->prefix."pppt_services ORDER BY pppt_property_title";
								$result=mysql_query($query) or die(mysql_error());
								if(mysql_num_rows($result)>0){
									$pppt_wform .= '<li class="ppptTitle">Property:</li><li><select name="pppt_propertyID" id="pppt_propertyID">';
									$pppt_wform .= '<option value="">Please Select</option>';
									while($row=mysql_fetch_assoc($result)){
										if($pppt_show_am_text=="1"){
											$pppt_wform .= '<option value="'.$row["pppt_property_id"].'">'.stripslashes($row["pppt_property_title"]).'</option>';
										} else { 
											$pppt_wform .= '<option value="'.$row["pppt_property_id"].'">'.stripslashes($row["pppt_property_title"]).' - '.number_format($row["pppt_property_unit"],2).'</option>';
										}
									}
									$pppt_wform .= '</select></li>';
									if($pppt_show_am_text!="1"){ $pppt_wform .= '<input type="hidden" value="property" name="pppt_ptype" />'; }
								}
							}
						}
						//get comments display setting:
						$pppt_display_comment = get_option('pppt_show_comment_field');
						if($pppt_display_comment==1){
							$pppt_wform .= '<li class="ppptTitle">Comments:</li><li><textarea name="pppt_comments" id="pppt_comments"></textarea></li>';
						}
						$pppt_wform .= '<li class="ppptSubmit"><input type="submit" value="'.get_option('pppt_button_text').'"></li>'; //editable button text
						$pppt_wform .= '</ul>';
						$pppt_wform .= '<a href="http://www.iwantmywebsitenow.com">'.$copyright_img.'</a>';
						$pppt_wform .= '</form></div>';
					}
					
					if($pppt_submit_paypal){ $pppt_wform.=$paypal->submit_paypal_post_nonwidget();  }
					
					return $pppt_wform;
					
		}
		########################################## END OF SHORTCODE FUNCTION #####################################
		
		class pppt_widget extends WP_Widget {
			
			
				function pppt_widget(){
					/* Widget settings. */
					$widget_ops = array( 'classname' => 'pppt_widget', 'description' => __('SideBar Widget.', 'pppt_widget') );
					/* Widget control settings. */
					$control_ops = array( 'width' => 250, 'height' => 200, 'id_base' => 'pppt_widget' );
					/* Create the widget. */
					$this->WP_Widget( 'pppt_widget', __('SPM Tenant Payment', 'pppt_widget'), $widget_ops, $control_ops );
				}
			
			
				function widget( $args, $instance ) {
					$copyright_img = ""; //powered by Melissa Dufore.
					global $wp_url;
					global $wpdb;
					extract( $args );
					$pppt_msg = "";	
					$pppt_show_form = 1;
					$pppt_continue = false;	
					$pppt_submit_paypal = false;
					/* User-selected settings. */
					$title = apply_filters('widget_title', $instance['title'] );
					
					
					/* Paypal processing if form submitted. */
					$process_pppt = (!empty($_REQUEST["process_pppt"]))?addslashes(htmlentities(strip_tags($_REQUEST["process_pppt"]))):'';
					$pppt_comments = (!empty($_REQUEST["pppt_comments"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_comments"]))):'';
					$pppt_amount = (!empty($_REQUEST["pppt_amount"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_amount"]))):'';
					$pppt_serviceID = (!empty($_REQUEST["pppt_serviceID"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_serviceID"]))):'';
					$pppt_ptype = (!empty($_REQUEST["pppt_ptype"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_ptype"]))):'';
					
					
					
					
					/*
						THANK YOU / CANCEL / IPN PROCESSING PART ==================
					*/
					$pppt_action = (!empty($_REQUEST["pppt_action"]))?addslashes(htmlentities(strip_tags($_REQUEST["pppt_action"]))):'';
					if(!empty($pppt_action)){
						switch($pppt_action){
							case "cancel":
								$pppt_show_form = 1;
								$pppt_cancel_text = get_option('pppt_cancel_text');
								$pppt_msg = "<div class='pppt_error'>".(__($pppt_cancel_text))."</div>";
							break;
							case "success":
								$pppt_show_form = 0;
								$pppt_cancel_text = get_option('pppt_ty_text');
								$pppt_msg = "<div class='pppt_success'>".(__($pppt_ty_text))."</div>";
							break;
							case "ipn":
								require_once("paypal.ipn.php"); 
							break;
						}
					}
					/* 
						============================================================
					*/
					
					if(!empty($process_pppt) && $process_pppt=="yes"){
						/* 1. do all validations */
						if($pppt_ptype=="amount"){
							if(!empty($pppt_amount) && is_numeric($pppt_amount)){ $pppt_continue = true; } else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! Amount field cannot be empty and must contain numbers only."))."</div>";
							}
						} else if($pppt_ptype=="property"){
							if(!empty($pppt_propertyID) && is_numeric($pppt_propertyID)){ 
								$query="SELECT * FROM ".$wpdb->prefix."pppt_property WHERE pppt_property_id='".$pppt_propertyID."'";
								$result=mysql_query($query) or die(mysql_error());
								if(mysql_num_rows($result)>0){
									$r=mysql_fetch_assoc($result);
									$pppt_amount = number_format($r["pppt_property_unit"],2);
									$pppt_continue = true;
								} else {
									$pppt_msg = "<div class='pppt_error'>".(__("Error! Property doesn't exist!"))."</div>";
								}
							} else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! No property selected!"))."</div>";
							}
						} 
						
						/* 2. if everything ok, lets insert transaction record with status unPAID*/
						if($pppt_continue){
						//pppt_services_title	pppt_services_price	pppt_services_descr
							$query="INSERT INTO ".$wpdb->prefix."pppt_transactions (pppt_dateCreated, pppt_amount, pppt_comment,pppt_serviceID ) VALUES (NOW(), '".$pppt_amount."', '".$pppt_comments."', '".$pppt_serviceID."')";
							if(mysql_query($query)){							
							/* 3. show thank you message and redirect to paypal, we'll need to add hook here so that on body load form would get processed? or document.form.submit()? */				
							
								$orderID=mysql_insert_id();
								$blog_url = get_bloginfo('wpurl');
								
								$pppt_paypal_email = get_option('pppt_paypal_email');
								$pppt_paypal_currency = get_option('pppt_paypal_currency');
								
								require_once("paypal.class.php"); 
								$paypal = new paypal_class;
								
								$paypal->add_field('business', $pppt_paypal_email);
								$paypal->add_field('return', $blog_url.'?pppt_action=success');
								$paypal->add_field('cancel_return', $blog_url.'?pppt_action=cancel');
								$paypal->add_field('notify_url', $blog_url.'?pppt_action=ipn');
								$paypal->add_field('item_name_1', $_SERVER['HTTP_HOST']." payment");
								$paypal->add_field('amount_1', $pppt_amount);
								$paypal->add_field('item_number_1', "00001");
								$paypal->add_field('quantity_1', '1');
								$paypal->add_field('custom', $orderID);
								$paypal->add_field('upload', 1);
								$paypal->add_field('cmd', '_cart'); 
								$paypal->add_field('txn_type', 'cart'); 
								$paypal->add_field('num_cart_items', 1);
								$paypal->add_field('payment_gross', $pppt_amount);
								$paypal->add_field('currency_code', $pppt_paypal_currency);
								$pppt_show_form = 0;
								$pppt_submit_paypal = true;
							
								//$pppt_msg = "<div class='pppt_success'>".(_e("Thank you! You will be redirected to paypal.com for payment within 5 seconds."))."</div>";
							} else { 
								$pppt_msg = "<div class='pppt_error'>".(__("Error! Something went wrong, Please double check your input"))."</div>";
							}
						} 
						
					}
			
					/* Before widget (defined by themes). */
					echo $before_widget;
			
					/* Title of widget (before and after defined by themes). */
					if($pppt_show_form==1){
					if ( $title )
						echo $before_title . "Pay by <img src='".$wp_url."/wp-content/plugins/spmtenantpayment/resources/images/paypal_logo.png"."' width='70'>" . $after_title;
					}
			
					$pppt_wform = '<link rel="stylesheet" type="text/css" media="all" href="'.$wp_url.'/wp-content/plugins/spmtenantpayment/resources/css/pppt-widget.css" />';
					
					$pppt_wform .= $pppt_msg;
					
					if($pppt_show_form==1){
						$pppt_wform .= '<div class="pppt_widget_form">';
						if(stristr($_SERVER["REQUEST_URI"],"pppt_action=")){
							$ppptUrl = substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],"pppt_action="));
						} else { 
							$ppptUrl = "";
						}
						$pppt_wform .= '<form name="pppt_form" method="post" action="'.$ppptUrl.'" enctype="multipart/form-data">';
						$pppt_wform .= '<input type="hidden" value="yes" name="process_pppt" />';
						$pppt_wform .= '<ul id="pppt_widget_form_list">';
						
						//get services display setting:
						$pppt_show_dd_text = get_option('pppt_show_dd_text');
						$pppt_show_am_text = get_option('pppt_show_am_text');
						
						//show amount field
						if($pppt_show_am_text=="1"){
							$pppt_wform .= '<li class="ppptTitle">Amount:</li><li><input type="text" name="pppt_amount" id="pppt_amount" value="" /><input type="hidden" value="amount" name="pppt_ptype" /></li>';
						}
						
						//show services field
						if($pppt_show_dd_text==1){
							//lets get all services from database
							$query="SELECT * FROM ".$wpdb->prefix."pppt_property ORDER BY pppt_property_title";
							$result=mysql_query($query) or die(mysql_error());
							if(mysql_num_rows($result)>0){
								$pppt_wform .= '<li class="ppptTitle">Property:</li><li><select name="pppt_propertyID" id="pppt_propertyID">';
								$pppt_wform .= '<option value="">Please Select</option>';
								while($row=mysql_fetch_assoc($result)){
									if($pppt_show_am_text=="1"){
										$pppt_wform .= '<option value="'.$row["pppt_property_id"].'">'.stripslashes($row["pppt_property_title"]).'</option>';
									} else { 
										$pppt_wform .= '<option value="'.$row["pppt_property_id"].'">'.stripslashes($row["pppt_property_title"]).' - '.number_format($row["pppt_property_unit"],2).'</option>';
									}
								}
								$pppt_wform .= '</select>';
								if($pppt_show_am_text!="1"){ $pppt_wform .= '<input type="hidden" value="service" name="pppt_ptype" />'; }
							}
						}
						//get comments display setting:
						$pppt_display_comment = get_option('pppt_show_comment_field');
						if($pppt_display_comment==1){
							$pppt_wform .= '<li class="ppptTitle">Comments:</li><li><textarea name="pppt_comments" id="pppt_comments"></textarea></li>';
						}
						$pppt_wform .= '<li class="ppptSubmit"><input type="submit" value="'.get_option('pppt_button_text').'"></li>'; //editable button text
						$pppt_wform .= '</ul>';
						$pppt_wform .= '<a href="http://www.iwantmywebsitenow.com">'.$copyright_img.'</a>';
						$pppt_wform .= '</form></div>';
					}
					
					if($pppt_submit_paypal){ $paypal->submit_paypal_post();  }
					
					echo $pppt_wform;
					
					
					echo $after_widget;
				}
			
				function update( $new_instance, $old_instance ) {
					$instance = $old_instance;
			
					/* Strip tags (if needed) and update the widget settings. */
					$instance['title'] = strip_tags( $new_instance['title'] );
					//$instance['name'] = strip_tags( $new_instance['name'] );
					//$instance['sex'] = $new_instance['sex'];
					//$instance['show_sex'] = $new_instance['show_sex'];
			
					return $instance;
				}
			
				function form( $instance ) {

					/* Set up some default widget settings. */
					//$defaults = array( 'title' => 'Example', 'name' => 'John Doe', 'sex' => 'male', 'show_sex' => true );
					$defaults = array( 'title' => 'SPM Tenant Payment Widget');
					$instance = wp_parse_args( (array) $instance, $defaults ); ?>
					<p>
						<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
					</p>
					<?php 
				}
			
		}
?>