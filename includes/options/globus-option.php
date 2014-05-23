<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('Redux_Framework_globus_option')) {

    class Redux_Framework_globus_option {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

		private $menus		= array();

        public function __construct() {

			if (!class_exists('ReduxFramework')) {
                return;
            }

			$nav_menus = WPGlobus::_get_nav_menus();

			foreach ( $nav_menus as $menu ) {
				$this->menus[$menu->slug] = $menu->name;
			}
			if ( ! empty($nav_menus) && count($nav_menus) > 1 ) {
				$this->menus['all'] = 'All';
			}
			//$this->menus = array();
			//error_log( print_r( $menus, true ) );


            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        public function setSections() {

			global $WPGlobus_Config;

			$wpglobus_option = get_option('wpglobus_option');

            // ACTUAL DECLARATION OF SECTIONS
            $this->sections[] = array(
                'title'     => __('Home Settings', 'redux-framework-demo'),
                'desc'      => __('Redux Framework was created with the developer in mind. It allows for any theme developer to have an advanced theme panel with most of the features a developer would need. For more information check out the Github repo at: <a href="https://github.com/ReduxFramework/Redux-Framework">https://github.com/ReduxFramework/Redux-Framework</a>', 'redux-framework-demo'),
                'icon'      => 'el-icon-home',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields'    => array(
                    array(
                        'id'        => 'opt-web-fonts',
                        'type'      => 'media',
                        'title'     => __('Web Fonts', 'redux-framework-demo'),
                        'compiler'  => 'true',
                        'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
                        'desc'      => __('Basic media uploader with disabled URL input field.', 'redux-framework-demo'),
                        'subtitle'  => __('Upload any media using the WordPress native uploader', 'redux-framework-demo'),
                        'hint'      => array(
                            //'title'     => '',
                            'content'   => 'This is a <b>hint</b> tool-tip for the webFonts field.<br/><br/>Add any HTML based text you like here.',
                        )
                    ),
                )
            );

			/*
			 * LANGUAGES SECTION
			 */

			/** @var $all_languages contains all enabled languages */
			$all_languages 		= array();

			/** @var $enabled_languages contains all enabled languages except default language */
			$enabled_languages	= array();

			/** @var $more_languages */
			$more_languages 	= array();

			foreach ( $WPGlobus_Config->enabled_languages as $code ) {

				$all_languages[$code] = $WPGlobus_Config->language_name[$code];

				if ( $code != $WPGlobus_Config->default_language ) {
					$enabled_languages[$code] = $WPGlobus_Config->language_name[$code];
				}

			}
			if ( isset($wpglobus_option['more_languages']) && ! empty($wpglobus_option['more_languages']) ) {
				$all_languages[$wpglobus_option['more_languages']] = $WPGlobus_Config->language_name[$wpglobus_option['more_languages']];
				$enabled_languages[$wpglobus_option['more_languages']] = $WPGlobus_Config->language_name[$wpglobus_option['more_languages']];
			}


			foreach ( $WPGlobus_Config->flag as $code=>$file ) {
				if ( ! array_key_exists( $code, $all_languages ) ) {
					$more_languages[$code] = $WPGlobus_Config->language_name[$code];
				}
			}
			//error_log( print_r( $wpglobus_option, true) );


			$this->sections[] = array(
				'title'     => __( 'Languages', 'redux-framework-demo' ),
				'desc'      => __( '' ),
				'icon'      => 'el-icon-home',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields'    => array(
					array(
						'id'        => 'default_language',
						'type'      => 'select',
						#'type'      => 'select_with_flag',
						'title'     => __( 'Enabled/Default Language', 'redux-framework-demo' ),
						'compiler'  => 'false',
						'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'      => __( '', 'redux-framework-demo' ),
						'subtitle'  => __( '', 'redux-framework-demo' ),
						'placeholder'   => 'Select default language',
						'options'   => $all_languages,
						'default'   => 'en'
					),
					array(
						'id'        => 'enabled_languages',
						#'id'        => 'language_order',
						'type'      => 'sortable',
						'title'     => __( 'Enabled languages', 'redux-framework-demo' ),
						'compiler'  => 'false',
						'desc'      => __( '', 'redux-framework-demo' ),
						'subtitle'  => __( '', 'redux-framework-demo' ),
						'placeholder'   => 'navigation_menu_placeholder',
						'options'   => $enabled_languages,
						'mode'  	=> 'checkbox',
						'class'		=> 'test-class'
					),
					array(
						'id'        => 'more_languages',
						'type'      => 'select',
						#'type'      => 'image_select',
						'title'     => __( 'More language', 'redux-framework-demo' ),
						'compiler'  => 'false',
						'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'      => __( 'Select language and Save Change for add to Enabled languages', 'redux-framework-demo' ),
						'subtitle'  => __( 'Subtitle', 'redux-framework-demo' ),
						'placeholder'   => 'Select language',
						'options'   => $more_languages,
						#'default'  => 'code'
					)
				)
			);

			// miniGLOBUS SECTION

			if ( empty( $this->menus ) ) {
				$navigation_menu_placeholder = __('No navigation menu', 'redux-framework-demo');
			} else {
				$navigation_menu_placeholder = __('Select navigation menu', 'redux-framework-demo');
			}

			$this->sections[] = array(
				'title'     => __('miniGlobus Settings', 'redux-framework-demo'),
				'desc'      => __(''),
				'icon'      => 'el-icon-home',
				// 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
				'fields'    => array(
					array(
						'id'        => 'show_flag_name',
						'type'      => 'select',
						'title'     => __('Show flag name', 'redux-framework-demo'),
						'compiler'  => 'false',
						'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'      => __( '', 'redux-framework-demo' ),
						'subtitle'  => __( '', 'redux-framework-demo' ),
						'options'   => array(
							'code'  => 'Code',
							'name'  => 'Full language name',
							'empty' => 'Don\'t show'
						),
						'default'  => 'code',
						'hint'      => array(
							//'title'     => '',
							'content'   => 'Code - for example: &quot;en&quot;, &quot;ru&quot;',
						)
					),
					array(
						'id'        => 'use_nav_menu', # $WPGlobus_Config->nav_menu
						'type'      => 'select',
						'title'     => __( 'Use navigation menu', 'redux-framework-demo' ),
						'compiler'  => 'false',
						'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
						'desc'      => __( '', 'redux-framework-demo' ),
						'subtitle'  => __( '', 'redux-framework-demo' ),
						'options'   => $this->menus,
						'placeholder'   => $navigation_menu_placeholder,
						'default'  => 'code'
					)
				)
			);

        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'redux-framework-demo');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'wpglobus_option',         // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $theme->get('Name'),     // Name that appears at the top of your panel
                'display_version'   => $theme->get('Version'),  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('WPGlobus Options', 'redux-framework-demo'),
                'page_title'        => __('WPGlobus Options', 'redux-framework-demo'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => true,                    // Show the time the page took to load, etc
                'customizer'        => true,                    // Enable basic customizer support
                
                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => '_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
			/*
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/ReduxFramework/ReduxFramework',
                'title' => 'Visit us on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/pages/Redux-Framework/243141545850368',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://twitter.com/reduxframework',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://www.linkedin.com/company/redux-framework',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );		// */

            // Panel Intro text -> before the form
            if (!isset($this->args['global_variable']) || $this->args['global_variable'] !== false) {
                if (!empty($this->args['global_variable'])) {
                    $v = $this->args['global_variable'];
                } else {
                    $v = str_replace('-', '_', $this->args['opt_name']);
                }
                $this->args['intro_text'] = sprintf( __( '', 'redux-framework-demo' ), $v );
            } else {
                $this->args['intro_text'] = __('<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'redux-framework-demo');
            }

            // Add content after the form.
            $this->args['footer_text'] = __( '', 'redux-framework-demo' );
        }

    }
    

}