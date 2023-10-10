require(['jquery', 'owlcarousel'], function($){

    $(document).ready(function() { 
    
        $('.brand-logo .owl-carousel').owlCarousel({
            margin: 5,
            nav: true,
            loop: true,
            responsive: {
                0: {  items: 1},
                600: { items: 3 },
                1000: { items: 6 }
            }
        });
        
            $('.partnership-logo .owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            loop: true,
            responsive: {
                0: {  items: 1},
                600: { items: 3 },
                1000: { items: 5 }
            }
        });
    
        $('.awards-section .owl-carousel').owlCarousel({
            margin: 0,
            nav: true,
            loop: false,
            responsive: {
                0: {  items: 1},
                600: { items: 3 },
                1000: { items: 5 }
            }
        });
    
        $('.blogListing  .owl-carousel').owlCarousel({
            margin: 15,
            nav: true,
            loop: false,
            responsive: {
                0: {  items: 2},
                600: { items: 2 },
                1000: { items: 4 }
            }
        });
        $('.homeWorldsim .owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            loop: true,
            responsive: { 0: { items: 2 },
                600: { items: 3},
                1000: { items: 6}
            }
        });
        $('.simCardslider .worldSlider .owl-carousel, .product-detail-include .worldSlider .owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            loop: true,
            responsive: { 0: { items: 1 },
                600: { items: 1},     
                1000: { items: 1}
            }
        });
    
        $('.autoslider .owl-carousel').owlCarousel({
            margin: 30,
            nav: true,
            loop: true,
            responsive: { 0: { items: 1 },
                600: { items: 2 },
                1000: { items: 3 }
            }
        });
        $('.leftsimCard .worldSlider .owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            loop: true,
            responsive: {0: { items: 1  },
                600: {items: 1},
                1000: { items: 1}
            }
        });
		
		$('.NewWorldListing .owl-carousel').owlCarousel({
		margin: 10,
		nav: true,
		autoplay:true,
		loop: true,
		responsive: {0: { items: 1  },
			600: {items: 1},
			1000: { items: 1}
		}
		});
		
		$('.bestWorldListing .owl-carousel').owlCarousel({
		margin: 10,
		nav: true,
		autoplay:true,
		loop: true,
		responsive: {0: { items: 1  },
			600: {items: 1},
			1000: { items: 1}
		}
		});
		
        $('.worldSlider .owl-carousel').owlCarousel({
            margin: 10,
            nav: true,
            loop: true,
            responsive: {0: { items: 2  },
                600: {items: 1},
                1000: { items: 1}
            }
        }); 
    
        $('.showMenu, .closeNav') .click(function(){
            $('nav').toggleClass('showNav');
        }) 
        $('nav .navication li a') .click(function(){
            $(this).next ('.navDropdown').toggleClass('showDropdwon');
        })
        $('.closeDrovNav, .closeNav').click(function() {     
            $('.navDropdown').removeClass('showDropdwon'); 
        })
        $(window).scroll(function () {
            if ($(this).scrollTop() > 200) {
                $('.top-slide').addClass('showslide');
            } else {
                $('.top-slide').removeClass('showslide');
            }
        });
        $('.top-slide').on('click', function () {
    
            $('html, body').animate({                
                scrollTop: 0 }, 600);  return false;
        });
    
        $('.ftpHeading').click(function(){
            $('.ftpHeading').removeClass('icon1')
            $('.footerlinks').slideUp('slow');  
            $(this).addClass('icon1');
            $(this).next('.footerlinks').slideDown('slow');
        }) 
    
        $('.continueBtn').click(function(){ 
            $('.cookiesBlog').removeClass('showcookies')         
        });
    
        $('.simaccording  .according, .creditBlog .title ').click(function(){
            $(this).toggleClass('showarrow')
            $(this).next('.delivery-content, .card').slideToggle('slow')         
        })
    
    });   
    $(window).on('load', function () {
        setTimeout(function(){
            $('#windowloadPopup').modal('show');
            }, 1000);
    
    });
    
    var x=0;
    var element = document.getElementById("value"); 
    var elementnew = document.getElementById("valueedit"); 
    function numberup(){
        element.value = ++x;
        elementnew.value = ++x;
    }
    function numberdown(){
        element.value = --x;
        elementnew.value = --x;
    }
    $('.filter').click(function(){
        $('.leftblock').toggleClass('showfilter') 
    
    })
    $('.filterClose').click(function(){
        $('.leftblock').toggleClass('showfilter') 
    
    })
    $('.comparisontable ul li.legend').click(function(){
    
        $ (this).parent ('ul').toggleClass('slow')
    })
    
    $(document).ready(function(){
        $( "a.scrollLink" ).click(function( event ) {
            event.preventDefault();
            $("html, body").animate({ scrollTop: $($(this).attr("href")).offset().top }, 500);
        });
    
        if ($(window).width() <= 766) {
            $(window).scroll(function(){
                var ws = $(window).scrollTop();
                if(ws > 100 ) {
                   $('.headerNav').addClass('navScroll')
                }
                else{
                  $('.headerNav').removeClass('navScroll')   
                }
            });
        };
    });

});
