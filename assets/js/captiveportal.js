$(document).ready(function() {
	$(".form.active").slideDown();

	$("button").click(function() {
		$("button.active").removeClass("active");
		$(this).addClass("active");
		var currentId = $(this).attr("id");
		if ($(".form.active").length === 0) {
			$("#form_" + currentId).addClass("active").slideDown();
		}
		else {
			$(".form.active").removeClass("active").slideUp(function(){
				$("#form_" + currentId).addClass("active").slideDown();
			});
		}
	});
});

function validateForm(form) {
	var errors = new Array();
	var elements = form.elements;

	for (var i=0; i < elements.length; i++) {

		var el = elements[i];

		// gsm validation
		if (el.name == "user[gsm]") {
			if (el.value == "") {
				errors[i] = "• <?=t('gsm_required')?>";
			} else if (el.value.charAt(0) == "0") {
				errors[i] = "• <?=t('gsm_zero')?>";
			} else if (/[^0-9]/g.test(el.value)) {
				errors[i] = "• <?=t('gsm_numeric')?>";
			} else if (!(el.value.length == 10)) {
				errors[i] = "• <?=t('gsm_valid')?>";
			}
		}

		// username validation
		if (el.name == "user[username]") {
			if (el.value == "") {
				errors[i] = "• <?=t('username_required')?>";
			}
		}

		// email validation
		if (el.name == "user[email]") {
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(el.value) == false) {
				errors[i] = "• <?=t('email_error')?>";
			}
		}

		// id_number validation
		if (el.name == "user[id_number]") {
			if (!validateTurkishIdentificationNumber(el.value)) {
				errors[i] = "• <?=t('invalid_id_number_js')?>";
			}
		}

		// gsm_permission_required validation
		if (el.name == "gsm_permission") {
			if (form.elements.hasOwnProperty("gsm_permission_required")) {
				if (!el.checked) {
					errors[i] = "• <?=t('gsm_permission_required')?>";
				}
			}
		}

		// email_permission_required validation
		if (el.name == "email_permission") {
			if (form.elements.hasOwnProperty("email_permission_required")) {
				if (!el.checked) {
					errors[i] = "• <?=t('email_permission_required')?>";
				}
			}
		}

	};

	if (errors.length != 0) {
		var message = "";
		for (var key in errors) {
			$(elements[key]).addClass("error");
			message = message + errors[key] + "\n";
		}
		message = message.slice(0, -1)
		alert(message);
		return false;
	}

	form.submit.disabled = true;
	form.submit.value = "<?=t('wait')?>";
	form.submit.style.backgroundColor = "gray";
	form.submit.style.cursor = "default";
	return true;
}

function checkphone(input, event) {
	if (input.value.length === 0 && String.fromCharCode(event.charCode) === '0') {
		event.preventDefault();
	}
}

var validateTurkishIdentificationNumber = function(n) {
    if(n.length != 11)
        return false;

    var evens = odds = all = 0;
    for (i = 0, l = n.length; i < l; i++) {
        var num = Number(n[i]);
        if(i < 10) {
            all += num;
            if(i < 9) {
                if(i % 2)
                    odds += num
                else
                    evens += num
            }
        }
    }

    if(((evens * 7) - odds) % 10 == n[9] && all % 10 == n[10])
        return true;
    else
        return false;
}
