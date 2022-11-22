<ul class="ps-2 list-group">
    @foreach ($ingredients as $ingredient)
        <li class="list-group-item">{{ $ingredient->name . ' - ' . $ingredient->strength}}</li>
    @endforeach
</ul>
