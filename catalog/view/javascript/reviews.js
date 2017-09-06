$('document').ready(function(){
    $('#reviewsAdd').on('click',function () {
        if(valid.valid()){
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

    if( $('input[type=tel]').exists()){
        $('input[type=tel]').mask('38 (999) 999-99-99');
    }

});


var valid = {
    inputs: [],
    area:   [],
    error_txt: {
        min_val: 'слишком коротко',
        max_val: 'слишком длинное',
        min_val_tel: 'слишком короткий номер',
        max_val_tel: 'слишком длинный номер',
        no_val: 'не коректно',
        no_valid: 'не валидно',
    },
    validate: true,
    init: function (el) {
        this.inputs =  $(el + ' input');
        this.area = $(el + ' textarea');
    },
    valid: function () {
        this.validate = true;
        if(this.inputs.length) {
            $.each(this.inputs, function (i, inp) {
                if (inp.required) {
                    switch (inp.type) {
                        case 'text':
                            valid._text(this);
                            break;
                        case 'email':
                            valid._email(this);
                            break;
                        case 'number':
                            valid._number(this);
                            break;
                        case 'password':
                            valid._password(this);
                            break;
                        case 'tel':
                            valid._tel(this);
                            break;
                        default:
                            console.log(i, this);
                    }

                }
            });
        }
        if(this.area.length){
            $.each(this.area, function(i, ar) {
                if(ar.required){
                    valid._area(ar);
                }
            });
        }
        return this.validate;
    },
    _number: function (inp) {
        vals = $(inp).val();
        if(/[^[0-9]/.test(vals)){
            this.validate = false;
            $(inp).addClass('error');
            console.log(valid.error_txt.no_val);
        }else{
            if(vals.length < 3){
                this.validate = false;
                $(inp).addClass('error');
                console.log(valid.error_txt.min_val);
            }else{
                if(vals.length > 40){
                    this.validate = false;
                    $(inp).addClass('error');
                    console.log(valid.error_txt.max_val);
                }else{
                    $(inp).removeClass('error');
                    $(inp).attr('title','');
                }
            }
        }
    },
    _password: function (inp) {
        vals = $(inp).val();
        if(vals.length < 3){
            this.validate = false;
            $(inp).addClass('error');
            console.log(valid.error_txt.min_val);
        }else{
            if(vals.length > 40){
                this.validate = false;
                $(inp).addClass('error');
                console.log(valid.error_txt.max_val);
            }else{
                $(inp).removeClass('error');
                $(inp).attr('title','');
            }
        }
    },
    _text: function (inp) {
        vals = $(inp).val();
        if(vals.length < 3){
            this.validate = false;
            $(inp).addClass('error');
            $(inp).attr('title',valid.error_txt.min_val);
            //console.log(valid.error_txt.min_val);
        }else{
            if(vals.length > 40){
                this.validate = false;
                $(inp).addClass('error');
                $(inp).attr('title',valid.error_txt.max_val);
            }else{
                $(inp).removeClass('error');
                $(inp).attr('title','');
            }
        }
    },
    _tel: function (inp) {
        vals = $(inp).val();
        if(/[^[0-9]/.test(vals)){
            this.validate = false;
            $(inp).addClass('error');
            $(inp).attr('title',valid.error_txt.no_val);

        }else{
            if(vals.length < 5){
                this.validate = false;
                $(inp).addClass('error');
                $(inp).attr('title',valid.error_txt.min_val_tel);
            }else{
                if(vals.length > 12){
                    this.validate = false;
                    $(inp).addClass('error');
                    $(inp).attr('title',valid.error_txt.max_val_tel);
                }else{
                    $(inp).removeClass('error');
                    $(inp).attr('title','');
                }
            }
        }
    },
    _email: function (inp) {
        vals = $(inp).val();
        if(vals.length < 3){
            this.validate = false;
            $(inp).addClass('error');
            $(inp).attr('title',valid.error_txt.min_val);
        }else{
            if(vals.length > 40){
                this.validate = false;
                $(inp).addClass('error');
                $(inp).attr('title',valid.error_txt.max_val);
            }else{
                if (vals.indexOf("@") == -1 ) {
                    $(inp).addClass('error');
                    this.validate = false;
                    $(inp).attr('title',valid.error_txt.no_valid);
                } else {
                    $(inp).removeClass('error');
                    $(inp).attr('title','');
                }
            }
        }
    },
    _area: function (inp) {
        vals = $(inp).val();
        if(vals.length < 25){
            this.validate = false;
            $(inp).addClass('error');
            $(inp).attr('title',valid.error_txt.no_valid);
        }else{
            if(vals.length > 1000){
                this.validate = false;
                $(inp).addClass('error');
                $(inp).attr('title',valid.error_txt.max_val);
            }else{
                $(inp).removeClass('error');
                $(inp).attr('title','');
            }
        }
    }
}

