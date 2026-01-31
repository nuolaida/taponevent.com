function select_country(field_country, field_city, country_selected) {
	my_length = document.forms['Forma'].elements[field_country].options.length;
	
	dis = false;
	for (i=0; i<my_length; i++) {
		if (document.forms['Forma'].elements[field_country].options[i].selected == true) {
	       	if (document.forms['Forma'].elements[field_country].options[i].value != country_selected || document.forms['Forma'].elements[field_country].options[i].value == 0) {
				dis = true;
        	}
		}
   	}
	document.forms['Forma'].elements[field_city].disabled = dis;
}

	function popup_window(url) {
		window.open(url, '_blank', 'width=800,height=600,status=no,location=no,toolbar=no,menubar=no,scrollbars=yes,resizable=yes,screenX=0,screenY=0');
	}


