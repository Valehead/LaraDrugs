<ul class="list-group">
    @foreach ($ingredients as $ingredient)
        <li class="list-group-item">{{ $ingredient->name . ' - ' . $ingredient->strength}}</li>
    @endforeach
    <li class="list-group-item"></li>
</ul>
