@php
    $formBuilder = new \App\FormBuilder();
    if (isset($result['values'])) {
        $values = $result['values']->getValues();
    } else {
        $result['errors'] = null;
        $values = null;
    }
@endphp

@extends('layouts.site')

@push('scripts')
    {!! viteAssets('resources/js/test.js', false, true) !!}
@endpush

@section('content')
    <section class="contact">
        @if($page->content)
            <div class="content">
                {!! $page->content !!}
            </div>
        @endif

        @include('blocs.message-flash')

        <form class="contact-form" action="" method="post">
            {!! $formBuilder->field($result['errors'], 'name', $values, 'Votre nom') !!}
            {!! $formBuilder->field($result['errors'], 'email', $values, 'Votre email') !!}
            {!! $formBuilder->field($result['errors'], 'message', $values, 'Votre message', ['type' => 'textarea']) !!}
            <button>Envoyer</button>
        </form>
    </section>
@endsection