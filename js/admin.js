jQuery(function($){

	let postType;
	let category;
	let style;
	let limit;
	let orderby;
	let order;

	$('#post-type-select').on('change focus', function(){
		postType = $(this).val();
		if ( postType == "posts" ) {
			$('#post-categories').show();
		} else if ( postType == "pages" ) {
			$('#post-categories').hide();
		} else if ( postType == "products" ) {
			$('#post-categories').hide();
		}
	});

	$('#style-select').on('change focus', function(){
		style = $(this).val();
		if ( style != "" ) {
			$('.postswiper-wrapper').attr('class','postswiper-wrapper').addClass(style);
		} else {
			$('.postswiper-wrapper').attr('class','postswiper-wrapper');
		}
	});

	$('#shortcode-generator input, #shortcode-generator select').on('change focus', function(){
		updateShortcode();
	});

	function updateShortcode(){

		postType = $('#post-type-select').val();

		if ( postType == "posts" ) {
			category = $('#post-category-name').val().toLowerCase();
		}

		style = $('#style-select').val();
		limit = $('#limit-num').val();
		orderby = $('#post-sort-select').val();
		order = $('#order-select').val();

		var shortcode = '[swiper post_type="' + postType + '"';

		if ( category != '' ) {
			shortcode += ' category="' + category + '"';
		}

		if ( style != '' ) {
			shortcode += ' style="' + style + '"';
		}

		if ( limit != 0 ) {
			shortcode += ' limit="' + limit + '"';
		}

		if ( orderby != '' ) {
			shortcode += ' orderby="' + orderby + '"';
		}

		if ( order != '' ) {
			shortcode += ' order="' + order + '"';
		}

		shortcode += ']';

		$('#shortcode-output').val( shortcode );
	}

	$(document).ready(function(){
		updateShortcode();
	});

	function moveCopiedLabel() {
		$('#shortcode-copied').css({'top':$('#shortcode-output').position().top,'left':$('#shortcode-output').position().left,'width':$('#shortcode-output').outerWidth(),'height':$('#shortcode-output').outerHeight(true)});
	}

	$(window).on('load resize',function(){
		moveCopiedLabel();
	});

	$('#copy-shortcode').on('click', function(){
		moveCopiedLabel();
		$('#shortcode-output').select();
		document.execCommand("copy");
		$('#shortcode-copied').show().delay(1000).fadeOut('slow');
		$('#shortcode-output').blur();
	});

});
