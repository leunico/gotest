@extends('operate::layouts.app')

@section('content')
    <h1><span>资讯中心</span><span>NEWS</span></h1>
        <div class="information_container">
            <h2><span>迪恩动态</span></h2>
            <div class="information_list">
                @foreach($articles as $article)
                <div class="information_item">
                    <div class="img_box">
                        <img src="{{ $article->image_url }}"/>
                    </div>

                    <div class="right_information_box">
                        <h3>
                            <a href="{{ route('article.show', $article->id) }}">
                                {{ $article->title }}
                            </a>

                            <span>{{ $article->created_at->format('m月d日') }}</span>
                        </h3>
                        <p>
{{--                            {{ mb_substr(clean($article->body,'article_abstract'),0,200,"utf-8") }}......--}}
                            {{ $article->abstract }}
                            <a href="{{ route('article.show', $article->id) }}">
                                【查看详情】
                            </a>
                        </p>
                    </div>
                </div>
                @endforeach

            </div>

            <div class="paginator_platerow" style="text-align: center;">
                <ul class="pagination">
                    {{ $articles->render() }}
                </ul>
            </div>
        </div>
@endsection
