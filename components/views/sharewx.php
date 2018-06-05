<?php
/**
 * Created by PhpStorm.
 * User: Duck
 * Date: 2017-07-07
 * Time: 11:24
 */

?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    var sharetitle='<?=$title?>';// 分享标题
    var shareimg='<?=$image?>';
    //var shareimg='http://demo.open.weixin.qq.com/jssdk/images/p2166127561.jpg';// 分享图标图片大小要大于300*300才能显示
    var sharelink='<?=$url?>';// 分享链接
    var sharedesc='<?=$describe?>';//分享描述
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?=$configShare['appId']?>', // 必填，公众号的唯一标识
        timestamp: <?=$configShare['timestamp']?>, // 必填，生成签名的时间戳
        nonceStr: '<?=$configShare['nonceStr']?>', // 必填，生成签名的随机串
        signature: '<?=$configShare['signature']?>',// 必填，签名，见附录1
        jsApiList: <?php echo json_encode($share);?> // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });



    /**
     *  批量显示菜单项
     *  list的格式
     [
     'menuItem:readMode', // 阅读模式
     'menuItem:share:timeline', // 分享到朋友圈
     'menuItem:copyUrl' // 复制链接
     ],
     * @param list [] 显示的数据
     * @param message 成功后要说的话
     * @param option 成功后的操作
     */
    function showMenuItems(list,message,option){
        wx.showMenuItems({
            menuList:list,
            success: function (res) {
                if(message){
                    ////alert(message);
                    ////alert(JSON.stringify(list));
                }
            },
            fail: function (res) {
                ////alert(JSON.stringify(res));
            }
        });
    }
    wx.ready(function(){
        //获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
        wx.onMenuShareTimeline({
            title: sharetitle,
            link: sharelink,
            imgUrl: shareimg,
            success: function (res) {
               ////alert('已分享');
            },
            cancel: function (res) {
                //alert('已取消');
            },
            fail: function (res) {
               ////alert('分享失败');
            }
        });
        //获取“分享给朋友”按钮点击状态及自定义分享内容接口
        wx.onMenuShareAppMessage({
            title: sharetitle,
            desc: sharedesc,
            link: sharelink,
            imgUrl: shareimg,
            type:'link',
            dataUrl:'',
            success: function (res) {
               ////alert('已分享');
            },
            cancel: function (res) {
               ////alert('已取消');
            },
            fail: function (res) {
               ////alert('分享失败');
            }
        });
        //获取“分享到QQ”按钮点击状态及自定义分享内容接口
        wx.onMenuShareQQ({
            title: sharetitle,
            desc: sharedesc,
            link: sharelink,
            imgUrl: shareimg,
            success: function (res) {
               //alert('已分享');
            },
            cancel: function (res) {
               //alert('已取消');
            },
            fail: function (res) {
               //alert('分享失败');
            }
        });
        //获取“分享到QQ空间”按钮点击状态及自定义分享内容接口
        wx.onMenuShareQZone({
            title: sharetitle, // 分享标题
            desc: sharedesc, // 分享描述
            link: sharelink, // 分享链接
            imgUrl: shareimg, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
               //alert('已分享');
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
               //alert('已取消');
            }
        });
    });
</script>
