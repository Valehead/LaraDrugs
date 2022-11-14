@props(['drug', 'iteration', 'appnum', 'appAccordionBool'])
{{-- {{dd($drug)}} --}}

@if ($appAccordionBool == true)

<tr>
    <th scope="row"><a href="/drugs/{{ $appnum }}/{{ $drug->product_number }}">{{ $iteration }}</a></th>
    <td>{{ $drug->brand_name }}</td>
    <td>
        @foreach ($drug->active_ingredients as $ingredient)

            {{ $ingredient->name . ': ' . $ingredient->strength }}

        @endforeach
    </td>
    <td>{{ $drug->dosage_form }}</td>
    <td>{{ $drug->route }}</td>
    <td><a href="/drugs/{{ $appnum }}/{{ $drug->product_number }}">Link</a></td>
</tr>

@else

<tr>
    <th scope="row"><a href="/drugs/{{ $appnum }}/{{ $drug->product_number }}">{{$iteration}}</a></th>
    <td>{{$drug->brand_name}}</td>
    <td>{{ $appnum }}</td>
    <td>{{$drug->dosage_form}}</td>
    <td>{{$drug->route}}</td>
    <td><a href="/drugs/{{ $appnum }}/{{ $drug->product_number }}">Link</a></td>
</tr>

@endif
