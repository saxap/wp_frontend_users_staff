var ajaxgo = false; // глобальная переменная, чтобы проверять обрабатывается ли в данный момент другой запрос
jQuery(document).ready(function(){ // после загрузки DOM
	var userform = jQuery('.userform'); // пишем в переменную все формы с классом userform
	function req_go(data, form, options) { // ф-я срабатывающая перед отправкой
		if (ajaxgo) { // если какой либо запрос уже был отправлен
			form.find('.response').html('<p class="error">Необходимо дождаться ответа от предыдущего запроса.</p>'); // в див для ответов напишем ошибку
			return false; // и ничего не будет делать
		}
		form.find('input[type="submit"]').attr('disabled', 'disabled').val('Подождите..'); // выключаем кнопку и пишем чтоб подождали
	    form.find('.response').html(''); // опусташаем див с ответом
	    ajaxgo = true; // записываем в переменную что аякс запрос ушел
	}
	function req_come(data, statusText, xhr, form)  { // ф-я срабатывающая после того как пришел ответ от сервера, внутри data будет json объект с ответом
		console.log(arguments); // это для дебага
		if (data.success) { // если все хорошо и ошибок нет
			var response = '<p class="success">'+data.data.message+'</p>'; // пишем ответ в <p> с классом success
			form.find('input[type="submit"]').val('Готово'); // в кнопку напишем Готово
		} else {  // если есть ошибка
		    var response = '<p class="error">'+data.data.message+'</p>'; // пишем ответ в <p> с классом error
		    form.find('input[type="submit"]').prop('disabled', false).val('Отправить'); // снова включим кнопку
		}
		form.find('.response').html(response); // выводим ответ
		if (data.data.redirect) window.location.href = data.data.redirect; // если передан redirect, делаем перенаправление
		ajaxgo = false; // аякс запрос выполнен можно выполнять следующий
	}

	var args = { // аргументы чтобы прикрепить аякс отправку к форме
		dataType:  'json', // ответ будем ждать в json формате
		beforeSubmit: req_go, // ф-я которая сработает перед отправкой
		success: req_come, // ф-я которая сработает после того как придет ответ от сервера
		error: function(data) {
			console.log(arguments);
		},
		url: ajax_var.url  // куда отправляем, задается в wp_localize_script  
	}; 
	userform.ajaxForm(args); // крепим аякс к формам

	jQuery('.logout').click(function(e){ // ловим клие по ссылке "выйти"
		e.preventDefault(); // выключаем стандартное поведение
		if (ajaxgo) return false; // если в данный момент обрабатывается другой запрос то ничего не делаем
		var lnk = jQuery(this); // запишем ссылку в переменную
		jQuery.ajax({ // инициализируем аякс
		    type: 'POST', // шлем постом
		    url: ajax_var.url, // куда шлем
		    dataType: 'json', // ответ ждем в json
		    data: 'action=logout_me&nonce='+jQuery(this).data('nonce'), // что отправляем
	        beforeSend: function(data) { // перед отправкой
	        	lnk.text('Подождите...'); // напишем чтоб подождали
	        	ajaxgo = true; // аякс отпраляется
	        },
	        success: function(data){ // после того как ответ пришел
	        	if (data.success) { // если ошибок нет
	        		lnk.text('Выходим...'); // пишем что выходим
	        		window.location.reload(true); // и обновляемся
	        	} else { // если ошибки есть
	        		alert(data.data.message); // просто покажим их алертом
	        	}
	        },
	        error: function (xhr, ajaxOptions, thrownError) { // для дебага
	            alert(arguments);
	        },
	        complete: function(data) { // при любом исходе
	            ajaxgo = false; // аякс больше не выполняется
	        }       
		});
	});

});