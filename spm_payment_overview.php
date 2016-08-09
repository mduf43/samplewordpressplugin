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
$wp_url = get_bloginfo('siteurl'); 
global $wpdb;
?>
        <link rel="stylesheet" media="screen" href="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/css/admin-style.css" />
		<div class="wrap">
			<?php    echo "<h2>" . __('Sterling Property Management Tenant Payments','') . "</h2>"; ?>
				
                
		
				<p><?php _e("This plugin was created to allow tenants to pay rental fees online. This plugin is still being tested, any errors should be reported to the web administrator. " ); ?></p>
                
		
				<?php    echo "<h4>" . __('Last 15 Transactions','') . "</h4>"; ?>
                <div class="transactions_overview_table">
                    <div class="table_wrapper">
                        <div class="table_header">
                            <ul>
                            	<li class="deleter">&nbsp;</li>
                                <li><?php _e('Date')?></li>
                                <li><?php _e('Property')?></li>
                                <li><?php _e('Unit#')?></li>
                                <li><?php _e('Amount')?></li>
                                <li class="lastTransColumn"><?php _e('Transaction ID')?></li>
                            </ul>
                        </div><br clear="all" />
                        <?php 
						//lets get all properties from database
						$query="SELECT * FROM ".$wpdb->prefix."pppt_transactions  WHERE 1 AND pppt_status='2' $sqlfilter $sqlorder";
						//echo $query;
						$result=mysql_query($query) or die(mysql_error());
						if(mysql_num_rows($result)>0){
							$del=true;
							$rClass = "row_b";
							while($row=mysql_fetch_assoc($result)){
						?>
                        <div class="<?php echo $rClass=($rClass=="row_b"?"row_a":"row_b")?>">
                             <ul>
                             	<li class="deleter">&nbsp;&nbsp;<input type="checkbox" value="<?php echo $row["pppt_id"]?>" name="toDelete[]" /></li>
                                <li><?php echo date("d M Y, h:i a", strtotime($row["pppt_dateCreated"]))?></li>
                                <li><?php echo stripslashes(strip_tags($row["pppt_property_title"]))?></li>
                                <li><?php echo stripslashes(strip_tags($row["pppt_property_unit"]))?></li>
                                                              
								<li><?php echo number_format(stripslashes(strip_tags($row["pppt_property_paymentamount"])),2); $ppptTotal+=$row["pppt_property_paymentamount"];?></li>
                                <?php
																	?>
                                <li><?php if($row["pppt_propertyID"]!=0){ echo stripslashes(strip_tags($row2["pppt_property_title"])); } else { echo "N/A"; }?></li>
                            	<li class="lastTransColumn"><?php echo stripslashes(strip_tags($row["pppt_transaction_id"]))?></li>
                            </ul>
                        </div> <br clear="all" />
                        
                        <?php }  } else { $del=false; ?>
                        <div class="row_msg">
                            <ul>
                                <li>0 transactions found</li>
                            </ul>
                        </div><br clear="all" />
                        <?php } ?>
                    </div>
                 </div>
                 <a href="admin.php?page=pppt_admin_transactions">View All Transactions</a>
			
		</div>
        <div class="pt_footer">
        	<a href="http://www.iwantmywebsitenow.com" target="_blank"><img src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/images/logo.png" /></a>
        </div>