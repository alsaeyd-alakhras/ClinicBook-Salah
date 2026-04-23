@props([
    'optionsId' => null,
    'options' => [],
    'name',
    'id' => null,
    'label'=>'',
    'value'=> null,
    'required' => false
])
@if ($label)
    <label class="form-label" for="{{$name}}">
        {{ $label }}
    </label>
@endif

<select 
    id="{{$id ?? $name}}"
    name="{{$name}}"
    {{$attributes->class([
        'form-select',
        'is-invalid' => $errors->has($name)
    ])}}>
    <option value="" @selected(old($name, $value) == null)>إختر القيمة</option>
    @if($optionsId!= null)
        @foreach ($optionsId as $item)
            <option value="{{ $item->id }}" @selected(old($name, $value) == $item->id)>{{ $item->name }}</option>
        @endforeach
    @else
        @foreach ($options as $key => $item)
            @php
                $optionValue = is_int($key) ? $item : $key;
                $optionLabel = $item;
            @endphp
            <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    @endif
</select>

{{-- Validation --}}
@error($name)
    <div class="invalid-feedback">
        {{$message}}
    </div>
@enderror