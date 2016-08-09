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
require_once('paypal.class.php');  // include the class file
$paypal = new paypal_class;             // initiate an instance of the class
$paypal->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
global $wpdb;
	/*
		THE PROCESS :)
		1) if we receive ipn call from paypal we need to get the custom parameter which we passed ( payment id) and update 'order' in the database.
		2) then we need to send notice to admin's email stating that new payment received
		3) we need to send confirmation to payee that his money were received.
	
	*/

   	  if ($paypal->validate_ipn()) {		  
		
				$orderID = $paypal->pp_data['custom'];
				$pppt_payer_email = $paypal->pp_data['payer_email'];
				$pppt_transaction_id = $paypal->pp_data['txn_id'];
				$pppt_payer_name = $paypal->pp_data['first_name']." ".$paypal->pp_data['last_name'];
				$t_total = $paypal->pp_data['mc_gross'];
				
				if(is_numeric($orderID) && !empty($orderID)){	
					
					$q="UPDATE ".$wpdb->prefix."pppt_transactions SET pppt_status='2', pppt_payer_email='".$pppt_payer_email."', pppt_transaction_id='".$pppt_transaction_id."', pppt_payer_name='".$pppt_payer_name."' WHERE pppt_id='".$orderID."'";
					mysql_query($q);
					
					$q="SELECT pppt_comment, pppt_serviceID FROM ".$wpdb->prefix."pppt_transactions WHERE pppt_id='".$orderID."'";
					$res=mysql_query($q);
					$row=mysql_fetch_assoc($res);
					
					//2. send notice to admin about successful payment
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=utf-8\n";
					$headers .= "From: '".$_SERVER['HTTP_HOST']." payment gateway' <noreply@".$_SERVER['HTTP_HOST']."> \n";
					$subject = "New paypal payment received";
					$message = "New paypal payment received from ".$pppt_payer_name."<br /><br />";
					$message .="<br />Total Amount: $".number_format($t_total,2);
					$message .="<br />Date & Time: ".date("d F Y, H:i");
					$message .="<br />Customer Comment: ".stripslashes($row["pppt_comment"]);
					if($row["pppt_serviceID"]!=0){ 
						$query2="SELECT pppt_services_title FROM ".$wpdb->prefix."pppt_services WHERE pppt_services_id='".$row["pppt_serviceID"]."'";
						$result2=mysql_query($query2);
						$row2=mysql_fetch_assoc($result2);
						$message .="<br />Selected Service: ".stripslashes($row2["pppt_services_title"]); 
					}
					$message .="<br /><br />Kind Regards, <br />".$_SERVER['HTTP_HOST'];
					$q="SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name='pppt_admin_email'";
					$result2=mysql_query($q);
					$r=mysql_fetch_assoc($result2);
					//mail($r["option_value"],$subject,$message,$headers);
					wp_mail($r["option_value"], $subject, $message, $headers);
					
					//3 send email to customer
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=utf-8\n";
					$headers .= "From: '".$_SERVER['HTTP_HOST']." payment gateway' <noreply@".$_SERVER['HTTP_HOST']."> \n";
					$subject = "Payment Confirmation";
					$message = "Dear ".$pppt_payer_name."<br /> <br />";
					$message .= "We have received your payment, thank you!";
					$message .="<br />Total Amount: $".number_format($t_total,2);
					$message .="<br />Date & Time: ".date("d F Y, H:i");
					if($row["pppt_serviceID"]!=0){ 
						$message .="<br />Selected Service: ".stripslashes($row2["pppt_services_title"]); 
					}
					$message .="<br /><br />Kind Regards, <br />".$_SERVER['HTTP_HOST'];
					//mail($pppt_payer_email,$subject,$message,$headers);
					wp_mail($pppt_payer_email, $subject, $message, $headers);
				}
		
      }
?>