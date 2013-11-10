function showsubmenu(id,cnt){ 	
	for(var i=1;i<=cnt;i++){	 
		if(id == i){			 
			$('tab_'+i).removeClassName('tab_'+ i);
			$('tab_'+i).addClassName('tab_'+ i+'_selected');						
			var subMenuList = $('sub_tab_'+ i).getElementsByTagName('div');
			var subMenuArray = $A(subMenuList);
			subMenuCount = subMenuArray.length;
			showsubmenuitem(id,null,subMenuCount); 			
			if(document.getElementById('sub_tab_'+id)){					
				$('sub_tab_'+id).show();
			}			
		} else {			
			$('tab_'+i).removeClassName('tab_'+ i+'_selected');
			$('tab_'+i).addClassName('tab_'+ i);			
			if(document.getElementById('sub_tab_'+i)){						
				$('sub_tab_'+i).hide();
			}
		}				
	}
}
function showsubmenuitem(id,itemid,cnt){  	
	for(var i=1;i<=cnt;i++){	 
		if(itemid == i){			
			$('sub_tab_item_'+id+'_'+i).removeClassName('sub_nav_tab_'+ id);		 			
			$('sub_tab_item_'+id+'_'+i).addClassName('sub_nav_tab_'+ id+'_selected');				 
		} else {
			$('sub_tab_item_'+id+'_'+i).removeClassName('sub_nav_tab_'+ id+'_selected');
			$('sub_tab_item_'+id+'_'+i).addClassName('sub_nav_tab_'+ id);	
		}
	} 
}
function showSelectedPageMenu()
{
	var menu_section = $('hidMenuSection').value;
	var menu_subsection = $('hidMenuSubSection').value;
	var menu_section_count = $('hidMenuSectionCount').value;
	var menu_subsection_count = $('hidMenuSubSectionCount').value;
	showsubmenu(menu_section,menu_section_count)
	if($('hidMenuSubSection').value != '')
	{		
		showsubmenuitem(menu_section,menu_subsection,menu_subsection_count);
	}
}

var $j = jQuery.noConflict();

$j(document).ready(function() {
        $j("#ap-articles").load("/articles/article-apinclude.htm");
});

