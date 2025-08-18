@if ($role == 1)

@foreach ($estatus as $estatusR1)
    <option value="{{ $estatusR1->id }}">{{ $estatusR1->estatus }}</option>
@endforeach

@elseif ($role == 2)

@foreach ($estatus as $estatusR2)
    @if (in_array($estatusR2->id, [1, 3]))
        <option value="{{ $estatusR2->id }}">{{ $estatusR2->estatus }}</option>
    @endif
@endforeach

@elseif ($role == 3)

@foreach ($estatus as $estatusR3)
    @if (in_array($estatusR3->id, [4, 6]))
        <option value="{{ $estatusR3->id }}">{{ $estatusR3->estatus }}</option>
    @endif
@endforeach

@elseif ($role == 4)

@elseif ($role == 5)

@foreach ($estatus as $estatusR1)
    <option value="{{ $estatusR1->id }}">{{ $estatusR1->estatus }}</option>
@endforeach

@endif
