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
$sqlfilter = "";
$sqlorder = " ORDER BY pppt_dateCreated DESC ";
$ppptTotal = 0;
	
	if(!empty($_POST['toDelete']) && count($_POST["toDelete"])>0) {
		$deleted=0;
		for($i=0; $i<count($_POST["toDelete"]); $i++){
			$query="DELETE FROM ".$wpdb->prefix."pppt_transactions WHERE pppt_id='".$_POST["toDelete"][$i]."'";
			mysql_query($query) or die(mysql_error());
			$deleted++;
		}
		
		if($deleted>0){
		?> <div class="deleted"><p><strong><?php _e('Selected transaction(s) deleted!' ); ?></strong></p></div><?php
		}
	}
	
	
	if($_POST['pppt_filter_submit'] == 'yes') {
		if(!empty($_POST["pppt_date1"]) || !empty($_POST["pppt_date2"])){
			$tmp1 = explode("/",$_POST["pppt_date1"]);
			$tmp2 = explode("/",$_POST["pppt_date2"]);
			$ppptd1 = (!empty($_POST["pppt_date1"])?$tmp1[2]."-".$tmp1[0]."-".$tmp1[1]:date("Y-m-d"))." 00:00:00";
			$ppptd2 = (!empty($_POST["pppt_date2"])?$tmp2[2]."-".$tmp2[0]."-".$tmp2[1]:date("Y-m-d"))." 23:59:59";
			$sqlfilter  .= " AND (pppt_dateCreated BETWEEN '".$ppptd1."' AND '".$ppptd2."') ";
		}
		
		if(!empty($_POST["pppt_sortby"]) && !empty($_POST["pppt_dir"])){
			$sqlorder  = " ORDER BY ".$_POST["pppt_sortby"]." ".$_POST["pppt_dir"];
		}
		
		if(!empty($_POST["pppt_keyword"])){
			$sqlfilter  .= " AND ( pppt_payer_name LIKE '%".$_POST["pppt_keyword"]."%' OR pppt_payer_email LIKE '%".$_POST["pppt_keyword"]."%' OR pppt_transaction_id LIKE '%".$_POST["pppt_keyword"]."%' )";
		}
	}
	
	
?>
	<?php $wp_url = get_bloginfo('siteurl');  ?>
    <link rel="stylesheet" media="screen" href="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/css/admin-style.css" />
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/js/functions.js"></script>
    <link rel="stylesheet" media="screen" href="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/css/ui-lightness/jquery-ui-1.7.3.custom.css" />
    <script type="text/javascript" src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/js/ui.datepicker.js"></script>
    <script>
	$(function() {
		$( "#pppt_date1" ).datepicker();
		$( "#pppt_date2" ).datepicker();
	});
	</script>

	
		<div class="wrap">
			<?php    echo "<h2>" . __('SPM Tenant Payments','') . "</h2>"; ?>
				<p><?php _e("Please use filters and sorting functions of this page to view all or specific transactions. " ); ?></p>
                
                
                                
                
             <form name="pppt_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                 <input type="hidden" name="pppt_filter_submit" value="yes">   
                <?php    echo "<h4>" . __('Filter by Date','') . "</h4>"; ?>
				<p>From: <input type="text" id="pppt_date1" name="pppt_date1" value="<?php echo isset($_POST["pppt_date1"])?$_POST["pppt_date1"]:""?>"> &nbsp; &nbsp; To: <input type="text" id="pppt_date2" name="pppt_date2" value="<?php echo isset($_POST["pppt_date2"])?$_POST["pppt_date2"]:""?>"> &nbsp; &nbsp;<input type="submit" name="Submit" value="<?php _e('Apply Filter', '' ) ?>" /></p>
            
             
                 <?php    echo "<h4>" . __('Sorting','') . "</h4>"; ?>
                 <p>Sort By: <select name="pppt_sortby" id="pppt_sortby">
                 	<option value="">Please Select</option>
                 	<option value="pppt_amount" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_amount")?"selected":""?>>Amount</option> 	
                 	<option value="pppt_payer_email" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_payer_email")?"selected":""?>>Email</option> 
                 	<option value="pppt_payer_name" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_payer_name")?"selected":""?>>Name</option>
                 	<option value="pppt_serviceID" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_serviceID")?"selected":""?>>Service</option>
                 	<option value="pppt_dateCreated" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_dateCreated")?"selected":""?>>Transaction Date</option>
                 	<option value="pppt_transaction_id" <?php echo (isset($_POST["pppt_sortby"]) && $_POST["pppt_sortby"]=="pppt_transaction_id")?"selected":""?>>Transaction ID</option>
                 </select> 
                 Direction:  <select name="pppt_dir" id="pppt_dir"><option value="ASC" <?php isset($_POST["pppt_dir"]) && $_POST["pppt_dir"]=="ASC"?"selected":""?>>Ascending</option><option value="DESC"  <?php isset($_POST["pppt_dir"]) && $_POST["pppt_dir"]=="DESC"?"selected":""?>>Descending</option></select> &nbsp; &nbsp;<input type="submit" name="Submit" value="<?php _e('Apply Sorting', '' ) ?>" /></p>


                <?php    echo "<h4>" . __('Search Transactions','') . "</h4>"; ?>
                <p>Keyword: <input type="text" id="pppt_keyword" name="pppt_keyword" value="<?php echo isset($_POST["pppt_keyword"])?$_POST["pppt_keyword"]:""?>"> &nbsp; &nbsp;<input type="submit" name="Submit" value="<?php _e('Search', '' ) ?>" /></p>
                <p> Note: search will be performed by name, email, transaction ID</p>

                
             </form>
                
                
                
                
                
                
                <?php    echo "<h4>" . __('All Transactions','') . "</h4>"; ?>
                <form name="pppt_form_del" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <div class="transactions_table">
                    <div class="table_wrapper">
                        <div class="table_header">
                            <ul>
                            	<li class="deleter">&nbsp;</li>
                                <li><?php _e('Date')?></li>
                                <li><?php _e('Name')?></li>
                                <li><?php _e('Email')?></li>
                                <li><?php _e('Amount')?></li>
                                <li><?php _e('Service')?></li>
                                <li class="lastTransColumn"><?php _e('Transaction ID')?></li>
                            </ul>
                        </div><br clear="all" />
                        <?php 
						//lets get all services from database
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
                                <li><?php echo stripslashes(strip_tags($row["pppt_payer_name"]))?></li>
                                <li><?php echo stripslashes(strip_tags($row["pppt_payer_email"]))?></li>
                                <li><?php echo number_format(stripslashes(strip_tags($row["pppt_amount"])),2); $ppptTotal+=$row["pppt_amount"];?></li>
                                <?php 
									$query2="SELECT pppt_services_title FROM ".$wpdb->prefix."pppt_services WHERE pppt_services_id='".$row["pppt_serviceID"]."'";
									$result2=mysql_query($query2);
									$row2=mysql_fetch_assoc($result2);
								?>
                                <li><?php if($row["pppt_serviceID"]!=0){ echo stripslashes(strip_tags($row2["pppt_services_title"])); } else { echo "N/A"; }?></li>
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
                    <?php if($ppptTotal>0){?><strong>Total Amount:</strong> <?php echo number_format($ppptTotal,2)?><br /><?php } ?>
                </div>
                <?php if($del){?>
                <p class="submit">
                	<input type="submit" name="Submit" value="<?php _e('Delete Selected Transactions', '' ) ?>" />
                </p>
                <? } ?>
                </form>
                
                
                
                
		</div>
        <div class="pt_footer">
        	<a href="http://www.iwantmywebsitenow.com" target="_blank"><img src="<?php echo $wp_url?>/wp-content/plugins/spmtenantpayment/resources/images/logo.png" /></a>
        </div>
     	