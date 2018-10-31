/// <reference path="../typings/jquery/jquery.d.ts" />

var home = {};
$(document).ready(function(){

    FastClick.attach(document.body);

    // $('.carousel').carousel({ interval: 500 });

    var homeHeaderSpan = $('#home-header-ul>li span');
    homeHeaderSpan.each(function(i,element){
        $(element).click(function(){
            removeClass(homeHeaderSpan,'header-active');
            $(this).addClass('header-active');
        });
    });    

    home = {
        bannerIndex:0,
        investIndex:0,
        bannerTimer:null,        
        bannerSwitch:$('.home-2 .h2-hover>div'),
        bannerFade:$('.banner .home-banner li')
    }

    home.bannerTimer =setInterval(function () {
            bannerShowFun();
            spanIntervalFun();
    },3000);    

    home.bannerSwitch.hover(function(){
        var spanIndex = $(this).index();
        bannerSwitchFun(spanIndex);
    },function(){

    });    



    var browser={  
        versions:function(){   
            var u = navigator.userAgent, app = navigator.appVersion;   
            return {//移动终端浏览器版本信息   
                    trident: u.indexOf('Trident') > -1, //IE内核  
                    presto: u.indexOf('Presto') > -1, //opera内核  
                    webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核  
                    gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核  
                    mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端  
                    ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端  
                    android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器  
                    iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器  
                    iPad: u.indexOf('iPad') > -1, //是否iPad    
                    webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部  
                };  
            }(),  
            language:(navigator.browserLanguage || navigator.language).toLowerCase()  
    }   

    if(browser.versions.mobile || browser.versions.ios || browser.versions.android ||   
        browser.versions.iPhone || browser.versions.iPad){            
            loadjscssfile('themes/czmf/2017/GWV2/css/mobile.css','css');           
            
    } else{        
        
                    //   pc
    }        

});

function removeClass(This,className){
    This.each(function(i,element){
        $(element).removeClass(className);
    });
}

function spanIntervalFun(){
    home.bannerSwitch.each(function(i,el){
        $(el).find('img').each(function(i,element){            
            $(element).removeClass('img_active');
            if(i==0){
                $(element).addClass('img_active');
            }
        });        
    });    
    home.bannerSwitch.eq(home.bannerIndex).find('img').eq(0).removeClass('img_active');
    home.bannerSwitch.eq(home.bannerIndex).find('img').eq(1).addClass('img_active');
}

function bannerSwitchFun(spanIndex) {
    clearInterval(home.bannerTimer);
    var bannerArr = [2, 0, 1];
    home.bannerIndex = bannerArr[spanIndex];
    bannerShowFun();
    spanIntervalFun();
    home.bannerTimer = setInterval(function () {
        bannerShowFun();
        spanIntervalFun();
    },3000);
}

function bannerShowFun (){
        switch (home.bannerIndex) {
        case 0:
            home.bannerFade.eq(1).fadeIn();   
            home.bannerFade.eq(0).fadeOut();
            home.bannerFade.eq(2).fadeOut();
            break;
        case 1:
            home.bannerFade.eq(2).fadeIn();   
            home.bannerFade.eq(0).fadeOut();
            home.bannerFade.eq(1).fadeOut();
            break;         
        case 2:
            home.bannerFade.eq(0).fadeIn();   
            home.bannerFade.eq(1).fadeOut();
            home.bannerFade.eq(2).fadeOut();
            break;                 
        default:
            break;
    }
    home.bannerIndex++;
    if (home.bannerIndex>=3) {
        home.bannerIndex=0;
    }
}

function submitUserInfo(name,phone,topicid,orderCode){
    if(phone && /^1[3|4|7|5|8]\d{9}$/.test(phone) ){
        $.ajax({
            url:"/addPhone.php?act=addPhone",
            data: {'mobile':phone,'name':name,'topicid':topicid},
            type:'post',
            success: function(data){
                if(data){       
                    alert('预约成功，稍后会有工作人员和您联系！');                       
                    if(orderCode==1){
                        $('#myOrder').modal('hide');
                        jinxiu.orderUserName='';
                        jinxiu.orderPhoneNumber='';
                    }
                    if(orderCode==2){                        
                        mAgent.myAgentInput.each(function(i,element){
                            $(element).val('');
                        })
                    }                    
                }else{
                    alert('预约失败，请重新输入！');
                }
            }
        });
    }else{
        alert('请输入正确的手机号码!');
    }

}


//动态加载js/css
function loadjscssfile(filename,filetype){
    if(filetype == "js"){
        var fileref = document.createElement('script');
        fileref.setAttribute("type","text/javascript");
        fileref.setAttribute("src",filename);
    }else if(filetype == "css"){
        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",filename);
    }
   if(typeof fileref != "undefined"){
        document.getElementsByTagName("head")[0].appendChild(fileref);
    }
    
}	
