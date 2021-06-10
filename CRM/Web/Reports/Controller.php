<?php

namespace CRM\Web\Reports;

use App\Http\Controllers\Controller as ControllersController;
use Illuminate\Http\Request;

class Controller extends ControllersController
{


    public function __construct()
    {
        $this->mainTitle = __('menus.backend.sidebar.reports');
    }

    public function insight_view(Request $request)
    {
        return view('backend.pages.reports.insights')
            ->withPageTitle("{$this->mainTitle} ~ " . __('menus.backend.sidebar.insights'));
    }

    public function statistic_view(Request $request)
    {
        return view('backend.pages.reports.statistic')
            ->withPageTitle("{$this->mainTitle} ~ " . __('menus.backend.sidebar.statistics.main'));
    }

    public function summary_view(Request $request)
    {
        return view('backend.pages.reports.summaries')
            ->withPageTitle("{$this->mainTitle} ~ " . __('menus.backend.summaries.main'));
    }
}
