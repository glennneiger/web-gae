 function settopicbox(CheckValue)
	    {
     var array = document.getElementsByTagName("input");
      for(var ii = 0; ii < array.length; ii++)
       {
      if(array[ii].type == "checkbox")
               {
      if(array[ii].className == "topic_select")
               {
        array[ii].checked = CheckValue;
               }
            }
                }
         }
	  function setauthorbox(CheckValue)
	    {
     var array = document.getElementsByTagName("input");
      for(var ii = 0; ii < array.length; ii++)
       {
      if(array[ii].type == "checkbox")
               {
      if(array[ii].className == "author_select")
               {
        array[ii].checked = CheckValue;
               }
            }
                }
         }	 