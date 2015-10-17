/**
 * WPGlobus Customize Control
 * Interface JS functions
 *
 * @since 1.2.1
 *
 * @package WPGlobus
 * @subpackage Customize Control
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCoreData */

var WPGlobusCustomize;
jQuery(document).ready(function ($) {	
    "use strict";
	if ( typeof WPGlobusCoreData.customize === 'undefined' ) {
		return;	
	}
	
	var api = {
		languages: {},
		index: 0,
		length: 0,
		positionSet: false,
		selectorHtml: '<span style="margin-left:5px;" class="wpglobus-icon-globe"></span><span style="font-weight:bold;">{{language}}</span>',
		init: function(args) {
			$.each( WPGlobusCoreData.enabled_languages, function(i,e){
				api.languages[i] = e;
				api.length = i;
			});
			api.addLanguageSelector();
			api.setElements();
			api.addListeners();
		},
		setTitle: function() {
			$(WPGlobusCoreData.customize.info.element).html(WPGlobusCoreData.customize.info.html);
		},
		getString: function(s) {
			// using ':::' mark for correct work with url
			var tr = WPGlobusCore.getTranslations(s),
				sR = [], i = 0;
			$.each(tr, function(l,t){
				sR[i] = t == '' ? 'null' : t;
				i++;
			});
			sR = sR.join(':::');
			return sR;
		},		
		setElements: function() {
			api.setTitle();
			var value;
			$.each(WPGlobusCoreData.customize.addElements, function(i,e){
				$(e.element).attr('id',i).val(e.value).trigger('change');
				if ( typeof e.options !== 'undefined' ) {
					if ( typeof e.options.setValue !== 'undefined' && e.options.setValue ) {
						value = $(e.origin_element).val();
						$(e.element).data( 'source', value );
						$(e.element).val( WPGlobusCore.TextFilter( value, WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
						if ( $(e.element).hasClass('wpglobus-control-url') ) {
							$(e.origin_element).val( api.getString( value ) );	
						}	
					}
					if ( typeof e.options.setLabel !== 'undefined' && e.options.setLabel ) {
						$(e.title).text( $(e.origin_title).text() );
					}
				}	
				$(e.element).on('change',function (ev){
					var $t = $(this),
						$e = $( WPGlobusCoreData.customize.addElements[$(this).data('customize-setting-link')].origin_element );
					
					$t.data( 'source', WPGlobusCore.getString( $t.data('source'), $t.val() ) );
					if ( $t.hasClass('wpglobus-control-url') ) {
						$e.val( api.getString( $t.data('source') ) );
					} else {
						$e.val( WPGlobusCore.getString( $e.val(), $t.val() ) );
					}	
					$e.trigger('change');
				});		
			});		
		},	
		addLanguageSelector: function() {
			$('<a style="margin-left:48px;" class="customize-controls-close wpglobus-customize-selector"><span class="wpglobus-globe"></span></a>').insertAfter('.customize-controls-preview-toggle');	
			$('.wpglobus-customize-selector').html( api.selectorHtml.replace('{{language}}', WPGlobusCoreData.language) );
		},
		setPosition: function(e) {
			if ( typeof e.options.setPosition !== 'undefined' && e.options.setPosition ) {
				var el = $(e.parent).detach();
				el.insertBefore( e.origin_parent );
				$(e.parent).css({'display':'block'});
			}
		},	
		addListeners: function() {
			$(document).on('click','.control-section', function(ev){
				if ( api.positionSet ) {
					return;
				}	
				api.positionSet = true;
				$.each(WPGlobusCoreData.customize.addElements, function(i,e){
					$(e.origin_parent).css({'display':'none'});
					$(e.origin_parent+' label' ).css({'display':'none'}); // from WP4.3				
					if ( typeof e.options !== 'undefined' ) {
						api.setPosition(e);
					}	
				});
			});			
					
			$(document).on('click','.wpglobus-customize-selector', function(ev){
				if ( api.index > api.length-1 ) {
					api.index = 0;
				} else {
					api.index++;
				}	

				WPGlobusCoreData.language = api.languages[api.index];
				
				$(this).html( api.selectorHtml.replace('{{language}}', WPGlobusCoreData.language) );
				
				$('.wpglobus-customize-control').each(function(i,e){
					var or = $( WPGlobusCoreData.customize.addElements[$(e).data('customize-setting-link')].origin_element ),
						$e = $(e);
					if ( $e.hasClass('wpglobus-control-url') ) {
						$e.val( WPGlobusCore.TextFilter( $e.data('source'), WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					} else {	
						$e.val( WPGlobusCore.TextFilter( or.val(), WPGlobusCoreData.language, 'RETURN_EMPTY' ) );
					}	
				});
				
			});

			$(document).ajaxSend(function(event, jqxhr, settings){
				if ( 'undefined' == typeof settings.data ) {
					return;	
				}	
				if ( settings.data.indexOf('action=customize_save') >= 0 ) {
					var s=settings.data.split('&'),
						ss, source;

					$.each(s, function(i,e){
						ss = e.split('=');
						if ( 'customized' == ss[0] ) {
							source = ss[1];
							return;	
						}	
					});
					
					var q = decodeURIComponent(source);
					q = JSON.parse(q);
					$.each(WPGlobusCoreData.customize.addElements, function(elem,value){			
						if ( typeof q[elem] !== 'undefined' ) {
							q[value.origin] = $(WPGlobusCoreData.customize.addElements[elem].origin_element).val();
						}	
					});
					settings.data = settings.data.replace( source, JSON.stringify(q) );
				}
			});				
		}	
	};
	
	WPGlobusCustomize =  $.extend( {}, WPGlobusCustomize, api );	
	
	WPGlobusCustomize.init();

});	
