function submitWryFrm(inputval){
		$('#inputvalue').val(inputval);
		var checkInputVal=0;
		$('#frmWorry input[type=text]').each(function()
   		{
		     if(this.value=="Enter mouse over copy here" || this.value==""){
				checkInputVal=1;
			}
		}); 
		if(!checkInputVal){
			frmWorry.submit();	
		}else{
			alert('Please Enter mouse over text.'); 
			return false;	
		}
	}
	
	
	function deleteWryFrm(action,id,worry_id){
		var url = host+'/admin/lloyds-wall-of-worry/worry_mod.htm';
		var worrysequence="";
		var myTD=document.getElementById(worry_id);
		myTD.parentNode.removeChild(myTD);
		var rows = this.table.tBodies[0].rows;
		 for (var i=1; i<rows.length; i++) {
			worrysequence += rows[i].id;
			if(i!==rows.length-1){	
				worrysequence +=",";
			}
		}
		$('#sequence').val(worrysequence);
		var pars = "action="+action+"&id="+id+"&worry_id="+worry_id+"&sequence="+worrysequence;
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
			onComplete:function(req){
				//alert(req.responseText);
				$('#addworrylist').html(req.responseText);
				if(worrysequence!=''){
					$("#btnsubmit").css({display:"block"});
				}else{
					$("#btnsubmit").css({display:"none"});
				}
			}
		});
	}
	
	function addWorryInList(worry_id,id){
		var url = host+'/admin/lloyds-wall-of-worry/worry_mod.htm';
		var worrysequence="";
		var rows = this.table.tBodies[0].rows;
		for (var i=1; i<rows.length; i++) {
			worrysequence += rows[i].id;
			if(i!==rows.length-1){	
				worrysequence +=",";
			}
		}
		var serialCount = i;
		if(worrysequence == ""){
			worrysequence = worry_id;
		}else{
			worrysequence = worrysequence+","+worry_id;
		}
		$('#sequence').val(worrysequence);
		var pars = "action=addWorry"+"&id="+id+"&worry_id="+worry_id+"&sequence="+worrysequence+"&serialCount="+serialCount;
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
			onComplete:function(req){
				$('#table-1 > tbody:last').append(req.responseText);
				var pars = "action=removefrmlist"+"&sequence="+worrysequence;
				var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
						onComplete:function(req){
							$('#addworrylist').html(req.responseText);
							if(worrysequence!=''){
								$("#btnsubmit").css({display:"block"});
							}else{
								$("#btnsubmit").css({display:"none"});
							}
						}
				});
			}
		});
		setTimeout("dragWorry()",5000);						   
	}
	
	function checkWorrySummary(worryId){
		if($('#summary-'+worryId).val()=="Enter mouse over copy here"){
			$('#summary-'+worryId).val('');	
		}
	}
	
	
	
function dragWorry(){
var table = document.getElementById('table-1');
var tableDnD = new TableDnD();
// Redefine the findDropTargetRow to put some debug information in
tableDnD.findDropTargetRow = function(y) {
    var rows = this.table.tBodies[0].rows;
	var debugStr = "y is "+y+"<br>";
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        var rowY    = this.getPosition(row).y;
        var rowHeight = parseInt(row.offsetHeight)/2;
		if (row.offsetHeight == 0) {
			rowY = this.getPosition(row.firstChild).y;
			rowHeight = parseInt(row.firstChild.offsetHeight)/2;
		}
		debugStr += "row["+i+"] between "+(rowY-rowHeight)+' and '+(rowY+rowHeight)+"<br>";
        // Because we always have to insert before, we need to offset the height a bit
        if ((y > rowY - rowHeight) && (y < (rowY + rowHeight))) {
            // that's the row we're over
			/*document.getElementById('debug').innerHTML = debugStr+"found row";*/
            return row;
        }
    }
	/*document.getElementById('debug').innerHTML = debugStr+"no matching row";*/
    return null;
}
// Redefine the onDrop so that we can display something
	tableDnD.onDrop = function(table, row) {
    var rows = this.table.tBodies[0].rows;
	var worrysequence = "";
	
    for (var i=0; i<rows.length; i++) {
		if(rows[i].id){
		worrysequence += rows[i].id;
			if(i!==rows.length-1){	
				worrysequence +=",";
			}
		}
	}
		$('#sequence').val(worrysequence);

		var id=$('#id').val();
		if(id){
			var url = host+'/admin/lloyds-wall-of-worry/worry_mod.htm';
			var action='dragworry';
			var pars = "action="+action+"&id="+id+"&sequence="+worrysequence;
			var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
				onComplete:function(req){
				// alert(req.responseText);
					
				}
			});
		}
	
	
}

tableDnD.init(table);

// And now for the second table
var table2 = document.getElementById('table-2');
var tableDnD2 = new TableDnD();
tableDnD2.init(table2);

	
	
	}
	
function updateSummary(summary,id,worryId){
	if(id == ''){
		return false;
	}
	
	if(summary == "Enter mouse over copy here" || summary==''){
		alert('Please Enter mouse over text.');
		this.focus();
		return false;
	}else{
		var url = host+'/admin/lloyds-wall-of-worry/worry_mod.htm';
		var action='updateSummary';
		var pars = "action="+action+"&id="+id+"&worryId="+worryId+"&summary="+summary;
		var postAjax = new Ajax.Request(url, {method: 'post', parameters: pars,
			onComplete:function(req){
			// alert(req.responseText);
				
			}
		});
	}
}