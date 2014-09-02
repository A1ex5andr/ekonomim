<?php if ( ! defined('PUKKA_VERSION')) exit('No direct script access allowed');
/*
* HTML helper class.
* All inputs used in theme (for Theme settings or meta boxes) are printed here.
*
*/

if (!class_exists('HtmlHelper')) :

	class HtmlHelper {

		// contains string that indicates where the element is being rendered
		// (options_page, metabox etc.)
		protected $context;

		/**
		 * Class constructor
		 *
		 * @since Pukka 1.0
		 *
		 */
		public function __construct() {

		}

		/**
		 * Sets string that indicates where the element is being rendered to
		 * (options_page, metabox etc.)
		 *
		 * @since Pukka 1.0
		 *
		 * @param string $c
		 */
		public function setContext($c) {
			$this->context = $c;
		}

		/**
		 * Rendering back-end menu page
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $page Array containing menu page data
		 */
		public function printMenuPage($page=false) {

			if (!$page) {
				return;
			}

			$this->openPageWrapper();
			?>
			<div id="pukka-heading" class="pukka-clearfix">
				<div id="pukka-logo">
					<a href="<?php echo PUKKA_HOMEPAGE; ?>" target="_blank"><img src="<?php echo PUKKA_FRAMEWORK_URI .'/images/admin-logo.png'; ?>" /></a>
				</div> <!-- #pukka-logo -->
				<h1 class="pukka-left"><?php echo $page['page_title']; ?></h1>
				<div class="page-description"><?php echo $page['page_description']; ?></div>
			</div> <!-- #pukka-heading -->
			<form action="<?php echo admin_url('admin-ajax.php') ?>" id="pukka-settings" method="post">

			<?php // Dont make tab nav if there's only one tab to show  ?>
					<?php $build_tabs = (count($page['tabs']) > 1) ? true : false; ?>

				<div class="pukka-tabs">
						<?php if ($build_tabs) : ?>
						<ul>
							<?php
							foreach ($page['tabs'] as $tab) {
								echo '<li>';
								echo '<a href="#' . $this->slugify($tab['title']) . '" data-icon="' . (isset($tab['icon']) ? $tab['icon'] : '') . '">' . $tab['title'] . '</a>';
								echo '</li>' . "\n";
							}
							?>
						</ul>
					<?php endif; // if( $build_tabs ) ?>

					<?php
					wp_enqueue_media(); // used for wp media uploader, needed only on theme option pages
					// Print tab contents
					foreach ($page['tabs'] as $tab) {
						echo '<div id="' . $this->slugify($tab['title']) . '" class="pukka-fields">' . "\n";
						foreach ($tab['fields'] as $field) {
							// Because heading doesn't have id, this needs to be checked
							$field['value'] = '';
							if(isset($field['id'])){
								$field['value'] = pukka_get_option($field['id']);
							}
							$this->printInput($field);
						}
						echo '</div>' . "\n";
					}
					?>
				</div> <!-- .pukka-tabs -->

				<input type="hidden" name="action" value="pukka_framework_save" />
				<input type="hidden" name="pukka_nonce" value="<?php echo wp_create_nonce('pukka_framework_save'); ?>" />
				<input type="button" id="pukka-reset-settings" class="pukka-button" value="<?php _e('Reset settings', 'pukka'); ?>" data-type="reset" />
				<img class="waiting pukka-ajax-reset" src="<?php echo admin_url(); ?>/images/wpspin_light.gif" />

				<input type="submit" id="pukka-save-settings" class="pukka-button-primary" value="<?php _e('Save settings', 'pukka'); ?>" data-type="save" />
				<img class="waiting pukka-ajax-load" src="<?php echo admin_url(); ?>/images/wpspin_light.gif" />
			</form>

			<?php
			$this->closePageWrapper();
		}


		/**
		 * Rendering HTML elements: Opening of the page wrapper div
		 *
		 * @since Pukka 1.0
		 */
		public function openPageWrapper() {
			?>
			<div id="pukka-wrap" class="pukka-settings">
				<?php
		}

		 /**
		 * Rendering HTML elements: Closing of the page wrapper div
		 *
		 * @since Pukka 1.0
		 */
		public function closePageWrapper() {
				?>
			</div> <!-- #pukka-wrap -->
			<?php
		}

		/**
		 * This method checks input type and renders it according to its type
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Element to be rendered to HTML
		 */
		public function printInput($input) {
			// if element is heading or section, we need to open wrapper div
			if ($input['type'] != 'heading' && 'section-open' != $input['type'] &&
					'section-close' != $input['type']) {
				$css_classes = isset($input['css_classes']) ? $input['css_classes'] : '';
				$this->openFieldWrapper($css_classes);
			}

			switch ($input['type']) {
				case 'heading': $this->printHeading($input);
					break;
				case 'text': $this->printText($input);
					break;
				case 'textarea': $this->printTextarea($input);
					break;
				case 'file': $this->printFile($input);
					break;
				case 'checkbox': $this->printCheckbox($input);
					break;
				case 'radio': $this->printRadio($input);
					break;
				case 'select': $this->printSelect($input);
					break;
				case 'color-picker': $this->printColorPicker($input);
					break;
				case 'section-open': $this->printSectionOpen($input);
					break;
				case 'section-close': $this->printSectionClose($input);
					break;
				case 'custom-html': $this->printCustomHTML($input);
					break;
			}

			// if element was heading or section, wrapper div needs to be closed
			if ($input['type'] != 'heading' && 'section-open' != $input['type'] &&
					'section-close' != $input['type']) {
				$this->closeFieldWrapper();
			}
		}

		/**
		 * Rendering HTML elements: Opening of the section div
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Element to be rendered to HTML
		 */
		public function printSectionOpen($input) {
			extract($input);
			// additional classes for section div
			$css_classes = '';
			if (!empty($classes)) {
				// if array is passed, loop trough all the elements
				if (is_array($css_classes)) {
					foreach ($classes as $class) {
						$css_classes .= ' ' . $class;
					}
				} else {
					// if not array, just print it
					$css_classes = ' ' . $classes;
				}
			}
			echo '<div class="section' . esc_attr($css_classes) . '">';
		}

		/**
		 * Rendering HTML elements: Closing section div
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Element to be rendered to HTML
		 */
		public function printSectionClose($input) {
			echo '</div><!-- end .section -->';
		}

		/**
		 * Rendering HTML element: Opening field wrapper div.
		 *
		 * @since Pukka 1.0
		 *
		 * @param string $css_classes Additional classes for field wrapper div.
		 */
		public function openFieldWrapper($css_classes='') {
			$css_classes = trim('pukka-input-wrap pukka-clearfix ' . $css_classes);
			echo '<div class="' . $css_classes . '">' . "\n";
		}

		/**
		 * Rendering HTML element: Closing field wrapper div.
		 *
		 * @since Pukka 1.0
		 */
		public function closeFieldWrapper() {
			echo '</div>' . "\n";
		}

		/**
		 * Rendering custom HTML content
		 *
		 * @since Pukka 1.0
		 *
		 * @param type $input String containing custom html
		 */
		public function printCustomHTML($input){
			extract($input);
			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<?php echo $content; ?>
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Input field.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printText($input) {
			extract($input);
			// value must not be empty
			if (empty($value)) {
				$value = '';
				if (!empty($default)) {
					$value = $default;
				}
			}
			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<input type="text" id="<?php echo $id ?>" name="pukka[<?php echo $id; ?>]" value="<?php echo esc_attr($value); ?>" size="50" autocomplete="off"/>
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Textarea.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printTextarea($input) {
			extract($input);
			// value must not be empty
			if (empty($value)) {
				$value = '';
				if (!empty($default)) {
					$value = $default;
				}
			}
			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<textarea id="<?php echo $id ?>" name="pukka[<?php echo $id; ?>]" rows="20" cols="40"><?php echo esc_textarea($value); ?></textarea>
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Checkbox.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printCheckbox($input) {
			extract($input);
			// value must not be empty
			if (empty($value)) {
				$value = 'off';
				if (!empty($default)) {
					$value = $default;
				}
			}
			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<input type="hidden" id="<?php echo $id ?>" name="pukka[<?php echo $id; ?>]" value="off" autocomplete="off" />
				<input type="checkbox" id="<?php echo $id ?>" name="pukka[<?php echo $id; ?>]" value="on" <?php checked($value, 'on', true); ?> size="100" autocomplete="off" />
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Radio button.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printRadio($input) {
			extract($input);
			// value must not be empty
			if (empty($value)) {
				$value = '';
				if (!empty($default)) {
					$value = $default;
				}
			}
			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
			<?php
			// check if only one radio button is to be rendered
			if (!is_array($options))
				// if yes, convert it to array so it can use same code for ptinting
				$options = (array) $options;
			?>
			<?php foreach ($options as $k => $v) { ?>
					<input type="radio" name="pukka[<?php echo $id; ?>]" value="<?php echo $k; ?>" <?php checked($k, $value); ?> /> <?php echo $v; ?>
				<?php
			}
			?>
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Select box.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printSelect($input) {
			extract($input);
			// options must exist
			if (!isset($options)) {
				// if not set, we create one
				$options = array();
			}

			// if subtype is page, a list of all pages on website needs to be generated, so...
			if (isset($subtype) && $subtype == 'page') {
				// fetch all pages
				$pages = get_pages();
				foreach ($pages as $page) {
					// populate array for printing
					$options[$page->ID] = $page->post_title;
				}
			// if subtype is cat, we need a list of all categories
			} elseif (isset($subtype) && ($subtype == 'cat' || $subtype == 'category') ) {
				// get all categories
				$cats = get_categories('orderby=name&hide_empty=0');

				foreach ($cats as $cat) {
					// populate array for printing
					$options[$cat->term_id] = $cat->name;
				}
			// if subtype is range, we create list of elements from min to max value with step of 1
			} elseif (isset($subtype) && $subtype == 'range') {

				if (isset($min) && isset($max)) {
					$inc = 1;
					if(isset($increment)){
						$inc = $increment;
					}
					for ($i = $min; $i <= $max; $i += $inc) {
						$options[$i] = $i;
					}
				// if min or max is not set, we create range from 0 to 20
				} else {
					$options = range(0, 20);
				}
			}
			// if options is single element (string or number) convert it to array so we can use
			// same code for ptinting multiple elements
			if (!is_array($options)) {
				$options = (array) $options;
			}

			if (empty($value)) {
				$value = '';
				if (!empty($default)) {
					$value = $default;
				}
			}

			$multi = '';
			$multi_class = '';
			if( isset($multiple) ){
				$multi_class = ' pukka-multiple-select';
				$multi = 'multiple="multiple" size="'. $multiple .'"';
			}else{
				$multi_class .= ' pukka-single-select';
			}

			?>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">

				<?php if( isset($multiple) ) : ?>
				<?php /* no value selected fix */ ?>
				<input type="hidden" id="<?php echo $id ?>" name="pukka[<?php echo $id; ?>]" value="" autocomplete="off" />
				<?php endif; ?>

				<select id="<?php echo $id; ?>" name="pukka[<?php echo $id; ?>]<?php if( isset($multiple) ) echo '[]'?>" <?php echo $multi; ?> class="<?php echo $multi_class; ?>">
					<?php
					// check if we want "Select..." text to be the first element in the list
					if(!isset($no_default_text) || true != $no_default_text) { ?>
					<option value=""><?php _e('Select..', 'pukka'); ?></option>
					<?php }
				// print list
				foreach ($options as $k => $v) { ?>
						<?php
						$selected = '';
						if( '' != selected($k, $value, false) || (is_array($value) && in_array($k, $value)) ){
							$selected = 'selected="selected"';
						}
						?>
						<option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
				<?php
			}
			?>
				</select>
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: File upload.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printFile($input) {
			extract($input);
			if (empty($value)) {
				$value = '';
				if( !empty($default) ){
					$value = $default;
				}
			}
			if( $value != '' ){
				$img_src = wp_get_attachment_image_src($value, 'full');
				$thumb_css_class = '';
			}
			else{
				$img_src = '';
				$thumb_css_class = 'pukka-file-placeholder';
			}

			?>
			<script type="text/javascript">
				// Upload
				jQuery(document).ready(function($){
					"use strict";
					$("#pukka-remove-upload-<?php echo $id; ?>").click(function(e){
						e.preventDefault();
						$("#<?php echo $id; ?>-thumb").empty();
						$("#<?php echo $id; ?>").val("");
						$("#<?php echo $id; ?>-thumb").addClass('pukka-file-placeholder');
						$(this).hide();
						//return false;
					})

					// Uploading files
					var file_frame;
					var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id

			<?php if ($this->context == 'metabox') : ?>
								var post_id = <?php echo get_the_ID(); ?>;
			<?php else : ?>
								var post_id = 0;
			<?php endif; ?>

							jQuery('#pukka-upload-<?php echo $id; ?>').on('click', function( event ){

								event.preventDefault();

								// If the media frame already exists, reopen it.
								if ( file_frame ) {
									// Set the post ID to what we want
									file_frame.uploader.uploader.param( 'post_id', post_id );
									// Open frame
									file_frame.open();
									return;
								} else {
									// Set the wp.media post id so the uploader grabs the ID we want when initialised
									wp.media.model.settings.post.id = post_id;
								}

								// Create the media frame.
								file_frame = wp.media.frames.file_frame = wp.media({
									title: $( this ).data( 'uploader_title' ),
									button: {
										text: $( this ).data( 'uploader_button_text' )
									},
									multiple: false  // Set to true to allow multiple files to be selected
								});

								// When an image is selected, run a callback.
								file_frame.on( 'select', function() {
									// We set multiple to false so only get one image from the uploader
									var attachment = file_frame.state().get('selection').first().toJSON();

									// Do something with attachment.id and/or attachment.url here
									//console.log('img url: '+ attachment.url);
									$("#<?php echo $id; ?>").val(attachment.id);
									$("#<?php echo $id; ?>-thumb").html("<img src="+ attachment.url +" style='max-width:200px;' />");
									$("#pukka-remove-upload-<?php echo $id; ?>").show();
									$("#<?php echo $id; ?>-thumb").removeClass('pukka-file-placeholder');

									// Restore the main post ID
									wp.media.model.settings.post.id = wp_media_post_id;
								});

								// Finally, open the modal
								file_frame.open();
							});

							// Restore the main ID when the add media button is pressed
							jQuery('a.add_media').on('click', function() {
								wp.media.model.settings.post.id = wp_media_post_id;
							});

						}); //document ready
			</script>
			<h4><?php echo $title; ?></h4>
			<?php if (empty($css_class)) {
				$css_class = '';
			} ?>
			<div class="pukka-input-description <?php echo $css_class; ?>">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<span id="<?php echo $id; ?>-thumb" class="pukka-img-wrap <?php echo $thumb_css_class; ?>">
			<?php if( $img_src != '' ) : ?>
						<img src="<?php echo esc_attr($img_src[0]); ?>" style="max-width:200px;" />
			<?php endif; ?>
				</span>
				<span class="pukka-upload-buttons">
					<button id="pukka-upload-<?php echo $id; ?>" class="pukka-button-primary" data-uploader_title="<?php _e('Select image', 'pukka'); ?>" data-uploader_button_text="<?php _e('Select', 'pukka'); ?>"><?php _e('Upload', 'pukka'); ?></button>
					<a href="#" id="pukka-remove-upload-<?php echo $id; ?>" class="pukka-remove-upload" style="display:<?php echo ($value != '' ? 'block' : 'none') ?>"><?php _e('Remove', 'pukka'); ?></a>
				</span>
				<input type="hidden" id="<?php echo $id; ?>" name="pukka[<?php echo $id; ?>]" value="<?php echo esc_attr($value); ?>" />
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: WP Color picker. (uses default WordPress color-picker script)
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printColorPicker($input) {
			extract($input);
			if (empty($value)) {
				$value = '';
				if (!empty($default)) {
					$value = $default;
				}
			}
			?>
			<script type="text/javascript">
				jQuery(function($){
					$("#<?php echo $id; ?>").wpColorPicker();
				});
			</script>
			<h4><?php echo $title; ?></h4>
			<div class="pukka-input-description">
			<?php echo $desc; ?>
			</div>
			<div class="pukka-input">
				<input type="text" name="pukka[<?php echo $id; ?>]" value="<?php echo $value; ?>" id="<?php echo $id; ?>" data-default-color="<?php echo $default; ?>"  autocomplete="off" />
			</div>
			<?php
		}

		/**
		 * Rendering HTML element: Heading.
		 *
		 * @since Pukka 1.0
		 *
		 * @param array $input Array containing field data.
		 */
		public function printHeading($input) {
			extract($input);
			?>
			<h2><?php echo $title ?></h2>
			<?php
		}

		/**
		 * Printing nonce and necessary hidden fields.
		 *
		 * @since Pukka 1.0
		 */
		public function printNonce() {

			$output = '';
			//if we are viewing a page and not a meta box
			if ($this->context == 'options_page') {
				$nonce = wp_create_nonce('pukka_framework_save');
				$output .= '		<input type="hidden" name="pukka_nonce" value="' . $nonce . '" />';
				$output .= '		<input type="hidden" name="action" value="pukka_framework_save" />';
			}
			//if the code was rendered for a meta box
			if ($this->context == 'metabox') {
				$nonce = wp_create_nonce('pukka_nonce_save_metabox');
				$output .= '		<input type="hidden" name="pukka_nonce" value="' . $nonce . '" />';
			}

			echo $output;
		}

		/**
		 * Converting string to url safe string for slugs
		 *
		 * @since Pukka 1.0
		 */
		public function slugify($text) {
			$table = array(
				"Š" => "S", "š" => "s", "Đ" => "Dj", "đ" => "dj", "Ž" => "Z", "ž" => "z", "Č" => "C", "č" => "c", "Ć" => "C", "ć" => "c",
				"À" => "A", "Á" => "A", "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A", "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
				"Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I", "Î" => "I", "Ï" => "I", "Ñ" => "N", "Ò" => "O", "Ó" => "O", "Ô" => "O",
				"Õ" => "O", "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U", "Û" => "U", "Ü" => "U", "Ý" => "Y", "Þ" => "B", "ß" => "Ss",
				"à" => "a", "á" => "a", "â" => "a", "ã" => "a", "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c", "è" => "e", "é" => "e",
				"ê" => "e", "ë" => "e", "ì" => "i", "í" => "i", "î" => "i", "ï" => "i", "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
				"ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o", "ù" => "u", "ú" => "u", "û" => "u", "ý" => "y", "ý" => "y", "þ" => "b",
				"ÿ" => "y", "Ŕ" => "R", "ŕ" => "r", "/" => "-", " " => "-"
			);

			// Remove duplicated spaces
			$text = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $text);

			// Returns the slug
			return strtolower(strtr($text, $table));
		}

	}

	 // Class end

endif;