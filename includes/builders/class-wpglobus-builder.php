<?php
/**
 * File: class-wpglobus-builder.php
 *
 * @package WPGlobus\Builders
 * @author Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Builder.
 *
 * @since 1.9.17
 */
if ( ! class_exists( 'WPGlobus_Builder' ) ) :

	class WPGlobus_Builder {
	
		/**
		 * Current language of post.
		 */
		protected $language = null;
		
		/**
		 * Builder ID.
		 */
		protected $id = null;
		
		/**
		 * Array of activated builders.
		 *
		 * @since  1.9.17
		 * @access protected
		 * @var    array
		 */
		//protected $builders = array();
		
		protected $builder_post = null;
		
		/**
		 * Constructor method.
		 *
		 * @since  1.9.17
		 * @access public
		 * @return void
		 */
		public function __construct($id) {
			
			$this->id = $id;

			$this->set_current_language();

			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				/**
				 * @todo Add the handling of AJAX.
				 */
			}
			
			if ( is_admin() ) {
				
				add_action( 'redirect_post_location', array( $this, 'on__redirect' ), 5, 2 );
				
				add_filter( 'admin_body_class', array( $this, 'filter__add_admin_body_class' ) );
				
				/**
				 * @see wp-includes\post.php
				 */
				add_filter('content_edit_pre', array( $this, 'filter__content' ), 5, 2 );
				add_filter('title_edit_pre', array( $this, 'filter__title' ), 5, 2 );
				add_filter('excerpt_edit_pre', array( $this, 'filter__excerpt' ), 5, 2 );
				
			}
			
			/**
			 * Show language tabs in post.php page.
			 * @see wpglobus\includes\class-wpglobus.php
			 */
			add_filter( 'wpglobus_show_language_tabs', array( $this, 'filter__show_language_tabs' ), 5 );
			
		}

		/**
		 * Filter title.
		 */
		public function filter__title($value, $post_id) {
			$value = WPGlobus_Core::text_filter($value, $this->get_current_language(), WPGlobus::RETURN_EMPTY);
			return $value;
		}
		
		/**
		 * Filter content.
		 */		
		public function filter__content($content, $post_id) {
			$content = WPGlobus_Core::text_filter($content, $this->get_current_language(), WPGlobus::RETURN_EMPTY);
			return $content;
		}		
	
		/**
		 * Filter excerpt.
		 */		
		public function filter__excerpt($excerpt, $post_id) {
			$excerpt = WPGlobus_Core::text_filter($excerpt, $this->get_current_language(), WPGlobus::RETURN_EMPTY);
			return $excerpt;
		}
		
		/**
		 * Redirect.
		 */
		public function on__redirect( $location, $post_id ) {
			/**
			 * Tested with:
			 * - Page Builder by SiteOrigin OK.
			 */
			return  $location . '&language='.$this->language;
		}
		
		public function get_id() {
			return $this->id;	
		}
		
		public function is_builder_post() {
			if ( is_null($this->builder_post) ) {
				return false;
			}
			return true;
		}

		/**
		 * Get hidden "wpglobus-language" field.
		 * 
		 * @since 1.9.17
		 * @return string
		 */		
		public function get_language_field() {
			return '<input type="hidden" id="wpglobus-language" name="wpglobus-language" value="'.$this->get_current_language().'" />';
		}
		
		/**
		 * Return current language.
		 * 
		 * @since 1.9.17
		 * @return string
		 */
		public function get_current_language() {
			return $this->language;
		}		
	
		/**
		 * Set current language.
		 *
		 * @since 1.9.17
		 * @return void
		 */		
		public function set_current_language() {
			
			if( ! is_null($this->language) ) {
				return;
			}
			
			$language = WPGlobus::Config()->default_language;
		
			if ( 1 ) {

				$_set = false;
				
				/**
				 * Get language code: order is important.
				 */
				/**
				 * 1.
				 */	
				if ( isset( $_REQUEST['language'] ) ) { // WPCS: input var ok, sanitization ok.
					$language = sanitize_text_field($_REQUEST['language']);
					$_set = true;
				}
				/**
				 * 2.
				 */					
				if ( isset( $_REQUEST['wpglobus-language'] ) ) { // WPCS: input var ok, sanitization ok.
					$language = sanitize_text_field($_REQUEST['wpglobus-language']);
					$_set = true;
				}
				/**
				 * 3. Meta
				 */
				$post_id = '';
				if ( empty($_REQUEST['post']) ) {
					
					/**
					 * @todo add doc
					 */

				} else {
					if ( ! empty($_REQUEST['post']) ) {
						$post_id = $_REQUEST['post'];
					} else if( ! empty($_REQUEST['id']) ) {
						$post_id = $_REQUEST['id'];
					} else if( ! empty($_REQUEST['post_ID']) ) {
						$post_id = $_REQUEST['post_ID'];
					}
				}
				
				if ( ! empty( $post_id ) ) {
					if ( $_set ) {
						update_post_meta($post_id, WPGlobus::Config()->builder->get_language_meta_key(), $language);
					} else {
						$language = get_post_meta($post_id, WPGlobus::Config()->builder->get_language_meta_key(), true);
					}
				}
				
			} // endif;
			
			if ( ! in_array( $language, WPGlobus::Config()->enabled_languages ) ) {
				$language = WPGlobus::Config()->default_language;
				update_post_meta($post_id, WPGlobus::Config()->builder->get_language_meta_key(), $language);
			}

			$this->language = $language;
			
		}	
		
		/**
		 *
		 */
		public function filter__save_post_data( $data, $postarr ) {
			
			if ( (int) $postarr['ID'] == 0 ) {
				return $data;
			}
		
			if ( 'revision' == $postarr['post_type'] ) {
				/**
				 * Don't work with revisions
				 * note: revision there are 2 types, its have some differences
				 *        - [post_name] => {post_id}-autosave-v1    and [post_name] => {post_id}-revision-v1
				 *        autosave         : when [post_name] == {post_id}-autosave-v1  $postarr has [post_content] and [post_title] in default_language
				 *        regular revision : [post_name] == {post_id}-revision-v1 $postarr has [post_content] and [post_title] in all enabled languages with delimiters
				 * @see https://codex.wordpress.org/Revision_Management
				 * see $postarr for more info
				 */
				return $data;
			}

			if ( 'auto-draft' == $postarr['post_status'] ) {
				/**
				 * Auto draft was automatically created with no data.
				 */
				return $data;
			}

			/*
			if ( $this->disabled_entity( $data['post_type'] ) ) {
				return $data;
			} */

			/** @global string $pagenow */
			//global $pagenow;

			/**
			 * Now we save post content and post title for all enabled languages for post.php, post-new.php
			 * @todo Check also 'admin-ajax.php', 'nav-menus.php', etc.
			 */
			 /*
			$enabled_pages[] = 'post.php';
			$enabled_pages[] = 'post-new.php';

			if ( ! in_array( $pagenow, $enabled_pages ) ) {
				return $data;
			}

			// @todo check work with trash. !!!!!!
			if ( 'trash' === $postarr['post_status'] ) {
				//return $data;
			}
			// */
			
			if ( isset( $_GET['action'] ) && 'untrash' === $_GET['action'] ) { // WPCS: input var ok, sanitization ok.
				/**
				 * Don't work with untrash.
				 */
				return $data;
			}
		
			global $wpdb;
			
			/**
			 * Get previous post data.
			 */
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $postarr['ID'] ) );
			
			/**
			 * Get new post data.
			 */
			$fields = array(
				'post_title' 	=> trim($postarr['post_title']),
				'post_content' 	=> trim($postarr['post_content']),
				'post_excerpt' 	=> trim($postarr['post_excerpt'])
			);

			foreach( $fields as $field=>$new_value ) { 
				
				$new_value = trim( $new_value );
				
				$_new_text = array();

				foreach ( WPGlobus::Config()->enabled_languages as $lang ) {
					
					if ( $lang == $this->get_current_language() ) {
						
						//*
						$text = $new_value;
						if ( WPGlobus_Core::has_translations($new_value) ) {
							/**
							 * $new_value may have string with language marks, e.g. Gutenberg.
							 */
							$text = WPGlobus_Core::text_filter($new_value, $lang , WPGlobus::RETURN_EMPTY);
						}
						if ( ! empty($text) ) {
							$_new_text[$lang] = $text;
						} 
						// */
						
						/**
						if ( ! empty($new_value) ) {
							$_new_text[$lang] = $new_value;
						}	*/					
						
					} else {
						
						/*
						//$text = WPGlobus_Core::text_filter($_post->$field, $lang , WPGlobus::RETURN_EMPTY);
						$text = WPGlobus_Core::text_filter($new_value, $lang , WPGlobus::RETURN_EMPTY);
						if ( ! empty($text) ) {
							$_text[$lang] = $text;
						} // */
						
						/**
						 * Just get text for not current language.
						 */
						$text = WPGlobus_Core::text_filter($_post->$field, $lang , WPGlobus::RETURN_EMPTY);
						if ( ! empty($text) ) {
							$_new_text[$lang] = $text;
						}				
					}
					

				}

				$data[$field] = WPGlobus_Utils::build_multilingual_string($_new_text);
			
			}
			
			//$data = apply_filters( 'wpglobus_save_post_data', $data, $postarr, $devmode );
			
			return $data;
		}		
		
		/**
		 * Show language tabs in post.php page.
		 * 
		 * @see includes\class-wpglobus.php
		 * @param bool
		 * Returning boolean.
		 */
		public function filter__show_language_tabs($value) {

			global $pagenow;
			
			$classes = array();
			$classes['wpglobus-post-tab'] = 'wpglobus-post-tab';
			$classes['ui-state-default']  = 'ui-state-default';
			$classes['ui-corner-top']     = 'ui-corner-top';
			$classes['ui-tabs-active']    = 'ui-tabs-active';
			$classes['ui-tabs-loading']   = 'ui-tabs-loading';
			
			?>
			<ul class="wpglobus-post-body-tabs-list">    <?php
				$order = 0;
				foreach ( WPGlobus::Config()->open_languages as $language ) {
					
					$tab_suffix = $language == WPGlobus::Config()->default_language ? 'default' : $language;
					
					$_classes = $classes;
					if ( $language == $this->language ) {
						$_classes[] = 'ui-state-active';
					}
					
					$link = add_query_arg( array(
								'post' => $_GET['post'],
								'action' => 'edit',
								'language' => $language,
							), admin_url($pagenow) );

					?>
					<li id="link-tab-<?php echo esc_attr( $tab_suffix ); ?>" data-language="<?php echo esc_attr( $language ); ?>"
						data-order="<?php echo esc_attr( $order ); ?>"
						class="<?php echo implode( ' ', $_classes ); ?>">
						<!--<a href="#tab-<?php echo esc_attr( $tab_suffix ); ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>-->
						<a href="<?php echo $link; ?>"><?php echo esc_html( WPGlobus::Config()->en_language_name[ $language ] ); ?></a>
					</li> <?php
					$order ++;
				} ?>
			</ul>   
			<?php
			/**
			 * Return false to prevent output standard WPGlobus tabs.
			 */
			return false;
		}
		
		/**
		 * Add class to body in admin.
		 * @see admin_body_class filter
		 *
		 * @since 1.9.17
		 * @param string $classes
		 *
		 * @return string
		 */	
		public function filter__add_admin_body_class($classes) {
			return $classes . ' wpglobus-wp-admin-builder wpglobus-wp-admin-builder-'.$this->id;
		}	

	}

endif;
# --- EOF