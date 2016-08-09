<?php
#******************************************************************************
#                      WP Sterling Property Management Payment Terminal
#	Author: Melissa Dufore
#	http://www.iwantmywebsitenow.com
#	Version: 1.0
#	Released: March 6 2011
#
#******************************************************************************
	global $wpdb;
	
	if($_POST['pppt_submit_service'] == 'yes') {
		//Form data sent
		$pt_title = $_POST['pppt_property_title'];
		$pt_descr = $_POST['pppt_property_unit'];
		$pt_price = $_POST['pppt_property_paymentamount'];
		
		if(is_numeric($pt_price) && !empty($pt_title)){
			$query="INSERT INTO ".$wpdb->prefix."pppt_property (pppt_property_title, pppt_property_unit, pppt_property_paymentamount) VALUES ('".addslashes(strip_tags($pt_title))."','".addslashes(strip_tags($pt_descr))."','".addslashes(strip_tags($pt_price))."')";
			mysql_query($query) or die(mysql_error());
			?><div class="updated"><p><strong><?php _e('Property added!' ); ?></strong></p></div><?php
		} else { 
		?><div class="updated"><p><strong><?php _e('Property not added! Please check your input. Amount must contain numbers only and name cannot be blank.' ); ?></strong></p></div><?php
		}

 } $pppt_services_descr = "";

	if(!empty($_POST['toDelete']) && count($_POST["toDelete"])>0) {
		$deleted=0;
		for($i=0; $i<count($_POST["toDelete"]); $i++){
			$query="DELETE FROM ".$wpdb->prefix."pppt_property WHERE pppt_property_id='".$_POST["toDelete"][$i]."'";
			mysql_query($query) or die(mysql_error());
			$deleted++;
		}
		
		if($deleted>0){
		?> <div class="updated"><p><strong><?php _e('Selected service(s) deleted!' ); ?></strong></p></div><?php
		}
	}

?>

	<?php $wp_url = get_bloginfo('siteurl');  ?>
    <link rel="stylesheet" media="screen" href="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/css/admin-style.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/js/functions.js"></script>
		<div class="wrap">
			<?php    echo "<h2>" . __('SPM Tenant Payments','') . "</h2>"; ?>

				<p><?php _e("List each property." ); ?></p>
                
                
                <?php    echo "<h4>" . __( 'Add New Property', '' ) . "</h4>"; ?>
                <form name="pppt_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="pppt_submit_service" value="yes">
                    
                    <p><?php _e("Property Name: " ); ?><input type="text" name="pppt_property_title" id="pppt_property_unit" value="" size="40"><?php _e(" (This is what customers will see in the property dropdown to select from when they decide to pay)" ); ?></p>
                    <p><?php _e("Unit Number: " ); ?><input type="text" name="pppt_property_unit" id="pppt_property_unit" onkeyup="noAlpha(this)"  value="" size="40"><?php _e(" (Numbers only. ex. 10.99)" ); ?></p>
                    <p><?php _e("Rent Payment Amount: " ); ?><input type="text" name="pppt_property_paymentamount" id="pppt_property_paymentamount" onkeyup="noAlpha(this)"  value="" size="40"><?php _e(" (Numbers only. ex. 10.99)" ); ?></p>
                    					</div> 
    
                    <p class="submit">
                    <input type="submit" name="Submit" value="<?php _e('Add Property', '' ) ?>" />
                    </p>
                    
                </form>




                
                <?php    echo "<h4>" . __('List of Properties','') . "</h4>"; ?>
                <form name="pppt_form_del" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <div class="services_table">
                    <div class="table_wrapper">
                        <div class="table_header">
                            <ul>
                            	<li class="deleter">&nbsp;</li>
								<li  style="width: 10%;"><?php _e('Property ID')?></li>
                                <li><?php _e('Property Name')?></li>
                                <li><?php _e('Unit Number')?></li>
                                <li class="lastColumn"><?php _e('Rental Amount')?></li>
                            </ul>
                        </div><br clear="all" />
                        <?php 
						//lets get all properties from database
						$query="SELECT * FROM ".$wpdb->prefix."pppt_property ORDER BY pppt_property_title";
						$result=mysql_query($query) or die(mysql_error());
						if(mysql_num_rows($result)>0){
							$del=true;
							$rClass = "row_b";
							while($row=mysql_fetch_assoc($result)){
						?>
                        <div class="<?php echo $rClass=($rClass=="row_b"?"row_a":"row_b")?>">
                             <ul>
                             	<li class="deleter">&nbsp;&nbsp;<input type="checkbox" value="<?php echo $row["pppt_property_id"]?>" name="toDelete[]" /></li>
                                <li  style="width: 10%;"><?php echo $row["pppt_property_id"]?></li>
								<li><?php echo stripslashes(strip_tags($row["pppt_property_title"]))?> <a href="admin.php?page=pppt_admin_services_edit&amp;pppt_serviceID=<?php echo $row["pppt_property_id"]?>">edit</a></li>
                                <li><?php echo number_format(stripslashes(strip_tags($row["pppt_property_unit"])),2)?></li>
                                <li class="lastColumn"><?php echo stripslashes(strip_tags($row["pppt_property_paymentamount"]))?></li>
                            </ul>
                        </div> <br clear="all" />
                        <?php }  } else { $del=false; ?>
                        <div class="row_msg">
                            <ul>
                                <li>0 service records found in the database</li>
                            </ul>
                        </div><br clear="all" />
                        <?php } ?>
                    </div>
                </div>
                <?php if($del){?>
                <p class="submit">
                	<input type="submit" name="Submit" value="<?php _e('Delete Selected Property', '' ) ?>" />
                </p>
                <? } ?>
                </form>
                
                
		</div>
        <div class="pt_footer">
        	<a href="http://www.iwantmywebsitenow.com" target="_blank"><img src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/images/logo.png" /></a>
        </div>