/* Turkish initialisation for the jQuery UI date picker plugin. */
/* Written by Izzet Emre Erkan (kara@karalamalar.net). */
jQuery(function($){
	$.datepicker.regional['tr'] = {
		closeText: 'kapat',
		prevText: '&#x3c;geri',
		nextText: 'ileri&#x3e',
		currentText: 'bugün',
		monthNames: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran',
		'Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
		monthNamesShort: ['Oca','Şub','Mar','Nis','May','Haz',
		'Tem','Ağu','Eyl','Eki','Kas','Ara'],
		dayNames: ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'],
		dayNamesShort: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
		dayNamesMin: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
		weekHeader: 'Hf',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['tr']);

/* Turkish translation for the jQuery Timepicker Addon */
/* Written by Fehmi Can Saglam, Edited by Goktug Ozturk */

	$.timepicker.regional['tr'] = {
		timeOnlyTitle: 'Zaman Seçiniz',
		timeText: 'Zaman',
		hourText: 'Saat',
		minuteText: 'Dakika',
		secondText: 'Saniye',
		millisecText: 'Milisaniye',
		timezoneText: 'Zaman Dilimi',
		currentText: 'Şu an',
		closeText: 'Tamam',
		timeFormat: 'HH:mm',
		amNames: ['ÖÖ', 'Ö'],
		pmNames: ['ÖS', 'S'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['tr']);
});