<div class="filter-item">
    <form action="" method="GET">
        <label for="tag-select">Filtrer par:</label>
        <select name="tag" id="tag-select">
            @if($sorted)
                @if( array_key_exists ($_GET['tag'], $categories) )
                    <option value="{{ $_GET['tag'] }}">{{ $categories[$_GET['tag']] }}</option>
                    @php unset($categories[$_GET['tag']]) @endphp
                @endif
                @foreach($categories as $k => $category)
                    <option value="{{ $k }}">{{ $category }}</option>
                @endforeach
                <option value="all">Toutes</option>
            @else
                <option value="all">Toutes</option>
                @foreach($categories as $k => $category)
                    <option value="{{ $k }}">{{ $category }}</option>
                @endforeach
            @endif
        </select>
        <input id="setFilter" type="submit" value="filtrer">
    </form>
</div>