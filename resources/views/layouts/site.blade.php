<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="description" content="{{ $page->meta_description ? $page->meta_description : getEnvValue('APP_META_DESC') }}" />
        <link rel="shortcut icon" type="image/x-icon" sizes="16x16" href="{{ asset('img/favicons/favicon.ico') }}">

        @stack('css')

        {!! viteAssets() !!}

        @stack('scripts')

        <title>{{ $page->title ? $page->title : getEnvValue('APP_NAME') }}</title>

    </head>
    <body>
        <nav class="container">
            <div class="wrapper main-navigation">
                <div class="logo">
                    <img src="{{ asset('/img/svg/logo.svg') }}" alt="Logo">
                </div>
                @include('blocs.header-menu')
            </div>
        </nav>
        <header class="container">
            <div class="wrapper header">
                <h1>{!! $page->header_title !!}</h1>
            </div>
        </header>

        <main class="content container">
            <div class="wrapper">
                @yield('content')
            </div>
        </main>
        <footer class="container">
            <div class="wrapper">
                <div class="col-2">
                    <div class="block">
                        @foreach($structure['blocs']['bloc_footer'] as $content)
                            <div class="block-title">
                                {{ $content["title"] }}
                            </div>
                            <div class="block-content">
                                {{ $content["content"] }}
                            </div>
                        @endforeach
                    </div>
                    <nav class="footer-menu">
                        <ul>
                            @foreach($structure['menus']['menu_footer'] as $title => $content)
                                <li class="menu-item">
                                    <a href="{{ $content['link'] }}" target="{{ $content['target'] ? $content['target'] : '_self' }}" title="{{ $content['title'] ? $content['title'] : 'Voir la page' }}">
                                        {{ $title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        </footer>

    </body>
</html>
