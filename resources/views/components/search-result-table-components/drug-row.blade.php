@props(['drug', 'iteration', 'appnum'])
{{-- {{dd($drug)}} --}}
<tr>
    <th scope="row">{{$iteration}}</th>
    <td>{{$drug->brand_name}}</td>
    <td>Generic Name</td>
    {{-- <td>{{$drug->openfda->generic_name[0]}}</td> --}}
    <td>{{$drug->dosage_form}}</td>
    <td>{{$drug->route}}</td>
    <td><a href="/drugs/{{ $appnum }}/{{ $drug->brand_name }}">Link</a></td>
</tr>
