jQuery(function($){
    $('.filter_form input[type="submit"]').click(function(){
        $('.filter_form').fadeOut(100);
    });

    $('.call_form input[type="submit"]').click(function(){
        $('.call_form').fadeOut(100);
    });
    
    // кнопка закрыть системное сообщение
    $('.errorMessage .close, .successMessage .close').click(function(){
    	$(this).parent().fadeOut(500,function(){
    		$(this).remove();
    	});
    });
    
    // для выбора типа регистрации
    $('input[name="register_type"]').change(function(){
        $('.register.inner, .register.outer').hide();
        $('.register.'+$(this).val()).show();
    });
    
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
});