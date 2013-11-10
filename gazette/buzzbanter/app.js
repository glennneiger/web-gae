
function rand(){
	return Math.random().toString().substring(2)
}

function isInShell(){
	if(!window.external)return false;
	if(typeof(window.external.SetTitle)=="undefined")return false;
	return true;
}

function setTitle(title){
	if(!title)title="";
	if(!isInShell())return;
	window.external.SetTitle(title);
}
function setResizable(torf){
	torf=(torf?true:false);
	if(!isInShell())return;
	window.external.SetResizeable(torf);
}
function setResizeable(torf){//spelling
	setResizable(torf);
}
function putGlobal(name,val){//put a colon in front of val to have it evaluated
	if(!isInShell())return;
	window.external.PutGlobalContext(name,val);
}
function getGlobal(name){
	if(!isInShell())return;
	var ret=window.external.GetGlobalContext(name);
	if(ret.charAt(0)==":"){
		eval("ret="+ret.substring(1));
	}
	return ret;
}
function getRegVar(name,client){
	if(!isInShell())return;
	if(!client)client=""
		return window.external.GetRegistryString("HKCU",client,name);
}

function setRegVar(name,val,client){
	if(!isInShell())return;
	if(!client)client="";
	return window.external.SetRegistryString("HKCU",client,name,val);
}
function sysAlert(msg,wstatus,is_encoded){
	if(!isInShell())return;
	if(typeof(sysHandle)!="undefined"){
		try{
			sysHandle.CloseWindow();
		}catch(e){
			void(0);
		}
	}
	if(!msg)msg="";
	if(!wstatus)wstatus="";
	if(!is_encoded)is_encoded=0;
	if(!msg&&!wstatus)return;
	var url=getGlobal("BASE_URL")+"sysalert.htm?is_encoded="+is_encoded+"&wmsg="+msg+"&wstatus="+wstatus
	sysHandle=window.external.ShowSysAlert(url,219,131,7000);
}

function flashTaskbar(){
	if(!isInShell())return;
	try{
		window.external.FlashTaskBar();
	}catch(e){
		void(0)
	}
}

function routeDownload(callback_arg){
	var args=callback_arg.split("||");
	var callback=args[0];
	var arg=args[1];
	if(typeof(window[callback])!="function")return;
	window[callback](arg);
}

function downloadFile(url,dest,callback,callbackarg){
	if(!isInShell())return;
	window.external.SetJavaScriptCallBack("DownloadComplete","routeDownload");
	window.external.DownloadFile(url,dest,callback+"||"+callbackarg);
}

function setSize(w,h){
	if(!isInShell())return;
	with(window.external){
		SetHeight(h);
		SetWidth(w);
	}
}

function showConfig(context){
	if(!isInShell())return;
	var oldState=window.external.GetGlobalContext("ConfigState");
	with(window.external){
		PutGlobalContext("ConfigState",context);
		ShowConfig();
		PutGlobalContext("ConfigState",oldState);
	}
	oldState=null;
}


function protocolHandler(arg){
	if(!isInShell())return;
	arg = arg.substring(arg.indexOf(":")+1)
	var func = arg.substring(0,arg.indexOf("?"));
	var args  = arg.substring(arg.indexOf("?")+1).split("&");
	var expo=getGlobal("EXPOSED_FUNCS");
	if(in_array(func,expo)){
		if(typeof(window[func]=="function")){
			window[func](args);
		}
	}
}


function contextMenu(hashMenuItems){//hashMenuItems={itemtext:["callBack",callBackArg],...etc}
	if(!isInShell())return;
	var menu = window.external.CreateContextMenu();
	var i=0;
	for(e in hashMenuItems){
		menu.AddMenuItem(i++,e,hashMenuItems[e][0],hashMenuItems[e][1]);
	}
	menu.ShowMenu(event.screenX,event.screenY);
	for(e in hashMenuItems){
		menu.RemoveMenuItem(e);
	}

	window.external.DestroyObject(menu);
	menu=i=null;
}

/*----------------------------------------filesystem--------------------------------*/
			/*--------- paths ------------*/
function fixpath(path){
	if(stristr("/",path))
		path=path.replace(/\//g,"\\");
	if(stristr("\\\\",path))
		path=path.replace(/\\\\/g,"\\");
	return path;
}

function cwd(){
	if(!isInShell())return;
	var fs=window.external.GetFileSystemObject();
	var ret=fs.Cwd();
	fs=null;
	return ret;
	return dirname(fixpath(unescape(loc)))
}

function tmpfile(){
	if(!isInShell())return;
	var ret;
	ret=fixpath(cwd()+"/"+rand()+".tmp");
	fs=null;
	return ret;
}

function basename(path){
	path=fixpath(path)
	return path.substring(path.lastIndexOf("\\")+1);
}

function dirname(path){
	path=fixpath(path)
	return path.substring(0,path.lastIndexOf("\\"));
}

			/*--------- moving files------------*/
function rm(path){
	if(!isInShell())return;
	path=fixpath(path);
	var fs=window.external.GetFileSystemObject();
	var ret;
	if(fs.FileExists(path)){
		fs.Rm(path);
	}
	fs=null;
	return ret;
}

function copy(src,dest){
	if(!isInShell())return;
	src=fixpath(src);
	dest=fixpath(dest);
	var fs=window.external.GetFileSystemObject();
	var ret;
	if(!fs.FileExists(src) || !fs.IsFile(src)){
		return 0;
	}
	ret=fs.Copy(src,dest);
	alert(src+','+dest);
	fs=null;
	return ret;
}
			/*---------  file's existence ------------*/		
function is_dir(filespec){
	if(!isInShell())return;
	var fs=window.external.GetFileSystemObject();
	var ret=fs.IsDir(fixpath(filespec))?1:0;
	fs=null;
	return ret;
}

function is_file(filespec){
	if(!isInShell())return;
	var fs=window.external.GetFileSystemObject();
	var ret=fs.IsFile(fixpath(filespec));
	fs=null;
	return ret;
}

function file_exists(path){
	if(!isInShell())return;
	path=fixpath(path);
	var fs = window.external.GetFileSystemObject();
	var ret=1;
	if(!fs.IsFile(path) && !fs.IsDir(path))
		ret=0;
	fs=null;
	return ret;
}
			/*---------  file properties ------------*/	
function filesize(path){
	if(!isInShell())return;
	var fs,ret;
	path = fixpath(path);
	fs=window.external.GetFileSystemObject();
	if(!fs.IsFile(path)){
		ret = 0;
		alert(path+" doesn't exist");
	}else{
		ret=fs.FileSize(path);
	}
	fs=null;
	return ret;
}

function hidden(path){
	if(!isInShell())return;
	var fs,ret;	
	path=fixpath(path);
	fs.window.external.GetFileSystemObject(path)
	if(!fs.IsFile(path)&&!fs.IsDir(path))
		ret = 0;
	else
		ret = fs.SetFileHidden(path,true);
	fs=null;
	return ret;
}

function readonly(path){
	if(!isInShell())return;
	path = fixpath(path);
	var fs,ret;
	fs=window.external.GetFileSystemObject();
	if(!fs.IsFile(path) && !fs.IsDir(path))
		ret=false;
	else
		ret=fs.SetFileReadOnly(path,true);
	fs=null;
	return ret;
}
			/*---------  file/dir read/write  ------------*/	
function getDir(folderspec,recurse,search){
	if(!isInShell())return;
	folderspec=fixpath(folderspec);
	recurse=recurse?true:false;
	var fs=window.external.GetFileSystemObject();
	var ret=new Array();
 	if(!recurse)recurse=0;
	if(!fs.IsDir(folderspec)){
		fs=null;
		return ret;
	}
	var files = fs.ReadDir(folderspec,recurse).split(/\n/);
	fs=null;
	if(!search)
		return files;
	for(var i=0;i<files.length;i++){
		if(files[i].match(search) && files[i])
			ret.push(fixpath(files[i]))
	}
	files=null;
	return ret;
}

function readDir(folderspec,recurse){
	return getDir(folderspec,recurse)
}

function mkdir(dirpath){
	if(!isInShell())return;
	var fs=window.external.GetFileSystemObject();
	var ret=fs.MkDir(fixpath(dirpath));
	fs=null;
	return ret;
}

function readfile(path){
	if(!isInShell())return;
	var ret,fs;
	path=fixpath(path);
	fs=window.external.GetFileSystemObject();
	if(!fs.IsFile(path)){
		ret="";
	}else{
		fh=fs.OpenTextFile(path,true,false,false)// (filepath,read,write,create)
		ret=fh.ReadAll(); 
		fh.Close();
		fh=null;
	}
	fs=null;
	return ret;
}

function writefile(path,data){
	if(!isInShell())return;
	path=fixpath(path);
	var fs=window.external.GetFileSystemObject();
	var fh=fs.OpenTextFile(path,false,true,true);// (filepath,read,write,create)
	fh.WriteLine(data);
	fh.close();
	fh=fs=null;
}
