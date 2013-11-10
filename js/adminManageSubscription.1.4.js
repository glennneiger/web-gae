function isValidEmail(emails,divid){
	var bools=true;
	if(!(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-zA-Z]{2,6}(?:\.[a-zA-Z]{2})?)$/.test(emails)))
	{
		jQuery('#'+divid).css("background-color","#FFEEEE");
		bools=false;
		return false;
	}
	return bools;
}

function isValidDate(date)
{
	var bools=true;
	var matches = /^(\d{4})[-\/](\d{2})[-\/](\d{2})$/.exec(date);
	if (matches == null){ 
		bools=false;
		return false;
	}
	return bools;
}

function checkUpdatedInfo(){
	var manageEmail = jQuery('#email').val();
	if(manageEmail==''){
		jQuery('#email').css("background-color","#FFEEEE");
		jQuery('#userSubError').html("Email is mandatory.<br>");
		return false;
	}
	
    if(!isValidEmail(manageEmail,'email'))
	{
    	jQuery('#userSubError').html("Invalid Email id");
		return false;
	}
    jQuery('#updateInfo').submit();
}

function checkAddOrder(){
	var planCode = jQuery('#planCode').val();
	if(planCode==''){
		jQuery('#planCode').css("background-color","#FFEEEE");
		jQuery('#productError').html("Plan Code is mandatory.<br>");
		return false;
	}

	var planName = jQuery('#planName').val();
	if(planName==''){
		jQuery('#planName').css("background-color","#FFEEEE");
		jQuery('#productError').html("Plan Name is mandatory.<br>");
		return false;
	}
	/*var uuid = jQuery('#uuid').val();
	if(uuid==''){
		jQuery('#uuid').css("background-color","#FFEEEE");
		jQuery('#productError').html("uuid is mandatory.<br>");
		return false;
	}*/
	var state = jQuery('#state').val();
	if(state==''){
		jQuery('#state').css("background-color","#FFEEEE");
		jQuery('#productError').html("Status is mandatory.<br>");
		return false;
	}
	var qty = jQuery('#qty').val();
	if(qty==''){
		jQuery('#qty').css("background-color","#FFEEEE");
		jQuery('#productError').html("Quantity is mandatory.<br>");
		return false;
	}
	var amt = jQuery('#amt').val();
	if(amt==''){
		jQuery('#amt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Plan Charge is mandatory.<br>");
		return false;
	}
	var activatedAt = jQuery('#activatedAt').val();
	if(activatedAt==''){
		jQuery('#activatedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Start Date is mandatory.<br>");
		return false;
	}
	if(isValidDate(activatedAt)==false){
		jQuery('#activatedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Invalid Start Date.<br>");
		return false;
	}
	
	var startedAt = jQuery('#startedAt').val();
	if(startedAt==''){
		jQuery('#startedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Current Period Start Date is mandatory.<br>");
		return false;
	}
	if(isValidDate(startedAt)==false){
		jQuery('#startedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Invalid Current Period Start Date.<br>");
		return false;
	}
	
	var endAt = jQuery('#endAt').val();
	if(endAt==''){
		jQuery('#endAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Current Period End Date is mandatory.<br>");
		return false;
	}
	if(isValidDate(endAt)==false){
		jQuery('#endAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Invalid Current Period End Date.<br>");
		return false;
	}
	
	var trialStartedAt = jQuery('#trialStartedAt').val();
	/*if(trialStartedAt==''){
		jQuery('#trialStartedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Trial Start Date is mandatory.<br>");
		return false;
	}*/
	if(trialStartedAt!='' && isValidDate(trialStartedAt)==false){
		jQuery('#trialStartedAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Invalid Trial Start Date.<br>");
		return false;
	}
	
	var trialEndAt = jQuery('#trialEndAt').val();
	/*if(trialEndAt==''){
		jQuery('#trialEndAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Trial Start Date is mandatory.<br>");
		return false;
	}*/
	if(trialEndAt!='' && isValidDate(trialEndAt)==false){
		jQuery('#trialEndAt').css("background-color","#FFEEEE");
		jQuery('#productError').html("Invalid Trial Start Date.<br>");
		return false;
	}
	
	 jQuery('#addOrder').submit();
}

function getSelectedCountry(country){
	jQuery('select#country option[value="'+country+'"]').attr('selected', true);
}

function returnProductName(productdiv,filepath,userid){
	var product = $('#'+productdiv).val();
	if(userid==''){
		window.location= filepath+'?product='+product;
	}else{
		window.location= filepath+'?id='+userid+'&product='+product;
	}
    exit;
}

function returnPlanDetails(productdiv,filepath,plandiv,userid){
	var plan = $('#'+plandiv).val();
	var product = $('#'+productdiv).val();
	if(userid==''){
		window.location = filepath+'?product='+product+'&plan='+plan;
	}else{
		window.location = filepath+'?id='+userid+'&product='+product+'&plan='+plan;
	}
    exit;
}

function checkProductInfo(){
	var product = jQuery('#product').val();
	if(product=='0'){
		jQuery('#product').css("background-color","#FFEEEE");
		jQuery('#subError').html("Please select Product.<br>");
		jQuery('#product').focus();
		return false;
	}
	
	var planCode = jQuery('#planCode').val();
	if(planCode==''){
		jQuery('#planCode').css("background-color","#FFEEEE");
		jQuery('#subError').html("Plan Code is mandatory.<br>");
		jQuery('#planCode').focus();
		return false;
	}
	
	var planName = jQuery('#planName').val();
	if(planName==''){
		jQuery('#planName').css("background-color","#FFEEEE");
		jQuery('#subError').html("Plan Name is mandatory.<br>");
		jQuery('#planName').focus();
		return false;
	}
	
	var planterm = jQuery('#planterm').val();
	if(planterm=='0'){
		jQuery('#planterm').css("background-color","#FFEEEE");
		jQuery('#subError').html("Please select Plan Term.<br>");
		jQuery('#planterm').focus();
		return false;
	}
	
	var planType = jQuery('#planType').val();
	if(planType==''){
		jQuery('#planType').css("background-color","#FFEEEE");
		jQuery('#subError').html("Plan Type is mandatory.<br>");
		jQuery('#planType').focus();
		return false;
	}
	
	var planPeriod = jQuery('#planPeriod').val();
	if(planPeriod=='0'){
		jQuery('#planPeriod').css("background-color","#FFEEEE");
		jQuery('#subError').html("Please select Plan Period.<br>");
		jQuery('#planPeriod').focus();
		return false;
	}
	
	var planCharge = jQuery('#planCharge').val();
	if(planCharge==''){
		jQuery('#planCharge').css("background-color","#FFEEEE");
		jQuery('#subError').html("Plan Charge is mandatory.<br>");
		jQuery('#planCharge').focus();
		return false;
	}
	
	var planSetupFee = jQuery('#planSetupFee').val();
	if(planSetupFee==''){
		jQuery('#planSetupFee').css("background-color","#FFEEEE");
		jQuery('#subError').html("One time Set Up Fee is mandatory.<br>");
		jQuery('#planSetupFee').focus();
		return false;
	}
	
	var planTrial = jQuery('#planTrial').val();
	if(planTrial=='0'){
		jQuery('#planTrial').css("background-color","#FFEEEE");
		jQuery('#subError').html("Please select Plan Trial.<br>");
		jQuery('#planTrial').focus();
		return false;
	}
	
	var recurly_plan_promotional_headline = jQuery('#recurly_plan_promotional_headline').val();
	if(recurly_plan_promotional_headline==''){
		jQuery('#recurly_plan_promotional_headline').css("background-color","#FFEEEE");
		jQuery('#subError').html("Funnel Page Heading is mandatory.<br>");
		jQuery('#recurly_plan_promotional_headline').focus();
		return false;
	}
	
	var recurly_plan_feature_headline = jQuery('#recurly_plan_feature_headline').val();
	if(recurly_plan_feature_headline==''){
		jQuery('#recurly_plan_feature_headline').css("background-color","#FFEEEE");
		jQuery('#subError').html("Funnel Page Feature Heading is mandatory.<br>");
		jQuery('#recurly_plan_feature_headline').focus();
		return false;
	}
	
	var recurly_plan_promotional_features = jQuery('#recurly_plan_promotional_features').val();
	if(recurly_plan_promotional_features==''){
		jQuery('#recurly_plan_promotional_features').css("background-color","#FFEEEE");
		jQuery('#subError').html("Funnel Page Features is mandatory.<br>");
		jQuery('#recurly_plan_promotional_features').focus();
		return false;
	}
	
	var recurly_plan_promotional_desc = jQuery('#recurly_plan_promotional_desc').val();
	if(recurly_plan_promotional_desc==''){
		jQuery('#recurly_plan_promotional_desc').css("background-color","#FFEEEE");
		jQuery('#subError').html("Funnel Page Description is mandatory.<br>");
		jQuery('#recurly_plan_promotional_desc').focus();
		return false;
	}
	jQuery('#addPlan').submit();
}

function generateSubReport(reportType,pageNumber,colName,orderBy,isRefreshSummary){
	if(jQuery('#reportError').html()!=''){
		jQuery('#reportError').empty();
	}
	
	if(jQuery('#recordCount').html()!=''){
		jQuery('#recordCount').empty();
	}
		
	var productName = jQuery('#product').val();
	/*if(productName=='0'){
		jQuery('#product').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Please Select Product Name.<br>");
		return false;
	}*/
	
	var fromDate = jQuery('#fromDate').val();
	var toDate = jQuery('#toDate').val();
	
	if(fromDate=='' && toDate==''){
		jQuery('#reportError').html("Please Select Date Range.<br>");
		return false;
	}
	
	if(fromDate==''){
		jQuery('#reportError').html("Please enter From Date.<br>");
		return false;
	}
	
	if(toDate==''){
		jQuery('#reportError').html("Please enter To Date.<br>");
		return false;
	}
	
	var freeTrial = jQuery('#freeTrial').val();
	var inFreeTrial = jQuery('#inFreeTrial').val();
	
	/*var from = fromYear+'/'+fromMonth+'/'+fromDate;
	var to = toYear+'/'+toMonth+'/'+toDate;*/
	
	if(!compareDates(fromDate,toDate)){
		jQuery('#reportError').html("Invalid Date Range.<br>");
		return false;
	}
	if(isRefreshSummary=="newSummary"){
		showSummary(reportType,fromDate,toDate,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
	}else{
		showReports(reportType,fromDate,toDate,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
	}
}

function compareDates(frmDate,toDate)
{
    var str1 = frmDate;
    var str2 = toDate;
    var yr1  = parseInt(str1.substring(0,4),10);
    var mon1 = parseInt(str1.substring(5,7),10);
    var dt1  = parseInt(str1.substring(8,10),10);
    var yr2  = parseInt(str2.substring(0,4),10);
    var mon2 = parseInt(str2.substring(5,7),10);
    var dt2  = parseInt(str2.substring(8,10),10);
    var date1 = new Date(yr1, mon1, dt1);
    var date2 = new Date(yr2, mon2, dt2);
 
    var bools=true;
    if(date2 < date1)
    {
    	bools=false;
        return false;
    }
    return bools;
}

function checkUserDetail(){
	var manageEmail = jQuery('#email').val();
	if(manageEmail==''){
		jQuery('#email').css("background-color","#FFEEEE");
		jQuery('#userSubError').html("Email is mandatory.<br>");
		return false;
	}
	
    if(!isValidEmail(manageEmail,'email'))
	{
    	jQuery('#userSubError').html("Invalid Email id");
		return false;
	}
    
    var prefix = jQuery('#prefix').val();
    var fname = jQuery('#fname').val();
    if(fname==''){
		jQuery('#fname').css("background-color","#FFEEEE");
		jQuery('#userSubError').html("First Name is mandatory.<br>");
		return false;
	}
    
    var lname = jQuery('#lname').val();
    if(lname==''){
		jQuery('#lname').css("background-color","#FFEEEE");
		jQuery('#userSubError').html("Last Name is mandatory.<br>");
		return false;
	}
    
    var password = jQuery('#password').val();
    if(password==''){
		jQuery('#password').css("background-color","#FFEEEE");
		jQuery('#userSubError').html("Password is mandatory.<br>");
		return false;
	}
    
    var phone = jQuery('#phone').val();
    
    jQuery('#addUser').submit();
}


function showReports(reportName,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial){
	if(reportName=='subscriprionReport'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/subReportMod.php",
			data : "type=activeUsers&fromDate="+from+"&toDate="+to+"&product="+productName+"&p="+pageNumber+"&colName="+colName+"&orderBy="+orderBy+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial,
			beforeSend : function(){
				if(jQuery('#recordCount').html()!=''){
					jQuery('#recordCount').empty();
				}
				var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
				jQuery('#reportContent').html(content);
			},
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					var reportBody = result.body;
					var recordCount = result.count;
					var currentPageCount = result.currentPageCount;
					jQuery('#recordCount').html(recordCount+' Record Found.<br>'+currentPageCount+' record on this Page');
					jQuery('#reportContent').html(reportBody);
					if(orderBy!=''){
						if(orderBy=="asc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingDesc');
						}else if(orderBy=="desc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingAsc');
						}
					}
				}else{
					var reportBody = result.body;
					jQuery('#reportContent').html(reportBody);
				}
			}
		});
	}else if(reportName=="exportSubscriptionReport"){
		window.open("subsciprtionReport.export.htm?fromDate="+from+"&toDate="+to+"&product="+productName+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial);
	}
}

function addMoreuser(pathName){
	window.location=pathName;
    exit;
}

function updatePlanPeriod(term){
	if(term=='0' || term=="Soft Trial"){
		jQuery('select#planPeriod option[value="0"]').attr('selected', 'selected');
	}else{
		if(term=="Monthly" || term=="Monthly Trial"){
			jQuery('select#planPeriod option[value="1 months"]').attr('selected', 'selected');
		}else if(term=="Quaterly" || term=="Quaterly Trial"){
			jQuery('select#planPeriod option[value="3 months"]').attr('selected', 'selected');
		}else if(term=="Annual" || term=="Annual Trial" || term=="Complimentary"){
			jQuery('select#planPeriod option[value="1 year"]').attr('selected', 'selected');
		}
	}
	
}


function generateMarketingReport(reportType,pageNumber,colName,orderBy){
	jQuery('#recordCount').empty();
	if(jQuery('#reportError').html()!=''){
		jQuery('#reportError').empty();
	}
		
	var productName = jQuery('#product').val();
	
	var fromDate = jQuery('#fromDate').val();
	if(fromDate=='0'){
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var fromMonth = jQuery('#fromMonth').val();
	if(fromMonth=='0'){
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var fromYear = jQuery('#fromYear').val();
	if(fromYear=='0'){
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var toDate = jQuery('#toDate').val();
	if(toDate=='0'){
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var toMonth = jQuery('#toMonth').val();
	if(toMonth=='0'){
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var toYear = jQuery('#toYear').val();
	if(toYear=='0'){
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var from = fromYear+'/'+fromMonth+'/'+fromDate;
	var to = toYear+'/'+toMonth+'/'+toDate;
	
	if(!compareDates(from,to)){
		jQuery('#reportError').html("Invalid Date Range.<br>");
		return false;
	}
	
	showMarketReports(reportType,from,to,productName,pageNumber,colName,orderBy);
}

function showMarketReports(reportName,from,to,productName,pageNumber,colName,orderBy){
	if(reportName=='marketingDashboard'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/marketReportMod.php",
			data : "type=marketReport&fromDate="+from+"&toDate="+to+"&product="+productName+"&p="+pageNumber+"&colName="+colName+"&orderBy="+orderBy,
			beforeSend : function(){
				var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
				jQuery('#reportContent').html(content);
			},
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					var reportBody = result.body;
					var recordCount = result.count;
					if(recordCount>0){
						jQuery('#recordCount').html(recordCount+' Record Found.');
					}
					jQuery('#reportContent').html(reportBody);
					if(orderBy!=''){
						if(orderBy=="asc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingDesc');
						}else if(orderBy=="desc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingAsc');
						}
					}
				}else if(result.status=='error'){
					var reportBody = result.msg;
					jQuery('#reportError').html(reportBody);
					jQuery('#reportContent').empty();
				}else{
					
					var reportBody = result.body;
					jQuery('#reportContent').html(reportBody);
				}
			}
		});
	}else if(reportName=="exportMarketingReport"){
		window.open("marketingReport.export.htm?fromDate="+from+"&toDate="+to+"&product="+productName);
	}
}

function showSummary(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial){
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/subReportMod.php",
		data : "type=showSummary&fromDate="+from+"&toDate="+to+"&product="+productName+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial,
		beforeSend : function(){
			jQuery('#summaryContent').empty();
			var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
			jQuery('#reportContent').html(content);
		},
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
				showReports(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
			}else{
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
			}
		}
	});
}

function generateOptSubReport(reportType,pageNumber,colName,orderBy,isRefreshSummary){
	if(jQuery('#reportError').html()!=''){
		jQuery('#reportError').empty();
	}
		
	var productName = jQuery('#product').val();
	/*if(productName=='0'){
		jQuery('#product').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Please Select Product Name.<br>");
		return false;
	}*/
	
	var fromDate = jQuery('#fromDate').val();
	if(fromDate=='0'){
		//jQuery('#fromDate').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var fromMonth = jQuery('#fromMonth').val();
	if(fromMonth=='0'){
		//jQuery('#fromMonth').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var fromYear = jQuery('#fromYear').val();
	if(fromYear=='0'){
		//jQuery('#fromYear').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid From Date.<br>");
		return false;
	}
	
	var toDate = jQuery('#toDate').val();
	if(toDate=='0'){
		//jQuery('#toDate').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var toMonth = jQuery('#toMonth').val();
	if(toMonth=='0'){
		//jQuery('#toMonth').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var toYear = jQuery('#toYear').val();
	if(toYear=='0'){
		//jQuery('#toYear').css("background-color","#FFEEEE");
		jQuery('#reportError').html("Invalid To Date.<br>");
		return false;
	}
	
	var freeTrial = jQuery('#freeTrial').val();
	var inFreeTrial = jQuery('#inFreeTrial').val();
	
	var from = fromYear+'/'+fromMonth+'/'+fromDate;
	var to = toYear+'/'+toMonth+'/'+toDate;
	
	if(!compareDates(from,to)){
		jQuery('#reportError').html("Invalid Date Range.<br>");
		return false;
	}
	if(isRefreshSummary=="newSummary"){
		showOptSummary(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
	}else{
		showOptReports(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
	}
}

function showOptSummary(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial){
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/opt_subReportMod.php",
		data : "type=showSummary&fromDate="+from+"&toDate="+to+"&product="+productName+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial,
		beforeSend : function(){
			jQuery('#summaryContent').empty();
			var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
			jQuery('#reportContent').html(content);
		},
		error : function(){},
		success : function(res){
			
			var result = eval('(' + res + ')');
			if(result.status==true){
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
				showOptReports(reportType,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial);
			}else{
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
			}
		}
	});
}

function showOptReports(reportName,from,to,productName,pageNumber,colName,orderBy,freeTrial,inFreeTrial){
	if(reportName=='subscriprionReport'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/opt_subReportMod.php",
			data : "type=activeUsers&fromDate="+from+"&toDate="+to+"&product="+productName+"&p="+pageNumber+"&colName="+colName+"&orderBy="+orderBy+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial,
			beforeSend : function(){
				if(jQuery('#recordCount').html()!=''){
					jQuery('#recordCount').empty();
				}
				var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
				jQuery('#reportContent').html(content);
			},
			error : function(){},
			success : function(res){

				var result = eval('(' + res + ')');
				if(result.status==true){
					var reportBody = result.body;
					var recordCount = result.count;
					var currentPageCount = result.currentPageCount;
					jQuery('#recordCount').html(recordCount+' Record Found.<br>'+currentPageCount+' record on this Page');
					jQuery('#reportContent').html(reportBody);
					if(orderBy!=''){
						if(orderBy=="asc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingDesc');
						}else if(orderBy=="desc"){
							jQuery('td#'+colName).removeClass('labelHeading');
							jQuery('td#'+colName).addClass('labelHeadingAsc');
						}
					}
				}else{
					var reportBody = result.body;
					jQuery('#reportContent').html(reportBody);
				}
			}
		});
	}else if(reportName=="exportSubscriptionReport"){
		window.open("opt_subsciprtionReport.export.htm?fromDate="+from+"&toDate="+to+"&product="+productName+"&freeTrial="+freeTrial+"&inFreeTrial="+inFreeTrial);
	}
}

function generateRefundReport(reportType,pageNumber,colName,orderBy,refreshSummary){
	if(jQuery('#reportError').html()!=''){
		jQuery('#reportError').empty();
	}
	
	if(jQuery('#recordCount').html()!=''){
		jQuery('#recordCount').empty();
	}
		
	var productName = jQuery('#product').val();
	var fromDate = jQuery('#fromDate').val();
	var toDate = jQuery('#toDate').val();
	
	if(fromDate=='' && toDate==''){
		jQuery('#reportError').html("Please Select Date Range.<br>");
		return false;
	}
	
	if(fromDate==''){
		jQuery('#reportError').html("Please enter From Date.<br>");
		return false;
	}
	
	if(toDate==''){
		jQuery('#reportError').html("Please enter To Date.<br>");
		return false;
	}
	
	if(!compareDates(fromDate,toDate)){
		jQuery('#reportError').html("Invalid Date Range.<br>");
		return false;
	}
	
	if(refreshSummary=="yes"){
		showRefundSummary(reportType,fromDate,toDate,productName,pageNumber,colName,orderBy);
	}else{
		showRefundReport(reportType,fromDate,toDate,productName,pageNumber,colName,orderBy);
	}
}

function showRefundReport(reportName,from,to,productName,pageNumber,colName,orderBy){
	if(reportName=='viewRefundReport'){
		jQuery.ajax({
			type : "POST",
			url : host+"/admin/refundReportMod.php",
			data : "type=activeUsers&fromDate="+from+"&toDate="+to+"&product="+productName+"&p="+pageNumber+"&colName="+colName+"&orderBy="+orderBy,
			beforeSend : function(){
				if(jQuery('#recordCount').html()!=''){
					jQuery('#recordCount').empty();
				}
				var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
				jQuery('#reportContent').html(content);
			},
			error : function(){},
			success : function(res){
				var result = eval('(' + res + ')');
				if(result.status==true){
					var reportBody = result.body;
					var recordCount = result.count;
					var currentPageCount = result.currentPageCount;
					jQuery('#recordCount').html(recordCount+' Record Found.<br>'+currentPageCount+' record on this Page');
					jQuery('#reportContent').html(reportBody);
					if(orderBy!=''){
						if(orderBy=="asc"){
							jQuery('td#'+colName).removeClass('refundLabelHeading');
							jQuery('td#'+colName).addClass('refundLabelHeadingDesc');
						}else if(orderBy=="desc"){
							jQuery('td#'+colName).removeClass('refundLabelHeading');
							jQuery('td#'+colName).addClass('refundLabelHeadingAsc');
						}
					}
				}else{
					var reportBody = result.body;
					jQuery('#reportContent').html(reportBody);
				}
			}
		});
	}else if(reportName=="exportRefundReport"){
		window.open("refundReportExport.htm?fromDate="+from+"&toDate="+to+"&product="+productName);
	}
}

function showRefundSummary(reportType,from,to,productName,pageNumber,colName,orderBy){
	jQuery.ajax({
		type : "POST",
		url : host+"/admin/refundReportMod.php",
		data : "type=showSummary&fromDate="+from+"&toDate="+to+"&product="+productName,
		beforeSend : function(){
			jQuery('#summaryContent').empty();
			var content = '<center><img src="http://storage.googleapis.com/mvassets/images/recurly/submitting.gif" /></center>';
			jQuery('#reportContent').html(content);
		},
		error : function(){},
		success : function(res){
			var result = eval('(' + res + ')');
			if(result.status==true){
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
				showRefundReport(reportType,from,to,productName,pageNumber,colName,orderBy);
			}else{
				var reportBody = result.body;
				jQuery('#summaryContent').html(reportBody);
				jQuery('#reportContent').empty();
			}
		}
	});
}