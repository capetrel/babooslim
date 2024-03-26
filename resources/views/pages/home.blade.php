@extends('layouts.site')

@push('scripts')
    {!! viteAssets('resources/js/test.js', false, true) !!}
@endpush

@section('content')
    <section class="intro">
        {!! $page->content !!}
    </section>

    <section class="works">
        @foreach($page->clients as $infos)
            <div class="card">
                <div class="card_header">
                    <a href="/clients/{{ $infos['client'] }}">
                        <h3>{!! $infos['nom'] !!}</h3>
                    </a>
                </div>
                <div class="card_body">
                    <p>{!! $infos['contenu'] !!}</p>
                </div>
            </div>
        @endforeach
    </section>
@endsection
