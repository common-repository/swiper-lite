jQuery(function($){

	let likedPosts = [];

	let bodyClass = $('body').attr('class').replace(/\s/g,'_').trim();

	if (Cookies.get(`likedPosts_${bodyClass}`)){
		likedPosts = JSON.parse(Cookies.get(`likedPosts_${bodyClass}`));
		renderLikedPosts();
		for (let i = 0; i<likedPosts.length; i++) {
			$('.postswiper-posttitle').each(function(){
				let thisTitle = $(this).html();
				if (thisTitle==likedPosts[i]){
					$(this).parent().parent().hide();
				}
			});
		}
	}

	$('.postswiper-post').on('swiperight',function(){
		if ( !$(this).hasClass('rot-left') && !$(this).hasClass('rot-right') ){
			$(this).addClass('rot-left');
			$('.postswiper-post').find('.status').remove();

			$(this).append('<div class="status like">Like!</div>');

			let postTitle = $(this).find('.postswiper-posttitle').html();
			likedPosts.push(postTitle);
			Cookies.set(`likedPosts_${bodyClass}`,JSON.stringify(likedPosts), {
				expires: 9999
			});
			renderLikedPosts();
		}
	});

	$('.postswiper-post').on('swipeleft',function(){
		if ( !$(this).hasClass('rot-left') && !$(this).hasClass('rot-right') ){
			$(this).addClass('rot-right');
			$('.postswiper-post').find('.status').remove();
			$(this).append('<div class="status dislike">Dislike!</div>');
		}

	});

	$('#swiper-start-again').on('click',function(e){
		e.preventDefault();
		$('.postswiper-likedlist-list').slideUp();
		Cookies.remove(`likedPosts_${bodyClass}`);
		$('.postswiper-post').each(function(){
			$(this).hide();
			$(this).find('.status').remove();
			$(this).removeClass('rot-left');
			$(this).removeClass('rot-right');
			$(this).fadeIn();
		});
		likedPosts = [];
		renderLikedPosts();
		$('.postswiper-likedlist-opener').addClass('disabled').removeClass('toggle-open');
		$('.postswiper-likedlist-count').text('View my liked items (0)');
	});

	$('.postswiper-likedlist-opener').on('click',function(){
		if (likedPosts.length > 0) {
			if ( $(this).hasClass('toggle-open') ){
				$(this).removeClass('toggle-open');
				$('.postswiper-likedlist-list').slideUp();
			} else {
				$(this).addClass('toggle-open');
				$('.postswiper-likedlist-list').slideDown();
			}
		}
	});

	function renderLikedPosts(){
		$('.postswiper-likedlist-list').html('');
		for (let i = 0; i<likedPosts.length; i++) {
			$('.postswiper-likedlist-list').append('<div class="postswiper-likedlist-liked">' + likedPosts[i] + '</div>');
			$('.postswiper-likedlist-count').text('View my liked items (' + likedPosts.length + ')');
		}
		if ( $('.postswiper-likedlist-opener').hasClass('disabled') ) {
			$('.postswiper-likedlist-opener').removeClass('disabled');
		}
	}

	function resizeWrapper(){
		let postHeight = 0;
		$('.postswiper-post').each(function(){
			let thisHeight = parseInt($(this).height());
			if (thisHeight > postHeight) {
				postHeight = thisHeight;
			}
		});
		$('.postswiper-wrapper').css('height',postHeight+60);
		$('.postswiper-post').css('height',postHeight+30);
	}

	$(window).resize(function(){
		resizeWrapper();
	});

	resizeWrapper();

});
