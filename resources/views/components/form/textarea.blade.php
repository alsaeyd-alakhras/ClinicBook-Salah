@props([
    'value' => '',
    'name',
    'label'=>'',
    'rows' => 3
])
@if ($label)
    <label class="form-label" for="{{$name}}">
        {{ $label }}
    </label>
@endif

<textarea
    name="{{$name}}"
    id="{{$name}}"
    rows="{{$rows}}"
    {{$attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ])}};
>{{old($name,$value)}}</textarea>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{$message}}
    </div>
@enderror
