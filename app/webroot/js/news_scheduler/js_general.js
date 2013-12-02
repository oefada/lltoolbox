function check_offer_product_required(myForm) {
	if(!myForm.clientId.value && !myForm.offer_id.value) {
		objProd = myForm.clientId;		
		objOffer = myForm.offer_id;
		objSegType = myForm.mailing_segment_credit_type_id;

		objProd.setAttribute('required', '');
		objOffer.setAttribute('required', '');
		objSegType.setAttribute('required', '');
	}
}

function validate_form(myForm) {
	var debugTxt = '';
	var message = '';
	var label = '';
	check_offer_product_required(myForm);
	
	for(i=0; i<myForm.elements.length; i++) {
		if(myForm.elements[i].getAttribute('required') && myForm.elements[i].value == '') {
			label = myForm.elements[i].getAttribute('label');
			message += label + '\n';
		}
	}

	if(message) {
		message = 'Please enter data for the following fields:\n' + message;
		alert(message);
		return false;
	}
	myForm.submit();
}
	
function set_field_enable(myForm, segment_type_id) {
		objProd = myForm.clientId;
		objOffer = myForm.offer_id;
		objSegType = myForm.mailing_segment_credit_type_id;
		
		objProd.disabled = true;
		objOffer.disabled = true;
		objSegType.disabled = true;
		
		objProd.setAttribute('required', '');
		objOffer.setAttribute('required', '');
		objSegType.setAttribute('required', '');
				
	if(segment_type_id == 1 || segment_type_id == 2 || segment_type_id == 3) {

		objProd.disabled = false;
		objSegType.disabled = false;
		objProd.setAttribute('required', 1);
		objSegType.setAttribute('required', 1);
	}
	if(segment_type_id == 2) {
		objOffer.disabled = false;
		objOffer.setAttribute('required', 1);
	}
}

function show_what(segment_type_id) {
	for(i=1;i<5;i++) {
		show_stuff(i, 'none');
	}
	if(segment_type_id == 1 || segment_type_id == 3) {
		show_stuff(1, 'block');
		show_stuff(3, 'block');
		document.mailing_segment_form.offer_id.value = '';
	}else if(segment_type_id == 2) {
		show_stuff(2, 'block');
		show_stuff(4, 'block');
		document.mailing_segment_form.clientId.value = '';
	}
	obj = document.getElementById('product_offer_id');
	obj.disabled = false;
	show_stuff(szDivID, 'block');
}

function show_stuff(szDivID, iState){ // 1 visible, 0 hidden
	obj = document.getElementById(szDivID);
	obj.style.display = iState;
}