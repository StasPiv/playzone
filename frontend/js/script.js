jQuery(function($){
	// клик на лого
	$('.header .logo').click(function(){
		window.location.href = '/';
	})
	
    // всплывайка для фильтра партий
    $('.apply_filter a').click(function(){
        $('.footer .overlay').fadeIn(100,function(){
            $('.filter_form').fadeIn(100);
        })
        return false;
    })
    $('.footer .overlay, .filter_form input[type="submit"]').click(function(){
        $('.footer .overlay').fadeOut(100,function(){
            $('.filter_form').fadeOut(100);
        })
    })
    
    // всплывайка для вызова на партию
    $('.apply_call a').click(function(){
        $('.footer .overlay').fadeIn(100,function(){
            $('.call_form').fadeIn(100);
        })
        return false;
    })
    $('.footer .overlay, .call_form input[type="submit"]').click(function(){
        $('.footer .overlay').fadeOut(100,function(){
            $('.call_form').fadeOut(100);
        })
    })
    
    // кнопка закрыть системное сообщение
    $('.errorMessage .close, .successMessage .close').click(function(){
    	$(this).parent().fadeOut(500,function(){
    		$(this).remove();
    	});
    })
    
    // для выбора типа регистрации
    $('input[name="register_type"]').change(function(){
        $('.register.inner, .register.outer').hide();
        $('.register.'+$(this).val()).show();
    })
    
    // для выпадающего меню
    $('.header .menu ul li').hover(
        function()
        {
            globalThis = $(this);
            setTimeout(
              function(){
                $(globalThis).find('ul').fadeIn(200)
              },
              0  
            );
        },
        function()
        {
            $(this).find('ul').fadeOut(200);
        }
    );
    
    // делаем сообщение прочитанным
    $('.successMessage.read .close').click(
        function()
        {
            $.get('/event/markread/'+$(this).attr('read')+'/');
        }
    );
    
    // по переходу в системном сообщении клацаем закрыть
    $('.successMessage.read a').click(
        function()
        {
            //$(this).parents('.successMessage.read').find('.close').click();
        }
    );
    
    // оформление турнирной таблицы
    $('.tournament_table a').each(function(){
        if($(this).html() == 1)
        {
            $(this).css('color','red').css('font-weight','bold');
        }
        else if($(this).html() == 0)
        {
            $(this).css('color','black').css('font-weight','bold');
        }
        else if($(this).html().indexOf('½') > -1)
        {
            $(this).css('color','green').css('font-weight','bold');
        }
    })
})