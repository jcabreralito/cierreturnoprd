@if ($role == 1)

@foreach ($estatus as $estatusR1)
    <option value="{{ $estatusR1->id }}" @if ($estatusR1->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR1->estatus }}</option>
@endforeach

@elseif ($role == 2)

    {{--  Tipo 1 = Solicitud por gerented, tipo 2 = Solicitud por jefes de area  --}}
    @if ($solicitudItem->tipo == 1)
        @foreach ($estatus as $estatusR2A)
            @if (in_array($estatusR2A->id, [1, 3, 9]))
                <option value="{{ $estatusR2A->id }}" @if ($estatusR2A->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR2A->estatus }}</option>
            @endif
        @endforeach
    @else
        @foreach ($estatus as $estatusR2B)
            @if (in_array($estatusR2B->id, [3, 9]))
                <option value="{{ $estatusR2B->id }}" @if ($estatusR2B->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR2B->estatus }}</option>
            @elseif (in_array($estatusR2B->id, [4]))
                <option value="{{ $estatusR2B->id }}" disabled @if ($estatusR2B->id == $solicitudItem->estatus_id) selected @endif >{{ $estatusR2B->estatus }}</option>
            @endif
        @endforeach
    @endif

@elseif ($role == 3)

    @if ($solicitudItem->tipo == 1)
        @foreach ($estatus as $estatusR3A)
            @if (in_array($estatusR3A->id, [3]))
                <option value="{{ $estatusR3A->id }}" @if ($estatusR3A->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR3A->estatus }}</option>
            @elseif (in_array($estatusR3A->id, [1]))
                <option value="{{ $estatusR3A->id }}" disabled @if ($estatusR3A->id == $solicitudItem->estatus_id) selected @endif >{{ $estatusR3A->estatus }}</option>
            @endif
        @endforeach
    @else
        @foreach ($estatus as $estatusR3B)
            @if (in_array($estatusR3B->id, [4, 6]))
                <option value="{{ $estatusR3B->id }}" @if ($estatusR3B->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR3B->estatus }}</option>
            @endif
        @endforeach
    @endif

@elseif ($role == 4)

@foreach ($estatus as $estatusR4)
    <option value="{{ $estatusR4->id }}" @if ($estatusR4->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR4->estatus }}</option>
@endforeach

@elseif ($role == 5)

@foreach ($estatus as $estatusR5)
    <option value="{{ $estatusR5->id }}" @if ($estatusR5->id == $solicitudItem->estatus_id) selected @endif>{{ $estatusR5->estatus }}</option>
@endforeach

@endif
