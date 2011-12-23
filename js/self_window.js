var name_self=0;
var g_url_img_star;
var g_md5_cross;
var g_login_user="";
var g_article_page;
var g_auto_star_Mnum;
var g_ICC_cWins = new Array();
var g_ICC_star_property = 0;
function init_g_val(url_img_star,md5_cross,login_user,article_page){
		g_url_img_star = url_img_star;
		g_md5_cross = md5_cross;
		g_login_user = login_user;
		g_article_page = article_page;
}
function pointerX(ev) { return ev.pageX || (ev.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft)); } 
function pointerY(ev) { return ev.pageY || (ev.clientY + (document.documentElement.scrollTop || document.body.scrollTop)); }
function show_self(star_num,den_x,den_y,star_property,ev,article_title){
		if(g_ICC_cWins.length==1)
		{
			show_star(g_ICC_cWins.pop());
		}
		if (!ev) ev = window.event;
		den_x = pointerX(ev);
		den_y = pointerY(ev);
		name_self++;
		win4 = new Window('dialog'+name_self, {className: "alphacube",opacity:1.0,hideEffect: Effect.Squish,showEffect:Effect.SlideDown2, title: "In-Context Comments "+star_num, width:486, height:600, top:den_y, left:den_x,zIndex:1000 });
		var wordpress_window_url = "http://incontext.wizag.com/blogcomment/ICC/comment.php?Get_Array="+star_num+";"+g_url_img_star+";"+g_article_page+";"+g_login_user+";"+g_md5_cross+";"+g_auto_star_Mnum+";"+star_property+";"+article_title;
		win4.setURL(wordpress_window_url);
		win4.setDestroyOnClose();
		win4.setCloseCallback(show_star);
		win4.show();
		g_ICC_cWins.push(win4);
		var div_image;
		g_ICC_star_property = star_property;
		if(star_property==0){
			div_image = document.getElementById("panelDiv"+star_num);
		}
		else{
			div_image = document.getElementById("add_img_slf_"+star_num);
		}
		if(div_image){div_image.style.visibility="hidden";}
}

document.onmousedown = function ICC_whichElement(ev){
	if (!ev) ev = window.event;
	var icc_window_h;
	var icc_window_w;
	if(window.innerWidth){	icc_window_w = window.innerWidth;}
  else{	icc_window_w = document.documentElement.clientWidth;}
  if(window.innerHeight){	icc_window_h = window.innerHeight;}
  else{	icc_window_h = document.documentElement.clientHeight;}
  icc_window_h = icc_window_h-20;
  icc_window_w = icc_window_w-20;  
	if(ev.clientY>icc_window_h||ev.clientX>icc_window_w){	return ;}
	if(g_ICC_cWins.length==1)
	{
		show_star(g_ICC_cWins.pop());
	}
}

function show_star(win){
	win4.setURL("");
	win.setCloseCallback();
	var star_num = win.options.title;
	var rgExp = /In-Context Comments /i;
	star_num = star_num.replace(rgExp, "");
	if (win)	win.close();
	var div_image;
	if(g_ICC_star_property==0){div_image = document.getElementById("panelDiv"+star_num);}
	else{	div_image = document.getElementById("add_img_slf_"+star_num);}
	if(div_image){div_image.style.visibility="visible";}
	RefreshNum_ajax(g_url_img_star,g_article_page,star_num);
	if(document.getElementById("dialog"+name_self))
	{
		var icc_tmp_dd = "ICC_Delete_Dialog('dialog"+name_self+"')";
		setTimeout(icc_tmp_dd,3000);
	}
}

function RefreshNum_ajax(wordpress_url,wordpress_page,keyword)
{
  var ajaxurl = g_url_img_star+"/wp-admin/admin-ajax.php";
  var pars = "wordpress_url="+wordpress_url+"&action=refreshNum&wordpress_page="+wordpress_page+"&keyword="+keyword+"&star_property="+g_ICC_star_property;
  var ajax01 = InitAjax();
  ajax01.onreadystatechange = function() 
  {
      if (ajax01.readyState == 4)
      {
          if (ajax01.status == 200)
          {
        	  In_C_Comment_showResponse(ajax01);
          }
      }
  }
      ajax01.open("POST",ajaxurl,true);
      ajax01.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
      ajax01.send(pars);
}

function ICC_Delete_Dialog(icc_d_id)
{
var icc_delete_dialog=document.getElementById(icc_d_id);
icc_delete_dialog.parentNode.removeChild(icc_delete_dialog);
}
function In_C_Comment_showResponse(originalRequest)
{
	var reg_R_Text = /<ICC-Star-Num0>(.+)<ICC-Star-Num1>/i;
	var R_Text =  reg_R_Text.exec(originalRequest.responseText);
	var cur_star_num = R_Text[1];
	if(cur_star_num.length>5||cur_star_num.length==0){	return;}
	var reg_R_Star_Num = /<ICC-Star-Keys0>(.+)<ICC-Star-Keys1>/i;
	var R_Star_Num = reg_R_Star_Num.exec(originalRequest.responseText);
	var star_num = R_Star_Num[1];
	var div_image;
	if(g_ICC_star_property==1)
	{	
		div_image = document.getElementById("add_img_slf_"+star_num);
		div_image.innerHTML = "&nbsp;"+cur_star_num+"&nbsp;<span class='InContext_HaveComments_Up'></span>";
		if(cur_star_num>10){	div_image.innerHTML = cur_star_num+"<span class='InContext_HaveComments_Up'></span>";}
		if(cur_star_num==0){	
			div_image.className = "InContext_NoComments";
			div_image.innerHTML = "&nbsp;"+cur_star_num+"&nbsp;<span class='InContext_NoComments_Up'></span>";
		}
		else{	div_image.className = "InContext_HaveComments";}
	}
	else{
			div_image = document.getElementById("panelDiv"+star_num);
			var rgExp_update_num = /(.+)?<span/i;
			var rgExp_class2;
			if(cur_star_num==0){
				rgExp_class2 = /InContext_HaveComments_Up/i;
				div_image.innerHTML = div_image.innerHTML.replace(rgExp_class2, "InContext_NoComments_Up");
			}
			else{
				rgExp_class2 = /InContext_NoComments_Up/i;
				div_image.innerHTML = div_image.innerHTML.replace(rgExp_class2, "InContext_HaveComments_Up");			
			}
			if(cur_star_num<10){	div_image.innerHTML = div_image.innerHTML.replace(rgExp_update_num, "&nbsp;"+cur_star_num+"&nbsp;<span");}
			else{	div_image.innerHTML = div_image.innerHTML.replace(rgExp_update_num, ""+cur_star_num+"<span");}
			if(cur_star_num==0){	div_image.className = "InContext_NoComments";}
			else{	div_image.className = "InContext_HaveComments";}
	}
}

function InitAjax()
{
    var httpAjax = null;
    try
    {
        httpAjax = new ActiveXObject("MSXML2.XMLHTTP");
    }
    catch(e1)
    {
        try
        {
            httpAjax = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch(e2)
        {
        }
    }
    if(!httpAjax && XMLHttpRequest != 'undefined')
    {
          httpAjax = new XMLHttpRequest();  
    }
    return httpAjax;
}
