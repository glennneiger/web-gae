jQuery(function() {

	var r = Raphael('map', 610, 387), attributes = {
		fill : '#b8e69e',
		'fill-opacity' : 0,
		stroke : '#b5c8d6',
		'stroke-opacity' : 0,
		'stroke-width' : 0,
		'stroke-linejoin' : 'round'
	},

	issueJson = "";
	jQuery.ajax( {
		type : "POST",
		url : "ajax.php",
		data : "action=get_issue_detail",
		dataType : "json",
		success : function(data) {
			issueJson = data;
		}
	});

	// alert(arIssueDetail);

	arr = new Array();
	arActiveIssue = new Array();

	var i = 0;
	for ( var country in paths) {

		var obj = r.path(paths[country].path);

		obj.attr(attributes);

		arr[obj.id] = country;

		if (paths[country].active_issue) {

			arActiveIssue[i] = country;
			i++;
			obj.attr( {
				cursor : 'pointer'
			});

			obj.click(function() {

						var point = this.getBBox(0);

						jQuery('#map').next('.point').remove();

						jQuery('#map').after(
								jQuery('<div />').addClass('point'));

						if (paths[arr[this.id]].active_issue == true) {
							jQuery('#shadow').css( {
								height : jQuery(document).height()
							});
							jQuery('#shadow').show();
						}

						jQuery('.point')
								.html(paths[arr[this.id]].name)
								.prepend(
										"<div id='capture_result'></div><input type='hidden' id='current_issue_name' value='"
												+ arr[this.id] + "' />")
								.prepend(
										jQuery('<a />').attr('href', '#')
												.addClass('close')
												.text('Close')).fadeIn();
						/*if (paths[arr[this.id]].active_issue == false) {*/
							var map_position = jQuery("#map").position();

							jQuery('.point').css({
								left : map_position.left ,
								top : map_position.top 
							})
						//}
						if (jQuery('#sub_capture_email')) {
							jQuery('#sub_capture_email')
									.click(
											function() {
												var issue_name = jQuery(
														'#current_issue_name')
														.val();
												var email = jQuery(
														'#capture_email').val();
												var issue_id = issueJson[issue_name].issue_id;
												jQuery
														.ajax( {
															type : "POST",
															url : "ajax.php",
															data : "action=capture_email&issue_id="
																	+ issue_id
																	+ "&email="
																	+ email,
															dataType : "json",
															success : function(
																	data) {
																jQuery(
																		'#capture_result')
																		.html(
																				data.msg);
															}
														});

											});
						}
						if (jQuery('#downlod_issue')) {
							jQuery('#downlod_issue')
									.click(
											function() {
												var issue_name = jQuery(
														'#current_issue_name')
														.val();
												var subscription_def_id = issueJson[issue_name].subscription_def_id;
												var oc_id = issueJson[issue_name].oc_id;
												var oc_item_type = issueJson[issue_name].orderItemType;
												var product_name = issueJson[issue_name].product_name;
												var product_type = issueJson[issue_name].product_type;
												var email = jQuery(
														'#capture_email').val();
												checkcart(subscription_def_id,
														oc_id, oc_item_type,
														product_name,
														product_type, 'Landed');

											});
						}

					});
		}
		jQuery('.point').find('.close').live('click', function() {
			var t = jQuery(this), parent = t.parent('.point');

			parent.fadeOut(function() {
				parent.remove();
			});
			jQuery('#shadow').hide();
			return false;
		});
	}
	for (i = 0; i < arActiveIssue.length; i++) {
		jQuery('li[map_name="' + arActiveIssue[i] + '"]')
				.click(
						function() {

							var title = jQuery(this).attr('map_name');							
							
							jQuery('#map').next('.point').remove();

							jQuery('#map').after(
									jQuery('<div />').addClass('point'));

							jQuery('#shadow').css( {
								height : jQuery(document).height()
							});
							jQuery('#shadow').show();

							var map_position = jQuery("#map").position();							
							jQuery('.point').css( {
								left : map_position.left ,
								top : map_position.top 
							})
							
							jQuery('.point')
									.html(paths[title].name)
									.prepend(
											"<div id='capture_result'></div><input type='hidden' id='current_issue_name' value='"
													+ title + "' />").prepend(
											jQuery('<a />').attr('href', '#')
													.addClass('close').text(
															'Close')).fadeIn();

							if (jQuery('#sub_capture_email')) {
								jQuery('#sub_capture_email')
										.click(
												function() {

													var issue_name = jQuery(
															'#current_issue_name')
															.val();
													var email = jQuery(
															'#capture_email')
															.val();
													if (email == ''
															|| validateEmail(email) == false) {
														jQuery(
																'#capture_result')
																.html(
																		'Please enter a valid Email');
													} else {
														var issue_id = issueJson[issue_name].issue_id;
														jQuery
																.ajax( {
																	type : "POST",
																	url : "ajax.php",
																	data : "action=capture_email&issue_id="
																			+ issue_id
																			+ "&email="
																			+ email,
																	dataType : "json",
																	success : function(
																			data) {
																		jQuery(
																				'#capture_result')
																				.html(
																						data.msg);
																	}
																});
													}

												});
							}
							if (jQuery('#downlod_issue')) {
								jQuery('#downlod_issue')
										.click(
												function() {
													var issue_name = jQuery(
															'#current_issue_name')
															.val();
													var subscription_def_id = issueJson[issue_name].subscription_def_id;
													var oc_id = issueJson[issue_name].oc_id;
													var oc_item_type = issueJson[issue_name].orderItemType;
													var product_name = issueJson[issue_name].product_name;
													var product_type = issueJson[issue_name].product_type;
													var email = jQuery(
															'#capture_email')
															.val();
													checkcart(
															subscription_def_id,
															oc_id,
															oc_item_type,
															product_name,
															product_type,
															'Landed');

												});
							}
							var $target = jQuery("#hmr_middle");
							var targetOffset = ($target.offset().top - 20);
							jQuery('html,body').animate( {
								scrollTop : targetOffset
							}, 1800);

						});
	}
	if (jQuery("#product_id").val() != "") {
		var $target = jQuery("#hmr_login_panel");
		var targetOffset = ($target.offset().top - 20);
		jQuery('html,body').animate( {
			scrollTop : targetOffset
		}, 1800);
	}

	jQuery("#limited_offer").click(function() {
		jQuery('#hmr_login_panel').show();
		var $target = jQuery("#hmr_login_panel");
		var targetOffset = ($target.offset().top - 20);
		jQuery('html,body').animate( {
			scrollTop : targetOffset
		}, 1800);
	});
});
