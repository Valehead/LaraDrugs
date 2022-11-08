@props(['drug', 'iteration'])
{{-- {{dd($drug)}} --}}
<tr>
    <th scope="row">{{$iteration}}</th>
    <td>{{$drug->openfda->brand_name}}</td>
    <td>Generic Name</td>
    {{-- <td>{{$drug->openfda->generic_name[0]}}</td> --}}
    <td>{{$drug->dosage_form}}</td>
    <td>{{$drug->route}}</td>
    <td><a href="/drugs/{{ $drug->openfda->product_ndc[0] }}">Link</a></td>
</tr>
