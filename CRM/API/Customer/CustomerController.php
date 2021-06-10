<?php

namespace CRM\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Category;
use App\Models\CategoryCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
   private $customerRepository;

   public function __construct(CustomerRepository $customerRepository)
   {
      $this->customerRepository = $customerRepository;
   }

   public function index(Request $request)
   {
      $customers = $this->customerRepository->getAll($request);

      return response()->json($customers);
   }

   public function show(Request $request, $id)
   {
      $customer = $this->customerRepository->getById($id);
      
      return response()->json($customer);
   }

   // for Category here
   public function store(Request $request)
   {
      $customer = null;

      $request->validate([
         'profile_id' => 'required|unique:customers'
      ]);

      $customer->responseStatus = true;

      return response()->json($customer);
      
   }

   public function destroy(Request $request)
   {
      $customer = $this->customerRepository->getById($request->id);
      
      $customer->delete();


      return response()->json(true);
   }

}
