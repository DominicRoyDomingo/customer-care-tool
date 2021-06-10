<?php

namespace CRM\API\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Mails\Recipient;
use CRM\API\Campaign\CampaignRepository;
use CRM\API\Campaign\Traits\CampaignTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
   use CampaignTrait;

   private $campaignRepository;

   public function __construct(CampaignRepository $campaignRepository)
   {
      $this->campaignRepository = $campaignRepository;
   }

   public function index(Request $request)
   {
      $campaigns = $this->campaignRepository->getAll($request);

      return response()->json($campaigns);
   }

   public function show($id)
   {
      $campaign = $this->campaignRepository->show($id);

      return response()->json($campaign);
   }


   public function show_email($id)
   {
      $campaignEmail = $this->campaignRepository->show_email($id);

      return response()->json($campaignEmail);
   }
   

   public function store(Request $request)
   {
      $request->validate([
         'sender_name' => 'required',
         'sender_email' => 'required',
         'brand' => 'required',
      ]);

      $campaigns = $this->campaignRepository->create(
         $request->all()
      );

      if ($campaigns) {
         $campaign_preview = $this->campaignRepository->show_email($campaigns->id);
         return response()->json($campaign_preview);
      }

      return response()->json(false);
   }

   public function post_campaign(Request $request)
   {
      $request->validate([
         'campaign' => 'required|min:2',
      ]);

      $campaigns = '';

      if ($request->type === 'create') {

         $arrayList =  $this->campaignRepository->getAll($request);

         $checked = is_data_exist('campaign', $request->campaign, get_data($request->locale, ['campaign'], $arrayList));

         if ($checked) {
            throw ValidationException::withMessages(['campaign' => $request->campaign . ' is already Exist']);
         }
         $campaigns = $this->campaignRepository->create_campaign($request->only('locale', 'campaign'));
      } else {
         $campaigns = $this->campaignRepository->update_campaign($request->id, $request->only('locale', 'campaign'));
      }

      return response()->json($campaigns);
   }

   public function remove_recipient(Request $request)
   {
      $recipient = Recipient::findOrFail($request->id);

      $recipient->delete();

      return response()->json(true);
   }

   public function sendCampaignEmail(Request $request)
   {
      $request->validate([
         'id' => 'required',
      ]);

      $emailed = $this->campaignRepository->sendCampaignEmail($request->all());

      return response()->json($emailed);
   }

   public function sendEmail(Request $request)
   {
      $request->validate([
         'sender_name' => 'required',
         'sender_email' => 'required|email',
         'brand' => 'required',
      ]);

      $emailed = $this->campaignRepository->sendEmailToCustomer($request);

      return response()->json($emailed);
   }

   public function destroy(Request $request)
   {
      $campaign = $this->campaignRepository->getById($request->id);

      $campaign->delete();

      return response()->json(true);
   }
}
