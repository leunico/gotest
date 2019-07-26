@extends('operate::layouts.mobile')
@section('content')
	    <div class="seo_mobile">
	        <div class="seo_mobile_title">
	            <h1>资讯中心</h1>
	            <span>NEWS</span>
	        </div>
	        
	        <div class="seo_news_list">

	        </div>
            <div class="loading_box">
                <p>数据正在加载中·····</p>
            </div>
	    </div>
@endsection

@section('scriptsAfterJs')
<script type="text/javascript">
    $(function(){
        //最后加载的js文件，写在页面之下
        var dean_url = "{{route('api.article.index')}}";
        var list_page = 1;
        var flag_list = true;
        getNewslist(dean_url,list_page);
        function getNewslist(url,p){
            var succsess = '';

            $.ajax({
                url: dean_url,
                type:'GET',
                async:true,
                data:{
                    "page": p,
                },
                timeout:5000,    //超时时间
                dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text

                success:function(data,textStatus,jqXHR){
                    console.log(data.data);
                    if(data.data.length > 0){
                        for(let i in data.data){
                            var list_item = '<div class="seo_news_item" onclick="article_url('+data.data[i].id+')">' +
                                '<div class="img_box"><img src="'+data.data[i].image+'"/></div><div class="right_information_box"><h3>'+data.data[i].title+'</h3><p>'+
                                data.data[i].abstract
                                +'</p><span>'+data.data[i].date+'</span></div></div>';

                            $(".seo_news_list").append(list_item);

                        }
                    }else{
                        $(".loading_box").show();
                        $(".loading_box p").html("数据已经全部加载完毕！");
                        console.log("数据已经全部加载!"+p);
                        list_page = p-1;
                        flag_list = false;
                    }

                },

            });

        };

        $(window).bind("scroll", function () {
            if(getScrollHeight() == getDocumentTop() + getWindowHeight() && flag_list){
                //当滚动条到底时,这里是触发内容
                //异步请求数据,局部刷新dom
                //ajax_function()
                $(".loading_box").show();

                list_page++;
                getNewslist(dean_url,list_page)


            }
        });



        //文档高度
        function getDocumentTop() {
            var scrollTop =  0, bodyScrollTop = 0, documentScrollTop = 0;
            if (document.body) {
                bodyScrollTop = document.body.scrollTop;
            }
            if (document.documentElement) {
                documentScrollTop = document.documentElement.scrollTop;
            }
            scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
            console.log("scrollTop:"+scrollTop);
            return scrollTop;
        }

        //可视窗口高度
        function getWindowHeight() {
            var windowHeight = 0;
            if (document.compatMode == "CSS1Compat") {
                windowHeight = document.documentElement.clientHeight;
            } else {
                windowHeight = document.body.clientHeight;
            }
            console.log("windowHeight:"+windowHeight);
            return windowHeight;
        }

        //滚动条滚动高度
        function getScrollHeight() {
            var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
            if (document.body) {
                bodyScrollHeight = document.body.scrollHeight;
            }
            if (document.documentElement) {
                documentScrollHeight = document.documentElement.scrollHeight;
            }
            scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
            console.log("scrollHeight:"+scrollHeight);
            return scrollHeight;
        }
    });

    function article_url($url) {
        window.location = "/mobile-news/show/"+$url;
    }

</script>
@endsection
	    
