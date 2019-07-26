@extends('operate::layouts.mobile')

@section('title', $article->title)

@section('meta-desc', $article->description)

@section('meta-keyword', $article->keywords)

@section('content')
	    <div class="seo_mobile">
	        <div class="seo_mobile_title">
	            <h1>资讯中心</h1>
	            <span>NEWS</span>
	        </div>

	        <div class="article_detail">
	            <div class="information_title">
                    <h3>{{ $article->title }}</h3>
                    <span>{{ $article->created_at }}</span>
                </div>

                <div class="word_conter ql-editor">
                    {!! $article->body !!}
                </div>

                <div class="information_switch">
                    <a href="
{{ !empty($article->previous->id) ? route('mobile-article.show', $article->previous->id): "javascript:void(0);"}}
                            ">上一篇：<span>
					{{ !empty($article->previous->id) ? $article->previous->title: '无'}}
				</span></a>
                    <a href="
			{{ !empty($article->next->id) ? route('mobile-article.show', $article->next->id): "javascript:void(0);"}}
                            ">下一篇：<span>
					{{ !empty($article->next->id) ? $article->next->title: '无'}}
				</span></a>
                </div>

	        </div>

	    </div>

@endsection
