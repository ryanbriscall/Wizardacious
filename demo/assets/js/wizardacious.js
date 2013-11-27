var Wizardacious = (function($) {
	function submit(alias) {
		var form = $('#'+alias);

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			beforeSend: function(xhr) { 
				// Loading
				$(form).find('.navigation .wizard-back').attr('disabled', true);
			}
		}).done(function(response, textStatus, xhr) { // Success
			// Response
			var errors = false;
			
			// if (response.match(/(REJECTED|ERROR)/gi)) { errors = true; }
			var response_data = $.parseJSON(response);
			if (response_data.errors != '') errors = true;
			
			if (errors) {
				$(form).find('.error-content').show(); 
				$(form).find('.navigation .wizard-back').attr('disabled', false);
			}
			else {
				$(form).find('.success-content').show(); 
				$(form).find('.navigation').hide();
			}
		}).always(function(response) { // Complete
			$(form).find('.api-loading-content').hide(); 
		}).fail(function (xhr, textStatus, errorThrown) { // Error
			alert(textStatus+ ' - ' +errorThrown);
			$(form).find('.api-loading-content').hide(); 
			$(form).find('.failure-content').show(); 
		});
	}

	function prepare(alias) {
		/*
		// Tracking
		var step_id = $('#'+alias+'_tracking').val();
		if (step_id) { 
			$('iframe#tracking').attr('src', 'tracking.php?s='+step_id);
		}
		*/

		// Submit Ready?
		var submit_ready = $('#'+alias+'_submit');
		if (submit_ready.length) { 
			submit(alias);
			return false;
		}

		$('#'+alias+'-wizard :input').each(function(idx,el) {
			$(el).change(function(e){
				if ($(this).valid()) { /* $(this).parent().find('.error').remove(); */ }
			});
			$(el).keyup(function(e){
				if ($(this).valid()) { /* $(this).parent().find('.error').remove(); */ }
			});
		});

		// Custom preparations:

		// Fields' Masks
		// $(".phone").mask("(999) 999-9999");

		// END - Custom preparations.
	}

	function action(alias, type) {
		var form = $('#'+alias);
		var data = 'wizard='+alias;
		if (type == 'reset') { 
			if (form.find('.navigation .wizard-back').attr('disabled') == 'disabled') return;
			if (form.find('.navigation .wizard-next').attr('disabled') == 'disabled') return;
			data = data+'&reset=1'; 
		} 
		else if (type == 'prev') { 
			if (form.find('.navigation .wizard-back').attr('disabled') == 'disabled') return;
			data = data+'&nav=prev'; 
		}
		else if (type == 'next') { 
			if (form.find('.navigation .wizard-next').attr('disabled') == 'disabled') return;
			data = data+'&nav=next'; 

			// Validatation
			
			// Generic validation
			var inputs = form.find(':input.required');
			var valid = (!inputs.length || !!inputs.valid());
			if (!valid) { 
				// alert('Form is invalid.');
				for (i = 0; i < inputs.length; i++) {
					// if (!$(inputs[i]).valid()) alert($(inputs[i]).attr('name')+' is invalid.');
					if (!$(inputs[i]).hasClass('valid')) { $(inputs[i]).focus(); break; }
				}
				return false;
			}

			// Custom/specific validation
			/*
			var vacation_fields = form.find("[name='"+alias+"[vacation]']");
			if (vacation_fields.length) { 
				var vacation_validation = form.find("[name='"+alias+"[veteran]']:checked").val();
				if (!vacation_validation) { alert("Please choose Yes or No to continue."); return false; }
				if (vacation_validation == 'Yes') {
					var vacation_amount_validation = form.find("[name='"+alias+"[vacation_amount]']:checked").val();
					if (!vacation_amount_validation) { alert("Please choose Yes or No to continue."); return ''; }
				}
			}
			*/
		}

		// Prepare data for Submit
		data = data+'&'+form.serialize();

		$.ajax({
			url: 'wizard.php',
			type: 'POST',
			data: data,
			beforeSend: function(xhr) { 
				form.html(''); // Enhance: Perhaps change this to do an animated fade-out or slide-out.
				form.addClass('loading');
			}
		}).done(function(response, textStatus, xhr) { 
			form.html(response); // Enhance: Perhaps change this to do an animated fade-in or slide-in.
			prepare(alias);
		}).always(function(response) { // Complete
			form.removeClass('loading');
		}).fail(function (xhr, textStatus, errorThrown) { 
			alert('ERROR: Unable to connect.');  
		});
	}
	
	return { 
		action:action,
		prepare:prepare,
		submit:submit,
	}
})(jQuery);
