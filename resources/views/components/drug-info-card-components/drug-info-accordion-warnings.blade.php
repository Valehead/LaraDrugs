@props(['druginfo'])

@if (isset($druginfo->warnings[0]))
    <li class="list-group-item overflow-auto" style="height:200px"><p>{{$druginfo->warnings[0]}}</p></li>
@endif

@if (isset($druginfo->when_using[0]))
    <li class="list-group-item"><p>{{$druginfo->when_using[0]}}</p></li>
@endif

@if (isset($druginfo->stop_use[0]))
    <li class="list-group-item"><p>{{$druginfo->stop_use[0]}}</p></li>
@endif

@if (isset($druginfo->do_not_use[0]))
    <li class="list-group-item"><p>{{$druginfo->do_not_use[0]}}</p></li>
@endif

@if (isset($druginfo->pregnancy_or_breast_feeding[0]))
    <li class="list-group-item"><p>{{$druginfo->pregnancy_or_breast_feeding[0]}}</p></li>
@endif

@if (isset($druginfo->questions[0]))
    <li class="list-group-item"><p>{{$druginfo->questions[0]}}</p></li>
@endif
