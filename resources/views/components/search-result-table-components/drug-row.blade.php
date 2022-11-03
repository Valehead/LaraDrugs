@props(['drug', 'iteration'])
{{-- {{dd($drug)}} --}}
<tr>
    <th scope="row">{{$iteration}}</th>
    <td>{{$drug->openfda->brand_name[0]}}</td>
    <td>{{$drug->openfda->generic_name[0]}}</td>
    <td>{{$drug->products[0]->dosage_form}}</td>
    <td>{{$drug->products[0]->route}}</td>
    <td><a href="#">Link</a></td>
</tr>


