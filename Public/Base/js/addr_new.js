/**
 * 加载地址
 * country  加载的地址；；；
 * @param  string id 传入省市的id;
 * @return {[type]}         [description]
 */
document.write("<script src='./Public/Base/js/addr_json.js'></script>");

function address(id) {
    if (typeof(country[id]) != "undefined") {
        return country[id];
    }
};