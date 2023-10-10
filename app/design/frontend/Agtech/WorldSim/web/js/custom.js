require(['jquery', 'mageplaza/core/owl.carousel'], function($){

    $(document).ready(function() { 
	setTimeout(function(){
        var main_slider_left =$("div#banner-slider-carousel .owl-carousel").find("button.owl-prev span");
        var main_slider_right = $("div#banner-slider-carousel .owl-carousel").find("button.owl-next span");
        main_slider_left.html("");
        main_slider_right.html("");
        main_slider_left.addClass("fa fa-angle-left");
        main_slider_right.addClass("fa fa-angle-right");
	},500);
    $('.brand-logo .owl-carousel').owlCarousel({
        margin: 5,
        nav: false,
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
	$('.slideSpecial .owl-carousel').owlCarousel({
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
    $('nav .navication li span') .click(function(){
        $(this).next ('.navDropdown').toggleClass('showDropdwon');
    })
    $('.closeDrovNav, .closeNav').click(function() {     
        $('.navDropdown').removeClass('showDropdwon'); 
    })
    if ($(window).width() <= 980) {
        $( ".navication .form.minisearch .btn.btn-primary" ).html('<i class="fa fa-search" aria-hidden="true"></i>');
        $( ".panel.header .form.minisearch" ).remove();
        $( ".ammenu-header-container  .action.toggle.switcher-trigger strong" ).append('<i class="fa fa-angle-down" aria-hidden="true"></i>');
        $('nav .navication ul >li> a').click(function() { 
            $(this).parent('').removeClass('minsIcon')
            var $this = $(this); 
            if ($this.next().hasClass('showdrop')) {
                $this.next().removeClass('showdrop');
                $this.next().slideUp(350);  
                $(this).parent('').removeClass('minsIcon') 
                
            } else {
                $this.parent().parent().find('.navDropdown').removeClass('showdrop');
                $this.parent().parent().find('.navDropdown').slideUp(350);
                $this.next().toggleClass('showdrop');
                $this.next().slideToggle(350);
                $(this).parent('').addClass('minsIcon')
                $(this).parent('').siblings().removeClass('minsIcon');
            }

        });  
    }
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
		
	$("button#sim_credit_add_btn_orig,#sim_credit_add_btn_orig_n").click(function(){
	$("button#product-addtocart-button").trigger("click");
	var interval_add_tocart = setInterval(function(){
		var interval_add_tocart_disable = $("button#product-addtocart-button").html();
			$("button#sim_credit_add_btn_orig").html(interval_add_tocart_disable);
		if(!$("button#product-addtocart-button").hasClass("disabled")){
			clearInterval(interval_add_tocart);
		}
		},100);
	});
    $("button.btnnew.number-up").click(function(){
        var input_creditqty = $("input#creditQty").val();
        input_creditqty_increment =  parseInt(input_creditqty) + 1;
        $("input#creditQty").val(input_creditqty_increment);
        $("input#creditQty").change();
    });
    $("button.btnnew.number-down").click(function(){
        var input_creditqty = $("input#creditQty").val();
        if(parseInt(input_creditqty) > 1){
        var input_creditqty_decre =  parseInt(input_creditqty) - 1;
        $("input#creditQty").val(input_creditqty_decre);
            $("input#creditQty").change();
        }
    });
    $("input#creditQty").change(function(){
      var change_add_cart = $("input#creditQty").val();
        $(".box-tocart input#qty").val(change_add_cart);
    });
    /**** Check for additional usa is added or not ***/
	if($("button#sim_credit_add_btn").length){
    $("button#sim_credit_add_btn").show();
    }
    else{
        $("button#sim_credit_add_btn_orig").show();
    }
	
});
  
$(".faq-page a.faq-categories").click(function(){
    $(".tab-content-1").hide();
    $(this).next().show();  
    $("a.faq-categories").removeClass("active");
    $(this).addClass("active");
});
$('a.faq-categories').first().click();

    setTimeout(function(){
        $("body.mpblog-post-index").removeClass("page-layout-2columns-left");
        $("body.mpblog-post-index").addClass("page-layout-1column");  
      },1000);
    var body_padding = $("header#ammenu-header-container").height();
    $("body").css("padding-top",body_padding + 1);


    $( document ).ready(function() {
    //wait until the last element (.payment-method) being rendered
        var existCondition = setInterval(function() {
        if ($('.payment-method').length) { 
        clearInterval(existCondition);
        runMyFunction();
        }
        }, 100);

        function runMyFunction(){
            $('#shipping div[name="shippingAddress.createaccount"] input').click(function(){
                if($(this).is(":checked")){
                $('div[name="shippingAddress.custom_attributes.password"]').show();
                $('div[name="shippingAddress.custom_attributes.confpassword"]').show();
                }
                else{
                 $('div[name="shippingAddress.custom_attributes.password"]').hide();
                $('div[name="shippingAddress.custom_attributes.confpassword"]').hide();
                }
            });
        }
    }); 	
});
