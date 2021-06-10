<div id="local">
    {{-- @foreach(array_keys(config('locale.languages')) as $lang)
        @if($lang != app()->getLocale())
            <small><a href="{{ '/lang/'.$lang }}" class="dropdown-item pt-1 pb-1">@lang('menus.language-picker.langs.'.$lang)</a></small>
        @endif
    @endforeach --}}
    <local-select></local-select>
</div>
