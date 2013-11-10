function empty(mixed_var) {
    
    var key;    
    if (mixed_var === "" ||
        mixed_var === 0 ||
        mixed_var === "0" ||
        mixed_var === null ||
        mixed_var === false ||
        typeof mixed_var === 'undefined'
    ){
        return true;
    } 
    if (typeof mixed_var == 'object') {
        for (key in mixed_var) {
            return false;
        }        return true;
    }
 
    return false;
}

function validateLead(){	
var error;
	if(empty($("input[name='lead[fname]']").val()))
	{
		error="First Name is mandatory.";
	}else if(empty($("input[name='lead[lname]']").val())){
		error="Last Name is mandatory."
	}else if(empty($("input[name='lead[email]']").val()) && isValidEmail('lead[email]')){
		error="Email is mandatory.";
	}else if(empty($("input[name='lead[addr]']").val())){
		error="Address is mandatory.";
	}else if(empty($("input[name='lead[city]']").val())){
		error="City is mandatory.";
	}else if(empty($("select[name='lead[state]']").val())){
		error="State is mandatory.";
	}else if(empty($("input[name='lead[phone]']").val())){
		error="Phone # is mandatory.";
	}else if(empty($("input[name='lead[zip]']").val())){
		error="Zip Code is mandatory.";
	}
	else if(empty($("input[name='lead[product]']").val())){
		error="Please select a product.";
	}
	/*else if(empty($("input[name='lead[investableassets]']").val())){
		error="Please select investable assets.";
	}else if(empty($("input[name='lead[tradepermonth]']").val())){
		error="select select trades per month.";
	}*/
	else{		
		$("#registration").submit();
		return true;
	}		
	$("#error").html(error);
	return false;
}
