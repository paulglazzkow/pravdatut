/**
 * @file
 * Attaches is flag-widget rating.
 */



(function ($, Drupal) {
    Drupal.behaviors.appFlag = {
        attach: function (context, settings) {
            var comment_selector = 'article.comment';
            var flag_selector = '.comment-footer-links li div';

            var comment = $(comment_selector);

            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    // if (mutation.attributeName === 'class') {

                    var attributeValue = $(mutation.target).prop('class');
                    console.log('Class attribute changed to:', attributeValue);

                    // }
                });
            });

            comment.each(function () {
                var $this = $(this);
                var flags = $this.find(flag_selector).each(function () {
                    // var $this = $(this);
                    observer.observe(this, {
                        childList: true, characterData: true
                    });
                })
            })

        }
    };
})(jQuery, Drupal);
