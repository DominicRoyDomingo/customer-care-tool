<?php

namespace CRM\API\PaymentPlan;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan\PaymentPlan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use DB;

class PaymentPlanController extends Controller
{
    private $model;

    public function __construct(PaymentPlan $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $data = $this->model
            ->select('payment_plan.*')
            ->when($request->filter, function ($query) use ($request){
                $query->where('name', 'LIKE', "%{$request->filter}%");
             })
             ->when($request->sortbyLang, function ($query) use ($request) {
                $query->sortedByNoLang($request->sortbyLang);
             })
             ->with([
                'brand_paymentplan',
            ])
            ->orderBy('created_at', 'Desc')
            ->paginate($request->perPage);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $type = $request->type;
        
        Validator::make($request->all(), [
            'form.name' => ['required'],
            'form.typeOfPlan' => ['required'],
            'form.status' => ['required'],
            'form.price' => ['required'],
            'form.description' => ['required'],
            'form.features' => ['required'],
         ])->validate();
   
         $validateIfUnique = $this->validateIfUnique($request->form['name']);
   
         if ($validateIfUnique) {
            throw ValidationException::withMessages(['unique' =>  $request->form['name'].' is an existing Payment Plan Record']);
         }else{
            switch ($type) {
                case 'create':
                    $plan = $this->model->create([
                        "name" => to_json_add($request->locale, $request->form["name"]),
                        "type_plan" => $request->form["typeOfPlan"],
                        "status" => $request->form["status"],
                        "price" => $request->form["price"],
                        "description" => to_json_add($request->locale, $request->form["description"]),
                        "features" => to_json_add($request->locale, $request->form["features"]),
                    ]);
                   break;
                case 'edit':
                    $plan = $this->model->findOrFail($request->form['id']);
                    $plan->name =  to_json_add($request->language,$request->form['name'], to_json_remove($request->language,$plan->name));
                    $plan->type_plan =  $request->form["typeOfPlan"];
                    $plan->status = $request->form["status"];
                    $plan->price = $request->form["price"];
                    $plan->description =  to_json_add($request->language,$request->form['description'], to_json_remove($request->language,$plan->description));
                    $plan->features =  to_json_add($request->language,$request->form['features'], to_json_remove($request->language,$plan->features));
                    $plan->save();
                   break;
            }
    
            return response()->json($plan);
         }
    }

    public function validateIfUnique($name){
        $data = DB::table('payment_plan')->where([
           ['name', 'like', "%{$name}%"],
        ])->count();
  
        return ($data > 0) ? true : false;
     }

    public function destroy(Request $request)
    {
        $model = $this->model->findOrFail($request->id);

        $model->delete();

        return response()->json(true);
    }

}
