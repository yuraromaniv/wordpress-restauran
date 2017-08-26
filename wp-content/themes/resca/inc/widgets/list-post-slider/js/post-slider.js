(function ($) {
	"use strict";
	$(document).ready(function () {
		$(".kuler-module").each(function () {
			var $this = jQuery(this);
			var owl = $this.find('.owl-theme');
			var $item = owl.attr("data-column-slider");
			owl.owlCarousel({
				loop          : true,
				singleItem    : false,
				autoHeight    : false,
				pagination    : false,
				stopOnHover   : true,
				navigationText: false,
				items         : $item,
				responsive: true,
				itemsDesktop : [1199,1],
				itemsDesktopSmall : [980,1]
			});
			$this.find('.next').click(function () {
				owl.trigger('owl.next');
			});
			$this.find('.prev').click(function () {
				owl.trigger('owl.prev');
			});
		});
	});
})(jQuery);
