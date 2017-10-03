var valid = {
    re_init: true,
    inputs: [],
    area:   [],
    error_txt: {
        min_val: 'слишком коротко',
        max_val: 'слишком длинное',
        min_val_tel: 'слишком короткий номер',
        max_val_tel: 'слишком длинный номер',
        no_data: 'не заполнено',
        no_val: 'не коректно',
        no_valid: 'не валидно',
    },
    validate: true,
    init: function (el) {
        this.inputs[el] =  $(el + ' input:visible');
        this.area[el] = $(el + ' textarea:visible');
    },
    valid: function (el) {
        if(this.re_init){
            this.inputs = [];
            this.area = [];
            this.init(el)
        }
        this.validate = true;
        if(this.inputs[el].length) {
            $.each(this.inputs[el], function (i, inp) {
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
        if(this.area[el].length){
            $.each(this.area[el], function(i, ar) {
                if(ar.required){
                    valid._area(ar);
                }
            });
        }
        return this.validate;
    },
    resetErr: function (inp) {
        $(inp).removeClass('error').next('.reviews-text-error').remove();
        $(inp).attr('title', '');
        $(inp).parent().parent().find('.sing-log__label-name').removeClass('error').attr('title','');
    },
    addErr: function (el,txt) {
        pos = $(el).position();
        this.validate = false;
        $(el).addClass('error');
        if($(el).next('.reviews-text-error').length){
            $(el).next('.reviews-text-error').html(txt);//.css('left', pos.left+'px');
        }else{
            $(el).next('.reviews-text-error').remove();
            htm = '<span class="reviews-text-error hidden">'+txt+'</span>';
            $(el).addClass('error').after(htm).show();
            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',txt);
            //$(el).next('.reviews-text-error');//.css('left', pos.left+'px');
        }
        $(el).bind('click', {}, function(eventObject){ valid.resetErr(this);} );

    },
    _number: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            if (/^[0-9]/.test(vals)) {
               // this.addErr(inp, valid.error_txt.no_val);
            } else {
                if (vals.length < 1) {
                    this.addErr(inp, valid.error_txt.min_val);
                } else {
                    if (vals.length > 40) {
                        this.addErr(inp, valid.error_txt.max_val);
                    } else {
                        this.resetErr(inp);
                    }
                }
            }
        }
    },
    _password: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            if (vals.length < 3) {
                this.addErr(inp,valid.error_txt.min_val);
            } else {
                if (vals.length > 40) {
                    this.addErr(inp,valid.error_txt.max_val);
                } else {
                    this.resetErr(inp);
                }
            }
        }
    },
    _text: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            if (vals.length < 3) {
                this.addErr(inp,valid.error_txt.min_val);
            } else {
                if (vals.length > 40) {
                    this.addErr(inp,valid.error_txt.max_val);
                } else {
                    this.resetErr(inp);
                }
            }
        }
    },
    _tel: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            // if (/[+^[0-9]/.test(vals)) {
            //     this.addErr(inp,valid.error_txt.no_val);
            // } else {
                if (vals.length < 16) {
                    this.addErr(inp, valid.error_txt.min_val_tel);
                } else {
                    if (vals.length > 20) {
                        this.addErr(inp, valid.error_txt.max_val_tel);
                    } else {
                        this.resetErr(inp);
                    }
                }
            //}
        }
    },
    _email: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            if (vals.length < 3) {
                this.addErr(inp,valid.error_txt.min_val);
            } else {
                if (vals.length > 40) {
                    this.addErr(inp,valid.error_txt.max_val);
                } else {
                    if (vals.indexOf("@") == -1) {
                        this.addErr(inp,valid.error_txt.no_valid);
                    } else {
                        this.resetErr(inp);
                    }
                }
            }
        }
    },
    _area: function (inp) {
        vals = $(inp).val();
        if(vals.length == 0){
            this.addErr(inp,valid.error_txt.no_data);
        }else {
            if (vals.length < 25) {
                this.addErr(inp,valid.error_txt.min_val);
            } else {
                if (vals.length > 1000) {
                    this.addErr(inp,valid.error_txt.max_val);
                } else {
                    this.resetErr(inp);
                }
            }
        }
    }
}
