<!DOCTYPE html>
@langrtl
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endlangrtl
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @auth
    <meta name="user" content="{{auth()->user()}}">
    <meta name="org_id" content="{{session()->get('organization')}}">
    <meta name="brand_id" content="{{session()->has('brand') ? session()->get('brand') : 'false'}}">
    @endauth

    <title>@yield('title', app_name())</title>
    <meta name="description" content="@yield('meta_description', 'Customer Care Tool')">
    <meta name="author" content="@yield('meta_author', 'Med4Care Team')">
    @yield('meta')

    {{-- See https://laravel.com/docs/5.5/blade#stacks for usage --}}
    @stack('before-styles')

    <!-- Check if the language is set to RTL, so apply the RTL layouts -->
    <!-- Otherwise apply the normal LTR layouts -->
    {{-- {{ style(mix('css/app.scss')) }} --}}
    <link rel="stylesheet" href="/css/backend.css" />

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.js"></script> --}}
  
    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> --}}



    @stack('after-styles')
    <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.4.2/Sortable.min.js"></script>  
    <script src="https://npmcdn.com/vue-sortable@latest"></script>
</head>

{{--
     * CoreUI BODY options, add following classes to body to change options
     * // Header options
     * 1. '.header-fixed'					- Fixed Header
     *
     * // Sidebar options
     * 1. '.sidebar-fixed'					- Fixed Sidebar
     * 2. '.sidebar-hidden'				- Hidden Sidebar
     * 3. '.sidebar-off-canvas'		    - Off Canvas Sidebar
     * 4. '.sidebar-minimized'			    - Minimized Sidebar (Only icons)
     * 5. '.sidebar-compact'			    - Compact Sidebar
     *
     * // Aside options
     * 1. '.aside-menu-fixed'			    - Fixed Aside Menu
     * 2. ''			    - Hidden Aside Menu
     * 3. '.aside-menu-off-canvas'	        - Off Canvas Aside Menu
     *
     * // Breadcrumb options
     * 1. '.breadcrumb-fixed'			    - Fixed Breadcrumb
     *
     * // Footer options
     * 1. '.footer-fixed'					- Fixed footer
--}}
<body class="header-fixed sidebar-fixed aside-menu-off-canvas sidebar-lg-show">
    <v-app id="app-vue">
        <div class="app">
            @include('backend.includes.modal')
            @include('backend.includes.header')

            <div id="page-container" class="app-body">
                @include('backend.includes.sidebar')

                <main class="main" style="display: flex; flex-flow: column; height: 100%;">
                    @include('includes.partials.read-only')
                    @include('includes.partials.logged-in-as')

                    <div class="container-fluid flex-grow-1">
                        <div class="animated fadeIn mt-3">
                            <div class="content-header">
                                @yield('page-header')
                            </div><!--content-header-->

                            @include('includes.partials.messages')
                            @yield('content')
                        </div><!--animated-->
                    </div><!--container-fluid-->
                </main><!--main-->

                @include('backend.includes.aside')
            </div><!--app-body-->

            @include('backend.includes.footer') 
        </div>
    </v-app>

    

    <!-- Scripts -->
    @yield('script')
    @stack('before-scripts')
    {!! script(mix('js/manifest.js')) !!}
    {!! script(mix('js/vendor.js')) !!}
    {!! script(mix('js/backend.js')) !!}
    @stack('after-scripts')

 
</body>
</html>
