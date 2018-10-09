/**
 * @file
 * Attaches is flag-widget rating.
 */



(function ($, Drupal) {
    Drupal.behaviors.likeRating = {
        attach: function (context, settings) {
            function getWidget($label, $style) {
                $style = $style === 'default' ? 'heart' : $style;

                $button = '<a href="#" title="' + $label + '"><i class="fa fa-' + $style + '"></i></a>';
                return '<div class="flag-widget-rating">' + $button + '</div>';
            }

            function setWidgetClass($widget, $value) {
                if ($value === 1) {
                    $widget.find('a').addClass('flag-widget-on');
                } else {
                    $widget.find('a').removeClass('flag-widget-on');
                }
            }

            $('body').find('.flag-widget').each(function () {
                var $this = $(this);
                $(this).find('select').once('processed').each(function () {
                    $this.find('[type=submit]').hide();
                    var $select = $(this);
                    var isPreview = $select.data('is-edit');

                    $widget = getWidget($select.data('style'));
debugger;
                    $select.after($widget).hide();

                    $this.find('.flag-widget-rating a').eq(0).each(function () {

                        setWidgetClass($this, $select.get(0).selectedIndex);


                        $(this).bind('click', function (e) {
                            if (isPreview) {
                                return;
                            }
                            e.preventDefault();
                            $select.get(0).selectedIndex = +!$select.get(0).selectedIndex;

                            $this.find('[type=submit]').trigger('click');
                            setWidgetClass($this, $select.get(0).selectedIndex);

                            $this.find('.vote-result').html();
                        })
                    })

                })
            });
        }
    };
})(jQuery, Drupal);
