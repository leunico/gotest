<!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title></title>
	<style>
		.exam_container {
        min-width: 1200px;
        padding: 50px;
		}
		.exam_container .bg_white {
		background-color: #fff;
		box-shadow: 0px 4px 45px 0px rgba(0, 0, 0, 0.08);
		padding: 50px;
		box-sizing: border-box;
		}
		.exam_container .file_title {
		font: bold 24px "微软雅黑";
		text-align: center;
		margin: 0;
		color: #ee5337;
		}
		.exam_container .vital_ahead {
		margin: 50px auto;
		}
		.exam_container .vital_ahead .tips_vital {
		font: 18px "微软雅黑";
		margin: 0;
		line-height: 1.5;
		}
		.exam_container .vital_ahead .tips_vital a {
		color: #2284f1;
		}
		.exam_container .vital_ahead .tips_vital u {
		font-weight: bold;
		}
		.exam_container .vital_ahead h3 {
		font: 20px "微软雅黑";
		color: #333;
		}
		.exam_container .vital_ahead table {
		width: 100%;
		}
		.exam_container .vital_ahead table td {
		padding: 10px;
		font-size: 16px;
		}
		.exam_container .notice_plate .download_chrome p {
		font: bold 16px "微软雅黑";
		}
		.exam_container .notice_plate .download_chrome p a {
		display: inline-block;
		color: #2284f1;
		}
		.exam_container .notice_plate h3 {
		font: 20px "微软雅黑";
		}
		.exam_container .notice_plate h5 {
		font: 18px "微软雅黑";
		}
		.exam_container .notice_plate li {
		font: 16px "微软雅黑";
		line-height: 1.5;
		margin: 5px;
		}
		.exam_container .notice_plate li u {
		font-weight: bold;
		}
		.exam_container .notice_plate .focus_tips {
		font: bold 20px "微软雅黑";
		display: block;
		margin: 50px auto 0;
		}
	</style>
	</head>
	<body>
	    <div class="exam_container">
	        <div class="container bg_white">
	            <h2 class="file_title">{{ $eexaminee->examination->match->title }}考试须知</h2>
	            <div class="vital_ahead">
	                <p class="tips_vital">恭喜你的报名信息已审核通过，请尽快登录至<a href="{{ $url }}" title="编玩边学">在线考试平台（跳转链接）</a>修改密码并<u>完成设备检测，设备检测不合格的考生将无法参加考试</u>。</p>
	                <h3>参赛信息：</h3>
	                <table border="1">
	                	<tbody><tr>
	                	    <td>姓名</td>
	                	    <td>{{ $eexaminee->examinee->name }}</td>
	                	    <td>准考证号</td>
	                	    <td>{{ $eexaminee->admission_ticket }}</td>
	                	</tr>
	                	<tr>
	                	    <td>证件类型</td>
	                	    <td>{{ $certificate_type_str }}</td>
	                	    <td>证件号码</td>
	                	    <td>{{ $eexaminee->examinee->certificates }}</td>
	                	</tr>
	                	<tr>
	                	    <td>比赛名称</td>
	                	    <td>{{ $eexaminee->examination->title }}</td>
	                	    <td>考试时间</td>
	                	    <td>{{ $eexaminee->examination->start_at }}</td>
	                	</tr>
	                	<tr>
	                	    <td>考试事务咨询联络老师</td>
	                	    <td colspan="3">{{ $eexaminee->examinee->remarks }}</td>
	                	</tr>
	                	<tr>
                            <td>考试系统网址</td>
                            <td colspan="3"><a href="{{ $url }}">{{ $url }}</a></td>
                        </tr>
                        <tr>
                            <td>初始密码</td>
                            <td colspan="3">{{ $password }}</td>
                        </tr>
	                </tbody></table>
	            </div>	            
	            <div class="notice_plate">
	                <h3>考试须知：</h3>
	                <h5>一、考试环境的硬件要求</h5>
	                <ol type="1">
	                    <li>提前准备带有摄像头、麦克风的电脑；</li>
	                    <li>操作系统：win7含以上或mac；</li>
	                    <li>浏览器：考试开启视频监控功能，则必须使用GoogleChrome谷歌72或以上版本浏览器；</li>
	                    <li>考试系统需要联网进行，请保证在考试期间拥有良好的网络环境。</li>
	                </ol>	                
	                <div class="download_chrome">
	                    <p>谷歌下载链接：<a href="https://www.google.cn/chrome/">Windows/Mac系统下载</a></p>
	                </div>	                
	                <h5>二、考试流程及注意事项</h5>
                    <ol type="1">
                        <li>请确认参考信息是否正确，如信息有误请及时联系报名机构老师。考生本人须妥善保管准考证号及密码并保证不向任何他人透露。否则，考生本人将对由此产生的所有后果负责。</li>
                        <li>考试前<u>1小时内</u>请登录“线上考试平台”，完成人脸身份信息认证，确保设备正常，并做好考前准备工作。</li>
                        <li>考试将严格按照北京时间开考，因个人原因无法按时参考产生的所有后果自行负责。</li>
                        <li>线上考试平台采用视频监考的方式，考试过程中考生的一切状态将会展示在蓝桥杯组委会监考老师面前，并不定时抓拍考生在考试过程中的照片上传至考试平台，请各位考生注意仪表仪态，并以严肃、严谨的态度对待此次考试。</li>
                        <li>在考试过程中，考生不得以任何媒介复制任何部分的考试材料。不得切屏到考试系统以外的界面，考试期间独立完成题目，不得抄袭作弊，否则会被<u>禁赛</u>。</li>
                        <li>考生本人保证报名时提交的个人信息资料真实、正确，并将对个人信息不真实或不正确而导致无法参加考试以及其他直接或间接的后果负责。</li>
                        <li>考生本人已经认真阅读并认同本文件的全部内容。</li>
                    </ol>	                
	                <h5>三、人脸验证注意事项</h5>
                    <ol type="1">
                        <li>务必确保脸部正面面向摄像头。</li>
                        <li>光线适宜，不宜过亮也不宜过暗。</li>
                        <li>所处背景尽量保持颜色单一。</li>
                    </ol>                    
                    <h5>四、考前测试流程及注意事项</h5>
                    <ol type="1">
                        <li>人脸验证-摄像头检测-麦克风检测-浏览器检测-考前测试-完成。</li>
                        <li>需完成所有考前测试的题目，且正常交卷。（测试题目不判定对错，只判定是否完成所有的题目，包括编程题目，且正常提交试卷。）</li>
                    </ol>                   
                    <u class="focus_tips">领队或家长，请您与参赛选手一起阅读考试须知上的各项提示信息，并做好相应准备。</u>
	            </div> 
	        </div>
	    </div>
	<script type="text/javascript">
		$(function(){
		    //最后加载的js文件，写在页面之下
		});
	</script>
</body></html>
