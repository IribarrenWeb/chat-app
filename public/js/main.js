(function ($) {
    "use strict";

    const url = window.location.host

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(e){
        e.preventDefault();
        var check = true;
        const option = $(this).data('option');

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        if(check){
            let data = new FormData($(this)[0])

            let urlPost = ''

            if(option == 'registro'){
                urlPost = '/register'
            }else{
                urlPost = '/login'
            }

            $.ajax({
                type: "POST",
                url: urlPost,
                data: data,
                contentType : false,
                processData : false,
                dataType: 'json',
                success: function (response) {
                    if(!response.status){
                        if(option != 'registro')
                        {
                            input.parent('.validate-input').attr('data-validate',response.msg)
                            showValidate(input)
                        }else{
                            for (let index = 0; index < input.length; index++) {
                                
                                const inputName = input[index].name;

                                for(let element in response.errors){
                                    if(element == inputName){
                                        console.log('siii',input[index])
                                        let inputVal = input[index]
                                        let value = response.errors[element]
                                        console.log(element)
                                        $(inputVal).parents('.validate-input').attr('data-validate',value)
                                        showValidate(input[index]);
                                    }    
                                }
                                
                            }
                        }

                        check = false
                    }else{

                        window.location.assign('/home')
                        check = true

                    }
                }
            });
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }
    
    

})(jQuery);