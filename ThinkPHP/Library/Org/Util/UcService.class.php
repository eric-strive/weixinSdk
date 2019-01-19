<?PHP

/**
 * Class UcService
 *Ucenter的通讯处理类文件
 */
namespace Org\Util;
class UcService
{
    /**
     * UcService constructor.
     * 构造方法
     * 包含文件
     */
    public function __construct()
    {
        include_once(WBLOG_ROOT_PATH.'Application/Stamp/Conf/config.inc.php');//ucenter的配置处理类文件
        include_once(WBLOG_ROOT_PATH.'uc_client/client.php');//客户文件
    }
    /**
     * 会员注册
     *
     */
    public function register($username, $password,$phone,$questionid = '', $answer = '', $regip = '',$regdate){
        $uid = uc_user_register($username, $password,$phone,$questionid, $answer, $regip,$regdate);//UCenter的注册验证函数
        return intval($uid);
    }

    /**
     * @param $username 用户名
     * @param $password 密码
     * @return array|string 返回值
     * 用户登录
     */
    public function uc_login($username, $password){
        list($uid, $username, $password, $phone) = uc_user_login($username, $password);
        if($uid > 0) {
            return array(
                'uid' => $uid,
                'username' => $username,
                'password' => $password,
                'phone' => $phone
            );
        }else{
            return  intval($uid);
        }
//        return intval($uid);
    }

    /**
     * @param $uid 用户id
     * @return string
     * 使用uc同步登录
     */
    public function uc_synlogin($uid){
        return uc_user_synlogin($uid);
    }
    /**
     * @param $uid 用户id
     * @return string
     * 获取用户数据
     */
    public function uc_get($username){
        if($data = uc_get_user($username)) {
            list($uid, $username, $email) = $data;
            return array(
                'uid' => $uid,
                'username' => $username,
                'phone' => $email
            );
        } else {
            return '用户不存在';
        }
    }
    /**
     * @param $uid 用户id
     * @return string
     * 1:更新成功
     *0:没有做任何修改
     *-1:旧密码不正确
     *-4:Email 格式有误
     *-5:Email 不允许注册
     *-6:该 Email 已经被注册
     *-7:没有做任何修改
     *-8:该用户受保护无权限更改
     * 更新用户资料
     */
    public function uc_edit($username,$oldpassword , $newpassword , $email){
        $ucresult = uc_user_edit($username,$oldpassword, $newpassword,$email);
        return intval($ucresult);
    }
    /**
     * @param $uid 用户id
     * @return string
     * 同步退出
     */
    public function user_synlogout(){
        $return = uc_user_synlogout();
        return $return;
    }
    /**
     * 获取用户的图像
     * rray(22) {
    [0] => string(5) "width"
    [1] => string(3) "450"
    [2] => string(6) "height"
    [3] => string(3) "253"
    [4] => string(5) "scale"
    [5] => string(8) "exactfit"
    [6] => string(3) "src"
    [7] => string(278) "http://uc.vhi99.com/images/camera.swf?inajax=1&appid=3&input=b5eaRdd4FtrdH8vQFpcVPrx8N7%2BUYAli6t35weB3B6%2Bc2jHAKroY1rbhkVp%2Bew7SKIp5RtGQbHZ4OyIylG724fphmw6cxEApqvvkghE0kuZ7os%2FBZRYPfg7Ixmc&agent=dc74887a67d15d6e8ec94ebb9e7f8d3b&ucapi=uc.vhi99.com&avatartype=&uploadSize=2048"
    [8] => string(2) "id"
    [9] => string(8) "mycamera"
    [10] => string(4) "name"
    [11] => string(8) "mycamera"
    [12] => string(7) "quality"
    [13] => string(4) "high"
    [14] => string(7) "bgcolor"
    [15] => string(7) "#ffffff"
    [16] => string(4) "menu"
    [17] => string(5) "false"
    [18] => string(13) "swLiveConnect"
    [19] => string(4) "true"
    [20] => string(17) "allowScriptAccess"
    [21] => string(6) "always"
     */
    public function uc_avatar($uid,$type,$html){
        return uc_avatar($uid,$type,$html);
    }
    /*
     * 检查图像是否存在
     */
    public function check_avatar($uid){
        return uc_check_avatar($uid);
    }

}
