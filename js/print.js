function showHidediv(idname,className,id,num)
{
	for(var i=1;i<=num;i++)
	{
		if(document.getElementById(idname+i)){
			if(i==id)
			{
				document.getElementById(idname+i).style.display='block';
				document.getElementById(className+i).className='selected';
			}
			else
			{
				document.getElementById(idname+i).style.display='none';
				document.getElementById(className+i).className='';
			}
		}
	}
}
