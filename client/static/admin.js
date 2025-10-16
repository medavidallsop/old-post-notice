jQuery(document).ready(function ($) {
	// Translation.
	const { __, _x, _n, _nx, sprintf } = wp.i18n;

	// Color picker.
	if ($('.old-post-notice-color-picker').length > 0) {
		$('.old-post-notice-color-picker').wpColorPicker();
	}

	// Old posts.
	if ($('#old-post-notice-old-posts-table').length > 0) {
		var oldPosts = jQuery.ajax({
			data: {
				action: 'old_post_notice_old_posts',
				nonce: oldPostNotice.nonce,
				type: $('#old-post-notice-old-posts-table').attr('data-type'),
			},
			method: 'POST',
			url: oldPostNotice.ajaxUrl,
		});

		oldPosts.done(function (response) {
			// Populate the table with the old posts.
			if (response.success) {
				var oldPosts = response.data;
				var oldPostsExist = false;
				var oldPostsRows = '';

				for (var i = 0, len = oldPosts.length; i < len; ++i) {
					var oldPost = oldPosts[i];
					var oldPostsRows =
						oldPostsRows +
						'<tr><td><a href="' +
						oldPost.url +
						'" target="_blank">' +
						oldPost.title +
						'</a></td><td>' +
						oldPost.published +
						'</td><td>' +
						oldPost.modified +
						'</td>';
					var oldPostsExist = true;
				}

				if (oldPostsExist == true) {
					$('#old-post-notice-old-posts-table tbody').html(
						oldPostsRows
					);
				} else {
					$('#old-post-notice-old-posts-table tbody').html(
						'<tr><td colspan="100%">' +
							__('No old posts.', 'old-post-notice') +
							'</td></tr>'
					);
				}
			} else {
				// Handle error case.
				var errorMessage =
					response.data && response.data.message
						? response.data.message
						: __('Error.', 'old-post-notice');
				$('#old-post-notice-old-posts-table tbody').html(
					'<tr><td colspan="100%">' + errorMessage + '</td></tr>'
				);
			}
		});
	}
});
