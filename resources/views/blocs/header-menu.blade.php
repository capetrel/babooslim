<ul class="menu">
    @foreach($structure['menus']['menu_header'] as $title => $content)
        <li class="menu-item {{ isset($content['submenu']) ? 'dropdown-item' : '' }} {{ setActiveItem($content['link']) }}">
            @if(isset($content['submenu']))
                {{ $title }}
                <ul class="dropdown-content">
                    @foreach($content['submenu'] as $subtitle => $value)
                        <a class="subitem-link {{ setActiveItem($value['link']) }}" href="{{ $value['link'] }}" target="{{ $value['target'] ? $value['target'] : '_self' }}" title="{{ $value['title'] ? $value['title'] : 'Voir la page' }}">
                            <li>{{ $subtitle }}</li>
                        </a>
                    @endforeach
                </ul>
            @else
                <a class="item-link {{ setActiveItem($content['link']) }}" href="{{ $content['link'] }}" target="{{ $content['target'] ? $content['target'] : '_self' }}" title="{{ $content['title'] ? $content['title'] : 'Voir la page' }}">
                    {{ $title }}
                </a>
            @endif
        </li>
    @endforeach
</ul>