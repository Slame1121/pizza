$('document').ready(function(){
    $('#reviewsAdd').on('click',function () {
        if(valid.valid('#reviewsAddForm')){
            var action = $('#reviewsAddForm #action').val();
            var param = $('#reviewsAddForm input, #reviewsAddForm textarea').serializeArray();
            $.ajax({
                url: action,
                type: 'post',
                data: param,
                dataType: 'json',
                crossDomain: true,
                // beforeSend: function() {
                //     $('select[name=\'currency\']').prop('disabled', true);
                // },
                // complete: function() {
                //     $('select[name=\'currency\']').prop('disabled', false);
                // },
                success: function(json) {
                    console.log('ret ' + json);
                    if(json.success){
                        if(json.success.review && json.success.return){
                            $('#reviewsAddForm input').val();
                            $('#reviewsAddForm textarea').text();
                            //$('.container_reviews').html(json.success.review);
                            $('#reviewsAddForm').addClass('success-reviews').html(json.success.msg);
                        }
                    }else{
                        if(json.error){
                            $.each(json.error, function(i, er) {
                                $("#reviewsAddForm "+er.inp).addClass('error').attr('title',er.text);
                            });
                        }
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
    valid.init('#reviewsAddForm');
    $('a.card-reviews__stars-item').on('click',function (e) {
        e.preventDefault();
        e.stopPropagation();
        var ind = $(this).index();
        $("input[name='rating']").val(ind + 1);
        $("#reviewsAddForm .card-reviews__stars").removeClass('error').attr('title','');
        var star = $('a.card-reviews__stars-item');
        $.each(star, function(i, st) {
            $(st).removeClass('fill');
            if( i < ind + 1 ){
                $(st).addClass('fill');
            }
        });

    })

    //if( $('input[type=tel]').exists()){
        //$('input[type=tel]').mask('38 (999) 999-99-99');
   // }

});