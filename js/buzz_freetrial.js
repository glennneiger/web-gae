// allow only characters
function validChar(value) {		
	var string = $.trim(value);
	var bVal = true;
	if (!string){	
		bVal = false;
	}			
	var Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	for (var i = 0; i < string.length; i++) {
		if (Chars.indexOf(string.charAt(i)) == -1){
			 bVal = false; 
		}
	}			
	return bVal;			
} 			
// validate email address
function validEmail(email){

	if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(email))){					
			return false;
		}
	return true;
}
function getFreeTrial()
{		
	var error_msg = "";			
	var firstname = $.trim($('#firstname').val());
	var lastname = $.trim($('#lastname').val());			
	var email = $.trim($('#email').val());			
	var pwd = $.trim($('#pwd').val());			
	var phone = $.trim($('#phone').val());
	if(firstname == "Please Enter First Name")
	{				
		firstname = "";
		$('#firstname').val("");
	}
	if(lastname == "Please Enter last Name")
	{
		lastname = "";
		$('#lastname').val("");
	}
	if(email == "Please Enter Your Email")
	{
		email = "";
		$('#email').val("");
	}
	if(pwd == "Please Select A Password")
	{
		 pwd = "";
		 $('#pwd').val("");
	}
	if(phone == "Please Enter Your Telephone")
	{ 
		phone = "";
		 $('#phone').val("");
	}									
	if(firstname== '')
	{
		error_msg+= "Enter First Name\n";
	}
	else if(validChar(firstname) == false)
	{
		error_msg+= "Enter a valid First Name\n";
	}
	if(lastname== '')
	{
		error_msg+= "Enter Last Name\n";
	}
	else if(validChar(lastname) == false)
	{
		error_msg+= "Enter a valid Last Name\n";
	}
	if(email== '')
	{
		error_msg+= "Enter Email\n";
	}
	else if(validEmail(email) == false)
	{
		error_msg+= "Enter a valid Email\n";
	}
	if(pwd== '')
	{
		error_msg+= "Enter Password\n";
	}
	if(error_msg == "")
	{
		var pars="type=buzz_oneweek";
		pars+='&uid='+email;		
		pars+='&pwd='+pwd;
		pars+='&firstname='+firstname;
		pars+='&lastname='+lastname;					
		pars+='&phone='+phone;
		pars+='&promocode='+$('#promocode').val();			
		var url=host+'/subscription/softtrials/ajaxcontroller.php';	
		$("#loading").ajaxStart(function(){
		$(this).html("Loading ...");
			 });
		$.ajax({
			   type: "POST",
			   url: url,
			   data: pars,
			   dataType: "json",
			   success :function(data){
				if(data.status == true)
					{
						window.location.href=host+"/subscription/softtrials/buzz/welcome2012.htm";
					}
					else
					{
						$("#reg_error").html(data.msg);
					}
				 }
			});				
	}
	else
	{
		alert(error_msg);
		return false;
	}			
}