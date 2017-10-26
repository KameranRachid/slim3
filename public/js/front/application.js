$(document).ready(function() {
  $(".animsition").animsition({
    inClass: 'zoom-in',
    outClass: 'fade-out',
    inDuration: 1500,
    outDuration: 800,
    linkElement: '.animsition-link',
    // e.g. linkElement: 'a:not([target="_blank"]):not([href^="#"])'
    loading: true,
    loadingParentElement: 'body', //animsition wrapper element
    loadingClass: 'animsition-loading',
    loadingInner: '', // e.g '<img src="loading.svg" />'
    timeout: false,
    timeoutCountdown: 5000,
    onLoadEvent: true,
    browser: [ 'animation-duration', '-webkit-animation-duration'],
    // "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
    // The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
    overlay : false,
    overlayClass : 'animsition-overlay-slide',
    overlayParentElement : 'body',
    transition: function(url){ window.location.href = url; }
  });

  $("form#newsLetter").on('submit', function(e) {
    e.preventDefault();
    FrontActions.newsletter($(this));
  });

});

var FrontActions = {
  newsletter: function(form) {
      $.ajax({
          url: baseUrl + '/newsletter',
          type: 'POST',
          data: {
              person_name: form.find("input[name=person_name]").val(),
              email: form.find("input[name=email]").val()
          },
          success: function (data) {
              if(data === true ){
                  $(document).find('.alert-success').removeClass('hidden-xs-up');
                  $(document).find('.alert-danger').addClass('hidden-xs-up');
              }else{
                  $(document).find('.alert-danger').removeClass('hidden-xs-up');
                  $(document).find('.alert-success').addClass('hidden-xs-up');
              }
          }
      });
  }
};


// back to top button - docs
$(function () {
  if ($('.docs-top').length) {
    _backToTopButton()
    $(window).on('scroll', _backToTopButton)
    function _backToTopButton () {
      if ($(window).scrollTop() > $(window).height()) {
        $('.docs-top').fadeIn()
      } else {
        $('.docs-top').fadeOut()
      }
    }
  }

  // doc nav js
  var $toc = $('#markdown-toc')
  var $window = $(window)

  if ($toc[0]) {
    $('#markdown-toc li').addClass('nav-item')
    $('#markdown-toc li > a').addClass('nav-link')

    maybeActivateDocNavigation()
    $window.on('resize', maybeActivateDocNavigation)

    function maybeActivateDocNavigation () {
      if ($window.width() > 768) {
        activateDocNavigation()
      } else {
        deactivateDocNavigation()
      }
    }

    function deactivateDocNavigation() {
      $window.off('resize.theme.nav')
      $window.off('scroll.theme.nav')
      $toc.css({
        position: '',
        left: '',
        top: ''
      })
    }

    function activateDocNavigation() {

      var cache = {}

      function updateCache() {
        cache.containerTop   = $('.docs-content').offset().top
        cache.containerRight = $('.docs-content').offset().left + $('.docs-content').width() + 40
        measure()
      }

      function measure() {
        var scrollTop = $window.scrollTop()
        var distance =  Math.max(scrollTop - cache.containerTop, 0)

        if (!distance) {
          $($toc.find('li a')[1]).addClass('active')
          return $toc.css({
            position: '',
            left: '',
            top: ''
          })
        }

        $toc.css({
          position: 'fixed',
          left: cache.containerRight,
          top: 0
        })
      }

      updateCache()

      $(window)
        .on('resize.theme.nav', updateCache)
        .on('scroll.theme.nav', measure)

      $('body').scrollspy({
        target: '#markdown-toc',
        children: 'li > a'
      })

      setTimeout(function () {
        $('body').scrollspy('refresh')
      }, 1000)
    }
  }
})
