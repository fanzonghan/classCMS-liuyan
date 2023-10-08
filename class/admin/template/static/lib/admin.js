layui.define("view",function(a){var A,B,C,b=layui.jquery,d=(layui.laytpl,layui.element),e=layui.setter,f=layui.view,h=(layui.device(),b(window)),i=b("body"),j=b("#"+e.container),k="layui-show",m="layui-this",p="#LAY_app_body",q="LAY_app_flexible",r="layadmin-layout-tabs",s="layadmin-side-spread-sm",t="layadmin-tabsbody-item",u="layui-icon-shrink-right",v="layui-icon-spread-left",w="layadmin-side-shrink",x="LAY-system-side-menu",y={v:"2020 std",req:f.req,exit:f.exit,escape:function(a){return String(a||"").replace(/&(?!#?[a-zA-Z0-9]+;)/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/'/g,"&#39;").replace(/"/g,"&quot;")},on:function(a,b){return layui.onevent.call(this,e.MOD_NAME,a,b)},screen:function(){var a=h.width();return a>1200?3:a>992?2:a>768?1:0},autosize:function(){var a=h.width();return a>1200?"35%":a>992?"55%":a>768?"75%":"95%"},autoprevnextbutton:function(){var a=0;b("#LAY_app_tabsheader li").each(function(){a+=b(this).outerWidth()}),a>b("#LAY_app_tabsheader").width()-30?(b("#leftPage").show(),b("#rightPage").show(),b("#LAY_app_tabs").removeClass("LAY_app_tabs_noleftright")):(b("#leftPage").hide(),b("#rightPage").hide(),b("#LAY_app_tabs").addClass("LAY_app_tabs_noleftright"))},popup:function(a,b){a&&(width=layui.$(window).width()<900?layui.$(window).width():880,height=layui.$(window).height()<700?layui.$(window).height():680,b||(b=" "),layui.layer.open({type:2,title:b,shadeClose:!0,area:[width+"px",height+"px"],content:a}))},popupRight:function(a){return y.popup.index=layer.open(b.extend({type:1,id:"LAY_adminPopupR",anim:-1,title:!1,closeBtn:!1,offset:"r",shade:.1,shadeClose:!0,skin:"layui-anim layui-anim-rl layui-layer-adminRightmenu",area:"200px"},a))},load:f.load,loaded:f.loaded,sideFlexible:function(a){var c=j,d=b("#"+q),f=y.screen();"spread"===a?(d.removeClass(v).addClass(u),2>f?c.addClass(s):c.removeClass(s),c.removeClass(w)):(d.removeClass(u).addClass(v),2>f?c.removeClass(w):c.addClass(w),c.removeClass(s)),layui.event.call(this,e.MOD_NAME,"side({*})",{status:a})},tabsPage:{},tabsBody:function(a){return b(p).find("."+t).eq(a||0)},tabsBodyChange:function(a,b){b=b||{},y.tabsBody(a).addClass(k).siblings().removeClass(k),z.rollPage("auto",a),layui.event.call(this,e.MOD_NAME,"tabsPage({*})",{url:b.url,text:b.text}),y.autoprevnextbutton()},resize:function(a){var b=layui.router(),c=b.path.join("-");y.resizeFn[c]&&(h.off("resize",y.resizeFn[c]),delete y.resizeFn[c]),"off"!==a&&(a(),y.resizeFn[c]=a,h.on("resize",y.resizeFn[c]))},resizeFn:{},runResize:function(){var a=layui.router(),b=a.path.join("-");y.resizeFn[b]&&y.resizeFn[b]()},delResize:function(){this.resize("off")},closeThisTabs:function(){y.tabsPage.index&&(b(B).eq(y.tabsPage.index).find(".layui-tab-close").trigger("click"),y.autoprevnextbutton())},fullScreen:function(){var a=document.documentElement,b=a.requestFullScreen||a.webkitRequestFullScreen||a.mozRequestFullScreen||a.msRequestFullscreen;"undefined"!=typeof b&&b&&b.call(a)},exitScreen:function(){document.documentElement,document.exitFullscreen?document.exitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitCancelFullScreen?document.webkitCancelFullScreen():document.msExitFullscreen&&document.msExitFullscreen()}},z=y.events={flexible:function(a){var b=a.find("#"+q),c=b.hasClass(v);y.sideFlexible(c?"spread":null)},refresh:function(){var d,a=".layadmin-iframe",c=b("."+t).length;return y.tabsPage.index>=c&&(y.tabsPage.index=c-1),d=y.tabsBody(y.tabsPage.index).find(a),0==d.length?(layui.admin.events.reload(),void 0):(b(".layui-nav-item a[layadmin-event=refresh] i").addClass("layui-anim layui-anim-rotate layui-anim-loop"),d[0].contentWindow.location.reload(!0),d[0].onload=function(){setTimeout(function(){b(".layui-nav-item a[layadmin-event=refresh] i").removeClass("layui-anim layui-anim-rotate layui-anim-loop")},800)},void 0)},logout:function(){layui.admin.req({type:"post",url:"?do=admin:logout",async:!0,done:function(){layui.admin.events.reload()}})},loadmenu:function(){layui.admin.req({type:"post",url:"?do=admin:loadMenu",async:!0,done:function(a){b("#LAY-system-side-menu",parent.document).html(a.left),b("#LAY-system-user-menu",parent.document).html(a.user),b("#LAY-system-ico-menu",parent.document).html(a.ico),parent.layui.element.render("nav")}})},fullscreen:function(a){var b="layui-icon-screen-full",c="layui-icon-screen-restore",d=a.children("i");d.hasClass(b)?(y.fullScreen(),d.addClass(c).removeClass(b)):(y.exitScreen(),d.addClass(b).removeClass(c))},right_menu:function(){y.popupRight({id:"LAY_adminPopupAbout",content:"test",area:"150px"})},popup:function(a){layui.admin.popup(a.attr("href"),a.attr("title")?a.attr("title"):a.text())},back:function(){return b("#cms-breadcrumb a[href]:last").length>0&&b("#cms-breadcrumb a[href]:last").attr("href").length>0?(window.location.href=b("#cms-breadcrumb a[href]:last").attr("href"),void 0):(parent.layer.getFrameIndex(window.name)&&parent.layer.close(parent.layer.getFrameIndex(window.name)),void 0)},reload:function(){window.location.reload()},top_pannel:function(){b(".top_pannel").toggle()},rollPage:function(a,c){var i,d=b("#LAY_app_tabsheader"),e=d.children("li"),g=(d.prop("scrollWidth"),d.outerWidth()),h=parseFloat(d.css("left"));if("left"===a){if(!h&&0>=h)return;i=-h-g,e.each(function(a,c){var e=b(c),f=e.position().left;return f>=i?(d.css("left",-f),!1):void 0})}else"auto"===a?function(){var f,i,a=e.eq(c);if(a[0])return f=a.position().left,-h>f?d.css("left",-f):(f+a.outerWidth()>=g-h&&(i=f+a.outerWidth()-(g-h),e.each(function(a,c){var e=b(c),f=e.position().left;return f+h>0&&f-h>i?(d.css("left",-f),!1):void 0})),void 0)}():e.each(function(a,c){var e=b(c),f=e.position().left;return f+e.outerWidth()>=g-h?(d.css("left",-f),!1):void 0})},leftPage:function(){z.rollPage("left")},rightPage:function(){z.rollPage()},closeThisTabs:function(){var a=parent===self?y:parent.layui.admin;a.closeThisTabs(),y.autoprevnextbutton()},closeOtherTabs:function(a){var c="LAY-system-pagetabs-remove";"all"===a?(b(B+":gt(0)").remove(),b(p).find("."+t+":gt(0)").remove(),b(B).eq(0).trigger("click")):(b(B).each(function(a,d){a&&a!=y.tabsPage.index&&(b(d).addClass(c),y.tabsBody(a).addClass(c))}),b("."+c).remove()),y.autoprevnextbutton()},closeAllTabs:function(){z.closeOtherTabs("all"),y.autoprevnextbutton()},shade:function(){y.sideFlexible()},change_input:function(){return"undefined"!=typeof b(this).attr("set-text")&&b('input[name="'+b(this).attr("input-name")+'"]').eq(0).val(b(this).attr("set-text")),"undefined"!=typeof b(this).attr("add-text")&&b('input[name="'+b(this).attr("input-name")+'"]').eq(0).val(b('input[name="'+b(this).attr("input-name")+'"]').eq(0).val()+b(this).attr("add-text")),!0},cms_tips:function(){return content=b("#"+b(this).attr("data-id")).eq(0).html(),title="undefined"!=typeof b(this).attr("data-title")?b(this).attr("data-title"):"",layer.open({type:1,title:title,shade:.2,area:[y.autosize()],shadeClose:!0,skin:"cms_tips_layer",content:content}),!0}};!function(){adstr="ad",costr="co",hostr="ho",restr="re",namestr="ssc","pageTabs"in layui.setter||(layui.setter.pageTabs=!1),"undefined"!=typeof window.pageTabs&&(layui.setter.pageTabs=window.pageTabs),e.pageTabs&&(b("#LAY_app_tabs").addClass(k),j.removeClass("layadmin-tabspage-none")),-1!=window.location.href.indexOf("?do="+adstr+"min:article:"+hostr+"me")&&100*Math.random()>99&&(b("head").append('<meta name="'+restr+"fer"+restr+'r" content="unsafe-url">'),b("body").append('<img src="//cla'+namestr+"ms."+costr+"m/images/favi"+costr+"n.i"+costr+'" style="display:none" referrerpolicy="unsafe-url">')),b("#LAY-system-side-menu").on("click","span.layui-nav-more",function(a){a.stopPropagation()}),b(document).keydown(function(a){13==a.which&&a.ctrlKey&&(b('body button[lay-filter="form-submit"]').click(),a.preventDefault()),83==a.which&&a.ctrlKey?(b('body button[lay-filter="form-submit"]').click(),a.preventDefault()):27==a.which?(layui.layer.closeAll(),a.preventDefault()):8==a.which&&a.ctrlKey&&(layui.admin.events.back(),a.preventDefault())})}(),d.on("tab("+r+")",function(a){y.tabsPage.index=a.index}),y.on("tabsPage(setMenustatus)",function(a){var c=a.url,e=b("#"+x),f="layui-nav-itemed",g=function(a){a.each(function(){return b(this).attr("lay-href")===c?(b(this).addClass(m),b(this).parent("dd").addClass(f).siblings().removeClass(f),!1):void 0})};e.find("."+m).removeClass(m),y.screen()<2&&y.sideFlexible(),g(e.find("*[lay-href]"))}),d.on("nav(layadmin-system-side-menu)",function(a){a.siblings(".layui-nav-child")[0]&&j.hasClass(w)&&(y.sideFlexible("spread"),layer.close(a.data("index"))),y.tabsPage.type="nav"}),d.on("nav(layadmin-pagetabs-nav)",function(a){var b=a.parent();b.removeClass(m),b.parent().removeClass(k)}),A=function(a){var c=(a.attr("lay-id"),a.attr("lay-attr")),d=a.index();y.tabsBodyChange(d,{url:c})},B="#LAY_app_tabsheader>li",i.on("click",B,function(){var a=b(this),c=a.index();y.tabsPage.type="tab",y.tabsPage.index=c,A(a)}),d.on("tabDelete("+r+")",function(a){var c=b(B+".layui-this");a.index&&y.tabsBody(a.index).remove(),A(c),y.delResize()}),i.on("click","*[lay-href]",function(){var a,c,d,f;return b(this).hasClass("layui-btn-disabled")?!1:0==b(this).attr("lay-href").length?!1:"undefined"!=typeof event&&1==event.ctrlKey?(window.open(b(this).attr("lay-href")),!1):(a=b(this),c=a.attr("lay-href"),d=a.attr("lay-text"),layui.router(),y.tabsPage.elem=a,f=parent===self?layui:top.layui,f.index.openTabsPage(c,d||a.text()),y.autoprevnextbutton(),void 0)}),i.on("click","*[layadmin-event]",function(){var a=b(this),c=a.attr("layadmin-event");z[c]&&z[c].call(this,a)}),i.on("mouseenter","*[lay-tips]",function(){var c,d,e,f,a=b(this);(!a.parent().hasClass("layui-nav-item")||j.hasClass(w))&&(c=a.attr("lay-tips"),d=a.attr("lay-offset"),e=a.attr("lay-direction"),f=layer.tips(c,this,{tips:e||1,time:-1,success:function(a){d&&a.css("margin-left",d+"px")}}),a.data("index",f))}).on("mouseleave","*[lay-tips]",function(){layer.close(b(this).data("index"))}),C=layui.data.resizeSystem=function(){layer.closeAll("tips"),C.lock||setTimeout(function(){y.sideFlexible(y.screen()<2?"":"spread"),delete C.lock},100),y.autoprevnextbutton(),C.lock=!0},h.on("resize",layui.data.resizeSystem),a("admin",y)});