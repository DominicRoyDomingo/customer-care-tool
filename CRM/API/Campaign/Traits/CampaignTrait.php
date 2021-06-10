<?php

namespace CRM\API\Campaign\Traits;

use Illuminate\Http\Request;

/**
 * Campaign Traits
 */
trait CampaignTrait
{
    public function checkCampaignEmailForm(Request $request)
    {
        $request->validate([
            'sender_name' => 'required',
            'sender_email' => 'required',
            'brand' => 'required',
            'template_id' => 'required'
        ]);

        return response()->json(true);
    }

    public function checkCampaignContentForm(Request $request)
    {
    }
}
