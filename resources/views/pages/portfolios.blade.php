@php
    $sorted = false;
    if( isset($_GET['tag'] )) {
        if ( $_GET['tag'] === 'all' ){
            $sorted = false;
            $portfolios = $page->portfolios;
        } else {
            $sorted = true;
            $portfolios = filterContentByTag($page->portfolios, $_GET['tag']);
        }
    } else {
        $portfolios = $page->portfolios;
    }
    $categories = setTagsForInput($page->portfolios, 'tag');
@endphp

@extends('layouts.site')

@section('content')
    <section class="intro">
        {!! $page->content !!}
    </section>
    <section class="portfolios">

        @include('blocs.filters')

        @foreach($portfolios as $portfolio)
            <div class="item">
                <div class="item-image">
                    <img src="{{ asset('/img/'. $portfolio['image']) }}" alt="{{ $portfolio['titre'] }}">
                </div>
                <div class="item-body">
                    <h3>{{ $portfolio['titre'] }}</h3>
                    <p class="tag">
                        @foreach($portfolio['tag'] as $k => $tag)
                            @php $last = end($portfolio['tag']) @endphp
                            {{ $tag }} {{ $tag === $last ? '' : ', ' }}
                        @endforeach
                    </p>
                    <p class="item-content">{{ $portfolio['content'] }}</p>
                    <a href="/portfolio/{{ $portfolio['link'] }}">En savoir +</a>
                </div>
            </div>
        @endforeach

    </section>

@endsection