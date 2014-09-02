<?php

	// FRAMEWORK INIT
	include_once(PUKKA_FRAMEWORK_DIR .'/php/html.helper.class.php');
	include_once(PUKKA_FRAMEWORK_DIR . '/php/pukka.theme.class.php');
	include_once(PUKKA_FRAMEWORK_DIR . '/php/functions.php');

	include_once(PUKKA_FRAMEWORK_DIR . '/php/pukka.metabox.class.php');

	// SOCIAL MEDIA SUPPORT
	include_once(PUKKA_FRAMEWORK_DIR . '/php/social/pukka.social.media.class.php');

	$social_media = new PukkaSocialMedia(array('og_desc_length' => 220)); // Social media


	// Importer
	if(is_admin() && current_user_can('manage_options')){
		include_once PUKKA_FRAMEWORK_DIR . '/php/importer/pukka.import.class.php';
		$pukka_importer = new PukkaImport(); //Content Import Handler
	}