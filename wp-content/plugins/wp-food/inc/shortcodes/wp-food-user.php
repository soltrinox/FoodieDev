<?php
function exfood_shortcode_user( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	ob_start();
	$login_url = isset($atts['login_url']) ? $atts['login_url'] : '';
	$current_user = wp_get_current_user();
    if ( ! $current_user->exists() ) {
    	if($login_url!=''){
    		echo '<a href="'.esc_url($login_url).'">'.esc_html__( 'Please login to view this page', 'wp-food').'</a>';
    	}else{
		    wp_login_form();
		    wp_register('', '');
		}
	    return;
    }
    $id_user = $current_user->ID;
    global $wp;
    $curent_url = home_url( $wp->request );
	if(isset($_POST['save_address'])){
		$fname = sanitize_text_field(isset($_POST['exorder_fname']) ? $_POST['exorder_fname'] : '');
		$lname = sanitize_text_field(isset($_POST['exorder_lname']) ? $_POST['exorder_lname'] : '');
		$phones = sanitize_text_field(isset($_POST['exorder_phone']) ? $_POST['exorder_phone'] : '');
		$emails = sanitize_email(isset($_POST['exorder_email']) ? $_POST['exorder_email'] : '');
		$addresses = sanitize_text_field(isset($_POST['exorder_address']) ? $_POST['exorder_address'] : '');
		update_user_meta( $current_user->ID, 'exorder_fname', $fname );
		update_user_meta( $current_user->ID, 'exorder_lname', $lname );
		update_user_meta( $current_user->ID, 'exorder_phone', $phones );
		update_user_meta( $current_user->ID, 'exorder_email', $emails );
		update_user_meta( $current_user->ID, 'exorder_address', $addresses );
	}
    ?>
	<div class="exfd-user-main">
		<div class="exfd-user-left">
			<ul class="exfd-dashboard-list">
            	<li <?php if (!isset($_GET['view']) || (isset($_GET['view']) && $_GET['view'] == 'dashboad')) {
            		echo 'class="exfd-current"';
            	}?>><a href="<?php echo esc_url(add_query_arg( array('view' => 'dashboad'), $curent_url )); ?>"><?php echo esc_html__('Dashboard','wp-food');?></a></li>
            	<li <?php if (isset($_GET['view']) && $_GET['view'] == 'orders') {
            		echo 'class="exfd-current"';
            	}?>><a href="<?php echo esc_url(add_query_arg( array('view' => 'orders'), $curent_url )); ?>"><?php echo esc_html__('Orders','wp-food');?></a></li>
            	<li <?php if (isset($_GET['view']) && $_GET['view'] == 'address') {
            		echo 'class="exfd-current"';
            	}?>><a href="<?php echo esc_url(add_query_arg( array('view' => 'address'), $curent_url )); ?>"><?php echo esc_html__('Address','wp-food');?></a></li>
            	<li <?php if (isset($_GET['view']) && $_GET['view'] == 'logout') {
            		echo 'class="exfd-current"';
            	}?>><a href="<?php echo wp_logout_url(); ?>" ><?php echo esc_html__('Logout','wp-food');?></a></li>
            </ul>
		</div>
		<div class="exfd-user-content">
			<?php
				if (isset($_GET['idorder'])) {
					$idorder = sanitize_text_field($_GET['idorder']);					
					echo '<h3 class="exfd-order-header">'.esc_html__('Order details','wp-food').'</h3>';
					$userfood = get_post_meta( $idorder, 'exorder_food', true);
					$total_price = 0;
					?>
					<div class="exfood-thankyou">
						<div class="ex-order-info">
							<ul>
							  <li>
							    <span><?php esc_html_e('Order Number','wp-food');?></span>
							    <strong>#<?php echo esc_html($idorder);?></strong>
							  </li>
							  <li>
							    <span><?php esc_html_e('Order Type','wp-food');?></span>
							    <strong><?php 
							    	$order_type='';
									$order_type = get_post_meta( $idorder, 'exorder_type', true );
									if ($order_type == 'order-delivery') {
										echo esc_html_e('Order and  wait delivery','wp-food');
									}else{
										echo esc_html_e('Order and carryout','wp-food');
									}
							    ?></strong>
							  </li>
							  <li>
							    <span><?php esc_html_e('Date Delivery','wp-food');?></span>
							    <strong><?php 
							    	$date_di = get_post_meta( $idorder, 'exorder_date', true );
								    echo $date_di!='' && is_numeric($date_di) ? date_i18n(get_option('date_format'),$date_di) : $date_di;
								    ?>
							    </strong>
							  </li>
							  <li>
							    <span><?php esc_html_e('Time Delivery','wp-food');?></span>
							    <strong><?php echo get_post_meta( $idorder, 'exorder_time', true );?></strong>
							  </li>
							  <li>
							    <span><?php esc_html_e('Order Phone','wp-food');?></span>
							    <strong><?php echo get_post_meta( $idorder, 'exorder_phone', true );?></strong>
							  </li>
							  <li>
							    <span><?php esc_html_e('Order Email','wp-food');?></span>
							    <strong><?php echo get_post_meta( $idorder, 'exorder_email', true );?></strong>
							  </li>
							</ul>
						</div>
					</div>
					<table class="exfd-order-detail">
						<thead>
						<tr>
						  <th><?php esc_html_e('Product','wp-food');?></th>
						  <th><?php esc_html_e('Quantity','wp-food');?></th>
						  <th><?php esc_html_e('Detail','wp-food');?></th>
						</tr>
						</thead>
						<tbody>
							<?php
								foreach ($userfood as $key => $value) {
									$food_id = $value['food_id'];
									$price_food = get_post_meta( $food_id, 'exfood_price', true );
									$saleprice = get_post_meta( $food_id, 'exfood_sale_price', true );
									$price_food = $saleprice!='' && is_numeric($saleprice) ? $saleprice : $price_food;
									$price_food = is_numeric($price_food) ? $price_food : 0;
									$customlink = get_edit_post_link($food_id);

									?>
									<tr>
										<td>
											<span class="exfd-user-tittle"><?php echo get_the_title($food_id)?></span>
											<?php
												foreach ($value as $key_it => $item_meta) {
													if(is_array($item_meta)){
													echo '<span class="exfood-addon">';
													foreach ($item_meta as $val) {
													  $val = explode("|",$val);
													  $price = isset ($val[2]) ? $val[2] : '';
													  $price_food = $price!='' && is_numeric($price) ? $price_food + $price*1 : $price_food;
													  if($price!=''){
													    echo '<p>'.wp_kses_post($val[1]) .': '.exfood_price_with_currency($price).'</p>';
													  }else{
													    echo '<p>'.wp_kses_post($val[1]) .'</p>';
													  }
													}
													echo '</span>';
													}
												}
												$price_food = $price_food * $value['food_qty'];
												$total_price = $total_price + $price_food;
											?>
										</td>
										<td class="">
											<span><?php echo wp_kses_post($value['food_qty']); ?></span>
										</td>
										<td>
											<?php echo exfood_price_with_currency($price_food); ?>
										</td>
									</tr>
								<?php }?>
								<tr><td class="exfd-user-total" colspan="2"><?php esc_html_e('Total','wp-food');?></td>
									<td><?php echo exfood_price_with_currency($total_price); ?></td>
								</tr>
						</tbody>
					</table>
					<div class="ex-order-billing">
				    <h3><?php esc_html_e('Order billing','wp-food');?></h3>
				    <address>
				      <?php 
				      	$termad = get_term_by('slug', get_post_meta( $idorder, 'exorder_location', true ), 'exfood_loc');
				      	$name = $termad->name;
				        echo esc_html__('Name: ','wp-food').get_post_meta( $idorder, 'exorder_fname', true ).' '.get_post_meta( $idorder, 'exorder_lname', true ).'<br>';
				        echo esc_html__('Location: ','wp-food').$name.'<br>';
				        $order_store = get_post_meta( $idorder, 'exorder_store', true );
				        if ($order_store !='') {
				        	echo esc_html__('Store: ','wp-food').get_the_title( $order_store ).'<br>';
				        }
				        echo esc_html__('Address: ','wp-food').get_post_meta( $idorder, 'exorder_address', true ).'<br>';
				        echo esc_html__('Phone: ','wp-food').get_post_meta( $idorder, 'exorder_phone', true ).'<br>';
				        echo esc_html__('Email: ','wp-food').get_post_meta( $idorder, 'exorder_email', true ).'<br>';
				        echo esc_html__('Note :','wp-food').get_post_meta( $idorder, 'exorder_note', true ).'<br>';
				      ?>
				    </address>
				  </div>
				<?php
				}elseif(isset($_GET['view']) && !isset($_GET['idorder'])) {
					$view = sanitize_text_field($_GET['view']);
					$first_name = get_user_meta ( $current_user->ID,'exorder_fname', true);
					$last_name = get_user_meta ( $current_user->ID,'exorder_lname', true);
					$phone = get_user_meta ( $current_user->ID,'exorder_phone', true);
					$email = get_user_meta ( $current_user->ID,'exorder_email', true);
					$address = get_user_meta ( $current_user->ID,'exorder_address', true);

					if ($view == 'dashboad') {
					echo '<p>'.esc_html__('Hello','wp-food').' <strong>'.$current_user->user_login.'</strong></p>';
					echo '<p>'.esc_html__('Wellcome to account dashboard page','wp-food').'</p>';
					echo '<p>'.esc_html__('From your account dashboard you can view your recent orders, manage addresses.','wp-food').'</p>';

					}elseif ($view == 'orders') {?>
						<div class="exfd-user-order">
							<table class="exfd-table-order">
						      <thead>
						        <tr>
						          <th><?php esc_html_e('Tittle','wp-food');?></th>
						          <th><?php esc_html_e('Date','wp-food');?></th>
						          <th><?php esc_html_e('ID','wp-food');?></th>
						          <th><?php esc_html_e('Status','wp-food');?></th>
						        </tr>
						      </thead>
						      <tbody>
						      	<?php
									$args = array(
										'post_status' => array( 'publish'),
										'post_type'  => 'exfood_order',
										'meta_key'   => 'exorder_userid',
										'meta_value'    => $id_user,
									);
									$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
									$args['paged'] = $paged;
									$the_query = new WP_Query( $args );
									if($the_query->have_posts()){
										while ($the_query->have_posts()) { $the_query->the_post();?>
										<tr>
								          <td><a href="<?php echo esc_url(add_query_arg( array('view' => 'orders','idorder' => get_the_ID()), $curent_url )); ?>"><?php the_title(); ?></a></td>
								          <td><?php echo get_the_date(); ?></td>
								          <td><?php the_ID(); ?></td>
								          <?php $status =get_post_meta( get_the_ID(), 'exfood_order_status', true );
								          	switch ($status) {
											    case "on-hold":
											        $status = esc_html__('On Hold','wp-food');
											        break;
											    case "process":
											        $status = esc_html__('Processing','wp-food');
											        break;
											    case "complete":
											        $status = esc_html__('Completed','wp-food');
											        break;
										        case "cancel":
											        $status = esc_html__('Cancelled','wp-food');
											        break;
											    default:
											        $status = esc_html__('On Hold','wp-food');
											}
								          ?>
								          <td><?php echo $status;?></td>
								        </tr>
									<?php }
									}
									wp_reset_postdata();
						      	?>
						        
						      </tbody>
						    </table>
						    <?php  exfood_pagenavi_no_ajax($the_query);?>
						</div>
					<?php }elseif($view == 'address'){?>
						<form method="post" action="<?php echo esc_url(add_query_arg( array('view' => 'address'), $curent_url )); ?>">
							<h3><?php esc_html_e('Billing Information','wp-food') ?></h3>
							<p class="exfd-column">
								<label><?php esc_html_e('First Name:','wp-food') ?></label>
								<input type="text" name="exorder_fname" value="<?php echo esc_attr($first_name);?>">	
							</p>
							<p class="exfd-column exfd-column-padding">
								<label><?php esc_html_e('Last name:','wp-food') ?></label>
								<input type="text" name="exorder_lname" value="<?php echo esc_attr($last_name);?>">	
							</p>
							<p>
								<label><?php esc_html_e('Phone:','wp-food') ?></label>
								<input type="text" name="exorder_phone" value="<?php echo esc_attr($phone);?>">	
							</p>
							<p>
								<label><?php esc_html_e('Email:','wp-food') ?></label>
								<input type="email" name="exorder_email" value="<?php echo esc_attr($email);?>">	
							</p>
							<p>
								<label><?php esc_html_e('Address:','wp-food') ?></label>
								<input type="text" name="exorder_address" value="<?php echo esc_attr($address);?>">	
							</p>
							<p>
								<button type="submit" class="" name="save_address"><?php esc_html_e('Save address','wp-food') ?></button>
							</p>
						</form>
						
					<?php }
				}else{
					echo '<p>'.esc_html__('Hello','wp-food').' <strong>'.$current_user->user_login.'</strong></p>';
					echo '<p>'.esc_html__('Wellcome to account dashboard page','wp-food').'</p>';
					echo '<p>'.esc_html__('From your account dashboard you can view your recent orders, manage addresses, and edit your account details.','wp-food').'</p>';
				}
			?>
		</div>
	</div>
    <?php
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_food_user', 'exfood_shortcode_user' );
add_action( 'after_setup_theme', 'ex_food_reg_user_vc' );
function ex_food_reg_user_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("wp-food - My account", "wp-food"),
	   "base" => "ex_food_user",
	   "class" => "",
	   "icon" => "",
	   "controls" => "full",
	   "category" => esc_html__('wp-food','wp-food'),
	   "params" => array(
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Login Url and regiter", "wp-food"),
			"param_name" => "login_url",
			"value" => "",
			"description" => esc_html__("Enter login url and regiter instead of using form from WP Food", 'wp-food'),
		  ),
	   )
	));
	}
}