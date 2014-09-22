/**
 * It is using for visually disabling UI elements and prevent them from all mouse related evetns
 * Details here: https://github.com/trivially/blindover
 */
(function($) {
  /* obscure content with blind */
  $.fn.blindOver = function() {
    this.each(function(i,el) {
      el = $(el);
      el.addClass('blinded');
      if (!el.is('body') && !(el.css('position') === 'absolute')) {
        el.css({position:'relative'});
      }
      var blind = $('<div class="blindover"></div>');
      blind.css({
        height: el.height()+'px',
        width: el.width()+'px',
        background: '#FFF',
        position: 'absolute',
        top: 0,
        left: 0,
        'z-index': 10000,
        opacity: 0.5
      });
      el.prepend(blind);
    });
    return this;
  }
})(jQuery);