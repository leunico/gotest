@extends('operate::layouts.app')

@section('title', $article->title)

@section('meta-desc', $article->description)

@section('meta-keyword', $article->keywords)

@section('content')
	<h1><span>资讯中心</span><span>NEWS</span></h1>
	<p>
		<span>
			<a href="{{ route('article.index') }}" style="text-decoration:none;color: #ffffff">资讯中心</a>
		</span>

		<a style="text-decoration:none;color: #ffffff" href="{{ route('article.show', $article->id) }}"><span>>>{{ $article->title }}</span></a>

	</p>
	<div class="information_detail_container">
		<div class="information_title">
			<h3>{{ $article->title }}</h3>
			<span>{{ $article->created_at }}</span>
		</div>

		<div class="word_conter ql-editor">
			{!! $article->body !!}
		</div>

		<div class="information_switch">
			<a href="
{{ !empty($article->previous->id) ? route('article.show', $article->previous->id): "javascript:void(0);"}}
					">上一篇：<span>
					{{ !empty($article->previous->id) ? $article->previous->title: '无'}}
				</span></a>
			<a href="
			{{ !empty($article->next->id) ? route('article.show', $article->next->id): "javascript:void(0);"}}
					">下一篇：<span>
					{{ !empty($article->next->id) ? $article->next->title: '无'}}
				</span></a>
		</div>

	</div>

@endsection
