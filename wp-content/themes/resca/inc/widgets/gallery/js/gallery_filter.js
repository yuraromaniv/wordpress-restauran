jQuery(function ($) {
 	$(".fancybox").fancybox();
	// filter selector
	$(".filter").on("click", function () {
		var $this = $(this);
		if ( !$this.hasClass("active") ) {
			$(".filter").removeClass("active");
			$this.addClass("active"); // set the active tab
			// get the data-rel value from selected tab and set as filter
			var $filter = $this.data("rel");
			$filter == 'all' ?
				$(".fancybox")
					.attr("data-fancybox-group", "gallery")
					.not(":visible")
					.fadeIn()
				: // otherwise
				$(".fancybox")
					.fadeOut(0)
					.filter(function () {
						return $(this).data("filter") == $filter;
					})
					.attr("data-fancybox-group", $filter)
					.fadeIn(1000);
		}
	});
});