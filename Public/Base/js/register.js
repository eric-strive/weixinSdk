var emailLoginUrlArrar = ['@gmail.com=http://mail.google.com/',
    '@163.com=http://mail.163.com/',
    '@126.com=http://mail.126.com/',
    '@hotmail.com=http://www.hotmail.com/',
    '@sina.com=http://mail.sina.com/',
    '@vip.sina.com=http://mail.sina.com/',
    '@tom.com=http://mail.tom.com/',
    '@qq.com=http://mail.qq.com/',
    '@139.com=http://mail.10086.cn/',
    '@msn.com=https://login.live.com/login.srf',
    '@sohu.com=http://mail.sohu.com/'];

function getEmailLoginUrl(email) {

    email = email.toLowerCase();
    if (email == "" || !isEmail(email)) {
        return null;
    }
    var index = email.indexOf("@");
    var emailSurfix = email.substring(index, email.length);
    for (var i = 0; i < emailLoginUrlArrar.length; i++) {
        if (emailLoginUrlArrar[i].indexOf(emailSurfix) == 0) {
            return emailLoginUrlArrar[i].split("=")[1];
        }
    }
    return null;
}

function isEmail(str) {
    return new RegExp("^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$").test(str);
}

function do_send_phone(phone, category_id, parameter){

    var parameter = $.extend({url:APP+"/Register/send_phone/phone/"+phone+"/category_id/"+category_id,type:"post",dataType:"JSON",success:function(data){},error:function(){}},parameter);

    $.ajax({
        
        url : parameter.url,
        type : parameter.type,
        dataType : parameter.dataType,
        success : parameter.success,
        error : parameter.error,

    });

}