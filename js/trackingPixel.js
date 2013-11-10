hostProd="https://www.minyanville.com";
setTrackingPixel();

function setTrackingPixel(){
    var planCode="";
    var utmSource="";
    var utmMedium="";
    var utmTerm="";
    var utmContent="";
    var utmCampaign="";
    var pageName="";
    var refererUrl= document.referrer;
    // track page name
    if((refererUrl.indexOf("dev.minyanville.com") > -1) || (refererUrl.indexOf("qa.minyanville.com") > -1)){
	return false;
    }
    var paramsUrl = getUrlParams();
    var planCode=paramsUrl.planCode;
    var utmSource=paramsUrl.utm_source;
    var utmMedium=paramsUrl.utm_medium;
    var utmTerm=paramsUrl.utm_term;
    var utmContent=paramsUrl.utm_content;
    var utmCampaign=paramsUrl.utm_campaign;
    var location = window.location.href;
    var pageName =jQuery("#pageName").val();
    if(location.indexOf("landing-page") > -1) {
        var pageName="Landing Page";
    }
    
    if(location.indexOf("step-2") > -1) {
        var pageName="Step 2";
    }
   // find product name 
    
    if(location.indexOf("buzz-banter") > -1) {
        var productName="buzz";
    }else if(location.indexOf("optionsmith") > -1) {
        var productName="optionsmith";
    }else if(location.indexOf("techstrat") > -1) {
        var productName="techstrat";
    }else if(location.indexOf("jeff-coopers-daily-market") > -1) {
        var productName="cooper";
    }else if(location.indexOf("buy-hedge") > -1) {
        var productName="buyhedge";
    }else if(location.indexOf("stock-playbook") > -1) {
        var productName="thestockplaybook";
    }else if(location.indexOf("ads-free") > -1) {
        var productName="adsfree";
    }
    
    if(planCode==undefined){planCode='';}
    if(productName==undefined){productName='';}
    if(utmCampaign==undefined){utmCampaign='';}
    if(utmContent==undefined){utmContent='';}
    if(utmMedium==undefined){utmMedium='';}
    if(utmSource==undefined){utmSource='';}
    if(utmTerm==undefined){utmTerm='';}
    
       jQuery.ajax({
		dataType : "jsonp",
		url : hostProd+"/subscription/register/trackingPixel.php",
		
                data : "url="+encodeURIComponent(window.location.href)+"&refererUrl="+encodeURIComponent(refererUrl)+"&productName="+productName+"&planCode="+planCode+"&utmSource="+utmSource+"&utmMedium="+utmMedium+"&utmTerm="+utmTerm+"&utmContent="+utmContent+"&utmCampaign="+utmCampaign+"&pageName="+pageName,
		beforeSend: function(xhr){
		  xhr.withCredentials = true;
		},
		error : function(){},
		success : function(res){
		}
	});
    
}

function getUrlParams() {
  var paramsUrl = {};
  window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(str,key,value) {
    paramsUrl[key] = value;
  });
 
  return paramsUrl;
}