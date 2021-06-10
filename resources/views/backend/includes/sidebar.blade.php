<div class="sidebar main-sidebar-wrapper">
  <nav class="sidebar-nav">
    <ul class="nav">
      <li class="nav-title nav-general">
        @lang('menus.backend.sidebar.general')
      </li>
      {{-- <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/dashboard')) }}" href="{{ route('admin.dashboard') }}">
          <i class="nav-icon fas fa-tachometer-alt"></i>
          @lang('menus.backend.sidebar.dashboard')
        </a>
      </li> --}}

      <li class="nav-item ">
        <a class="nav-link {{ active_class(Route::is('admin.profiles.index')) }}" href="{{ route('admin.profiles.index')}}">
          <i class="nav-icon fas fa-address-book"></i>
          @lang('menus.backend.sidebar.client_profiling')
        </a>
      </li>


      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.questionnaires*')), 'open')}}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.questionnaires.*')) }}" href="javascript:;">
          <i class="nav-icon fas fa-question"></i>
          Question
        </a>

        <ul class="nav-dropdown-items" >
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.questionnaires.index')) }}" href="{{ route('admin.questionnaires.index') }}">
              Questionnaires
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.questions.question-list.index')) }}" href="{{ route('admin.questions.question-list.index') }}">
              Question List
            </a>
          </li>
        </ul>
      </li>

      
      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.article-content-maker*')), 'open')}}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.article-content-maker*')) }}" href="#">
          <i class="nav-icon fas fa-newspaper"></i>
          @lang('menus.backend.sidebar.software_publishing')
        </a>

        <ul class="nav-dropdown-items" id="article-content-maker-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.article-content-maker.providers.index')) }}" href="{{ route('admin.article-content-maker.providers.index') }}">
              @lang('menus.backend.providers.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.article-content-maker.provider-groups.index')) }}" href="{{ route('admin.article-content-maker.provider-groups.index') }}">
              @lang('menus.backend.provider-groups.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-capitalize {{ active_class(Route::is('admin.categorized-terms.terminologies*')) }}" href="{{ route('admin.categorized-terms.terminologies') }}">
              @lang('menus.backend.sidebar.terms')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-capitalize {{ active_class(Route::is('admin.software-publishing*')) }} " href="{{ route('admin.software-publishing.publishing-items') }}">
              @lang('menus.backend.publishing.item')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.article-content-maker.geolocalizations.index')) }}" href="{{ route('admin.article-content-maker.geolocalizations.index') }}">
              @lang('menus.backend.geolocalizations.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.article-content-maker.actors.index')) }}" href="{{ route('admin.article-content-maker.actors.index') }}">
              @lang('menus.backend.actors.main')
            </a>
          </li>
          {{-- <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.article-content-maker.services.index')) }}" href="{{ route('admin.article-content-maker.services.index') }}">
              @lang('menus.backend.services.main')
            </a>
          </li> --}}
          <li class="nav-item">
            <a class="nav-link text-capitalize {{ active_class(Route::is('admin.categorized-terms.term-type')) }}" href="{{ route('admin.categorized-terms.term-type') }}">
              @lang('menus.backend.sidebar.type_of_terms')
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.reports*')) , 'open')}}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.reports*')) }}" href="#">
          <i class="nav-icon fas fa-file-alt"></i>
          @lang('menus.backend.sidebar.reports')
        </a>

        <ul class="nav-dropdown-items" id="reports-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.reports.statistics')) }}" href="{{ route('admin.reports.statistics') }}">
              @lang('menus.backend.statistics.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.reports.insights')) }}" href="{{ route('admin.reports.insights') }}">
              @lang('menus.backend.sidebar.insights')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.reports.summaries')) }}" href="{{ route('admin.reports.summaries') }}">
              @lang('menus.backend.sidebar.summary')
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.workforce-management.*') || Route::is('admin.workforce-management.*')) , 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.workforce-management*')) }}" href="#">
          <i class="nav-icon fa fa-user" aria-hidden="true"></i>
          @lang('menus.backend.workforce_management.main')
        </a>

        <ul class="nav-dropdown-items" id="workforce-management-links">
          @if($logged_in_user->isAdmin())
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.request-type*')) }}" href="{{ route('admin.workforce-management.request-type.index') }}">
                @lang('menus.backend.workforce_management.request_type')
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.reasons*')) }}" href="{{ route('admin.workforce-management.reasons.index') }}">
                @lang('menus.backend.workforce_management.reasons')
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.color-coding*')) }}" href="{{ route('admin.workforce-management.color-coding.index') }}">
                @lang('menus.backend.workforce_management.color_coding')
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.approval-settings*')) }}" href="{{ route('admin.workforce-management.approval-settings.index') }}">
                @lang('menus.backend.workforce_management.approval_settings')
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.default-groups*')) }}" href="{{ route('admin.workforce-management.default-groups.index') }}">
                @lang('menus.backend.workforce_management.default_groups')
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.workforce-management.calendar-notes*')) }}" href="{{ route('admin.workforce-management.calendar-notes.index') }}">
                @lang('menus.backend.workforce_management.calendar_notes')
              </a>
            </li>
          @endif
        </ul>
      </li>

      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.publishing-tools*') && !Route::is('admin.publishing-tools.tags*')) , 'open')}}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.publishing-tools*') && !Route::is('admin.publishing-tools.tags*')) }}" href="#">
          <i class="nav-icon fas fa-stamp"></i>
          @lang('menus.backend.sidebar.publishing_tools')
        </a>

        <ul class="nav-dropdown-items" id="publishing-tools-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.publishing-tools.content*')) }}" href="{{ route('admin.publishing-tools.content') }}">
              @lang('menus.backend.content.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.publishing-tools.items*')) }}" href="{{ route('admin.publishing-tools.items') }}">
              @lang('menus.backend.items.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.publishing-tools.publishing*')) }}" href="{{ route('admin.publishing-tools.publishing') }}">
              @lang('menus.backend.publishing.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.publishing-tools.dates')) }}" href="{{ route('admin.publishing-tools.dates') }}">
              @lang('menus.backend.dates.main')
            </a>
          </li>
        </ul>
      </li>


      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.campaigns.*') || Route::is('admin.templates.*')) , 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.publishing-tools*')) }}" href="#">
          <i class="nav-icon fa fa-envelope-open" aria-hidden="true"></i>
          @lang('menus.backend.campaigns.main')
        </a>

        <ul class="nav-dropdown-items" id="campaigns-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.campaigns*')) }}" href="{{ route('admin.campaign.index') }}">
              @lang('menus.backend.campaigns.emails')
            </a>
          </li>
          @if($logged_in_user->isAdmin())
            <li class="nav-item">
              <a class="nav-link {{ active_class(Route::is('admin.templates*')) }}" href="{{ route('admin.templates.index') }}">
                @lang('menus.backend.campaigns.templates')
              </a>
            </li>
          @endif
        </ul>
      </li>

  

      {{-- <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.categorized-terms*')) , 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.categorized-terms*')) }}" href="#">
          <i class="fas fa-notes-medical  nav-icon  "></i>
          @lang('menus.backend.sidebar.categorized_terms')
        </a>
        <ul class="nav-dropdown-items" id="workforce-management-links">
          <li class="nav-item">
            <a class="nav-link text-capitalize {{ active_class(Route::is('admin.categorized-terms.type-date')) }}" href="{{ route('admin.categorized-terms.type-date') }}">
              @lang('menus.backend.sidebar.type_dates')
            </a>
          </li>
       
        </ul>
      </li> --}}


      <li class="nav-item nav-dropdown {{ active_class((Route::is('admin.brands.*') || Route::is('admin.auth.*') || Route::is('admin.publishing-tools.tags')) , 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.brands*') || Route::is('admin.auth.*') || Route::is('admin.publishing-tools.tags')) }}" href="#">
          <i class="nav-icon fas fa-ellipsis-h    "></i>
          @lang('menus.backend.more.main')
        </a>

        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/brands')) }}" href="{{ route('admin.brands.index') }}">
              <i class="nav-icon fas fa-briefcase"></i>
              @lang('menus.backend.sidebar.brands')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/platform-item')) }}" href="{{ route('admin.platform-item') }}">
              <i class="nav-icon fas fa-laptop"></i>
              @lang('menus.backend.platform.main')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.publishing-tools.tags')) }}" href="{{ route('admin.publishing-tools.tags') }}">
              <i class="nav-icon fas fa-tags"></i>
              @lang('menus.backend.tags.main')
            </a>
          </li>
          @if($logged_in_user->isMainUser() || $logged_in_user->isAdmin())
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/auth/user*')) }}"
              href="{{ route('admin.auth.user.index') }}">
              <i class="nav-icon fas fa-users"></i>
              @lang('labels.backend.access.users.management')
              @if ($pending_approval > 0)
              <span class="badge badge-danger">{{ $pending_approval }}</span>
              @endif
            </a>
          </li>
          @endif
        </ul>
      </li>



      
      <!--Publishing content to be removed for master pushes-->
{{--  <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/date-status')) }}" href="{{ route('admin.publishing-tools.date-status.dates') }}">
          <i class="nav-icon fa fa-tags"></i>
          @lang('menus.backend.dates.main')
        </a>
      </li>
     
      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/tags')) }}" href="{{ route('admin.tags') }}">
          <i class="nav-icon fa fa-tags"></i>
          @lang('menus.backend.tags.main')
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/item-type')) }}" href="{{ route('admin.item-type') }}">
          <i class="nav-icon fa fa-tags"></i>
          @lang('menus.backend.publishing.type')
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/content')) }}" href="{{ route('admin.content') }}">
          <i class="nav-icon fa fa-box"></i>
          @lang('menus.backend.content.main')
        </a>
      </li>
--}}

      {{-- 
        <li class="nav-item">
          <a class="nav-link {{ active_class(Route::is('admin/brands')) }}" href="{{ route('admin.brands.index') }}">
            <i class="nav-icon fas fa-briefcase"></i>
            @lang('menus.backend.sidebar.brands')
          </a>
        </li>


      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/items')) }}" href="{{ route('admin.items') }}">
          <i class="nav-icon fa fa-box"></i>
          @lang('menus.backend.items.main')
        </a>
      </li>

      <li class="nav-item nav-dropdown {{ active_class(Route::is('admin/publishing*'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin/publishing*')) }}" href="#">
          <i class="nav-icon far fa-envelope"></i>
          @lang('menus.backend.publishing.main')
        </a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{active_class(Route::is('admin/publishing-type')) }}"
              href="{{ route('admin.publishing-type') }}">
              @lang('menus.backend.publishing.type')
            </a>
          </li>
        </ul>
      </li> --}}

      <!--Publishing content to be removed for master pushes-->

      @if ($logged_in_user->isAdmin())
      <li class="nav-title">
        @lang('menus.backend.sidebar.system')
      </li>

      <li class="nav-item nav-dropdown  {{ active_class(Route::is('admin/attachments'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.attachments.category')) }}" href="#">
          <i class="nav-icon fas fa-paperclip"></i>
          @lang('menus.backend.sidebar.attachments')
        </a>

        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.attachments.category')) }}"
              href="{{ route('admin.attachments.category') }}">
              @lang('menus.backend.sidebar.documents_category')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.attachments.type')) }}"
              href="{{ route('admin.attachments.type') }}">
              @lang('menus.backend.sidebar.documents_type')
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/locations')) }}" href="{{ route('admin.locations.index') }}">
          <i class="nav-icon fas fa-map-marker"></i>
          @lang('menus.backend.sidebar.locations')
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ active_class(Route::is('admin/contact_types')) }}"
          href="{{ route('admin.contact_types.index') }}">
          <i class="nav-icon fas fa-info"></i>
          @lang('menus.backend.sidebar.contact_types')
        </a>
      </li>

      <li class="nav-item nav-dropdown  {{ active_class(Route::is('admin/jobs*'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.jobs.category')) }}" href="#">
          <i class="nav-icon fa fa-suitcase" aria-hidden="true"></i>
          @lang('menus.backend.sidebar.jobs')

        </a>

        <ul class="nav-dropdown-items" id="profiles-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.jobs.category')) }}"
              href="{{ route('admin.jobs.category') }}">
              @lang('menus.backend.sidebar.category')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.jobs.profession')) }}"
              href="{{ route('admin.jobs.profession') }}">
              @lang('menus.backend.sidebar.profession')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.jobs.description')) }}"
              href="{{ route('admin.jobs.description') }}">
              @lang('menus.backend.sidebar.specialization')
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item nav-dropdown  {{ active_class(Route::is('admin/actions*'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin.actions*')) }}" href="#">
          <i class="nav-icon fas fa-tasks"></i>
          @lang('menus.backend.sidebar.actions')
        </a>

        <ul class="nav-dropdown-items" id="profiles-links">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.actions.activity')) }}"
              href="{{ route('admin.actions.activity') }}">
              @lang('menus.backend.sidebar.activities')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.actions.engagements')) }}"
              href="{{ route('admin.actions.engagements') }}">
              @lang('menus.backend.sidebar.engagements')
              
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin.actions.administrative')) }}"
              href="{{ route('admin.actions.administrative') }}">
              @lang('menus.backend.sidebar.administrative')
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item nav-dropdown {{ active_class(Route::is('admin/auth*'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin/auth*')) }}" href="#">
          <i class="nav-icon far fa-user"></i>
          @lang('menus.backend.access.title')
          @if ($pending_approval > 0)
          <span class="badge badge-danger">{{ $pending_approval }}</span>
          @endif
        </a>
        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/auth/role*')) }}"
              href="{{ route('admin.auth.role.index') }}">
              @lang('labels.backend.access.roles.management')
            </a>
          </li>
        </ul>
      </li>

      <li class="divider"></li>
      <!-- Default dropright button -->
      <li class="nav-item nav-dropdown {{ active_class(Route::is('admin/log-viewer*'), 'open') }}">
        <a class="nav-link nav-dropdown-toggle {{ active_class(Route::is('admin/log-viewer*')) }}" href="#">
          <i class="nav-icon fas fa-list"></i>
          @lang('menus.backend.log-viewer.main')
        </a>

        <ul class="nav-dropdown-items">
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/log-viewer')) }}"
              href="{{ route('log-viewer::dashboard') }}">
              @lang('menus.backend.log-viewer.dashboard')
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ active_class(Route::is('admin/log-viewer/logs*')) }}"
              href="{{ route('log-viewer::logs.list') }}">
              @lang('menus.backend.log-viewer.logs')
            </a>
          </li>
        </ul>
      </li>
      @endif
    </ul>
  </nav>

  <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
<!--sidebar-->