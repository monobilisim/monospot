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
                autoPlaceholder: "off",
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
			if (!validTCKN(el.value)) {
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

function validTCKN(value) {
    value = String(value);

    // T.C. identity number should have 11 digits and first should be non-zero.
    if (!(/^[1-9]\d{10}$/).test(value)) return false;

    const digits = value.split('');
    // store last 2 digits (10th and 11th) which are actually used for validation
    const d10 = Number(digits[9]);
    const d11 = Number(digits[10]);
    // we'll also need the sum of first 10 digits for validation
    let sumOf10 = 0;
    let evens = 0;
    let odds = 0;

    digits.forEach((d, index) => {
        d = Number(d);
        if (index < 10) sumOf10 += d;
        if (index < 9) {
            if ((index + 1) % 2 === 0) {
                evens += d;
            } else {
                odds += d;
            }
        }
    });

    // check if the unit-digit of the sum of first 10 digits equals to the 11th digit.
    if (sumOf10 % 10 !== d11) return false;

    // check if unit-digit of the sum of odds * 7 and evens * 9 is equal to 10th digit.
    if (((odds * 7) + (evens * 9)) % 10 !== d10) return false;

    // check if unit-digit of the sum of odds * 8 is equal to 11th digit.
    if ((odds * 8) % 10 !== d11) return false;

    return true;
}
