
$(window).on("load", function () {
  setTimeout(function(){
      $('#loading').fadeOut('slow', function () {});
  },1000);
});


var nav = $('.navbar');
var navHeight = nav.outerHeight();

$(document).ready(function(){
  $('a[href*="#"]:not([href="#"])').on("click", function () {
    var href = this.hash;
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var hash = $(this.hash);
      var target = hash.length ? hash : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: (target.offset().top - navHeight + 5)
        }, 850, function(){
          $('.navbar-collapse').collapse('hide');
        });
        return false;
      }
    }
  });
  // Back to top button
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
  });
  $('.back-to-top').click(function(){
      $('html, body').animate({scrollTop : 0},1500, function(){
        // window.location.hash = href;
      });
    return false;
  });

});

function resizeContentToMin() {
    var mHeight = window.innerHeight;
    var cHeight = $(".overflow-container").height();

    if (cHeight < mHeight) {
        $(".overflow-container").height(mHeight-160);
    }
}

// Alert
function message() {
  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, Confirm'
  }).then(function() {
    swal(
      'Success!',
      'Your Sugestion have been submit.',
      'success'
    )
  })
}

function showLoading() {
  $('#loading').fadeIn('slow', function () {});
}

function hideLoading() {
  $('#loading').fadeOut('slow', function () {});
}