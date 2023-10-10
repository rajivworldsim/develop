require(['jquery', 'jquery/ui'], function($){
    $('#include_sim:checkbox').change(function(){
        if($(this).is(":checked")) {                   
            $('#include_simcard_text').addClass("menuitemshow");
        } else {
            $('#include_simcard_text').removeClass("menuitemshow");
        }
    });
    $('.clickAcc').click (function(){
        $(this).next('div').slideToggle();
    })
    $(document).ready(function() {
        $('.toggleAcc').click(function() { 
            var $this = $(this);
            $('li').removeClass('arrowMine')                      
            if ($this.next().hasClass('showAcc')) {
                $this.next().removeClass('showAcc');
                $this.next().slideUp(350);
            } else {
                $this.parent().parent().find('li .inner').removeClass('showAcc');
                $this.parent().parent().find('li .inner').slideUp(350);
                $this.next().toggleClass('showAcc');
                $this.next().slideToggle(350);
                $this.parent('li').toggleClass('arrowMine') 
            }
        });
    });
});
            