Ext.onReady(function(){
    // create the Data Store
    var store = new Ext.data.Store({
        // load using script tags for cross domain, if the data in on the same domain as
        // this page, an HttpProxy would be better ScriptTagProxy
        proxy: new Ext.data.ScriptTagProxy({
			url: '../techstrat/closepositionsdata.htm'
        }),

        // create reader that reads the Topic records
        reader: new Ext.data.JsonReader({
            root: 'topics',
            totalProperty: 'totalCount',
            fields: [
                'Companyname','expirydate','ticker','creation_date','unit_price','Date Opened','Purchase Price','# of Shares','Purchase Amount','Sale Proceeds','GainLoss'
            ]
        }),
        // turn on remote sorting
        remoteSort: true
    });
	function colorgainloss(value, p, record){
		if(value.substr(1,1)=='-'){
		  return String.format(
                '<a style="color:red;">{0}</a>',value);
	     }else{
		   return String.format(
                '<a style="color:green;">{0}</a>',value);	 
		 }
		
	}
	function colorgainlosspercent(value, p, record){
		if(value.substr(0,1)=='-'){
		  return String.format(
                '<a style="color:red;">{0}</a>',value);
	     }else{
		   return String.format(
                '<a style="color:green;">{0}</a>',value);	 
		 }
		
	}
	

    var cm = new Ext.grid.ColumnModel([{
           id: 'topic', // id assigned so we can apply custom css (e.g. .x-grid-col-topic b { color:#333 })
           header: "Name",
           dataIndex: 'Companyname',
           width:95
        },{
           header: "Ticker",
           dataIndex: 'ticker',
           width: 95
        },{
           header: "Close Date",
           dataIndex: 'creation_date',
           width: 100
        },{
           header: "Sale Price",
           dataIndex: 'unit_price',
           width: 85
        },{
           header: "Open Date",
           dataIndex: 'Date Opened',
           width: 95
        },{
           header: "Purchase<br>Price",
           dataIndex: 'Purchase Price',
           width: 110
        },{
           header: "# of Shares",
           dataIndex: '# of Shares',
           width: 70
        },{
           header: "Purchase<br>Amount",
           dataIndex: 'Purchase Amount',
           width: 105
        },{
           header: "Sale<br>Proceeds",
           dataIndex: 'Sale Proceeds',
           width: 105
        },{
           header: "Gain/Loss $",
		   dataIndex: 'GainLoss',
           width: 85,
		   renderer: colorgainloss
        }]);
		
    // by default columns are sortable
    cm.defaultSortable = true;
    var grid = new Ext.grid.GridPanel({
        el:'techStratClosePosition',
        height:405,
        title:"TechStrat's - Closed Positions",
        store: store,
        cm: cm,
        trackMouseOver:false,
        sm: new Ext.grid.RowSelectionModel({selectRow:Ext.emptyFn}),
        loadMask: true,
        viewConfig: {
            forceFit:false,
            enableRowBody:true,
            showPreview:true,
            getRowClass : function(record, rowIndex, p, store){
            }
        },
        bbar: new Ext.PagingToolbar({
            pageSize:15,
            store: store,
            displayInfo: true,
            displayMsg: 'Displaying records {0} - {1} of {2}',
            emptyMsg: "No records to display",
            items:[
                '-', {
                pressed: true,
                enableToggle:true,
                text: '',
                cls: 'x-btn-text-icon details'
            }]
        })
    });

    grid.render();
    store.load({params:{start:0, limit:15}});

    function toggleDetails(btn, pressed){
        var view = grid.getView();
        view.showPreview = pressed;
        view.refresh();
    }
});
