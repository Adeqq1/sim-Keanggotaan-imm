@props([
    'action',
    'options',
    'selectedSort',
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
</form>
