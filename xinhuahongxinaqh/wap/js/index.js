/// <reference path="../typings/jquery/jquery.d.ts" />


var xinhua = {flag : true};

$(document).ready(function(){

    FastClick.attach(document.body);
   

    $('#order').on('click',function(){
        var phone = $('.footer input').val();
        var id = 38;
        if(xinhua.flag){
            xinhua.flag = false;
            submitUserInfo(phone,id);
        }        
    });    

});


function submitUserInfo(phone,id){
    if(phone && /^1[3|4|7|5|8]\d{9}$/.test(phone) ){
        $.ajax({
            url:"http://chengzimofang.com/addPhone.php?act=addPhone",
            data: {'mobile':phone,'topicid':id},
            type:'post',
            success: function(data){
                if(data){                    
                      alert('预约成功，稍后会有客服和您联系!');
                      $('.footer input').val('');
                }else{
                    alert('预约失败，请重新输入!');
                }
            },
            complete: function(){
                xinhua.flag = true;
            }
        });
    }else{
        alert('请输入正确的手机号码!');
        xinhua.flag = true;
    }
}
