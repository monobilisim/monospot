$(document).ready(function() {
	$("#register").click(function() {
		$(this).addClass("active");
		$("#login").removeClass("active");
		$("#form-login").before($("#form-register")).slideUp("slow");
		$("#form-register").slideDown("slow");
	});
	$("#login").click(function() {
		$(this).addClass("active");
		$("#register").removeClass("active");
		$("#form-register").before($("#form-login")).slideUp("slow");
		$("#form-login").slideDown("slow");
	});
});

function validateForm(form) {
	var errors = new Array();
	var elements = form.elements;

	for (var i=0; i < elements.length; i++) {

		var el = elements[i];
		if (el.type != "submit") el.removeAttribute("class");

		/* gsm validation */
		if (el.name == "user[gsm]") {
			if (el.value == "") {
				errors[i] = "<?=t('gsm_required')?>";
			} else if (el.value.charAt(0) == "0") {
				errors[i] = "<?=t('gsm_zero')?>";
			} else if (/[^0-9]/g.test(el.value)) {
				errors[i] = "<?=t('gsm_numeric')?>";
			} else if (!(el.value.length == 10)) {
				errors[i] = "<?=t('gsm_valid')?>";
			}
		}

		/* fullname validation */
		if (el.name == "user[fullname]") {
			if (el.value == "") {
				errors[i] = "<?=t('fullname_required')?>";
			}
		}
		
		/* id_number validation */
		if (el.name == "user[id_number]") {
		console.log('id');
			if (!validateTurkishIdentificationNumber(el.value)) {
				errors[i] = "<?=t('invalid_id_number_js')?>";
			}
		}

	};

	if (errors.length != 0) {
		for (var key in errors) {
			elements[key].className = "error";
		}
		alert(errors.join("\n"));
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
