<?php

namespace CRM\Web\Questionnaire;

use App\Http\Controllers\Controller as ControllersController;
use Illuminate\Http\Request;

class Controller extends ControllersController
{


    public function __construct()
    {
        // $this->mainTitle = __('menus.backend.sidebar.reports');
        $this->mainTitle = 'Questionnaires';
    }

    public function questionnaire_view(Request $request)
    {
        return view('backend.pages.questionnaires.index')
            ->withPageTitle("{$this->mainTitle}");
    }
}
