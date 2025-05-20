@component($typeForm, get_defined_vars())
    <textarea class="form-control text-dark" {{ $attributes }}>{{ $value ?? '' }}</textarea>
@endcomponent
