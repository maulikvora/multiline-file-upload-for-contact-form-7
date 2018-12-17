<?php
/**
* Plugin Name: MultiLine files for Contact Form 7
* Description: Upload unlimited files one by one to contact form 7
* Plugin URI: https://wordpress.org/plugins/multiline-files-for-contact-form-7/
* Version: 1.3
* Author: Zluck Solutions
* Author URI: https://profiles.wordpress.org/zluck
*/

/**

** base class for [multilinefile] and [multilinefile*]

**/

$latest_contact_form_7 = false;
if(function_exists('wpcf7_add_form_tag'))
	$latest_contact_form_7 = true;


/* Register activation hook. */
register_activation_hook( __FILE__, 'mfcf7_zl_admin_notice_activation_hook' );
function mfcf7_zl_admin_notice_activation_hook() {
  set_transient( 'mfcf7-zl-admin-do-not-show-rating-tip', true, 5 * DAY_IN_SECONDS );// show after 5 days
  set_transient( 'mfcf7-zl-admin-do-not-show-pro-tip', true, 3 * DAY_IN_SECONDS );// show after 3 days
}

/* Enqueue required javascript */
add_action('wp_enqueue_scripts', 'mfcf7_zl_multiline_files_enqueue_script');
function mfcf7_zl_multiline_files_enqueue_script() {
    wp_enqueue_script( 'mfcf7_zl_multiline_files_script', plugin_dir_url( __FILE__ ) . 'js/zl-multine-files.js' );
}

add_action('wp_enqueue_scripts', 'mfcf7_zl_plugin_button_style');
function mfcf7_zl_plugin_button_style() {
    wp_enqueue_style('mfcf7_zl_button_style', plugin_dir_url( __FILE__ ) . 'css/style.css' );
    wp_enqueue_style( 'mfcf7-zl-load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );
}



/* Define Shortcode handler */
add_action( 'wpcf7_init', 'mfcf7_zl_add_shortcode_multilinefile' );
function mfcf7_zl_add_shortcode_multilinefile() {
	global $latest_contact_form_7;
	if($latest_contact_form_7)
		wpcf7_add_form_tag( array( 'multilinefile', 'multilinefile*' ), 'mfcf7_zl_multilinefile_shortcode_handler', true );
	else
		wpcf7_add_shortcode( array( 'multilinefile', 'multilinefile*' ), 'mfcf7_zl_multilinefile_shortcode_handler', true );
}

function mfcf7_zl_multilinefile_shortcode_handler( $tag ) {
	$html = '';
	global $latest_contact_form_7;
	if($latest_contact_form_7)
		$tag = new WPCF7_FormTag( $tag );
	else
		$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) ) {

		return '';

	}



	$error_in_validation = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $error_in_validation ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	$atts['accept'] = $tag->get_option( 'accept', null, true);
	$atts['multiple'] = 'multiple';

	$values = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $values ) ) {
		$values = __( 'Upload', 'contact-form-7' );
	}
	$upload_label = $atts['value'] = $values;

  $accept_wildcard = '';
  $accept_wildcard = $tag->get_option( 'accept_wildcard');


	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( !empty($accept_wildcard)) {
		$atts['accept'] = $atts['accept'] .'/*';
	}

	$atts['aria-invalid'] = $error_in_validation ? 'true' : 'false';
	$atts['type'] = 'file';
	$atts['name'] = $tag->name.'[]';

	$atts = apply_filters('cf7_multilinefile_atts', $atts);

	$atts = wpcf7_format_atts( $atts );

	$html .= sprintf(

		apply_filters('cf7_multilinefile_input', '<span class="mfcf7-zl-multiline-sample" style="display:none"><p class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s <span class="mfcf7-zl-multifile-name"></span><a href="javascript:void(0);" class="mfcf7_zl_delete_file"><i class="fa fa-times" aria-hidden="true"></i></a></p></span>', $atts),

		sanitize_html_class( $tag->name ), $atts, $error_in_validation );

  $html = '<div id="mfcf7_zl_multifilecontainer">'.$html.'</div>';
  $html .= '<a href="javascript:void(0);" id="mfcf7_zl_add_file">'.$upload_label.'</a>';

	return $html;
}


/* Enctype filter */
add_filter( 'wpcf7_form_enctype', 'mfcf7_zl_multilinefile_form_enctype_filter' );
function mfcf7_zl_multilinefile_form_enctype_filter( $enctype ) {

	global $latest_contact_form_7;
	if($latest_contact_form_7)
		$multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'multilinefile', 'multilinefile*' ) ) );
	else
		$multipart = (bool) wpcf7_scan_shortcode( array( 'type' => array( 'multilinefile', 'multilinefile*' ) ) );
	if ( $multipart ) {
		$enctype = 'multipart/form-data';
	}

	return $enctype;
}





/* Validation + upload handling filter */
add_filter( 'wpcf7_validate_multilinefile', 'mfcf7_zl_multilinefile_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_multilinefile*', 'mfcf7_zl_multilinefile_validation_filter', 10, 2 );



function mfcf7_zl_multilinefile_validation_filter( $result, $tag ) {

	global $latest_contact_form_7;
	if($latest_contact_form_7)
		$tag = new WPCF7_FormTag( $tag );
	else
		$tag = new WPCF7_Shortcode( $tag );



	$name = $tag->name;

	$id = $tag->get_id_option();

  $uniqid = uniqid();



	$original_files_array = isset( $_FILES[$name] ) ? $_FILES[$name] : null;



  if ($original_files_array === null) {

    return $result;

  }



  $total = count($_FILES[$name]['name']);



  $files = array();

  $new_files = array();

  for ($i=0; $i<$total; $i++) {
	if(empty($original_files_array['tmp_name'][$i]))
		continue;

    $files[] = array(

      'name'      => $original_files_array['name'][$i],

      'type'      => $original_files_array['type'][$i],

      'tmp_name'  => $original_files_array['tmp_name'][$i],

      'error'     => $original_files_array['error'][$i],

      'size'      => $original_files_array['size'][$i]

    );

  }
  // file loop start

  foreach ($files as $file) {





    if ( $file['error'] && UPLOAD_ERR_NO_FILE != $file['error'] ) {

      $result->invalidate( $tag, wpcf7_get_message( 'upload_failed_php_error' ) );

      mfcf7_zl_multilinefile_remove($new_files);

      return $result;

    }


    if ( ! is_uploaded_file( $file['tmp_name'] ) )
	{
		$result->invalidate( $tag, wpcf7_get_message( 'upload_failed_php_error' ) );
      return $result;
	}


    $allowed_file_types = array();



    if ( $file_types_a = $tag->get_option( 'filetypes' ) ) {

      foreach ( $file_types_a as $file_types ) {

        $file_types = explode( '|', $file_types );



        foreach ( $file_types as $file_type ) {

          $file_type = trim( $file_type, '.' );

          $file_type = str_replace( array( '.', '+', '*', '?' ),

            array( '\.', '\+', '\*', '\?' ), $file_type );

          $allowed_file_types[] = $file_type;

        }

      }

    }



    $allowed_file_types = array_unique( $allowed_file_types );

    $file_type_pattern = implode( '|', $allowed_file_types );



    $allowed_size = apply_filters('cf7_multilinefile_max_size', 10048576); // default size 1 MB



    if ( $file_size_a = $tag->get_option( 'limit' ) ) {

      $limit_pattern = '/^([1-9][0-9]*)([kKmM]?[bB])?$/';



      foreach ( $file_size_a as $file_size ) {

        if ( preg_match( $limit_pattern, $file_size, $matches ) ) {

          $allowed_size = (int) $matches[1];



          if ( ! empty( $matches[2] ) ) {

            $kbmb = strtolower( $matches[2] );



            if ( 'kb' == $kbmb )

              $allowed_size *= 1024;

            elseif ( 'mb' == $kbmb )

              $allowed_size *= 1024 * 1024;

          }



          break;

        }

      }

    }



    /* File type validation */



    // Default file-type restriction

    if ( '' == $file_type_pattern )

      $file_type_pattern = 'jpg|jpeg|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv|txt';



    $file_type_pattern = trim( $file_type_pattern, '|' );

    $file_type_pattern = '(' . $file_type_pattern . ')';

    $file_type_pattern = '/\.' . $file_type_pattern . '$/i';



    if ( ! preg_match( $file_type_pattern, $file['name'] ) ) {

      $result->invalidate( $tag, wpcf7_get_message( 'upload_file_type_invalid' ) );

      mfcf7_zl_multilinefile_remove($new_files);

      return $result;

    }



    /* File size validation */



    if ( $file['size'] > $allowed_size ) {

      $result->invalidate( $tag, wpcf7_get_message( 'upload_file_too_large' ) );

      mfcf7_zl_multilinefile_remove($new_files);

      return $result;

    }



    wpcf7_init_uploads(); // Confirm upload dir

    $uploads_dir = wpcf7_upload_tmp_dir();

    $uploads_dir = wpcf7_maybe_add_random_dir( $uploads_dir );



    $filename = $file['name'];

    $filename = wpcf7_canonicalize( $filename );

    $filename = sanitize_file_name( $filename );

    $filename = wpcf7_antiscript_file_name( $filename );

    $filename = wp_unique_filename( $uploads_dir, $filename );



    $new_file = trailingslashit( $uploads_dir ) . $filename;



    if ( false === @move_uploaded_file( $file['tmp_name'], $new_file ) ) {

      $result->invalidate( $tag, wpcf7_get_message( 'upload_failed' ) );

      mfcf7_zl_multilinefile_remove($new_files);

      return $result;

    }



    $new_files[] = $new_file;


    // Make sure the uploaded file is only readable for the owner process

    @chmod( $new_file, 0400 );







  }

	if ( count( $new_files ) == 0) {

		if($tag->is_required())
		{
		  $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}
		  return $result;

	}

  // file loop end

  $zipped_files = trailingslashit( $uploads_dir ).$uniqid.'.zip';

  $zipping = mfcf7_zl_multilinefile_create_zip($new_files, $zipped_files);

  @chmod( $zipped_files, 0440 );



  if ($zipping === false) {

    $result->invalidate( $tag, wpcf7_get_message( 'zipping_failed' ) );

    mfcf7_zl_multilinefile_remove($new_files);

    return $result;

  }



  mfcf7_zl_multilinefile_remove($new_files);

  if ( $submission = WPCF7_Submission::get_instance() ) {

    $submission->add_uploaded_file( $name, $zipped_files );

  }



	return $result;

}





/* Messages */
add_filter( 'wpcf7_messages', 'mfcf7_zl_multilinefile_messages' );
function mfcf7_zl_multilinefile_messages( $messages ) {

	return array_merge( $messages, array(

		'upload_failed' => array(

			'description' => __( "Uploading a file fails for any reason", 'contact-form-7' ),

			'default' => __( "There was an error uploading the file to the server.", 'contact-form-7' )

		),
		'zipping_failed' => array(

			'description' => __( "Zipping files fails for any reason", 'contact-form-7' ),

			'default' => __( "There was an error in zippng the files.", 'contact-form-7' )

		),
		'upload_file_type_invalid' => array(

			'description' => __( "Uploaded file is not allowed for file type", 'contact-form-7' ),

			'default' => __( "You are not allowed to upload files of this type.", 'contact-form-7' )

		),
		'upload_file_too_large' => array(

			'description' => __( "Uploaded file is too large", 'contact-form-7' ),

			'default' => __( "Uploaded file is too big.", 'contact-form-7' )

		),
		'upload_failed_php_error' => array(

			'description' => __( "Uploading a file fails for PHP error", 'contact-form-7' ),

			'default' => __( "There was an error uploading the file.", 'contact-form-7' )

		)
	) );

}

require_once plugin_dir_path( __FILE__ ) . 'multiline-admin.php';

/* creates a compressed zip file */
function mfcf7_zl_multilinefile_create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }

	$valid_files = array();
	//if files are attached
	if(is_array($files)) {
		//check each file one by one
		foreach($files as $file) {
			//make sure file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have valid files.
	if(count($valid_files)) {
		//create the archive
		$zipped_file = new ZipArchive();
		if($zipped_file->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}

		//add files one by one
		foreach($valid_files as $file) {
			$zipped_file->addFile($file,basename($file));
		}
		//debug
		//echo 'The zip file has ',$zipped_file->numFiles,' files, status : ',$zipped_file->status;

		//close the zip!
		$zipped_file->close();
		//make sure file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function mfcf7_zl_multilinefile_remove($new_files) {
  if (!empty($new_files)) {
    foreach($new_files as $to_delete) {
      @unlink( $to_delete );
      @rmdir( dirname( $to_delete ) ); // remove parent dir if it's removable (empty).
    }
  }
}

// For safari Browser
add_filter( 'wpcf7_load_js', '__return_false' );

function mfcf7_zl_load_js_not_safari11() {
	global $is_safari;
	if($is_safari) {
		return false;
	} else {
		return true;
	}
}
add_filter( 'wpcf7_load_js', 'mfcf7_zl_load_js_not_safari11' );


?>
