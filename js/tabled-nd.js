
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
