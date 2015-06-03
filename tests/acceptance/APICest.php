<?php

/**
 * Various function calls done by @see WPGlobus_QA are shown on the /api-demo/ page.
 * Here, we parse that page and verify that all functions work correctly.
 */
class APICest {


	/**
	 * @see WPGlobus_QA::_common_for_all_languages()
	 *
	 * @param AcceptanceTester $I
	 */
	protected function _common_for_all_languages( AcceptanceTester $I ) {
		$I->see( '{:en}ENG{:}{:ru}РУС{:}', '#tag_text' );
		$I->assertEquals( 'ENG РУС', $I->grabTextFrom( '#filter__the_title__' . 'no_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag' . ' .filter__the_title__output' ) );
		$I->assertEquals( 'ENG', $I->grabTextFrom( '#filter__the_title__' . 'one_tag_qt_tags' . ' .filter__the_title__output' ) );

		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( 'boolean', $I->grabTextFrom( '#_test_get_the_terms' .
		                                               ' .non-existing-post-id' ) );
		$I->assertEquals( 'WP_Error', $I->grabTextFrom( '#_test_get_the_terms' .
		                                                ' .no-such-term' ) );

		/**
		 * @see WPGlobus_QA::_test_post_name
		 */
		$I->assertEquals( '', $I->grabTextFrom( '#_test_post_name' .
		                                        ' .wpg_qa_draft .wpg_qa_post_name' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                               ' .wpg_qa_draft .wpg_qa_sample_permalink' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                               ' .wpg_qa_publish .wpg_qa_post_name' ) );
		$I->assertEquals( 'post-en', $I->grabTextFrom( '#_test_post_name' .
		                                               ' .wpg_qa_publish .wpg_qa_sample_permalink' ) );

		/**
		 * @see WPGlobus_QA::_test_get_term()
		 * Don't filter ajax action 'inline-save-tax' from edit-tags.php page.
		 */
		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'category name EN{:}{:ru}' .
		                  WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'category name RU{:}', $I->grabTextFrom(
			'#_test_get_term_' . 'inline-save-tax' . ' .name' ) );

	}

	/**`
	 * @see WPGlobus_QA::_test_home_url()
	 *
	 * @param AcceptanceTester $I
	 * @param string           $home_url
	 */
	protected function _test_home_url( AcceptanceTester $I, $home_url = '' ) {
		$I->see( $home_url, '#_test_home_url code' );
	}

	/**
	 * @see WPGlobus_QA::_test_string_parsing()
	 *
	 * @param AcceptanceTester $I
	 * @param string           $test_id
	 * @param string           $test_output
	 */
	protected function _test_string_parsing( AcceptanceTester $I, $test_id = '', $test_output = '' ) {
		$I->assertEquals( $test_output, $I->grabTextFrom( '#filter__the_title__' . $test_id . ' .filter__the_title__output' ) );
	}

	/**
	 * @param AcceptanceTester $I
	 * @param string           $test_output
	 */
	protected function _test_string_parsing_ok( AcceptanceTester $I, $test_output = '' ) {
		foreach (
			array(
				'proper',
				'proper_swap',
				'extra_lead',
				'extra_trail',
				'qt_tags_proper',
				'qt_tags_proper_swap',
				'qt_comments_proper',
				'qt_comments_proper_swap',
			)
			as $test_id
		) {
			$this->_test_string_parsing( $I, $test_id, $test_output );
		}

	}

	/**
	 * TESTS
	 * -----
	 */

	/**
	 * Check the EN version of the api-demo page
	 *
	 * @param AcceptanceTester $I
	 */
	public function en( AcceptanceTester $I ) {
		$I->amOnPage( '/?wpglobus=qa' );

		$language        = 'en';
		$language_suffix = strtoupper( $language );

		$I->see( WPGlobus_Acceptance::COMMON_PREFIX . ' EN', 'h1' );

		/**
		 * @see WPGlobus_QA::_test_get_locale()
		 */
		$I->assertEquals( 'en_US', $I->grabTextFrom( '#_test_get_locale' ) );

		/**
		 * @see WPGlobus_QA::_create_qa_items()
		 */
		$I->assertEquals(
			'{:en}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_title EN' .
			'{:}' .
			'{:ru}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_title RU' .
			'{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_title' ) );

		$I->assertEquals(
			'{:en}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more EN' .
			'{:}' .
			'{:ru}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more RU' .
			'{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_content' ) );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' post_excerpt EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' post_excerpt RU{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_excerpt' ) );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_title EN'
			,
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_title' ) );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more EN'
			,
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_content' ) );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' post_excerpt EN',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_excerpt' ) );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_title EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_title RU{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_title' ) );

		$I->assertEquals(
			'{:en}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more EN' .
			'{:}' .
			'{:ru}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more RU' .
			'{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_content' ), __LINE__ );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_excerpt EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_excerpt RU{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_excerpt' ) );

		$I->assertEquals( '' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_title EN',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_title' ) );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more EN'
			,
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_content' ) );

		$I->assertEquals( '' . WPGlobus_Acceptance::COMMON_PREFIX . ' page_excerpt EN',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_excerpt' ) );

		$I->assertEquals( '' . WPGlobus_Acceptance::COMMON_PREFIX . ' blogdescription EN', $I->grabTextFrom( '#qa_blogdescription' ) );

		$this->_test_home_url( $I, WPGlobus_Acceptance::URL_QA_HOME );

		$this->_test_string_parsing_ok( $I, 'ENG' );

		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline' . ' .filter__the_title__output' ) );
		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( "ENG1\nENG2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_comments' . ' .filter__the_title__output' ) );

		$I->assertEquals( "ENG1", $I->grabTextFrom( '#filter__the_title__' . 'multipart' . ' .filter__the_title__output' ) );

		/**
		 * @see WPGlobus_QA::_test_get_pages()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' page_title EN',
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_title' ), __LINE__ );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more EN'
			,
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_content' ), __LINE__ );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' page_excerpt EN',
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_excerpt' ), __LINE__ );

		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__name' ),
			'test__get_the_terms__' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description EN", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__description' ),
			'test__get_the_terms__' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_object_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description EN", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .fields_names' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Invalid taxonomy", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .no_such_term' ),
			'test_wp_get_object_terms' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_get_terms_' . 'category' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description EN", $I->grabTextFrom(
			'#_test_get_terms_' . 'category' . ' .description' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " post_tag name EN", $I->grabTextFrom(
			'#_test_get_terms_' . 'post_tag' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " post_tag description EN", $I->grabTextFrom(
			'#_test_get_terms_' . 'post_tag' . ' .description' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_get_terms_' . 'name_only' ) );

		/**
		 * @see WPGlobus_QA::_test_wp_get_term()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name EN", $I->grabTextFrom(
			'#_test_get_term_' . 'category' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description EN", $I->grabTextFrom(
			'#_test_get_term_' . 'category' . ' .description' ) );

		/**
		 * @see WPGlobus_QA::_test_wp_trim_words()
		 */
		$I->assertEquals( "EN01 EN02 EN03 EN04 EN05…", $I->grabTextFrom( '#_test_wp_trim_words' ) );

		/**
		 * @covers WPGlobus_QA::_test_get_posts()
		 */
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_title " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_title' ), __LINE__ );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_content " . $language_suffix .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . " post_content_after_more " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_content' ), __LINE__ );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_excerpt " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_excerpt' ), __LINE__ );

		/**
		 * @covers \WPGlobus_QA::_test_wp_page_menu
		 */

		// - Link to the page
		$_post_id = $I->grabTextFrom( '#_test_wp_page_menu .post_id' );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " page_title " . $language_suffix
			,
			$I->grabTextFrom( '#_test_wp_page_menu .page-item-' . $_post_id ), __LINE__ );

		// - Switcher: parent level
		$I->assertEquals(
			$language,
			$I->grabTextFrom( '#_test_wp_page_menu .page_item_wpglobus_menu_switch .wpglobus_flag_' . $language ),
			__LINE__ );

		// - Switcher: child level
		$I->assertEquals(
			'ru',
			$I->grabTextFrom( '#_test_wp_page_menu .children .wpglobus_flag_' . 'ru' ),
			__LINE__ );


		$this->_common_for_all_languages( $I );
	}

	/**
	 * Check the RU version of the api-demo page
	 * Note: non-English texts should be entered here with the proper capitalization, as visible on the screen.
	 * Codeception does not apply UTF lowercase.
	 *
	 * @param AcceptanceTester $I
	 */
	public function ru( AcceptanceTester $I ) {
		$I->amOnPage( '/ru/?wpglobus=qa' );

		$language        = 'ru';
		$language_suffix = strtoupper( $language );

		$I->see( WPGlobus_Acceptance::COMMON_PREFIX . ' RU', 'h1' );

		/**
		 * @see WPGlobus_QA::_test_get_locale()
		 */
		$I->assertEquals( 'ru_RU', $I->grabTextFrom( '#_test_get_locale' ) );

		/**
		 * @see WPGlobus_QA::_create_qa_items()
		 */
		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_title EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_title RU{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_title' ) );

		$I->assertEquals(
			'{:en}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more EN' .
			'{:}' .
			'{:ru}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more RU' .
			'{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_content' ) );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_excerpt EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_excerpt RU{:}',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_raw' . ' .qa_post_excerpt' ) );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_title RU',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_title' ) );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' post_content_after_more RU'
			,
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_content' ) );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'post_excerpt RU',
			$I->grabTextFrom( '#_create_qa_items_post' . ' .qa_post_cooked' . ' .qa_post_excerpt' ) );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_title EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_title RU{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_title' ) );

		$I->assertEquals(
			'{:en}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content EN' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more EN' .
			'{:}' .
			'{:ru}' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more RU' .
			'{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_content' ), __LINE__ );

		$I->assertEquals( '{:en}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_excerpt EN{:}{:ru}' . WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_excerpt RU{:}',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_raw' . ' .qa_post_excerpt' ) );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_title RU',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_title' ) );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_content_after_more RU'
			,
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_content' ), __LINE__ );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'page_excerpt RU',
			$I->grabTextFrom( '#_create_qa_items_page' . ' .qa_post_cooked' . ' .qa_post_excerpt' ) );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' ' . 'blogdescription RU', $I->grabTextFrom( '#qa_blogdescription' ) );

		$this->_test_home_url( $I, WPGlobus_Acceptance::URL_QA_HOME . '/ru' );

		$this->_test_string_parsing_ok( $I, 'РУС' );

		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline' . ' .filter__the_title__output' ) );
		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_tags' . ' .filter__the_title__output' ) );
		$I->assertEquals( "РУС1\nРУС2", $I->grabTextFrom( '#filter__the_title__' . 'multiline_qt_comments' . ' .filter__the_title__output' ) );

		$I->assertEquals( "РУС1", $I->grabTextFrom( '#filter__the_title__' . 'multipart' . ' .filter__the_title__output' ) );

		/**
		 * @see WPGlobus_QA::_test_get_pages()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' page_title RU',
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_title' ), __LINE__ );

		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content RU' .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . ' page_content_after_more RU'
			,
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_content' ), __LINE__ );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . ' page_excerpt RU',
			$I->grabTextFrom( '#_test_get_pages' . ' .qa_post_excerpt' ), __LINE__ );


		/**
		 * @see WPGlobus_QA::_test_get_the_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__name' ),
			'test__get_the_terms__' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description RU", $I->grabTextFrom(
			'#_test_get_the_terms' .
			' .test__get_the_terms__description' ),
			'test__get_the_terms__' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_object_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .name' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description RU", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .description' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .fields_names' ),
			'test_wp_get_object_terms' );

		$I->assertEquals( "Неверная таксономия", $I->grabTextFrom(
			'#_test_wp_get_object_terms' . ' .no_such_term' ),
			'test_wp_get_object_terms' );

		/**
		 * @see WPGlobus_QA::_test_wp_get_terms()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_get_terms_' . 'category' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description RU", $I->grabTextFrom(
			'#_test_get_terms_' . 'category' . ' .description' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " post_tag name RU", $I->grabTextFrom(
			'#_test_get_terms_' . 'post_tag' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " post_tag description RU", $I->grabTextFrom(
			'#_test_get_terms_' . 'post_tag' . ' .description' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_get_terms_' . 'name_only' ) );

		/**
		 * @see WPGlobus_QA::_test_wp_get_term()
		 */
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category name RU", $I->grabTextFrom(
			'#_test_get_term_' . 'category' . ' .name' ) );
		$I->assertEquals( WPGlobus_Acceptance::COMMON_PREFIX . " category description RU", $I->grabTextFrom(
			'#_test_get_term_' . 'category' . ' .description' ) );

		/**
		 * @see WPGlobus_QA::_test_wp_trim_words()
		 */
		$I->assertEquals( "RU01 RU02 RU03 RU04 RU05…", $I->grabTextFrom( '#_test_wp_trim_words' ) );


		/**
		 * @covers WPGlobus_QA::_test_get_posts()
		 */
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_title " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_title' ), __LINE__ );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_content " . $language_suffix .
			' ' .
			WPGlobus_Acceptance::COMMON_PREFIX . " post_content_after_more " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_content' ), __LINE__ );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " post_excerpt " . $language_suffix
			,
			$I->grabTextFrom( '#_test_get_posts .post_excerpt' ), __LINE__ );

		/**
		 * @covers \WPGlobus_QA::_test_wp_page_menu
		 */

		// - Link to the page
		$_post_id = $I->grabTextFrom( '#_test_wp_page_menu .post_id' );
		$I->assertEquals(
			WPGlobus_Acceptance::COMMON_PREFIX . " page_title " . $language_suffix
			,
			$I->grabTextFrom( '#_test_wp_page_menu .page-item-' . $_post_id ), __LINE__ );

		// - Switcher: parent level
		$I->assertEquals(
			$language,
			$I->grabTextFrom( '#_test_wp_page_menu .page_item_wpglobus_menu_switch .wpglobus_flag_' . $language ),
			__LINE__ );

		// - Switcher: child level
		$I->assertEquals(
			'en',
			$I->grabTextFrom( '#_test_wp_page_menu .children .wpglobus_flag_' . 'en' ),
			__LINE__ );

		$this->_common_for_all_languages( $I );
	}

} // class

# --- EOF