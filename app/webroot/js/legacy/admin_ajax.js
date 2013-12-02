var xmlHttp

function executeAjax(file_name, state_change_func)
{ 
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null) {
		alert ("Your browser does not support AJAX!");
		return;
	} 
	var url = file_name;
	
	xmlHttp.onreadystatechange = state_change_func;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try {xmlHttp=new XMLHttpRequest();}
	catch (e){ 
		try {xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");}
		catch (e){ 
			xmlHttp=new
			ActiveXObject("Microsoft.XMLHTTP");
	    }
	}
	return xmlHttp;
}

