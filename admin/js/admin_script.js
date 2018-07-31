// admin_scripts
(function( $ ) {
	var postid   = $( "#post-id" ).val();
	var alphanum = /^[A-Za-z0-9 _.-]+$/;
	var url      = /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;
	// Add Quiz
	$( document ).on( "click", ".add-quiz", function(){
		var meta_key = $( this ).data( "meta" );
		var title    = $( "#question-"+meta_key ).val();
		var media    = $( "#media-"+meta_key ).val();
		var alphanum = /^[A-Za-z0-9 _.-]+$/;
		var url      = /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i;
		var submit_title = submit_media = 0;
		if ( title == "" && media == "" ) {
			$( "#question-"+meta_key ).addClass( "wpeq-error" );
			$( "#media-"+meta_key ).addClass( "wpeq-error" );
		} else {
			if ( alphanum.test(title) == true || title == '' ) {
				$( "#question-"+meta_key ).removeClass( "wpeq-error" );
				submit_title = 1;
			} else {
				$( "#question-"+meta_key ).addClass( "wpeq-error" );
			}
			if ( url.test(media) == true || media == '' ) {
				$( "#media-"+meta_key ).removeClass( "wpeq-error" );
				submit_media = 1;
			} else {
				$( "#media-"+meta_key ).addClass( "wpeq-error" );
			}
		}

		if ( submit_title == 1 && submit_media == 1 ) {
			var data = {
				'action'   : 'wp_easy_quiz_ajax_call',
				'nonce'    : make_call.nonce,
				'question' : title,
				'media'    : media,
				'postid'   : postid,
				'mode'     : 'add_qustn'
			};

			$.post(make_call.ajax_url, data, function(response) {
				$('.added-quiz-qstn').html(response);
			});
		}
	});

	// Save Quiz
	$( document ).on( "click", ".save-quiz", function(){
		var meta_key = $( this ).data( "meta" );
		var title    = $( "#question-"+meta_key ).val();
		var media    = $( "#media-"+meta_key ).val();
		var submit_title = submit_media = 0;
		var options_ans = [];
		var select_ans = [];

		$( '.optn-true-'+meta_key ).each(function(i) {
			var check_ans = $( this ).prop( "checked" );
			if( check_ans == true ) {
				right_ans = 'checked';
			} else {
				right_ans = '';
			}
			select_ans.push( right_ans );
		});

		var form_submit = 1;
		var optionOk = 1;
		$( '.ans-option-'+meta_key ).each(function(i) {
			var option_val = $(this).val();
			if ( option_val == '' ) {
				$( this ).addClass( 'wpeq-error' );
				optionOk = 0;
			} else {
				$( this ).removeClass( 'wpeq-error' );
			}
			options_ans.push( {'option':option_val,'select_ans':select_ans[i]} );
		});

		if ( title == "" && media == "" ) {
			$( "#question-"+meta_key ).addClass( "wpeq-error" );
			$( "#media-"+meta_key ).addClass( "wpeq-error" );
		} else {
			if ( alphanum.test(title) == true || title == '' ) {
				$( "#question-"+meta_key ).removeClass( "wpeq-error" );
				submit_title = 1;
			} else {
				$( "#question-"+meta_key ).addClass( "wpeq-error" );
			}
			if ( url.test(media) == true || media == '' ) {
				$( "#media-"+meta_key ).removeClass( "wpeq-error" );
				submit_media = 1;
			} else {
				$( "#media-"+meta_key ).addClass( "wpeq-error" );
			}
		}

		if ( submit_title == 1 && submit_media == 1 && optionOk == 1 ) {
			var data = {
				'action'   : 'wp_easy_quiz_ajax_call',
				'nonce'    : make_call.nonce,
				'question' : title,
				'media'    : media,
				'postid'   : postid,
				'meta_key' : meta_key,
				'options'  : options_ans,
				'mode'     : 'update_qustn'
			};

			$.post(make_call.ajax_url, data, function(response) {
				$( '#quiz-panel-'+meta_key ).html( response );
			});
		}
	});

	// Add options
	$( document ).on('click', '.option-plus', function(){
		var meta_key    = $( this ).data( "meta" );
		var add_option  = $( "#add-optn-"+meta_key ).val();
		var option_true = $( "#add-optn-true-"+meta_key ).prop( "checked" );
		var option_val  = '';
		if ( option_true == true ) {
			option_val = 'checked';
		}

		var dynamic_options = '<div class="wpeq-col-6"><div class="ans-option"><div class="first-field"><input value="'+add_option+'" type="text" class="input-control ans-option-'+meta_key+'"></div><div class="last-field"><input class="optn-true-'+meta_key+'" type="checkbox" value="yes" '+option_val+'></div></div></div>';

		if ( add_option == "" || alphanum.test(add_option) == false ) {
			$( "#add-optn-"+meta_key ).addClass( "wpeq-error" );
		} else {
			$( "#add-optn-"+meta_key ).removeClass( "wpeq-error" );
			$( "#option-panel-"+meta_key+" .wpeq-row" ).append( dynamic_options );
		}
		$( "#add-optn-"+meta_key ).val('');
		$( "#add-optn-true-"+meta_key ).prop( "checked", false );
	});

	// Show hide toggle
	$( document ).on( "click", ".wpeq-expand", function(){
		var meta_key = $( this ).data( "meta" );
		var toggle_id = $( this ).data( "toggle" );
		$( toggle_id ).slideToggle( 'slow', function() {
			$(this).attr('data-expand', ($(this).attr( 'data-expand' ) == "false" ? true : false));
			var expand = $(this).attr('data-expand');
			if ( expand == 'true' ) {
				$( this ).parent().addClass( "in" );
			} else {
				$( this ).parent().removeClass( "in" );
			}
		});
		var find_class = $( this ).closest('.quiz-panel').hasClass( 'in' );
		if ( find_class ) {
			$( this ).addClass( 'dashicons-arrow-down' );
			$( this ).removeClass( 'dashicons-arrow-up' );
			$('#quiz-panel-'+meta_key+' .wpeq-title').css({"opacity":"1"});
		} else {
			$( this ).removeClass( 'dashicons-arrow-down' );
			$( this ).addClass( 'dashicons-arrow-up' );
			$('#quiz-panel-'+meta_key+' .wpeq-title').css({"opacity":"0"});
		}
	});

	// Delete option answers
	$( document ).on( "click", ".wpeq-row .dashicons-trash", function(){
		var option_key = $( this ).data( "delete_optn" );
		$( "#ans-option-"+option_key ).remove();
	});

	// Cancel form data
	$( document ).on( "click", ".cancel-quiz", function(){
		var meta_key = $( this ).data( "meta" );
		$( "#question-"+meta_key ).val("");
		$( "#media-"+meta_key ).val("");
		$( "#media-"+meta_key ).removeClass( "wpeq-error" );
		$( "#question-"+meta_key ).removeClass( "wpeq-error" );
	});

	// Question media upload
	$( document ).on("click", ".m_upload", function(){
		var m_key = $( this ).data('meta');
		if (this.window === undefined) {
			this.window = wp.media({
				title    : 'Insert a media',
				library  : {type: [ 'video', 'image' ]},
				multiple : false,
				button   : {text: 'Insert'}
			});

			var self = this; // Needed to retrieve our variable in the anonymous function below
			this.window.on('select', function() {
				var first = self.window.state().get('selection').first().toJSON();
				var data = {
					'action'   : 'wp_easy_quiz_ajax_call',
					'nonce'    : make_call.nonce,
					'media_id' : first.id,
					'mode'     : 'attchment'
				};

				$.post(make_call.ajax_url, data, function(response) {
					$( '#media-'+m_key ).val(response);
				});
				//wp.media.editor.insert('[myshortcode id="' + first.id + '"]');
			});
		}

		this.window.open();
		return false;
	});
})( jQuery );