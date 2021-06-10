<footer class="app-footer">
    <div class="ml-3">
        <strong>
            @lang('labels.general.copyright') &copy; 2020
            {{-- {{ date('Y') }} --}}
            <a href="{{ route(home_route()) }}" class="text-capitalize">
                {{ app_name() }}
                {{-- @lang('strings.backend.general.boilerplate_link') --}}
            </a>
        </strong> 
        @lang('strings.backend.general.all_rights_reserved')
    </div>

    {{-- <div class="ml-auto">
        <strong class="text-uppercase">
            {{ app_name() }}
        </strong>
    </div> --}}
</footer>
