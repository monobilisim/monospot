/*
  ____________________________________________
 |                                            |
 | sitepoint.com's Ajax Modified by ahmad_511 |
 |____________________________________________|

*/

function Ajax(){
this.Method="GET";//String "GET" or "POST"
this.URL=null;// String
this.ResponseHandler=null;// Function
this.ErrorHandler=null;// Function
this.Request=null;
this.Async=true;// Boolean
this.Data = null;// String name=value pairs
this.ResponseFormat="text"; //String text, xml, request, object or json
this.ObjectState=null; //object can be used to send some info to the response handler function
this.init=function(){
	if (!this.Request) {
		try {
		// Try to create object for Firefox, Safari, IE7, etc.
		this.Request = new XMLHttpRequest();
		}
		catch (e) {
			try {
			// Try to create object for later versions of IE.
			this.Request = new ActiveXObject('MSXML2.XMLHTTP');
			}
			catch (e) {
				try {
				// Try to create object for early versions of IE.
				this.Request = new ActiveXObject('Microsoft.XMLHTTP');
				}
				catch (e) {
				// Could not create an XMLHttpRequest object.
				return false;
				}
			}
		}
	}

	if(this.Request.overrideMimeType ){
		if (this.ResponseFormat.toLowerCase()=="text")this.Request.overrideMimeType('text/html');
		if (this.ResponseFormat.toLowerCase()=="xml")this.Request.overrideMimeType('text/xml');
	}
	return this.Request;
}

// converting an object to name=value pairs
this.decode=function(obj){
	if(typeof obj=="object"){
		var urlstr="";
		for (var k in obj){
			if(typeof obj[k]=="object"){// should be an array
				for(var i=0;i<obj[k].length;i++){
					urlstr+=k+"="+encodeURIComponent(obj[k][i])+"&";
				}
			}else{
				urlstr+=k+"="+encodeURIComponent(obj[k])+"&";
			}	
		}
		if(urlstr.length>0)urlstr=urlstr.substr(0,urlstr.length-1);
	}else urlstr=obj;
	return urlstr;
}

// coverting name=value to pairsobject
this.encode=function(str){
	if(typeof obj=="string"){
		var obj={};
		var objArr=str.split("&");
		for (var i=0;i<objArr.length;i++){
			var kv=objArr[i].split("=");
			if (kv.length==2)obj[kv[0]]=decodeURIComponent(kv[1]);
		}
	}else obj=str;
	return obj;
}

this.Send=function(){
    if (!this.init()) {
		alert('Could not create XMLHttpRequest object.');
		return;
    }
    var self = this; // Fix loss-of-scope in inner function
	self.ResponseFormat=self.ResponseFormat.toLowerCase();
    this.Request.onreadystatechange=function() {
		var resp = null;
		if (self.Request.readyState == 4) {

			switch (self.ResponseFormat.toLowerCase()) {
				case "text":
				resp = self.Request.responseText;
				break;
				
				case "xml":
				resp = self.Request.responseXML;
				break;
				
				case "request":
				resp = self.Request;
				break;
				
				case "object":
				resp = self.encode(self.Request.responseText);
				break;
				
				case "json":
				if(self.Request.responseText==""){
					resp=null;
				}else{
					// check for Native JSON support (IE8+, FF3.5+, Safari 4+, Opera 10+, Chrome 4+), use eval if failed
					if(window.JSON)
						resp = JSON.parse(self.Request.responseText);
					else resp = eval('('+self.Request.responseText+')');
				}
				break;
			}
			if (!self.Async){
				if (self.ResponseHandler!=null)self.ResponseHandler(resp,self.ObjectState);
				if(self.Request.onload)self.Request.onload=null;
			}
			if (self.Request.status >= 200 && self.Request.status <= 304) {

				// when Async=false (none synchronized request)
				// chrome triggers onLoad + onStateChange 
				// FF triggers onLoad, IE triggers onStateChange
				if (self.ResponseHandler!=null)self.ResponseHandler(resp,self.ObjectState);
				if(self.Request.onload)self.Request.onload=null;
			}else {
				if(self.ErrorHandler!=null)self.ErrorHandler(resp);
					else alert("Problem while connecting to the server, Please refresh the page and try again");
			}
		}
    };

	if(this.Request.onload)this.Request.onload=this.Request.onreadystatechange
    if (this.Method.toLowerCase()=="get"){
		this.URL +="?"+this.decode(this.Data)
		this.Data=null
    }else{ 
		this.Data=this.decode(this.Data)     
    }
    this.Request.open(this.Method, this.URL, this.Async);
    if (this.Method.toLowerCase()=="post")this.Request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    this.Request.send(this.Data);
};


// abort
this.abort = function() {
	if (this.Request) {
		this.Request.onreadystatechange = function() {};
		this.Request.abort();
		this.Request = null;
	};
};

};