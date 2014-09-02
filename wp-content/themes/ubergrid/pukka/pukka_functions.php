<?php if ( ! defined('PUKKA_VERSION')) exit('No direct script access allowed');


	/* BEGIN: Dynamic meta section ***************************************/
	function pukka_get_dm_html($data){
		global $dynamic_meta;
		$out = $dynamic_meta->getDMHTML($data);

		return $out;
	}

	function pukka_get_dm_html_by_id($post_id, $echo = true){
		$meta = get_post_meta($post_id, '_pukka_dynamic_meta_box', true);
		return pukka_get_dm_html($meta);
	}
	
	function pukka_after_content_dynamic_meta($post_id){
		$meta = get_post_meta($post_id, '_pukka_dynamic_meta_box', true);
		if(!empty($meta)){
			echo '<div class="dm-wrap">' . pukka_get_dm_html($meta) . '</div>';
		}
	}
	add_action('pukka_after_content', 'pukka_after_content_dynamic_meta', 1, 1);
	/* END: Dynamic meta section ***************************************/


	/**
	 * Gets all theme option fields and its settings
	 *
	 * @return array
	 */
	function pukka_get_theme_options(){
		$options_name = pukka_get_options_name();
		// get all theme options
		$options = get_option($options_name);
		// get options page
		global $pukka_theme_option_pages;
		$options_page = $pukka_theme_option_pages['pukka_theme_settings_page'];

		$res = array();
		// iterate trough all tabs on page
		foreach ($options_page['tabs'] as $tab) {
			// and trough all fields on each tab
			foreach ($tab['fields'] as $field) {
				//we check if field is in $options array
				$tmp = array(); // this temp array will containt data about one field (id, values etc...)
								// that will be returned
				// if field doesn't have id, it does not store values in db, so just skip it
				if(!isset($field['id'])) continue;
				if(array_key_exists($field['id'], $options)){
					// save id to response
					$tmp['id'] = $field['id'];
					$tmp['value'] = $options[$field['id']];
					$tmp['type'] = $field['type'];

					// if $field type is 'file' then it is one of the images in
					// theme settings, so besides returning value (which is image id)
					// we also need to pass url for the image preview,
					// for everything else, we are good to go
					if('file' == $field['type']){
						$file = wp_get_attachment_image_src($tmp['value'], 'full');
						if($file){
							$tmp['url'] = $file[0];
						}else{
							$tmp['url'] = '';
						}
					}
					$res[] = $tmp;
				}
			}
		}

		return $res;
	}
	/**
	 * Returns fields required for contact forms spam check
	 *
	 * @since Pukka 1.1.1
	 *
	 * @return mixed
	 */
	function pukka_get_spam_fields(){
		$out = '';
		$numbers = explode(',', pukka_get_option('form_spam_numbers'));

		for($i=1; $i<=10; $i++ ){
			if(in_array($i, $numbers))
				$out .= '<input type="hidden" name="'. $i .'" value="'. $i .'" />' ."\n";
			else
				 $out .= '<input type="hidden" name="'. $i .'" value="" />' ."\n";
		}

		return $out;
	}
	
	function pukka_body_classes($classes) {
		$style = pukka_get_option(PUKKA_THEME_COLORSCHEME_NAME);
		if(!empty($style)){
			$classes[] = 'style-' . $style;
		}

		return $classes;
	}

	add_filter('body_class', 'pukka_body_classes');