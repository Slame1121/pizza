$('document').ready(function(){
    $('#reviewsAdd').on('click',function () {
        // e.preventDefault();
        // e.stopPropagation();
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
                if(json.success.review && json.success.return){
                    $('#reviewsAddForm input').val();
                    $('#reviewsAddForm textarea').text();
                    $('.container_reviews').html(json.success.review);
                }

                // $('.alert-dismissible, .text-danger').remove();
                // $('.form-group').removeClass('has-error');
                //
                // if (json['error']) {
                //     $('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                //
                //     // Highlight any found errors
                //     $('select[name=\'currency\']').closest('.form-group').addClass('has-error');
                // }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });


    });
});