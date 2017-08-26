jQuery(function ($) {
	product_slider_select();
	function product_slider_select() {
		/* sticky config */
		$('.thim-widget-field-product_slider_cats').hide();
		$('.thim-widget-field-product_slider_show .thim-widget-select').on('change',function () {
			if ($(this).val() == "category") {
				$('.thim-widget-field-product_slider_cats').slideDown(300, 'linear');
			} else {
				$('.thim-widget-field-product_slider_cats').slideUp(300, 'linear');
			}
		}).trigger('change');
	}
});