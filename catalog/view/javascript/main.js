

// аПаЕбаЕаКаЛббаЕаНаИаЕ аВбаПаАаДаАбаЕаК аНаА аГаЛаАаВаНаОаЙ
$('document').ready(function(){
	$('.reviews-faq__link').click(function(e) {
		e.preventDefault();
		var box = this.closest('.reviews-faq__box');
		box.classList.toggle('opened');
	});


	onResize();
	window.addEventListener('resize', onResize);

	function onResize() {
		if (screen.width <= 998) {
			$('.reviews__content').addClass('owl-carousel');
			$('.reviews__content').owlCarousel({
				items: 1,
				loop: true,
				nav: true,
				navText: ['', '']
			});
		} else {
			$('.reviews__content').removeClass('owl-carousel');
			$('.reviews__content').trigger('destroy.owl.carousel');
		}

		if (screen.width >= 768) {
			$('.profile-history__content').addClass('owl-carousel');
			$('.profile-history__content').owlCarousel({
				items: 3,
				nav: true,
				margin: 30,
				navText: ['',''],
				responsive:{
					0 : {
						items: 1
					},
					480 : {
						items: 1
					},
					768 : {
						items: 2
					},
					998 : {
						items: 2
					},
					1200: {
						items: 3
					}
				}

			});

		} else {
			$('.profile-history__content').removeClass('owl-carousel');
			$('.profile-history__content').trigger('destroy.owl.carousel');

			var itemsProf = $('.profile-history__item');
			var coundItem = 0;

			for ( var itemPr in itemsProf) {
				if (coundItem > 1) {
					itemPr.classList.add('hide');
				}
				coundItem++;
			}
		}

	}


	$('.profile-history__conrols a').click(function(e) {
		e.preventDefault();

		var items = $('.profile-history__item');

		for (var item in items) {
			item.classList.remove('hide');
		}

	});
	$('.header-box__link').click(function() {

		var items = $('.header-box__link');

		for ( var item in items) {
			item.classList.remove('active');
		}

		this.classList.add('active');

	});
	$('.menu-tabs__link').click(function(e) {
		e.preventDefault();

		var item = $(this).closest('.menu-tabs__item');

		item.toggleClass('active')
			.siblings()
			.removeClass('active');


	});
	$('.header-basket__box').click(function() {

		var log = $('.basket-log');

		log.toggleClass('opened');

	});
	$('.burg').click(function(e) {
		e.preventDefault();

		$('.drop-menu').toggleClass('opened');
	});

	$('.drop-menu__close').click(function(e) {
		e.preventDefault();

		$('.drop-menu').toggleClass('opened');
	});
	$('.drop-menu__bg').click(function() {
		$('.drop-menu').toggleClass('opened');
	});
	$('.header-sing').click(function() {
		$('.sing-log').toggleClass('opened');
	});
	$('.drop-menu__sing-link').click(function() {
		$('.drop-menu__sing-content').toggleClass('opened');
	});
	$('.basket-log__close').click(function() {
		$('.basket-log').toggleClass('opened');
	});
	$('.profile-form__add-label').click(function(e) {
		e.preventDefault();
		$('.profile-form__drop').toggleClass('opened');
	});
	$('.card-price__box').click(function() {
		$(this).addClass('active')
			.siblings()
			.removeClass('active');
	});

	$('.ingredients-popup__tabs-link').click(function(e) {
		e.preventDefault();
		$(this).addClass('active')
			.siblings()
			.removeClass('active');
	});
	document.addEventListener('click', area);

	function area(e) {
		var containers = $('.menu-tabs__item');
		if (containers.has(e.target).length === 0){
			containers.removeClass('active');
		}

		var basket = $('.basket-log');
		var baskerBox = $('.header-basket');
		if (baskerBox.has(e.target).length === 0) {
			basket.removeClass('opened');
		}

		var cardBox = $('.hit-item__info');
		if (cardBox.has(e.target).length !== 0) {
			e.preventDefault();
		}
	}


// $('.hit-item__info-box').click(function() {
//   $(this).addClass('active')
//     .siblings()
//     .removeClass('active');
// });


	

	var loginValid = valid;
	var btn_caption;

	loginValid.init('form.user-auth');
	$('.sing-log__reg').on('click', function () {
        $('form.user-auth .error').removeClass('error');
		var action = $('form.user-auth input.auth_registr').val();
        $('form.user-auth').prop('action', action);
		$('form.user-auth .label-email').removeClass('hidden');
        $('form.user-auth .label-password').removeClass('hidden');
        $('form.user-auth .label-confirm').removeClass('hidden');
        $('form.user-auth .label-tel').removeClass('hidden');
        $('form.user-auth .label-tel-codes').addClass('hidden');

		$('form.user-auth .sing-log__reg-wrap.login').removeClass('hidden');
        $('form.user-auth .auth_confirm').removeClass('hidden');
		$('form.user-auth .sing-log__reg-wrap.regis').addClass('hidden');
		if($('.smsRec').val() != ''){
            $('form.user-auth .label-tel-code').removeClass('hidden');
            $('form.user-auth .auth_tel_code').prop('required', true);
		}
		$('form.user-auth .auth_type').val('reg');
		$('form.user-auth .auth_email').prop('required', true);
        $('form.user-auth .auth_tel').prop('required', true);
        $('form.user-auth .auth_pass').prop('required', true);
        $('form.user-auth .auth_tel_codes').prop('required', false);

        var txt = $('form.user-auth .btn.sing-log__btn.btn-orange').attr('data-regis');
        $('form.user-auth .btn.sing-log__btn.btn-orange').html(txt);//'Регистрация'
        //auth
    })
	$('.sing-log__log').on('click', function () {
        $('form.user-auth .error').removeClass('error');
        var action = $('form.user-auth input.auth_login').val();
        $('form.user-auth').prop('action', action);
		$('form.user-auth .label-email').addClass('hidden');
        $('form.user-auth .label-password').removeClass('hidden');
        $('form.user-auth .label-confirm').removeClass('hidden');
        $('form.user-auth .label-tel').removeClass('hidden');
        $('form.user-auth .label-tel-code').addClass('hidden');
        $('form.user-auth .label-tel-codes').addClass('hidden');

        $('form.user-auth .sing-log__reg-wrap.login').addClass('hidden');
        $('form.user-auth .sing-log__reg-wrap.regis').removeClass('hidden');
        $('form.user-auth .auth_confirm').removeClass('hidden');
		$('form.user-auth .auth_type').val('auth');
        $('form.user-auth .auth_email').prop('required', false);
        $('form.user-auth .auth_tel_code').prop('required', false);
        $('form.user-auth .auth_tel').prop('required', true);
        $('form.user-auth .auth_pass').prop('required', true);
        $('form.user-auth .auth_tel_codes').prop('required', false);

        var txt = $('form.user-auth .btn.sing-log__btn.btn-orange').attr('data-auth');
        $('form.user-auth .btn.sing-log__btn.btn-orange').html(txt);//'Войти'
        //auth
    })
	$('.auth-forgot').on('click', function () {
        $('form.user-auth .error').removeClass('error');
        var action = $('form.user-auth input.auth_forgots').val();
        $('form.user-auth').prop('action', action);

        $('form.user-auth .label-email').addClass('hidden');
        //$('form.user-auth .label-email').removeClass('hidden');
        $('form.user-auth .label-password').addClass('hidden');
        //$('form.user-auth .label-tel').addClass('hidden');
        $('form.user-auth .label-confirm').addClass('hidden');
        $('form.user-auth .label-tel-code').addClass('hidden');

        $('form.user-auth .label-tel').removeClass('hidden');
        if($('.smsForg').val() != ''){
            $('form.user-auth .label-tel-codes').removeClass('hidden');
            $('form.user-auth .auth_tel_codes').prop('required', true);
        }

        $('form.user-auth .auth_email').prop('required', false);
        //$('form.user-auth .auth_email').prop('required', true);
        //$('form.user-auth .auth_tel').prop('required', false);
        $('form.user-auth .auth_tel').prop('required', true);
        $('form.user-auth .auth_tel_code').prop('required', false);
        $('form.user-auth .auth_pass').prop('required', false);

        $('form.user-auth .sing-log__reg-wrap.regis').addClass('hidden');
        $('form.user-auth .sing-log__reg-wrap.login').removeClass('hidden');
		$('form.user-auth .auth_type').val('forgot');
		var txt = $('form.user-auth .btn.sing-log__btn.btn-orange').attr('data-send');
        $('form.user-auth .btn.sing-log__btn.btn-orange').html(txt);//'Отправить'
        //auth
    })

	$('.sms-send').on('click', function () {
        if($('form.user-auth:visible input.auth_tel').val()){
            sendSMS();
        }else{
            loginValid.valid('.user-auth');
            return false;
        }
    })

	function sendSMS() {
        var tel = $('form.user-auth:visible input.auth_tel').val();
        var _email = $('form.user-auth:visible input.auth_email').val();
        $('form.user-auth .send-sms-tel').addClass('hidden');
        $.ajax({
            url: '/index.php?route=account/register/send',
            type: 'post',
            data:{
            	phone: tel,
				email: _email
			},
            dataType: 'json',
            crossDomain: true,
            success: function(json) {
                setTimeout(function () {
                    $('form.user-auth .send-sms-tel').removeClass('hidden');
                },1000);
                if(json.success){
					if(json.code){
                        $('img.sms-send').removeClass('redBord');
                        $('form.user-auth .smsRec').val(json.code);
                        $('form.user-auth .auth_tel_code').prop('required', true);
                        $('form.user-auth .label-tel-code').removeClass('hidden');
					}

                }else{
                    if(json.error){
                        $.each(json.error, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                            //$("form.user-auth "+er.inp).addClass('error').attr('title',er.text);
                        });
                        $('img.sms-send').addClass('redBord');
                    }
                    if(json.warning){
                        $.each(json.warning, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                        });
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    $('form.user-auth .btn.sing-log__btn.btn-orange').on('click',function (e) {
        if(loginValid.valid('.user-auth')){
        	if($('.auth_type').val() == 'auth'){
                formAuth();
			}else if($('.auth_type').val() == 'reg'){
                if($('form.user-auth .smsRec').val() != ''){
                    formAuth();
                }else{
                    sendSMS();
                }
        	}else if($('.auth_type').val() == 'forgot'){
                if($('form.user-auth .smsForg').val() != ''){
                    formAuth();
                }else{
                    formForgot();
                }
			}else{
                formAuth();
			}

        }else{
            return false;
        }
        e.preventDefault();
        return false;
    });

    $('form.user-auth').submit(function (e) {
        e.preventDefault();
        return false;
    });
	function formAuth() {
        var action = $('form.user-auth').prop('action');
        var param = $('form.user-auth:visible input').serializeArray();
        $.ajax({
            url: action,
            type: 'post',
            data: param,
            dataType: 'json',
            crossDomain: true,
            success: function(json) {
                if(json.success){
                    if(json.success.success){
						if(json.success.redirect){
							window.location.href = json.success.redirect;
						}
						if(json.success.text_success){
                            $('.sing-log__log').click();
                            $('form.user-auth .label-info span').html(json.success.text_success);
                            $('form.user-auth .label-info').removeClass('hidden');
                            setTimeout(function () {
                                $('form.user-auth .label-info').addClass('hidden');
                            },3000);
						}
                    }
                    if(json.success.redirect){
                        window.location.href = json.success.redirect;
                    }
                    if(json.error){
                        $.each(json.error, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                        });
                    }
                    if(json.warning){
                        $.each(json.warning, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            //prev("sing-log__label-name").addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                        });
                    }
                }else{
                    if(json.error){
                        $.each(json.error, function(i, er) {
                        	var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                            //$("form.user-auth "+er.inp).addClass('error').attr('title',er.text);
                        });
                    }
                    if(json.warning){
                        $.each(json.warning, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                        });
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
    function formForgot() {
        var action = 'index.php?route=account/forgotten/forgot_send';
        var param = $('form.user-auth:visible input').serializeArray();
        $('form.user-auth .send-sms-tel').addClass('hidden');
        $.ajax({
            url: action,
            type: 'post',
            data: param,
            dataType: 'json',
            crossDomain: true,
            success: function(json) {
                setTimeout(function () {
                    $('form.user-auth .send-sms-tel').removeClass('hidden');
                },1000);
                if(json.success){
                    if(json.code){
                        $('img.sms-send').removeClass('redBord');
                        $('form.user-auth .smsForg').val(json.code);
                        $('form.user-auth .auth_tel_code').prop('required', true);
                        $('form.user-auth .label-tel-codes').removeClass('hidden');
                    }

                }else{
                    if(json.error){
                        $.each(json.error, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                            //$("form.user-auth "+er.inp).addClass('error').attr('title',er.text);
                        });
                        $('img.sms-send').addClass('redBord');
                    }
                    if(json.warning){
                        $.each(json.warning, function(i, er) {
                            var el = $("form.user-auth "+er.inp);
                            $(el).next('.reviews-text-error').remove();
                            htm = '<span class="reviews-text-error hidden">'+er.text+'</span>';
                            $(el).parent().parent().find('.sing-log__label-name').addClass('error').attr('title',er.text);
                            $(el).addClass('error').after(htm).show();
                        });
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

	$('a.noLink').on('click',function (e) {
        e.preventDefault();
        return false;
    })

    $('body').on('click', '.add-product-cart', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id_p = $(this).attr('data-prodId');
        var i_key = $(this).attr('data-key');
        cart.add(id_p,1, $(".option"+id_p+"_"+i_key).serialize());

        return false;
    }).on('click', '.header-box__lang-box',function(){
		$('#form-language').submit();

	}).on('click', '#login_for_bonuses', function(){
		$('.basket-log').removeClass('opened');
		$('.sing-log').addClass('opened');
		$('html, body').animate({ scrollTop: '50px' }, 'slow');

	});

    $('.auth_tel').mask('+38 000 000 00 00');

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhcmQuanMiLCJtYWluLmpzIiwibWVudS5qcyIsInBvcHVwLmpzIiwic2xpZGVyLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDaEZBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdklBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJtYWluLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIiwiXHJcbi8vINC/0LXRgNC10LrQu9GO0YfQtdC90LjQtSDQstGL0L/QsNC00LDRiNC10Log0L3QsCDQs9C70LDQstC90L7QuVxyXG4kKCcucmV2aWV3cy1mYXFfX2xpbmsnKS5jbGljayhmdW5jdGlvbihlKSB7XHJcbiAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gIHZhciBib3ggPSB0aGlzLmNsb3Nlc3QoJy5yZXZpZXdzLWZhcV9fYm94Jyk7XHJcbiAgYm94LmNsYXNzTGlzdC50b2dnbGUoJ29wZW5lZCcpO1xyXG59KTtcclxuXHJcblxyXG5vblJlc2l6ZSgpO1xyXG53aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcigncmVzaXplJywgb25SZXNpemUpO1xyXG5cclxuZnVuY3Rpb24gb25SZXNpemUoKSB7XHJcblx0aWYgKHNjcmVlbi53aWR0aCA8PSA5OTgpIHtcclxuXHRcdCQoJy5yZXZpZXdzX19jb250ZW50JykuYWRkQ2xhc3MoJ293bC1jYXJvdXNlbCcpO1xyXG5cdFx0JCgnLnJldmlld3NfX2NvbnRlbnQnKS5vd2xDYXJvdXNlbCh7XHJcblx0XHRcdGl0ZW1zOiAxLFxyXG5cdFx0XHRsb29wOiB0cnVlLFxyXG5cdFx0XHRuYXY6IHRydWUsXHJcblx0XHRcdG5hdlRleHQ6IFsnJywgJyddXHJcblx0XHR9KTtcclxuXHR9IGVsc2Uge1xyXG5cdFx0JCgnLnJldmlld3NfX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnb3dsLWNhcm91c2VsJyk7XHJcblx0XHQkKCcucmV2aWV3c19fY29udGVudCcpLnRyaWdnZXIoJ2Rlc3Ryb3kub3dsLmNhcm91c2VsJyk7XHJcblx0fVxyXG5cclxuXHRpZiAoc2NyZWVuLndpZHRoID49IDc2OCkge1xyXG5cdFx0JCgnLnByb2ZpbGUtaGlzdG9yeV9fY29udGVudCcpLmFkZENsYXNzKCdvd2wtY2Fyb3VzZWwnKTtcclxuXHRcdCQoJy5wcm9maWxlLWhpc3RvcnlfX2NvbnRlbnQnKS5vd2xDYXJvdXNlbCh7XHJcblx0XHQgIGl0ZW1zOiAzLFxyXG5cdFx0ICBuYXY6IHRydWUsXHJcblx0XHQgIG1hcmdpbjogMzAsXHJcblx0XHQgIG5hdlRleHQ6IFsnJywnJ10sXHJcblx0XHQgIHJlc3BvbnNpdmU6e1xyXG5cdFx0ICAgICAgICAwIDoge1xyXG5cdFx0ICAgICAgICBcdGl0ZW1zOiAxXHJcblx0XHRcdCAgICB9LFxyXG5cdFx0XHQgICAgNDgwIDoge1xyXG5cdFx0XHQgICAgICAgIGl0ZW1zOiAxXHJcblx0XHRcdCAgICB9LFxyXG5cdFx0XHQgICAgNzY4IDoge1xyXG5cdFx0XHQgICAgICAgIGl0ZW1zOiAyXHJcblx0XHRcdCAgICB9LFxyXG5cdFx0XHQgICAgOTk4IDoge1xyXG5cdFx0XHQgICAgXHRpdGVtczogMlxyXG5cdFx0XHQgICAgfSxcclxuXHRcdFx0ICAgIDEyMDA6IHtcclxuXHRcdFx0ICAgIFx0aXRlbXM6IDNcclxuXHRcdFx0ICAgIH1cclxuXHRcdCAgICB9XHJcblx0XHQgIFxyXG5cdFx0fSk7XHJcblxyXG5cdH0gZWxzZSB7XHJcblx0XHQkKCcucHJvZmlsZS1oaXN0b3J5X19jb250ZW50JykucmVtb3ZlQ2xhc3MoJ293bC1jYXJvdXNlbCcpO1xyXG5cdFx0JCgnLnByb2ZpbGUtaGlzdG9yeV9fY29udGVudCcpLnRyaWdnZXIoJ2Rlc3Ryb3kub3dsLmNhcm91c2VsJyk7XHJcblxyXG5cdFx0dmFyIGl0ZW1zUHJvZiA9ICQoJy5wcm9maWxlLWhpc3RvcnlfX2l0ZW0nKTtcclxuXHRcdHZhciBjb3VuZEl0ZW0gPSAwO1xyXG5cclxuXHRcdGZvciAoIHZhciBpdGVtUHIgb2YgaXRlbXNQcm9mKSB7XHJcblx0XHRcdGlmIChjb3VuZEl0ZW0gPiAxKSB7XHJcblx0XHRcdFx0aXRlbVByLmNsYXNzTGlzdC5hZGQoJ2hpZGUnKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRjb3VuZEl0ZW0rKztcclxuXHRcdH1cclxuXHR9XHJcblxyXG59XHJcblxyXG5cclxuJCgnLnByb2ZpbGUtaGlzdG9yeV9fY29ucm9scyBhJykuY2xpY2soZnVuY3Rpb24oZSkge1xyXG5cdGUucHJldmVudERlZmF1bHQoKTtcclxuXHRcclxuXHR2YXIgaXRlbXMgPSAkKCcucHJvZmlsZS1oaXN0b3J5X19pdGVtJyk7XHJcblxyXG5cdGZvciAodmFyIGl0ZW0gb2YgaXRlbXMpIHtcclxuXHRcdGl0ZW0uY2xhc3NMaXN0LnJlbW92ZSgnaGlkZScpO1xyXG5cdH1cclxuXHJcbn0pOyIsIiQoJy5oZWFkZXItYm94X19saW5rJykuY2xpY2soZnVuY3Rpb24oKSB7XHJcblxyXG4gIHZhciBpdGVtcyA9ICQoJy5oZWFkZXItYm94X19saW5rJyk7XHJcbiAgXHJcbiAgZm9yICggdmFyIGl0ZW0gb2YgaXRlbXMpIHtcclxuICAgIGl0ZW0uY2xhc3NMaXN0LnJlbW92ZSgnYWN0aXZlJyk7XHJcbiAgfVxyXG5cclxuICB0aGlzLmNsYXNzTGlzdC5hZGQoJ2FjdGl2ZScpO1xyXG5cclxufSk7XHJcblxyXG5cclxuJCgnLm1lbnUtdGFic19fbGluaycpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcclxuICBlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG4gIHZhciBpdGVtID0gJCh0aGlzKS5jbG9zZXN0KCcubWVudS10YWJzX19pdGVtJyk7XHJcblxyXG4gIGl0ZW0udG9nZ2xlQ2xhc3MoJ2FjdGl2ZScpXHJcbiAgICAuc2libGluZ3MoKVxyXG4gICAgLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuXHJcblxyXG59KTtcclxuXHJcblxyXG4kKCcuaGVhZGVyLWJhc2tldF9fYm94JykuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgXHJcbiAgdmFyIGxvZyA9ICQoJy5iYXNrZXQtbG9nJyk7XHJcbiAgXHJcbiAgbG9nLnRvZ2dsZUNsYXNzKCdvcGVuZWQnKTtcclxuXHJcbn0pO1xyXG5cclxuXHJcbiQoJy5idXJnJykuY2xpY2soZnVuY3Rpb24oZSkge1xyXG4gIGUucHJldmVudERlZmF1bHQoKTtcclxuXHJcbiAgJCgnLmRyb3AtbWVudScpLnRvZ2dsZUNsYXNzKCdvcGVuZWQnKTtcclxufSk7XHJcblxyXG4kKCcuZHJvcC1tZW51X19jbG9zZScpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcclxuICBlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG4gICQoJy5kcm9wLW1lbnUnKS50b2dnbGVDbGFzcygnb3BlbmVkJyk7XHJcbn0pO1xyXG5cclxuJCgnLmRyb3AtbWVudV9fYmcnKS5jbGljayhmdW5jdGlvbigpIHtcclxuICAkKCcuZHJvcC1tZW51JykudG9nZ2xlQ2xhc3MoJ29wZW5lZCcpO1xyXG59KTtcclxuXHJcblxyXG4kKCcuaGVhZGVyLXNpbmcnKS5jbGljayhmdW5jdGlvbigpIHtcclxuICAkKCcuc2luZy1sb2cnKS50b2dnbGVDbGFzcygnb3BlbmVkJyk7XHJcbn0pO1xyXG5cclxuJCgnLmRyb3AtbWVudV9fc2luZy1saW5rJykuY2xpY2soZnVuY3Rpb24oKSB7XHJcbiAgJCgnLmRyb3AtbWVudV9fc2luZy1jb250ZW50JykudG9nZ2xlQ2xhc3MoJ29wZW5lZCcpO1xyXG59KTtcclxuXHJcbiQoJy5iYXNrZXQtbG9nX19jbG9zZScpLmNsaWNrKGZ1bmN0aW9uKCkge1xyXG4gICQoJy5iYXNrZXQtbG9nJykudG9nZ2xlQ2xhc3MoJ29wZW5lZCcpO1xyXG59KTtcclxuXHJcbiQoJy5wcm9maWxlLWZvcm1fX2FkZC1sYWJlbCcpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcclxuICBlLnByZXZlbnREZWZhdWx0KCk7XHJcbiAgJCgnLnByb2ZpbGUtZm9ybV9fZHJvcCcpLnRvZ2dsZUNsYXNzKCdvcGVuZWQnKTtcclxufSk7XHJcblxyXG5cclxuJCgnLmNhcmQtcHJpY2VfX2JveCcpLmNsaWNrKGZ1bmN0aW9uKCkge1xyXG4gICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpXHJcbiAgICAuc2libGluZ3MoKVxyXG4gICAgLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxufSk7XHJcblxyXG5cclxuJCgnLmluZ3JlZGllbnRzLXBvcHVwX190YWJzLWxpbmsnKS5jbGljayhmdW5jdGlvbihlKSB7XHJcbiAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICQodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpXHJcbiAgICAuc2libGluZ3MoKVxyXG4gICAgLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxufSk7XHJcblxyXG5cclxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBhcmVhKTtcclxuXHJcbmZ1bmN0aW9uIGFyZWEoZSkge1xyXG4gIHZhciBjb250YWluZXJzID0gJCgnLm1lbnUtdGFic19faXRlbScpO1xyXG4gIGlmIChjb250YWluZXJzLmhhcyhlLnRhcmdldCkubGVuZ3RoID09PSAwKXtcclxuICAgIGNvbnRhaW5lcnMucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4gIH1cclxuXHJcbiAgdmFyIGJhc2tldCA9ICQoJy5iYXNrZXQtbG9nJyk7XHJcbiAgdmFyIGJhc2tlckJveCA9ICQoJy5oZWFkZXItYmFza2V0Jyk7XHJcbiAgaWYgKGJhc2tlckJveC5oYXMoZS50YXJnZXQpLmxlbmd0aCA9PT0gMCkge1xyXG4gICAgYmFza2V0LnJlbW92ZUNsYXNzKCdvcGVuZWQnKTtcclxuICB9XHJcblxyXG4gIHZhciBjYXJkQm94ID0gJCgnLmhpdC1pdGVtX19pbmZvJyk7XHJcbiAgaWYgKGNhcmRCb3guaGFzKGUudGFyZ2V0KS5sZW5ndGggIT09IDApIHtcclxuICAgIGUucHJldmVudERlZmF1bHQoKTtcclxuICB9XHJcbn1cclxuXHJcblxyXG4vLyAkKCcuaGl0LWl0ZW1fX2luZm8tYm94JykuY2xpY2soZnVuY3Rpb24oKSB7XHJcbi8vICAgJCh0aGlzKS5hZGRDbGFzcygnYWN0aXZlJylcclxuLy8gICAgIC5zaWJsaW5ncygpXHJcbi8vICAgICAucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xyXG4vLyB9KTtcclxuXHJcblxyXG4kKCcuYmFza2V0LWxvZ19fYnRuJykuY2xpY2soZnVuY3Rpb24oZSkge1xyXG4gIGUucHJldmVudERlZmF1bHQoKTtcclxuXHJcbiAgdmFyIGJveCA9ICQodGhpcykuY2xvc2VzdCgnLmJhc2tldC1sb2dfX2JveCcpO1xyXG5cclxuICBib3gubmV4dCgpXHJcbiAgICAuYWRkQ2xhc3MoJ2FjdGl2ZScpXHJcbiAgICAuc2libGluZ3MoKVxyXG4gICAgLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxufSk7XHJcblxyXG4kKCcuYmFza2V0LWxvZ19fdG9wLWJ0bi0tbGVmdCcpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcclxuICBlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG4gIHZhciBib3ggPSAkKHRoaXMpLmNsb3Nlc3QoJy5iYXNrZXQtbG9nX19ib3gnKTtcclxuXHJcbiAgYm94LnByZXYoKVxyXG4gICAgLmFkZENsYXNzKCdhY3RpdmUnKVxyXG4gICAgLnNpYmxpbmdzKClcclxuICAgIC5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcbn0pO1xyXG5cclxuIiwiJCgnLmZvci1wb3B1cCcpLm1hZ25pZmljUG9wdXAoKTsiLCIkKCcjc2xpZGVyJykub3dsQ2Fyb3VzZWwoe1xyXG4gIGl0ZW1zOiAxLFxyXG4gIGxvb3A6IHRydWUsXHJcbiAgZG90czogdHJ1ZSxcclxuICBkb3RzRWFjaDogdHJ1ZVxyXG59KTtcclxuXHJcbiJdfQ==

});
