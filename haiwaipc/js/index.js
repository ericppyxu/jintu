var xinhua = {flag:true}

var xinhua2 = {flag:true}

$('.footer .btn').on('click',function(){
    var phNumber = $('.footer .txt').val();
    var topId = 38;
    if(xinhua.flag){
        xinhua.flag = false;  
        submitUserInfo(phNumber,topId);              
    }
});


$('.home .checkIn .btn').on('click',function(){

    var checkName = $('.checkIn .name').val();
    var checkPhone = $('.checkIn .phone').val();

    var topId = 38;
    if(xinhua2.flag){
        xinhua2.flag = false;  
        submitUserInfo2(checkPhone,topId,checkName);              
    }

})


function submitUserInfo2(phone,id,name){
    if(phone && /^1[3|4|7|5|8]\d{9}$/.test(phone) ){
        $.ajax({
            url:"http://www.jintu-media.com/addPhone.php?act=addPhone",
            data: {'mobile':phone,'topicid':id,'name':name},
            type:'post',
            success: function(data){
                if(data){                    
                      alert('预约成功，稍后会有客服和您联系!');
                          $('.checkIn .name').val('');
                          $('.checkIn .phone').val('');
                }else{
                    alert('预约失败，请重新输入!');
                }
            },
            complete: function(){
                xinhua2.flag = true;
            }
        });
    }else{
        alert('请输入正确的手机号码!');
        xinhua2.flag = true;
    }
}

function submitUserInfo(phone,id){
    if(phone && /^1[3|4|7|5|8]\d{9}$/.test(phone) ){
        $.ajax({
            url:"http://www.jintu-media.com/addPhone.php?act=addPhone",
            data: {'mobile':phone,'topicid':id},
            type:'post',
            success: function(data){
                if(data){                    
                      alert('预约成功，稍后会有客服和您联系!');
                      $('.footer .txt').val('');
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
