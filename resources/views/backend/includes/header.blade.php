<header class="app-header navbar app-header-color" id="app-header-vue">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
       
        {{-- <img class="navbar-brand-full" src="{{ asset('img/backend/brand/logo.svg') }}" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="{{ asset('img/backend/brand/sygnet.svg') }}" width="30" height="30" alt="CoreUI Logo"> --}}
        {{-- <ul class="nav navbar-nav d-md-down-none"> --}}
            {{-- <li class="nav-item px-3">
                <a class="nav-link" href="{{ route('frontend.index') }}"><i class="fas fa-home"></i></a>
            </li> --}}
            {{-- <li class="nav-item px-3">
                
            </li> --}}
            {{-- <li class="nav-item px-3">
                @include('includes.partials.brand')
            </li> --}}
            {{-- @if(config('locale.status') && count(config('locale.languages')) > 1)
                <li class="nav-item px-3 dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="d-md-down-none">@lang('menus.language-picker.language') ({{ strtoupper(app()->getLocale()) }})</span>
                    </a>
    
                    @include('includes.partials.lang')
                </li>
            @endif --}}
        {{-- </ul> --}}
        @include('includes.partials.workspace-and-brand')
        <ul class="nav navbar-nav ml-auto navbar-vertical-line">
            <li class="nav-item d-md-down-none">
                @include('includes.partials.lang')
            </li>
            <li class="nav-item d-md-down-none">
                <a class="nav-link" href="#">
                    <i class="far fa-flag" style="color: white; font-size: 18px;"></i>
                </a>
            </li>
            <li class="nav-item d-md-down-none">
                <notification-component :user_notifications="{{$logged_in_user->notifications}}"></notification-component>
            </li>
            <li class="nav-item d-md-down-none" style="padding: 0px 15px 0px 15px !important;">
                <account-component :user="{{$logged_in_user}}"></account-component>
            </li>
        </ul>
       
    {{-- @include('includes.partials.workspace')
    @include('includes.partials.brand') --}}
 

  
</header>

@section('script')
    <script>
        // $( document ).ready(function() {
            // $(document).on("click",".notification-href",function(e) {
            //     e.preventDefault()
            //     $(this).closest('form').submit()
            // });
        // });
    </script>
@endsection
{{-- <ul class="dropdown-menu dropdown-menu-right">
    @foreach ($logged_in_user->notifications as $notif)
        @if($notif->read_at == null)
            <li>
                <form method="POST" action="{{route('admin.auth.user.notif.read')}}">
                    @csrf
                    <input type="hidden" name="notif_id" value="{{$notif->id}}">
                    <a href="#" class="top-text-block notification-href" style="background: #a2dbee;">
                        <div class="top-text-heading">You've been invited to an Organization</div>
                        <div class="top-text-light">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                    </a>
                </form>
                
            </li>
        @else
            <li>
                <form method="POST" action="{{route('admin.auth.user.notif.read')}}">
                    @csrf
                    <input type="hidden" name="notif_id" value="{{$notif->id}}">
                    <a href="#" class="top-text-block notification-href">
                        <div class="top-text-heading">You've been invited to an Organization</div>
                        <div class="top-text-light">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                    </a>
                </form>
            </li>
        @endif
    @endforeach
</ul> --}}