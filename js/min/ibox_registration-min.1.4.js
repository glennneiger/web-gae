var fieldValidate=false;var isValidCC=false;var mothCVV=false;function validateIboxZipcode(e){var d=true;var b=e;var a="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-+)( ";for(var c=0;c<b.length;c++){if(a.indexOf(b.charAt(c))==-1){d=false}}return d}function isValidEmail(a){field=document.getElementById(a);var c=field.value.split(";");for(var b=0;b<c.length;b++){if(c[b]==""){alert("Email field can not be left blank.");field.select();field.focus();return false}if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(c[b]))){alert('Not a valid E-mail id "'+c[b]+'"');field.select();field.focus();return false}}return true}function chkSpaceNull(a,d,c){var b=/^\s{1,}$/g;if((d=="")||(d.length==0)||(d==null)||(d.search(b)>-1)){$(a).value=c;return false}else{return true}}function iboxisValidEmail(c,d){var f=true;var e=$(d).value.split(";");for(var b=0;b<e.length;b++){if(e[b]==""){$(c).innerHTML="Email field can not be left blank.";$(d).select();f=false;return false}if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(e[b]))){var a='Not a valid E-mail id "'+e[b]+'"';$(c).innerHTML=a;$(d).select();f=false;return false}}$(c).innerHTML="";return f}function iboxisValidPassword(d,c){var a=$F(c);var b=new Boolean(true);if(!a){$(d).innerHTML="Password field can not be left blank.";$(c).select();b=false}return b}function iboxChklogin(erroDiv,email,password,remmbermeid){var url=host+"/subscription/register/iboxLoginAjax.php";var auto=0;if($(remmbermeid).checked==true){auto=1}var pars="type=login&login="+email+"&pwd="+encodeURIComponent(password)+"&autologin="+auto;var myAjax4=new Ajax.Request(url,{method:"post",parameters:pars,onLoading:loading(erroDiv),onComplete:function getLoginStatus(req){var post=eval("("+req.responseText+")");var tageturl=$F("targeturl");if(post.status==true){iboxclose();if(tageturl!=""&&tageturl!="undefined"){window.location.href=host+tageturl}else{window.location.reload()}}else{$(erroDiv).innerHTML=post.msg;return false}}})}function iboxLogin(g,d,b,e){var f=$(g).value;var c=$(d).value;var a=iboxisValidEmail(e,g);if(a){if($(d).value.toLowerCase()=="password"){$(e).innerHTML="Please Enter Any other Password Except 'Password'";$(d).select();return false}}var h=iboxisValidPassword(e,d);if(a&&h){iboxChklogin(e,f,c,b)}}function disableLoginButton(){$("header_login").hide();$("header_login_hide").show()}function processLogin(email,password){var url=host+"/subscription/register/iboxLoginAjax.php";var auto=0;if($("chkremember").checked==true){auto=1}var pars="type=login&login="+email+"&pwd="+encodeURIComponent(password)+"&autologin="+auto;var myAjax4=new Ajax.Request(url,{method:"post",parameters:pars,onLoading:disableLoginButton(),onComplete:function getLoginStatus(req){var post=eval("("+req.responseText+")");if(post.status==true){window.location.reload()}else{$("header_login_hide").hide();$("header_login").show();alert(post.msg);return false}}})}function Login(e,c){var d=$(e).value;var b=$(c).value;var a=isValidEmail(e);if(a){if($(c).value.toLowerCase()=="password"){alert("Please Enter Any other Password Except 'Password'");$(c).select();return false}}if(a){processLogin(d,b)}}function checkPasswordField(a){$(a).type="password";$(a).value=""}function chkSpaceNullPasswod(a,e,d,c){var b=/^\s{1,}$/g;if((e=="")||(e.length==0)||(e==null)||(e.search(b)>-1)){$(a).type="text";$(a).value=d;return false}else{return true}}function checkPassWordLower(a,b){var c=$F(a).toLowerCase();if(c=="password"){$(b).innerHTML="Please Enter Any other Password Except 'Password'";$(a).select();return false}else{$(b).innerHTML="";return true}}function launchCategoryScreen(a,b,c){return registrtionFieldsValidate(a,b,c)}function registrtionFieldsValidate(l,b,u){var n=$F("useremail");var h=$F("useremailConf");var q=$F("userzipcode");try{var d=$F("tuserRPassword")}catch(g){var d=$F("userRPassword")}try{var a=$F("tuserConfPassword")}catch(g){var a=$F("userConfPassword")}if(($F("userFname").toLowerCase()=="first name")||($F("userFname").toLowerCase()=="firstname")){$(l).innerHTML="Not a valid 'First Name'";$("userFname").select();return false}var k=validateAlphaFieldsOnly(l,"userFname","First Name");if(k){var r=validateAlphaFieldsOnly(l,"userLname","Last Name")}if(r){var o=iboxisValidEmail(l,"useremail")}if(o){if(n!=h){$(l).innerHTML="Email and Confirm Email does not match.";$("useremail").select();return false}else{try{var f=checkPassWordLower("userRPassword",l)}catch(m){var f=checkPassWordLower("tuserRPassword",l)}if(f){try{var c=checkPassWordLower("userConfPassword",l)}catch(m){var c=checkPassWordLower("tuserConfPassword",l)}}if(f&&c){try{var t=iboxisValidPasswordRegistration(l,"userRPassword")}catch(g){var t=iboxisValidPasswordRegistration(l,"tuserRPassword")}}else{var t=false}}}if(t){if(d!=a){$(l).innerHTML="Password and Confirm password does not match.";$("tuserRPassword").select();return false}if(q==""){$(l).innerHTML="Enter zip code.";$("userzipcode").select();return false}if(validateIboxZipcode(q)==false){$(l).innerHTML="Invalid zip code.";$("userzipcode").select();return false}if($("agreechkbox").checked==false){$(l).innerHTML="You did not agree with our privacy terms.";return false}}if((k==false)||(r==false)||(o==false)||(t==false)){return false}else{$(l).innerHTML="";var e=host+"/subscription/register/iboxLoginAjax.php";var s="type=checkUser&login="+n;var p=new Ajax.Request(e,{method:"post",parameters:s,onLoading:loading(l),onComplete:function j(v){var w=0;w=parseInt(v.responseText);if(w){$(l).innerHTML="This user already in use our System. Please Select Another email ID";$("useremail").select();return false}else{$(l).innerHTML="";$(b).hide();$("cuurntscreen").value=parseInt($F("cuurntscreen"))+1;if($("cuurntscreen").value==2){$("ibox_wrapper").setStyle("width:537px;height:440px;");$("parentdiv").setStyle("width:537px;")}$(u).show();return true}}})}}function validateAlphaFieldsOnly(g,a,f){var c=trim($F(a));var e=new Boolean(true);if(!c){e=false}var b="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";for(var d=0;d<c.length;d++){if(b.indexOf(c.charAt(d))==-1){e=false}}if(!e){var h="Not a valid "+f;$(g).innerHTML=h;$(a).select()}return e}function launchNextScreen(b,d,e){var c=false;if(d=="screen_4"){if(!checkForAnySelectedProducts()){c=true;e="screen_6"}else{if($("buzzproduct").checked){var a=false;$("iboxregform").getInputs("radio","buzz_1").each(function(f){if(f.checked){a=true}});if(!a){$("producterr").innerHTML="Please Select Any subscription";return a}}else{if($("coopproduct").checked){var a=false;$("iboxregform").getInputs("radio","cooper_1").each(function(f){if(f.checked){a=true}});if(!a){$("producterr").innerHTML="Please Select Any subscription";return a}}else{if($("flexproduct").checked){var a=false;$("iboxregform").getInputs("radio","flexfolio_1").each(function(f){if(f.checked){a=true}});if(!a){$("producterr").innerHTML="Please Select Any subscription";return a}}}}}}if(c==false){$(d).hide();$("cuurntscreen").value=parseInt($F("cuurntscreen"))+1;if($("cuurntscreen").value==3){$("ibox_wrapper").setStyle("width:537px;height:475px;");$("parentdiv").setStyle("width:537px;")}else{if($("cuurntscreen").value==4){$("ibox_wrapper").setStyle("width:537px;height:415px;");$("parentdiv").setStyle("width:537px;")}else{if($("cuurntscreen").value==5){$("ibox_wrapper").setStyle("width:537px;height:445px;");$("parentdiv").setStyle("width:537px;")}}}$(e).show()}else{if((c==true)&&(e=="screen_6")){registeruser(0,b,d,e)}}}function checkForAnySelectedProducts(){var a=false;$$("input.checkboxes").each(function(b){if(b.checked){a="true"}});return a}function radiofldsupdates(a){switch(a){case"buzzproduct":if($(a).checked){$$("input.buzzs").each(function(b){b.enable()})}else{$$("input.buzzs").each(function(b){b.disable()})}break;case"coopproduct":if($(a).checked){$$("input.coopss").each(function(b){b.enable()})}else{$$("input.coopss").each(function(b){b.disable()})}break;case"flexproduct":if($(a).checked){$$("input.flexos").each(function(b){b.enable()})}else{$$("input.flexos").each(function(b){b.disable()})}break;default:return false}}function logout(e){var a=host+"/subscription/register/iboxLoginAjax.php";var b="type=logout&login="+e;var d=new Ajax.Request(a,{method:"post",parameters:b,onComplete:function c(f){if(f.responseText){if($("logouttarget").value!=""){window.location.href=host}else{window.location.reload()}}else{}}})}function validatePaymentInfo(c,b,a){if(!checkCreditCard(b,a)){$(c).innerHTML=(ccErrors[ccErrorNo]);if(ccErrors[ccErrorNo]!="Unknown card type"){$("iboxccnum").select()}else{$("iboxcctype").select()}return false}else{return true}}function validatePaymentFields(a){if($F("billingaddress1")==""){$(a).innerHTML="Billing Address Is Blank";$("billingaddress1").select();return false}else{if($F("billingcity")==""){$(a).innerHTML="City Is Blank";$("billingcity").select();return false}else{if(($F("iboxphone")=="")||(isNaN($F("iboxphone")))){$(a).innerHTML="Invalid Phone Number";$("iboxphone").select();return false}else{if($("iboxstate").disabled==false&&$F("iboxstate")==""){$(a).innerHTML="State is Blank";$("iboxstate").select();return false}else{if(($F("iboxcountry")=="")||($F("iboxcountry")=="CLEARED")){$(a).innerHTML="Please Select the country";$("iboxcountry").select();return false}else{return true}}}}}}function paymentScreenvalidate(skip,errorDiv,closescreenID,openscreenID){if(skip){if(confirm(skip_alert)){registeruser(skip,errorDiv,closescreenID,openscreenID)}else{return false}}if((skip==0)&&(closescreenID=="screen_5")){fieldValidate=validatePaymentFields(errorDiv);if(fieldValidate){var name=$F("iboxcctype");var number=$F("iboxccnum");if(name==""){$(errorDiv).innerHTML="Please Select the Credit Crard Type";$("iboxcctype").focus();return false}else{if((isNaN(number))||(number=="")){$(errorDiv).innerHTML="Invalid Credit Crard Number";$("iboxccnum").select();return false}}isValidCC=validatePaymentInfo(errorDiv,number,name);if(!isValidCC){return false}if((fieldValidate)&&(isValidCC)){mothCVV=checkMonthCvv(errorDiv)}}if(fieldValidate&&isValidCC&&mothCVV){var fnalcategory_ids="";var fnalcontributors_ids="";for(i=0;i<document.iboxregform.category.length;i++){if(document.iboxregform.category[i].checked){var category_ids="";category_ids=document.iboxregform.category[i].value;if(fnalcategory_ids==""){fnalcategory_ids=","+category_ids}else{fnalcategory_ids=fnalcategory_ids+","+category_ids}}}for(i=0;i<document.iboxregform.contributors.length;i++){if(document.iboxregform.contributors[i].checked){var contributors_ids="";contributors_ids=document.iboxregform.contributors[i].value;if(fnalcontributors_ids==""){fnalcontributors_ids=","+contributors_ids}else{fnalcontributors_ids=fnalcontributors_ids+","+contributors_ids}}}var myhash=($("iboxregform").serialize());var url=host+"/subscription/register/iboxLoginAjax.php";var pars="type=registration&productselected=1&"+myhash+"&emailCategories="+fnalcategory_ids+"&authorCategories="+fnalcontributors_ids;var myAjax4=new Ajax.Request(url,{method:"post",parameters:pars,onLoading:loading(errorDiv),onComplete:function submitRegData(req){var getStatus=0;var post=eval("("+req.responseText+")");if(post.status==true){$("jsfname").innerHTML=post.firstname;$("jslname").innerHTML=post.lastname;launchNextScreen(errorDiv,closescreenID,openscreenID)}else{$(errorDiv).innerHTML=post.msg;return false}}})}}}function checkMonthCvv(c){var b=true;if($F("iboxccexpire")==""){$(c).innerHTML="Expiration Date Is Blank";$("iboxccexpire").select();b=false;return false}var d=$F("iboxccexpire");if((d.search("/")==-1)||(d.length!=7)){$(c).innerHTML="Invalid Format";$("iboxccexpire").select();b=false}var a=d.split("/");if(isNaN(parseInt(a[0]))){$(c).innerHTML="Invalid Month";$("iboxccexpire").select();b=false}else{if((parseInt(a[0])<0)||(parseInt(a[0])>12)){$(c).innerHTML="Invalid Month";$("iboxccexpire").select();b=false}else{if(isNaN(parseInt(a[1]))){$(c).innerHTML="Invalid Year";$("iboxccexpire").select();b=false}else{if(parseInt(a[1])<0){$(c).innerHTML="Invalid Year";$("iboxccexpire").select();b=false}}}}if(b){$(c).innerHTML="";if((isNaN($F("iboxcvvnum")))||($F("iboxcvvnum")=="")){$(c).innerHTML="Invalid CVV Number";$("iboxcvvnum").select();b=false}}return b}function registeruser(skip,errorDiv,closescreenID,openscreenID){var fnalcategory_ids="";var fnalcontributors_ids="";for(i=0;i<document.iboxregform.category.length;i++){if(document.iboxregform.category[i].checked){var category_ids="";category_ids=document.iboxregform.category[i].value;if(fnalcategory_ids==""){fnalcategory_ids=","+category_ids}else{fnalcategory_ids=fnalcategory_ids+","+category_ids}}}for(i=0;i<document.iboxregform.contributors.length;i++){if(document.iboxregform.contributors[i].checked){var contributors_ids="";contributors_ids=document.iboxregform.contributors[i].value;if(fnalcontributors_ids==""){fnalcontributors_ids=","+contributors_ids}else{fnalcontributors_ids=fnalcontributors_ids+","+contributors_ids}}}var myhash=($("iboxregform").serialize());var url=host+"/subscription/register/iboxLoginAjax.php";var pars="skip="+skip+"&type=registration&productselected=0&"+myhash+"&emailCategories="+fnalcategory_ids+"&authorCategories="+fnalcontributors_ids;var myAjax4=new Ajax.Request(url,{method:"post",parameters:pars,onLoading:loading(errorDiv),onComplete:function submitRegData(req){var getStatus=0;var post=eval("("+req.responseText+")");if(post.status==true){$("jsfname").innerHTML=post.firstname;$("jslname").innerHTML=post.lastname;$(closescreenID).hide();if(skip){$("cuurntscreen").value=parseInt($F("cuurntscreen"))+1}else{$("cuurntscreen").value=parseInt($F("cuurntscreen"))+2}if($("cuurntscreen").value==6){$("ibox_wrapper").setStyle("width:537px;height:430px;");$("parentdiv").setStyle("width:537px;")}$(openscreenID).show()}else{return false}}})}function closemeibox(){if(parseInt($F("cuurntscreen"))==6){iboxclose();window.location.reload()}else{iboxclose()}}function checkforgotpassword(f,g){var a=iboxisValidEmail(f,g);if(a){var d=trim($F(g));var b=host+"/subscription/register/iboxLoginAjax.php";var c="type=forgotpassword&uid="+d;var e=new Ajax.Request(b,{method:"post",parameters:c,onLoading:loading(f),onComplete:function h(j){var k=0;k=parseInt(j.responseText);if(k){mailforgotpassword(f,g,k)}else{$(f).innerHTML="We couldn't find your password in our system. Either try again or re-register.";$(g).select();return false}}})}}function mailforgotpassword(c,h,g){var f=iboxisValidEmail(c,h);if(f){var d=trim($F(h));var a=host+"/subscription/register/iboxLoginAjax.php";var b="type=sendmail&uid="+d+"&viaid="+g;var j=new Ajax.Request(a,{method:"post",parameters:b,onLoading:$(c).innerHTML="Sending...",onComplete:function e(k){if(k.responseText!=""){$(c).innerHTML=k.responseText}else{$(c).innerHTML=""}}})}}function checkenterKey(c,b,d){var a=c.keyCode;if(a==undefined){a=c.which}if(a==27){closemeibox()}if(a==13){if((b=="iboxlogin")&&(d==1)){iboxLogin("useremailadd","userpassword","autologin","loginErrorMsg")}else{if((b=="iboxRegist")&&(d==1)){launchCategoryScreen("regError","screen_1","screen_2")}else{if((b=="iboxRegist")&&(d==2)){launchNextScreen("regError","screen_2","screen_3")}else{if((b=="iboxRegist")&&(d==3)){launchNextScreen("regError","screen_3","screen_4")}else{if((b=="iboxRegist")&&(d==4)){launchNextScreen("producterr","screen_4","screen_5")}else{if((b=="iboxRegist")&&(d==5)){paymentScreenvalidate(skip,"paymenterr","screen_5","screen_6")}}}}}}}}function textboxToPassword(d,f,b){var c=$(d.id);var e=document.createElement("input");e.setAttribute("name",f);e.setAttribute("id",f);e.setAttribute("type","password");e.setAttribute("tabindex",b);e.setAttribute("onKeyPress",'javascript:checkenterKey(event,"iboxRegist",1);');c.parentNode.replaceChild(e,c);var a=document.getElementById(f);$(f).addClassName("login_input_box");a.focus();$(f).select()};