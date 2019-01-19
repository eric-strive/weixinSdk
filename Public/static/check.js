function regcheck(obj,reg){
	if(reg.test(obj.val())){
		return true;
	}
	return false;
}

//验证收货地址
function check_consignee(obj){
	//var reg = /^[\u4e00-\u9fa5a-zA-Z0-9_-]{1,16}$/;
	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value.length > 25){
		return "long";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";
}

//验证省市区
function check_pcc(obj){

	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value=="省份" || value=="地级市" || value=="市、县级市"){
		return "unselete";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";
}

//验证省
function check_province(obj){

	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value=="省份"){
		return "unselete";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";

}

//验证市
function check_city(obj){

	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value=="地级市"){
		return "unselete";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";

}

//验证区
function check_county(obj){

	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value=="市、县级市"){
		return "unselete";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";

}

//验证街道地址
function check_address(obj){
	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value.length >100){
		return "long";
	}
	if (!is_forbid(value)) {
		return "forbid";
	}
	return "pass";
}

//验证手机号码
function check_phone(obj){
	var reg = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if (!regcheck(obj, reg)) {
		return "notmatch";
	}
	return "pass";
}

//验证固定电话
function check_tel(obj){
	var reg = /^[\d-]{7,13}$/;
	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if (!regcheck(obj, reg)) {
		return "notmatch";
	}
	return "pass";
}

//验证邮箱
function check_email(obj){
   	var reg = /(^\s*)\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*(\s*$)/;
   	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if (!regcheck(obj, reg)) {
		return "notmatch";
	}
	return "pass";
}

//验证昵称
function check_nickname(obj){

	var min = 1;
	var max = 20;

    var reg = /^[\da-zA-Z\u4e00-\u9fa5_-]{1,20}$/;
   	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if(value.length < min ){
		return "short";
	}
	if(value.length > max ){
		return "long";
	}
	if (!regcheck(obj, reg)) {
		return "notmatch";
	}
	return "pass";


}

//验证邮政编码
function check_postcode(obj){
	var reg =/^[0-9]{6}$/;
	var value = obj.val();
	if (isEmpty(value)) {
		return "empty";
	}
	if (!regcheck(obj, reg)) {
		return "notmatch";
	}
	return "pass";
}

/**
 * 判断是否是空
 * @param value
 */
function isEmpty(value){
	if(value == null || value == "" || value == "undefined" || value == undefined || value == "null"){
		return true;
	}
	else{
		value = value.replace(/\s/g,"");
		if(value == ""){
			return true;
		}
		return false;
	}
}

/**
 * 只包含中文和英文
 * @param cs
 * @returns {Boolean}
 */
function isGbOrEn(value){
    var regu = "^[a-zA-Z\u4e00-\u9fa5]+$";
    var re = new RegExp(regu);
    if (value.search(re) != -1){
      return true;
    } else {
      return false;
    }
}

/**
 * 判断是否是数字
 */
function isNumber(value){
	if(isNaN(value)){
		return false;
	}
	else{
		return true;
	}
}

function isInt(value){
	var reg = /^\d+$/;  //利用正则表达式
	return reg.test(value);
}

function isFloat(){

}

/**
 * 检查是否含有非法字符
 * @param temp_str
 * @returns {Boolean}
 */
function is_forbid(temp_str){
    temp_str = trimTxt(temp_str);
	temp_str = temp_str.replace('*',"@");
	temp_str = temp_str.replace('--',"@");
	temp_str = temp_str.replace('/',"@");
	temp_str = temp_str.replace('+',"@");
	temp_str = temp_str.replace('\'',"@");
	temp_str = temp_str.replace('\\',"@");
	temp_str = temp_str.replace('$',"@");
	temp_str = temp_str.replace('^',"@");
	temp_str = temp_str.replace('.',"@");
	temp_str = temp_str.replace(';',"@");
	temp_str = temp_str.replace('<',"@");
	temp_str = temp_str.replace('>',"@");
	temp_str = temp_str.replace('"',"@");
	temp_str = temp_str.replace('=',"@");
	temp_str = temp_str.replace('{',"@");
	temp_str = temp_str.replace('}',"@");
	var forbid_str=new String('@,%,~,&');
	var forbid_array=new Array();
	forbid_array=forbid_str.split(',');
	for(i=0;i<forbid_array.length;i++){
		if(temp_str.search(new RegExp(forbid_array[i])) != -1)
		return false;
	}
	return true;
}

//正则
function trimTxt(txt){
 return txt.replace(/(^\s*)|(\s*$)/g, "");
}

//密码格式检测
function check_password(obj){
	var reg = /^[0-9a-zA-Z]{6,16}$/;
	if(obj.val() == ""){
		return "empty";
	}else if(obj.val().length < 6){
		return "short";
	}else if(obj.val().length > 16){
		return "long";
	}else if(!regcheck(obj,reg)){
	  return "notmatch";
	}
	return "pass";
}

//检测ie版本
function checkIE(){
	if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/6./i)=="6."){
		alert("您当前IE版本过低，请更新版本");
	}
	else if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/7./i)=="7."){
		alert("您当前IE版本过低，请更新版本");
	}
	else if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/8./i)=="8."){
		alert("您当前IE版本过低，请更新版本");
	}
}