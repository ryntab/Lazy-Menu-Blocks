(function ($) {
    $(document).ready(function () {
        $('.menu-item-has-block').mouseenter(function hovering(){
            if ($(this).hasClass('loaded')) return;
            fetch(`${window.location.origin}/wp-json/lazyMenu/UX/block/${$(this).find('.so-lazy').data('block')}`)
            .then(response => response.json())
            .then(data => {
                $(this).addClass('loaded').find('.sub-menu').html(data.block).find('.bg-loaded').remove();
            })
        });
    });
})(jQuery);
