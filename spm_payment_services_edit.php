<?php
#******************************************************************************
#                      WP Sterling Property Management Payment Terminal
#
#	Author: Melissa Dufore
#	http://www.iwantmywebsitenow.com
#	Version: 1.0
#	Released: March 6 2011
#
#******************************************************************************
	global $wpdb;
	
	
	//$pppt_propertyID = $_GET['pppt_propertyID'];
	
	$ssdurl = $_SERVER['REQUEST_URI'];
	if(stristr($ssdurl, "&pppt_propertyID=")){
		$myurltemp = explode("&pppt_propertyID=",$ssdurl);
		$pppt_serviceID = $myurltemp[1];
	}

	
	if($_POST['pppt_submit_service'] == 'yes' && !empty($pppt_serviceID) && is_numeric($pppt_serviceID)) {
		//Form data sent
		$pt_property = $_POST['pppt_property_title'];
		$pt_unit = $_POST['pppt_property_unit'];
		$pt_amount = $_POST['pppt_property_paymentamount'];
		
		if(is_numeric($pt_unit) && !empty($pt_amount)){
			$query="UPDATE ".$wpdb->prefix."pppt_property SET pppt_property_title='".addslashes(strip_tags($pt_property))."', pppt_property_unit='".addslashes(strip_tags($pt_unit))."', pppt_property_paymentamount='".addslashes(strip_tags($pt_amount))."' WHERE pppt_property_id='".$pppt_propertyID."'";
			mysql_query($query) or die(mysql_error());
			?><div class="updated"><p><strong><?php _e('Property updated! <a href="admin.php?page=pppt_admin_services">Click here</a> to go back to all properties' ); ?></strong></p></div><?php
		} else { 
		?><div class="updated"><p><strong><?php _e('Property not updated! Please check your input. Unit must contain numbers only and Property cannot be blank.' ); ?></strong></p></div><?php
		}

 	} 
	
	
	$query2="SELECT * FROM ".$wpdb->prefix."pppt_property WHERE pppt_property_id='".$pppt_propertyID."'";
	$result2=mysql_query($query2) or die(mysql_error()); 	
	$row2=mysql_fetch_assoc($result2);
	$pppt_property_title = $row2["pppt_property_title"];
	$pppt_property_unit = $row2["pppt_property_unit"];
	$pppt_property_paymentamount = $row2["pppt_property_paymentamount"];
?>

	<?php $wp_url = get_bloginfo('siteurl');  ?>
    <link rel="stylesheet" media="screen" href="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/css/admin-style.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/js/functions.js"></script>
		<div class="wrap">
			<?php    echo "<h2>" . __('SPM Tenant Payment','') . "</h2>"; ?>

                <?php    echo "<h4>" . __( 'Edit Properties', '' ) . "</h4>"; ?>
                <form name="pppt_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="pppt_submit_service" value="yes">
                    <input type="hidden" name="pppt_propertyID" value="<?php echo $pppt_propertyID?>">
                    
                    <p><?php _e("Property Name: " ); ?><input type="text" name="pppt_property_title" id="pppt_property_title" value="<?php echo $pppt_property_title?>" size="40"><?php _e(" (This is what customers will see in the property dropdown to select from when they decide to pay)" ); ?></p>
                    <p><?php _e("Unit#: " ); ?><input type="text" name="pppt_property_unit" id="pppt_property_unit" onkeyup="noAlpha(this)"  value="<?php echo $pppt_property_unit?>" size="40"><?php _e(" (Numbers only. ex. 108)" ); ?></p>
                    
    <p><?php _e("Payment Amount: " ); ?><input type="text" name="pppt_property_paymentamount" id="pppt_property_paymentamount" onkeyup="noAlpha(this)"  value="<?php echo $pppt_property_paymentamount?>" size="40"><?php _e(" (Numbers only. ex. 650.00)" ); ?></p>                
					</div> 
    
                    <p class="submit">
                    <input type="submit" name="Submit" value="<?php _e('Update Property', '' ) ?>" />
                    </p>
                    
                </form>
                
                
		</div>
        <div class="pt_footer">
        	<a href="http://www.iwantmywebsitenow.com" target="_blank"><img src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayments/resources/images/logo.png" /></a>
        </div>