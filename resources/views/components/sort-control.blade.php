@props([
    'action',
    'options',
    'selectedSort',
    'selectedDirection',
    'preservedInputs' => [],
])

@php($controlId = 'sort-control-'.md5($action))

<form method="GET" action="{{ $action }}" class="row g-2 align-items-end mb-4" data-auto-submit-sort>
    @foreach($preservedInputs as $name => $value)
        @if($value !== null && $value !== '')
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endif
    @endforeach
    <div class="col-12 col-md-2">
        <label for="{{ $controlId }}-sort" class="form-label small fw-bold">Urutkan berdasarkan</label>
        <select id="{{ $controlId }}-sort" name="sort" class="form-select">
            @foreach($options as $key => $label)
                <option value="{{ $key }}" @selected($selectedSort === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-2">
        <label for="{{ $controlId }}-direction" class="form-label small fw-bold">Arah</label>
        <select id="{{ $controlId }}-direction" name="direction" class="form-select">
            <option value="asc" @selected($selectedDirection === 'asc')>Terlama</option>
            <option value="desc" @selected($selectedDirection === 'desc')>Terbaru</option>
        </select>
    </div>
</form>
