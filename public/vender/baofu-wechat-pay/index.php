<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>微信支付-DEMO</title>
</head>
<body>
<body style="margin:0 auto;">
<div style="margin:0 auto; width:800px;">
	<p class="STYLE1">&nbsp;</p>
	<p class="STYLE1">微信支付-DEMO</p>
	<p>&nbsp;</p>
	<p>01:<a href="Payindex.php" target="_blank">微信支付交易（需单独开通）</a>,</p>
	<p>02:<a href="Query.php" target="_blank">订单查询交易</a>,  </p>
        <p>03:<a href="PayApi.php" target="_blank">微信支付API交易（需单独开通）</a>,  </p>
        <p>04:<a href="PayApp.php" target="_blank">微信支付APP交易（需单独开通）</a>,  </p>
        <p>05:<a href="PayPublic.php" target="_blank">微信公从号支付交易（需单独开通）</a>,  </p>
        
   <p></p>
    <p>本DEMO只能运行于正式环境。需开通宝付微信支付产品后才可以使用。</p>
    <p>本实列仅供学习宝付微信支付产品使用，</p>
    <p>加密：RSA</p>
    <p>注意：在接收异步通知时处理完成后输出OK,Return_url不得有登陆过虑,本例有接收异步通知的样例在【ReturnAction.php】</p>
    <p>/Config	init.php   初始化<br>
        1、运行缺少$member_id ，$terminal_id，$private_key_password  参数填上正式环境的相关参数<br>
	2、$pfxfilename，$cerfilename  填入证书的路径<br>
	3、将私钥和宝付公钥文件放到CER目录</p>
</div>

</body>
</html>
