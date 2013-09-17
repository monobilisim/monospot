function browser(params){
	if(params==null)params={};
	if(params.contentsDisplay==null)params.contentsDisplay=document.body;
	if(params.currentPath==null)params.currentPath="";
	if(params.filter==null)params.filter="";
	if(params.loadingMessage==null)params.loadingMessage="Yükleniyor...";
	if(params.data==null)params.data="";

	var search=function(){
		if(params.pathDisplay!=null)params.pathDisplay.innerHTML=params.loadingMessage;
		
		var f=typeof(params.filter)=="object"?params.filter.value:params.filter;
		var a=new Ajax();
		with (a){
			Method="POST";
			URL="search_dir.php";
			Data="path="+params.currentPath+"&filter="+f+"&data="+params.data;
			ResponseFormat="json";
			ResponseHandler=showFiles;
			Send();
		}
	}
	
	if(params.refreshButton!=null)params.refreshButton.onclick=search;

	var showFiles=function(res){
		if(params.pathDisplay!=null){
			var p=res.currentPath;
			p=p.replace(/^(\.\.\/|\.\/|\.)*/g,"");
			
			if(params.pathDisplay!=null){
				params.pathDisplay.title=p;
				if(params.pathMaxDisplay!=null){
					if(p.length>params.pathMaxDisplay)p="..."+p.substr(p.length-params.pathMaxDisplay,params.pathMaxDisplay);
				}
				params.pathDisplay.innerHTML=p;
			}
		}
		
		params.contentsDisplay.innerHTML="";
		var oddeven="odd";
		
		for (i=0;i<res.contents.length;i++){
			var f=res.contents[i];
			var el=document.createElement("p");
			with(el){
				//etAttribute("title",f.fName);
				setAttribute("fPath",f.fPath);
				setAttribute("fType",f.fType);
				className=oddeven + " item ft_"+f.fType;
				//innerHTML=f.fName;
			}
			if (f.fType == "folder")
				el.innerHTML=f.fName;
			else
				el.innerHTML='<a href="download.php?file=' + f.fPath + '">' + f.fName + '</a>';
			
			var dogrula=document.createElement("span");
			dogrula.className = "dogrula";
			dogrula.innerHTML = 'Doğrula';
			var size = document.createElement("span");
			size.className = "size";
			size.innerHTML = f.fSize;
			if (f.fType != "folder") {
				el.appendChild(dogrula);
				el.appendChild(size);
			}
			params.contentsDisplay.appendChild(el);
			oddeven=(oddeven=="odd")?"even":"odd";
			el.onclick=selectItem;
			if (f.fType != "folder") dogrula.onclick=verifyItem;
		}
	}

	var selectItem=function(){
		var el = document.getElementById("dogrulama");
		el.className = "hide";
		var ftype=this.getAttribute("fType");
		var fpath=this.getAttribute("fPath");
		var ftitle=this.getAttribute("title");

		if(params.onSelect!=null)params.openFolderOnSelect=params.onSelect({"type":ftype,"path":fpath,"title":ftitle,"item":this},params);
		if(params.openFolderOnSelect==null)params.openFolderOnSelect=true;

		if(ftype=="folder" && params.openFolderOnSelect){
			params.currentPath=fpath;
			search();
		}
	}
	
	var verifyItem=function(event){
		event.stopPropagation();
		var el = document.getElementById("dogrulama");
		el.innerHTML = "Doğrulanıyor...";
		el.className = "show";
		var file =(this.parentElement.getAttribute('fpath'));
		
		var a=new Ajax();
		with (a){
			Method="POST";
			URL="dogrula.php";
			Data="file="+file;
			ResponseFormat="text";
			ResponseHandler=function(res){
				el.innerHTML = res;
				el.className = "show";
			};
			Send();
		}
	}

	search();
}