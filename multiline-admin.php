<?php

// enqueue admin style
function mfcf7_enqueue_plugin_style() {
        wp_register_style( 'mfcf7_admin_css', plugin_dir_url( __FILE__ ) . '/css/admin-style.css' );
        wp_enqueue_style( 'mfcf7_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'mfcf7_enqueue_plugin_style' );

/* Tag generator */
add_action( 'wpcf7_admin_init', 'mfcf7_zl_add_tag_for_multilinefile', 50 );
function mfcf7_zl_add_tag_for_multilinefile() {

	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->add( 'multilinefile', __( 'multilinefile', 'contact-form-7' ), 'mfcf7_zl_tag_multilinefile' );

}


function mfcf7_zl_tag_multilinefile( $contact_form, $args = '' ) {

	$args = wp_parse_args( $args, array() );
	$type = 'multilinefile';
	$description = __( "Generate a form-tag for a multiple file uploading field. For more details, see %s.", 'contact-form-7' );
	$desc_link = wpcf7_link( __( 'http://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File Uploading and Attachment', 'contact-form-7' ), array('target' => '_blank') );
?>

<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>
<table class="form-table">
<tbody>
	<tr>
		<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
		<td>
			<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
			<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Button Label', 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="limit" placeholder="For Ex:1048576, 1024kb, 1mb" class="filesize oneline option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Allowed file types', 'contact-form-7' ) ); ?></label></th>
		<td>
			<input type="text" name="filetypes" placeholder="For Ex:gif|png|jpg|jpeg" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-accept' ); ?>"><?php echo esc_html( __( 'Add input attribute', 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="accept" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-accept' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-accept_wildcard' ); ?>"><?php echo esc_html( __( 'Add  accept wildcard', 'contact-form-7' ) ); ?></label></th>
		<td>
			<fieldset>
				<input type="text" name="accept_wildcard" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-accept_wildcard' ); ?>" /><small><?php echo __('Type "yes" to add wildcard'); ?></small>
			</fieldset>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>
	<tr>
		<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
		<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />
	<div class="submitbox">
		<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>
	<br class="clear" />
	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To attach the file uploaded through this field to mail, you need to insert the corresponding mail-tag (%s) into the File Attachments field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>

<?php
}

/* Warning message */
add_action( 'wpcf7_admin_notices', 'mfcf7_zl_multilinefile_display_warning_message' );
function mfcf7_zl_multilinefile_display_warning_message() {

	if ( ! $contact_form = wpcf7_get_current_contact_form() ) {
		return;
	}

	$has_tags = (bool) $contact_form->form_scan_shortcode( array( 'type' => array( 'multilinefile', 'multilinefile*' ) ));
	if ( ! $has_tags ) {
		return;
	}

	$file_upload_dir = wpcf7_upload_tmp_dir();
	wpcf7_init_uploads();

	if ( !wp_is_writable( $file_upload_dir ) || !is_dir( $file_upload_dir )) {
		$message = sprintf( __( 'This contact form contains file uploading fields, but the temporary folder for the files (%s) does not exist or is not writable by wordpress. You can create the folder or change its permission manually.', 'contact-form-7' ), $file_upload_dir );
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
	}
}

/* Add review and premium plugin notice */
// remove admin notice for 7 days
add_action('admin_init', 'mfcf7_zl_notice_ignor_temp');
function mfcf7_zl_notice_ignor_temp(){
	if ( isset($_GET['mfcf7_zl_notice_ignor_temp']) && '7' == $_GET['mfcf7_zl_notice_ignor_temp'] ) {
		set_transient( 'mfcf7-zl-admin-do-not-show-rating-tip', true, 7 * DAY_IN_SECONDS );	//set for 7 days
		$current_url = home_url($_SERVER['REQUEST_URI']);
		list($url, $content) = explode('?', $current_url);
		wp_redirect($url);
		exit;
	}

	if ( isset($_GET['mfcf7_zl_pro_ver_notice_ignor_per']) && '0' == $_GET['mfcf7_zl_pro_ver_notice_ignor_per'] ) {
		set_transient( 'mfcf7-zl-admin-do-not-show-pro-tip', true, 365 * DAY_IN_SECONDS );	//set for longer time
		$current_url = home_url($_SERVER['REQUEST_URI']);
		list($url, $content) = explode('?', $current_url);
		wp_redirect($url);
		exit;
	}

	if ( isset($_GET['mfcf7_zl_notice_ignor_per']) && '0' == $_GET['mfcf7_zl_notice_ignor_per'] ) {
		set_transient( 'mfcf7-zl-admin-do-not-show-rating-tip', true, 365 * DAY_IN_SECONDS );	//set for longer time
		$current_url = home_url($_SERVER['REQUEST_URI']);
		list($url, $content) = explode('?', $current_url);
		wp_redirect($url);
		exit;
	}
}

// Add pro version notice
add_action( 'admin_notices', 'mfcf7_zl_admin_premium_ver_notice' );
function mfcf7_zl_admin_premium_ver_notice(){
  if( !get_transient('mfcf7-zl-admin-do-not-show-pro-tip')){
      ?>
			<div class="notice notice-info">
				<p>Thank you for choosing <strong><a href="https://wordpress.org/plugins/multiline-files-for-contact-form-7/" target="_blank">Multiline files upload for contact form 7</a></strong> plugin.</p>
			<p>For more advanced feature, please try our premium plugin.</p><span class="mfcf7-notice-image"><a href="https://wordpress.org/plugins/multiline-files-for-contact-form-7/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/multiline_file_plugin_icon.png"></a></span>
			<p>Premium plugin includes:</p>
			<ul class="mfcf7-premium-notice-features-list">
				<li>Remove files one by one even if selected together</li>
				<li>Change placement of selected files list</li>
				<li>Priority Support</li>
			</ul>
			<p class="mfcf7-premium-notice-btn"><a href="https://codecanyon.net/item/multiline-files-upload-for-contact-form-7/20632083" target="_blank">Get Pro version</a>&nbsp;<a href="?mfcf7_zl_pro_ver_notice_ignor_per=0">No Thanks</a></p>
      </div>
      <?php
    }
}

// Add review admin notice
add_action( 'admin_notices', 'mfcf7_zl_admin_rating_notice' );
function mfcf7_zl_admin_rating_notice(){
if(!get_transient('mfcf7-zl-admin-do-not-show-rating-tip')){
	?>
  <div class="notice notice-info 1">
		<p>Love using <strong>Multiline files upload for contact form 7</strong> plugin, why don’t appreciate us?</p>
    <p>We love and care about you. Our team is putting our maximum efforts to provide you the best functionalities.<br> We would really appreciate if you could spend a couple of seconds to give a Nice Review to the plugin for motivating us!</p>
		<p style="margin: 15px 0px;">
			<span class="mfcf7-premium-notice-btn">
				<a href="https://wordpress.org/plugins/multiline-files-for-contact-form-7/#reviews" target="_blank">Rate it Now</a>
			</span>
			<span class="mfcf7-premium-notice-btn"><a href="?mfcf7_zl_notice_ignor_temp=7">Maybe Later</a></span>
			 <span class="mfcf7-premium-notice-btn"><a href="?mfcf7_zl_notice_ignor_per=0">Already Rated</a></span>
		</p>
	</div>
<?php
	}
}

// Add Plugin row meta Upgrade link
function mfcf7_plugin_meta_links( $links, $file ) {
    if ( $file === 'multiline-1.2-free/multiline-files-upload-for-contact-form-7.php' ) {
        $links[] = '<a href="https://codecanyon.net/item/multiline-files-upload-for-contact-form-7/20632083" target="_blank" title="' . __( 'Upgrade multiline file upload plugin into pro version' ) . '"><strong>' . __( 'Upgrade to Pro' ) . '</strong></a>';
    }
    return $links;
}
add_filter( 'plugin_row_meta', 'mfcf7_plugin_meta_links', 10, 2 );
?>
