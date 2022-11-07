@props(['drug', 'iteration'])
{{-- {{dd($drug)}} --}}
<tr>
    <th scope="row">{{$iteration}}</th>
    <td>{{$drug->openfda->brand_name[0]}}</td>
    <td>{{$drug->openfda->generic_name[0]}}</td>
    <td>{{$drug->products[0]->dosage_form}}</td>
    <td>{{$drug->products[0]->route}}</td>
    {{-- <td><a href="/drugs/{{ strtolower($drug->sponsor_name) }}/{{ $drug->openfda->brand_name[0] }}/{{ $drug->openfda->product_ndc[0] }}">Link</a></td> --}}
    <td><a href="/drugs/{{ $drug->openfda->product_ndc[0] }}">Link</a></td>
</tr>
