<?php
/**
 * Unit test for Class WPGlobus_WP
 *
 * @package WPGlobus\Unit-Tests
 */
require_once dirname( __FILE__ ) . '/../includes/class-wpglobus-wp.php';

/**
 * Class WPGlobus_WP__Test
 */

/** @noinspection PhpUndefinedClassInspection */
class WPGlobus_WP__Test extends \PHPUnit\Framework\TestCase {

	/**
	 * @covers WPGlobus_WP::is_doing_ajax
	 */
	public function test_is_doing_ajax() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		if ( DOING_AJAX ) {
			self::assertTrue( WPGlobus_WP::is_doing_ajax() );
		} else {
			self::assertFalse( WPGlobus_WP::is_doing_ajax() );
		}
	}

	/**
	 * @covers WPGlobus_WP::is_admin_doing_ajax
	 */
	public function test_is_is_admin_doing_ajax() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		/**
		 * POST
		 */
		unset( $_GET['action'], $_POST['action'] );
		foreach (
			array(
				'inline-save',
				'save-widget',
			)
			as $action
		) {
			$_POST['action'] = $action;
			self::assertTrue( WPGlobus_WP::is_admin_doing_ajax(), $action );
		}

		/**
		 * GET
		 */
		unset( $_GET['action'], $_POST['action'] );
		foreach (
			array(
				'ajax-tag-search',
			)
			as $action
		) {
			$_GET['action'] = $action;
			self::assertTrue( WPGlobus_WP::is_admin_doing_ajax(), $action );
		}

		/** Cleanup */
		unset( $_GET['action'], $_POST['action'] );

	}

	/**
	 * @covers WPGlobus_WP::pagenow
	 * @covers WPGlobus_WP::is_pagenow
	 */
	public function test_is_pagenow() {
		// False because global is not initialized
		self::assertFalse( WPGlobus_WP::is_pagenow( 'unit-test-page' ) );

		global $pagenow;
		/** @noinspection OnlyWritesOnParameterInspection */
		$pagenow = 'unit-test-page';
		self::assertTrue( WPGlobus_WP::is_pagenow( 'unit-test-page' ) );
		self::assertTrue( WPGlobus_WP::is_pagenow( array( 'unit-test-page', 'another-page' ) ) );
		self::assertTrue( WPGlobus_WP::is_pagenow( array( new stdClass, 'unit-test-page' ) ) );
		self::assertFalse( WPGlobus_WP::is_pagenow( 'not-unit-test-page' ) );
		self::assertFalse( WPGlobus_WP::is_pagenow( array( 'not-unit-test-page', 'another-page' ) ) );
		self::assertFalse( WPGlobus_WP::is_pagenow( 3.14 ) );
		self::assertFalse( WPGlobus_WP::is_pagenow( new stdClass ) );
	}

	/**
	 * @covers WPGlobus_WP::plugin_page
	 * @covers WPGlobus_WP::is_plugin_page
	 */
	public function test_is_plugin_page() {
		// False because global is not initialized
		self::assertFalse( WPGlobus_WP::is_plugin_page( 'unit-test-page' ) );

		global $plugin_page;
		/** @noinspection OnlyWritesOnParameterInspection */
		$plugin_page = 'unit-test-page';
		self::assertTrue( WPGlobus_WP::is_plugin_page( 'unit-test-page' ) );
		self::assertTrue( WPGlobus_WP::is_plugin_page( array( 'unit-test-page', 'another-page' ) ) );
		self::assertTrue( WPGlobus_WP::is_plugin_page( array( new stdClass, 'unit-test-page' ) ) );
		self::assertFalse( WPGlobus_WP::is_plugin_page( 'not-unit-test-page' ) );
		self::assertFalse( WPGlobus_WP::is_plugin_page( array( 'not-unit-test-page', 'another-page' ) ) );
		self::assertFalse( WPGlobus_WP::is_plugin_page( 3.14 ) );
		self::assertFalse( WPGlobus_WP::is_plugin_page( new stdClass ) );
	}

	/**
	 * @covers WPGlobus_WP::is_http_post_action
	 */
	public function test_is_http_post_action() {
		$_POST['action'] = 'unit-test-action';
		self::assertTrue( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		self::assertFalse( WPGlobus_WP::is_http_post_action( '' ) );
		self::assertFalse( WPGlobus_WP::is_http_post_action( null ) );
		self::assertFalse( WPGlobus_WP::is_http_post_action( 3.14 ) );
		$bad_boy = new stdClass;
		self::assertFalse( WPGlobus_WP::is_http_post_action( $bad_boy ) );

		$_POST['action'] = 'not-unit-test-action';
		self::assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		unset( $_POST['action'] );
		self::assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );
		$_POST['action'] = 'unit-test-action';
		self::assertTrue( WPGlobus_WP::is_http_post_action( array( 'unit-test-action', 'not-unit-test-action' ) ) );

		$_POST['action'] = array( 'this-should-not-be-an-array' );
		self::assertFalse( WPGlobus_WP::is_http_post_action( 'unit-test-action' ) );

	}

	/**
	 * @covers WPGlobus_WP::is_http_get_action
	 */
	public function test_is_http_get_action() {
		$_GET['action'] = 'unit-test-action';
		self::assertTrue( WPGlobus_WP::is_http_get_action( 'unit-test-action' ) );
		self::assertFalse( WPGlobus_WP::is_http_get_action( '' ) );
		self::assertFalse( WPGlobus_WP::is_http_get_action( null ) );
		self::assertFalse( WPGlobus_WP::is_http_get_action( 3.14 ) );
		$bad_boy = new stdClass;
		self::assertFalse( WPGlobus_WP::is_http_get_action( $bad_boy ) );

		$_GET['action'] = 'not-unit-test-action';
		self::assertFalse( WPGlobus_WP::is_http_get_action( 'unit-test-action' ) );
		unset( $_GET['action'] );
		self::assertFalse( WPGlobus_WP::is_http_get_action( 'unit-test-action' ) );
		$_GET['action'] = 'unit-test-action';
		self::assertTrue( WPGlobus_WP::is_http_get_action( array( 'unit-test-action', 'not-unit-test-action' ) ) );

		$_GET['action'] = array( 'this-should-not-be-an-array' );
		self::assertFalse( WPGlobus_WP::is_http_get_action( 'unit-test-action' ) );

	}

	/**
	 * @see test_is_function_in_backtrace
	 */
	private static function _unit_test_for_backtrace() {
		self::assertTrue( WPGlobus_WP::is_function_in_backtrace( __FUNCTION__ ) );
	}

	/**
	 * @covers WPGlobus_WP::is_function_in_backtrace
	 */
	public static function test_is_function_in_backtrace() {
		self::assertTrue( WPGlobus_WP::is_function_in_backtrace( __FUNCTION__ ) );
		self::assertTrue( WPGlobus_WP::is_function_in_backtrace( array( __CLASS__, __FUNCTION__ ) ) );
		//self::assertTrue( WPGlobus_WP::is_function_in_backtrace( array( '\PHPUnit\Framework\TestCase', 'runTest' ) ) );

		self::assertFalse( WPGlobus_WP::is_function_in_backtrace( 'no-such-function' ) );
		self::assertFalse( WPGlobus_WP::is_function_in_backtrace( null ) );
		self::assertFalse( WPGlobus_WP::is_function_in_backtrace( 3.14 ) );
		self::assertFalse( WPGlobus_WP::is_function_in_backtrace( new stdClass ) );
		self::assertFalse( WPGlobus_WP::is_function_in_backtrace( array( 'a', 278, new stdClass ) ) );

		/**
		 * One level deeper
		 */
		self::_unit_test_for_backtrace();
	}

	/**
	 * @covers WPGlobus_WP::is_functions_in_backtrace
	 */
	public static function test_is_functions_in_backtrace() {
		self::assertTrue( WPGlobus_WP::is_functions_in_backtrace(
			array(
				__FUNCTION__,
			)
		) );
		self::assertTrue( WPGlobus_WP::is_functions_in_backtrace(
			array(
				array( __CLASS__, __FUNCTION__ ),
			)
		) );
		self::assertTrue( WPGlobus_WP::is_functions_in_backtrace(
			array(
				__FUNCTION__,
				array( __CLASS__, __FUNCTION__ ),
			)
		) );
		self::assertTrue( WPGlobus_WP::is_functions_in_backtrace(
			array(
				__FUNCTION__,
				array( __CLASS__, __FUNCTION__ ),
				'no-such-function',
				array( __CLASS__, 'no-such-function' ),
			)
		) );

		self::assertFalse( WPGlobus_WP::is_functions_in_backtrace(
			array(
				'no-such-function',
				array( __CLASS__, 'no-such-function' ),
			)
		) );
		self::assertFalse( WPGlobus_WP::is_functions_in_backtrace(
			array()
		) );
		self::assertFalse( WPGlobus_WP::is_functions_in_backtrace(
			array( 1 )
		) );
		self::assertFalse( WPGlobus_WP::is_functions_in_backtrace(
			array( new stdClass(), 'abc' )
		) );
	}
} // class

# --- EOF
