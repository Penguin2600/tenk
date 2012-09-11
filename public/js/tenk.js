function getZip(zipCode) {
	var zip = zipCode;
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'address' : zip
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[0]) {
				console.log(results[0])
				city = results[0].formatted_address.split(',')[0].toUpperCase();
				state = results[0].formatted_address.split(',')[1].split(" ")[1].toUpperCase();
				$('#city').val(city);
				$('#state').val(state);
			}
		}
	});
}

//validate bib not in use
jQuery.validator.addMethod("bibnumber", function(value) {
	var isSuccess = false;

	$.ajax({
		url : '/insert/checkbib',
		async : false,
		data : {
			bib : value
		},
		success : function(output) {
			isSuccess = output === "false" ? true : false;
		}
	});

	return isSuccess;

}, "Bib In Use");

$(document).ready(function() {
	
	$("#bibnumber").focus();

	$('#zipcode').live('blur', function() {
		getZip($('#zipcode').val());
	});

	$("#insertForm").validate({
		onkeyup : false,
		onclick : false,
		rules : {
			bibnumber : {
				required : true,
				digits : true,
				bibnumber : true
			},
			firstname : "required",
			lastname : "required",
			zipcode : {
				digits : true
			},
			age : {
				required : true,
				range : [1, 99],
				digits : true
			},

			email : {
				email : true
			},

			size : "required",
			registration : "required",
			event : "required",
			division : "required"
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
	
		$("#editForm").validate({
		onkeyup : false,
		onclick : false,
		rules : {
			bibnumber : {
				required : true,
				digits : true,
			},
			firstname : "required",
			lastname : "required",
			zipcode : {
				digits : true
			},
			age : {
				required : true,
				range : [1, 99],
				digits : true
			},

			email : {
				email : true
			},

			size : "required",
			registration : "required",
			event : "required",
			division : "required"
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
	
		$("#quickForm").validate({
		onkeyup : false,
		onclick : false,
		rules : {
			bibnumber : {
				required : true,
				digits : true,
				bibnumber : true
			},
			firstname : "required",
			lastname : "required",
			age : {
				required : true,
				range : [1, 99],
				digits : true
			},

			email : {
				email : true
			},

			event : "required",
			division : "required"
		},
		submitHandler : function(form) {
			form.submit();
		}
	});


});

