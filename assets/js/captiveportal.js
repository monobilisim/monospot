var intPhoneInput = {};

$(document).ready(function() {
    if (smsInternational) {
    	$(".intlPhone").each(function () {
    		var id = $(this).attr("id");
			var element = document.querySelector("#" + id);

            intPhoneInput[id] = {};
            intPhoneInput[id].element = element;
            intPhoneInput[id].iti = intlTelInput(element, {
                allowDropdown: true,
                initialCountry: "tr",
                preferredCountries: ["tr", "us", "gb"],
                utilsScript: "intlTelInputUtils.js",
                autoHideDialCode: false
            });
        });
    }

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

	$('a.popup').popup();
});

function validateForm(form) {
	var errors = new Array();
	var elements = form.elements;
	var phoneInputId = null;

	for (var i=0; i < elements.length; i++) {

		var el = elements[i];

		// gsm validation
		if (el.name == "user[gsm]") {
			if (form.elements.hasOwnProperty("gsm_required")) {
				if (smsInternational) {
					if (el.classList.contains("intlPhone")) {
						phoneInputId = el.id;
						if (!intPhoneInput[el.id].iti.isValidNumber()) {
                            errors[i] = "• <?=t('gsm_valid')?>";
						}
					}
				} else {
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
			if (form.elements.hasOwnProperty("email_required")) {
				if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(el.value) == false) {
					errors[i] = "• <?=t('email_error')?>";
				}
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

		// terms validation
		if (el.name == "terms") {
			if (!el.checked) {
				errors[i] = "• <?=t('terms_required')?>";
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

	// populate GSM field with the full international number
	if (smsInternational && phoneInputId) {
		$("#" + phoneInputId).val(intPhoneInput[phoneInputId].iti.getNumber());
	}

	return true;
}

function checkphone(input, event) {
	if (!smsInternational) {
        if (input.value.length === 0 && String.fromCharCode(event.charCode) === '0') {
            event.preventDefault();
        }
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
