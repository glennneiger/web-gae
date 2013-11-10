function returnproductname(){
		var product =$('#productName').val();
		window.location='manage_regis_funnel.htm?product='+product;
                exit;
	}
        
        function insertProductData(){
        
                var product = $('#productName').val();
                if(product==0)
                {
                    $('#showmsg').html('Please select product name.');
                    return false;
                }
		var heading=$("#product_heading").val();
                if(heading=="")
                {
                    $('#showmsg').html('Please enter the heading.');
                    return false;
                }
                $('#save').val('1');
                $('#productfrm').submit();			
	}