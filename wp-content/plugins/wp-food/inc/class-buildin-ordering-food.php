<?php
class EXfood_Ordering_Food {
	public function __construct()
    {
		add_action( 'wp_ajax_exfood_user_checkout', array( &$this,'exfood_user_checkout') );
		add_action( 'wp_ajax_nopriv_exfood_user_checkout', array( &$this,'exfood_user_checkout') );
		add_action( 'exfood_checkout_success', array( &$this,'user_order_success'),10,3 );

    }
    function validate_data($data,$recaptcha){

    	$fname = exfood_get_option('exfood_ck_fname','exfood_advanced_options');
		$lname = exfood_get_option('exfood_ck_lname','exfood_advanced_options');
		$date = exfood_get_option('exfood_ck_date','exfood_advanced_options');
		$time = exfood_get_option('exfood_ck_time','exfood_advanced_options');
		$location = exfood_get_option('exfood_ck_location','exfood_advanced_options');
		$address = exfood_get_option('exfood_ck_address','exfood_advanced_options');
		$phone = exfood_get_option('exfood_ck_phone','exfood_advanced_options');
		$email = exfood_get_option('exfood_ck_email','exfood_advanced_options');
		$note = exfood_get_option('exfood_ck_note','exfood_advanced_options');
    	$html ='';
    	if( $fname!='no' && (!isset($data['_fname']) || $data['_fname']=='') ){
    		$html .='<p>'.esc_html__('Billing First name is a required field','wp-food').'</p>';
    	}
    	if($lname!='no' && (!isset($data['_lname']) || $data['_lname']=='') ){
    		$html .='<p>'.esc_html__('Billing Last name is a required field.','wp-food').'</p>';
    	}
    	if($date!='no' && (!isset($data['_date']) || $data['_date']=='') ){
    		$html .='<p>'.esc_html__('Billing Date is a required field.','wp-food').'</p>';
    	}
    	if($time!='no' && (!isset($data['_time']) || $data['_time']=='') ){
    		$html .='<p>'.esc_html__('Billing Time is a required field.','wp-food').'</p>';
    	}
    	if($location!='no' && (!isset($data['_location']) || $data['_location']=='') ){
    		$html .='<p>'.esc_html__('Billing Location is a required field.','wp-food').'</p>';
    	}
    	if($address!='no' && $data['_type'] !='order-pick' && (!isset($data['_address']) || $data['_address']=='') ){
    		$html .='<p>'.esc_html__('Billing Address is a required field.','wp-food').'</p>';
    	}
    	if($phone!='no' && (!isset($data['_phone']) || $data['_phone']=='') ){
    		$html .='<p>'.esc_html__('Billing Phone is a required field.','wp-food').'</p>';
    	}
    	if($email!='no' && (!isset($data['_email']) || $data['_email']=='') ){
    		$html .='<p>'.esc_html__('Billing Email is a required field.','wp-food').'</p>';
    	}else if( $email!='no' && !exfood_checkemail($data['_email'])){
    		$html .='<p>'.esc_html__('Invalid email address.','wp-food').'</p>';
    	}
    	if($note!='no' && (!isset($data['_note']) || $data['_note']=='') ){
    		$html .='<p>'.esc_html__('Billing Note is a required field.','wp-food').'</p>';
    	}
    	$captcha = exfood_get_option('exfood_ck_captcha','exfood_advanced_options');
		$captcha_key = exfood_get_option('exfood_captcha_secret','exfood_advanced_options');
    	if($captcha!='no' && $captcha_key!=''){
	    	$post = http_build_query(
			 	array (
			 		'response' => $recaptcha,
			 		'secret' => $captcha_key,
			 		'remoteip' => $_SERVER['REMOTE_ADDR']
			 	)
			);
			$opts = array('http' => 
				array (
					'method' => 'POST',
					'header' => 'application/x-www-form-urlencoded',
					'content' => $post
				)
			);
			$context = stream_context_create($opts);
			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
				$serverResponse = @file_get_contents('http://www.google.com/recaptcha/api/siteverify', false, $context);
			}else{
				$serverResponse = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
			}
			if (!$serverResponse) {
				$html .='<p>'.esc_html__('Failed to validate Recaptcha.','wp-food').'</p>';
			}
			$result = json_decode($serverResponse);
			if (!$result -> success) {
				$html .='<p>'.esc_html__('Invalid Recaptcha.','wp-food').'</p>';
			}
		}
    	$html = apply_filters( 'exfood_validate_checkout_field', $html, $data );
    	return $html;
    }
    function exfood_user_checkout(){
    	$data_order = array();
		parse_str($_POST['data'], $data_order);
		$recaptcha = $_POST['recaptcha'];
		$validate_data = $this->validate_data($data_order,$recaptcha);
		if($validate_data!=''){
			$notice = '<div class="exfood-validate-warning">'.$validate_data.'</div>';
			$output =  array('status'=>2,'html_content'=> $notice);
			echo str_replace('\/', '/', json_encode($output));
			exit;
		}
		session_start();
		if(isset ($_SESSION['ex_userfood']) && !empty($_SESSION['ex_userfood'])){
			$status = 1;
			$data_food = $_SESSION['ex_userfood'];
			$attr = array(
				'post_title'    => $data_order['_fname'].' '.$data_order['_lname'].' - '.$data_order['_email'],
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_type'      => 'exfood_order',
			);
			if($new_ID = wp_insert_post( $attr, false )){
				update_post_meta( $new_ID, 'exorder_fname', $data_order['_fname']);
				update_post_meta( $new_ID, 'exorder_lname', $data_order['_lname']);
				update_post_meta( $new_ID, 'exorder_phone', $data_order['_phone']);
				update_post_meta( $new_ID, 'exorder_email', $data_order['_email']);
				update_post_meta( $new_ID, 'exorder_type', $data_order['_type']);
				update_post_meta( $new_ID, 'exorder_store', $data_order['_store']);
				update_post_meta( $new_ID, 'exorder_date', $data_order['_date']);
				update_post_meta( $new_ID, 'exorder_time', $data_order['_time']);
				update_post_meta( $new_ID, 'exorder_location', $data_order['_location']);
				update_post_meta( $new_ID, 'exorder_address', $data_order['_address']);
				update_post_meta( $new_ID, 'exorder_note', $data_order['_note']);
				update_post_meta( $new_ID, 'exorder_food', $data_food);
				if(isset ($_SESSION['ex_userid']) && $_SESSION['ex_userid']!=0){
					update_post_meta( $new_ID, 'exorder_userid', $_SESSION['ex_userid']);
				}
				//$html = '<div class="exfood-warning">'.esc_html__('your order has been placed, we will contact with you very soon','wp-food').'</div>';
				ob_start();
				do_action( 'exfood_checkout_success', $data_order, $data_food, $new_ID );
				$html = ob_get_contents();
				ob_end_clean();
				$_SESSION['ex_userfood'] = '';
			}else{
				$status = 0;
				$html = '<div class="exfood-warning">'.esc_html__('Oops, Something went wrong, please try again!','wp-food').'</div>';
			}
		}else{
			$status = 0;
			$html = '<div class="exfood-warning">'.esc_html__('Oops, Something went wrong, There is no item in your Cart!','wp-food').'</div>';
		}
		$output =  array('status'=>$status,'html_content'=> $html);
		echo str_replace('\/', '/', json_encode($output));
		exit;
    }
    function user_order_success($data_order, $data_food, $new_ID){
    	global $userfood, $billing, $id;
    	$userfood = $data_food; $id = $new_ID; $billing = $data_order;
    	exfood_template_plugin('thankyou',false);
    }
}
$EXfood_Ordering_Food = new EXfood_Ordering_Food();