<?php
class EXfood_Ordering_Food_Email {
	public function __construct()
    {
		add_action( 'exfood_checkout_success', array( &$this,'send_email_to_user'),9,3 );
		add_action( 'exfood_checkout_success', array( &$this,'send_email_to_admin'),9,3 );
		add_action( 'save_post', array($this,'admin_create_order'),1 );
    }
    function send_email_to_user( $data_order, $data_food, $new_ID,$status = false ) {
    	global $userfood,$id,$hd_title,$details,$billing;
    	$userfood = $data_food; $id = $new_ID; $billing = $data_order;
    	
    	if(isset($status) && $status =='process'){
    		$subject = sprintf ( esc_html__( 'Your order receipt from %s', 'exthemes' ), get_the_date( $d = '', $id ) );
    		$hd_title = esc_html__( 'Thank you for your order', 'exthemes' );
    		$details = esc_html__( 'Your order has been received and is now being processed. Your order details are shown below for your reference:', 'exthemes' );
    	}else if(isset($status) && $status =='complete'){
    		$subject = sprintf ( esc_html__( 'Your order receipt from %s is complete', 'exthemes' ), get_the_date( $d = '', $id ) );
    		$hd_title = esc_html__( 'Your order is complete', 'exthemes' );
    		$details = sprintf (esc_html__( 'Hi there. Your recent order on %s has been completed. Your order details are shown below for your reference:', 'exthemes' ), get_bloginfo( 'name', 'display' ) );
    	}else if(isset($status) && $status =='cancel'){
	    	$subject = esc_html__('Cancelled order','exthemes');
			$hd_title = esc_html__( 'Cancelled order', 'exthemes' );
			$details = esc_html__( 'Your order has been cancelled. The order was as follows:', 'exthemes' );
    	}else{
			$subject = sprintf ( esc_html__( 'Your order receipt from %s', 'exthemes' ), get_the_date( $d = '', $id ) );
			$hd_title = esc_html__( 'Thank you for your order', 'exthemes' );
			$details = esc_html__( 'Your order has been received. Your order details are shown below for your reference:', 'exthemes' );
		}
		$subject = apply_filters( 'exfood_subject_email_customer', $subject );
		ob_start();
		exfood_template_plugin('email-template',false);
		$email_content = ob_get_contents();
		ob_end_clean();
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $data_order['_email'], $subject, $email_content,$headers );
		
	}
	function send_email_to_admin( $data_order, $data_food, $new_ID ) {
    	global $userfood,$id,$billing;
    	$userfood = $data_food; $id = $new_ID; $billing = $data_order;
		$subject = esc_html__('New customer order - ','exthemes').get_the_date( $d = '', $id );
		$subject = apply_filters( 'exfood_subject_email_admin', $subject );
		ob_start();
		exfood_template_plugin('email-admin-template',false);
		$email_content = ob_get_contents();
		ob_end_clean();
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$mail = exfood_get_option('exfood_email_Recipient','exfood_advanced_options');
		$mail = $mail!='' ? $mail : get_option( 'admin_email' );
		wp_mail( $mail, $subject, $email_content,$headers );
	}
	function admin_create_order($post_id){
		if(get_post_meta( $post_id, 'exfood_price', true )==1 || 'exfood_order' != get_post_type()){
			return;
		}
		$data_food = get_post_meta( $post_id, 'exorder_food', true);
		$data_order= array();
		$data_order['_fname'] =  isset($_POST['exorder_fname']) ? $_POST['exorder_fname'] : '';
		$data_order['_lname'] =  isset($_POST['exorder_lname']) ? $_POST['exorder_lname'] : '';
		$data_order['_phone'] =  isset($_POST['exorder_phone']) ? $_POST['exorder_phone'] : '';
		$data_order['_location'] =  isset($_POST['exorder_location']) ? $_POST['exorder_location'] : '';
		$data_order['_address'] =  isset($_POST['exorder_address']) ? $_POST['exorder_address'] : '';
		$data_order['_email'] =  isset($_POST['exorder_email']) ? $_POST['exorder_email'] : '';
		$data_order['_note'] =  isset($_POST['exorder_note']) ? $_POST['exorder_note'] : '';

		if(isset($_POST['exfood_order_status']) && $_POST['exfood_order_status']=='on-hold'){
			if(get_post_meta( $post_id, '_new_email', true)!='1'){
				$this->send_email_to_user($data_order, $data_food, $post_id);
				update_post_meta( $post_id, '_new_email', 1 );
			}
		}else if(isset($_POST['exfood_order_status']) && $_POST['exfood_order_status']=='process'){
			if(get_post_meta( $post_id, '_process_email', true)!='1'){
				$this->send_email_to_user($data_order, $data_food, $post_id,'process');
				update_post_meta( $post_id, '_process_email', 1 );
			}
		}else if(isset($_POST['exfood_order_status']) && $_POST['exfood_order_status']=='complete'){
			if(get_post_meta( $post_id, '_complete_email', true)!='1'){
				$this->send_email_to_user($data_order, $data_food, $post_id,'complete');
				update_post_meta( $post_id, '_complete_email', 1 );
			}
		}else if(isset($_POST['exfood_order_status']) && $_POST['exfood_order_status']=='cancel'){
			if(get_post_meta( $post_id, '_cancel_email', true)!='1'){
				$this->send_email_to_user($data_order, $data_food, $post_id,'cancel');
				update_post_meta( $post_id, '_cancel_email', 1 );
			}
		}
	}
}
$EXfood_Ordering_Food_Email = new EXfood_Ordering_Food_Email();