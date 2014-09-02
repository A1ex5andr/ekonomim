<?php

	/**
	 * Returns options name depending on the currently active site language.
	 * Used for compatibility with WPML plugin.
	 *
	 * @since Pukka 1.0
	 *
	 * @return string Options key
	 */
	function pukka_get_options_name(){
		$opt_sufix = '';

		// set options name suffix if WPML is activated
		if(defined('ICL_LANGUAGE_CODE')){
			global $sitepress;
			$default_lng = $sitepress->get_default_language();
			if($default_lng != ICL_LANGUAGE_CODE){
				$opt_sufix .= '_' . ICL_LANGUAGE_CODE;
			}
		}
		return PUKKA_OPTIONS_NAME . $opt_sufix;
	}


	/**
	 * Returns theme option by option name.
	 *
	 * @since Pukka 1.0
	 *
	 * @param string $key Option name
	 * @return mixed
	 */
	function pukka_get_option($key){
		$options_name = pukka_get_options_name();
		$values = get_option($options_name);

		return (isset($values[$key])) ? $values[$key] : '';
	}

	/**
	 * Set single option in theme settings
	 *
	 * @since Pukka 1.0
	 *
	 * @param string $key Option name
	 * @param mixed $value Option value
	 */
	function pukka_set_option($key, $value){

		if (trim($key) == '')
				return;

		$options_name = pukka_get_options_name();
		$pukka_values = get_option($options_name);
		$pukka_values[$key] = $value;

		update_option($options_name, $pukka_values);
	}

	/* BEGIN: Pukka hook section ***************************************/

	function pukka_after_content(){
		global $post;
		do_action('pukka_after_content', $post->ID);
	}

	function pukka_after_body(){
		global $post;

		// search with no results
		if( isset($post) && is_object($post) ){
			do_action('pukka_after_body', $post->ID);
		}
	}

	/* END: Pukka hook section ***************************************/

	/*
	* Translates passed string using translation in .mo file
	* Used to translate theme settings page
	*/
	function pukka_translate($text, $domain = 'default') {
		global $l10n;

		if (isset($l10n[$domain]))
			return apply_filters('gettext', $l10n[$domain]->translate($text), $text, $domain);
		else
			return $text;
	}

	add_filter('pukka_translate_theme_settings', 'pukka_translate_theme_option_pages');
	function pukka_translate_theme_option_pages($theme_option_pages){

		// translate theme options
		foreach( $theme_option_pages as &$page ){
				$page['page_title'] = isset($page['page_title']) ? pukka_translate($page['page_title'], 'pukka') : '';
				$page['menu_title'] = isset($page['menu_title']) ? pukka_translate($page['menu_title'], 'pukka') : '';
				$page['page_description'] = isset($page['page_description']) ? pukka_translate($page['page_description'], 'pukka') : '';

			foreach( $page['tabs'] as &$tab ){
				$tab['title'] = pukka_translate($tab['title'], 'pukka');
				foreach( $tab['fields'] as &$field ){
					$field['title'] = isset($field['title']) ? pukka_translate($field['title'], 'pukka') : '';
					$field['desc'] = isset($field['desc']) ? pukka_translate($field['desc'], 'pukka') : '';
				}
			}
		}

		return $theme_option_pages;
	}

	/**
	 * This function queries database directliy bypassing wp default functions
	 * for database queries which is needed for many search and autocomplete
	 * functionalities
	 *
	 * @since Pukka 1.2.1
	 *
	 * @param $args mixed
	 *
	 * @return mixed
	 */
	function pukka_get_posts_by($args){
		$defaults = array(
			'term' => '',
			'no_post_ids' => '',
			'post_ids' => '',
			'cat' => '',
			'meta' => '',
			'lang' => '',
			'post_type' => 'post',
			'post_status' => 'publish'
		);
		global $wpdb;

		$term = empty($args['term']) ? '' : $args['term'];
		$status = empty($args['post_status']) ? 'publish' : $args['post_status'];

		$posts = $wpdb->posts;
		$terms_rel = $wpdb->term_relationships;
		$terms_tax = $wpdb->term_taxonomy;

		// if specific post type is set, get just that
		if(!empty($args['post_type'])){
			if(is_array($args['post_type'])){
				$post_types = " AND (";
				$cnt = count($args['post_type']);
				for($i = 0; $i < $cnt; $i++){
					$post_types .= "$posts.post_type = '" . $args['post_type'][$i] . "'";
					if($i < $cnt - 1){
						$post_types .= " OR ";
					}
				}

				$post_types .= ")";
			}else{
				$post_types = " AND $posts.post_type = '{$args['post_type']}'";
			}
		}else{
			// if not, get everithing except attachments, revisions and menu items
			$post_types = " AND $posts.post_type <> 'attachment' AND $posts.post_type <> 'revision' AND $posts.post_type <> 'nav_menu_item'";
		}

		$query = "SELECT $posts.ID, $posts.post_title, $posts.post_author, $posts.post_date, $posts.post_type
			FROM $posts";

		// if we want post from specific category, here we can set which one
		// TODO: add multicat select
		if(!empty($args['cat'])){
			$query .= " INNER JOIN $terms_rel ON $posts.ID = $terms_rel.object_id
						INNER JOIN $terms_tax ON $terms_rel.term_taxonomy_id = $terms_tax.term_taxonomy_id
						WHERE $terms_tax.term_id = {$args['cat']} AND";
		}else{
			$query .= " WHERE";
		}
		$query .= " $posts.post_title LIKE '%%%s%%'
					AND $posts.post_status = '$status'";

		//if WPML active, query only posts in current language
		if (!empty($args['lang'])) {
			$query .= " AND $posts.ID IN (SELECT element_id
									FROM " . $wpdb->prefix . "icl_translations
									WHERE language_code = '" . $args['lang'] . "')";
		}

		if(!empty($post_types)){
			$query .= $post_types;
		}

		if (!empty($args['no_post_ids'])) {
			// taken from wp-includes/query.php
			// we cant prepare NOT IN statement: (%s) becomes ('1,2,3,4'), so we use array_map instead
			$query .= " AND $posts.ID NOT IN (" . implode(',', array_map('absint', $args['no_post_ids'])) . ")";
		}

		if (!empty($args['post_ids'])) {
			// taken from wp-includes/query.php
			// we cant prepare IN statement: (%s) becomes ('1,2,3,4'), so we use array_map instead
			$query .= " AND $posts.ID IN (" . implode(',', array_map('absint', $args['post_ids'])) . ")";
		}

		$results = $wpdb->get_results($wpdb->prepare($query, $term), OBJECT);
		

		return $results;

	}