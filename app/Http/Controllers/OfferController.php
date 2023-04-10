<?php

namespace App\Http\Controllers;
use Hash;
use Session;
use App\Model\Seller;
use App\Model\User;
use App\Model\Buyer;
use App\Model\SystemStatus;
use App\Model\Address;
use App\Model\Offer;
use App\Model\Group;
use App\Model\Role;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;

class OfferController extends Controller
{    
    //web api
    public function get_offer_listsweb(Request $request, $id = null){
      $data = $request->all();
      $system_status = SystemStatus::where('key', 'LIKE', "%".'PROCESS'."%")->get();
      $offers = Offer::with(["address","seller","buyer","systemstatus"])->where("status",'!=',9);

        if(!is_null($id)){
            $offers = Offer::with(["address", "seller","buyer","systemstatus", "order"])->where("offer_id",$id)->where("status",'!=',9)->orderBy("created_at","DESC")->first();
          
            return response()->json([
                'status'=>200,
                'offers'=> $offers,
            ]);
        }
        if(isset($data['sfullname']) && $data['sfullname'] != null && $data['sfullname'] != ""){
            $sfullname = $data['sfullname'];
         
            $offers->whereHas('seller', function($q) use($sfullname){
            $q->where('fullname', 'LIKE', "%".$sfullname."%");
            // dd(DB::getQueryLog());
            });
         
         
        }
        if(isset($data['bfullname']) && $data['bfullname'] != null && $data['bfullname'] != ""){
            $bfullname = $data['bfullname'];
         
            $offers->whereHas('buyer', function($q) use($bfullname){
            $q->where('fullname', 'LIKE', "%".$bfullname."%");
            // dd(DB::getQueryLog());
            });
       
         
        }
        if(isset($data['listingsource']) && $data['listingsource'] != null && $data['listingsource'] != ""){
            $listingsource = $data['listingsource'];
            //  dd($listingsource);
            $offers->where('listing_source', 'LIKE', "%".$listingsource."%");
            // dd(DB::getQueryLog());
           
       
         
        }
        if(isset($data['status']) && $data['status'] != null && $data['status'] != ""){
            $status= $data['status'];
            // dd($status);
            $offers->whereHas('systemstatus', function($q) use($status){
                $q->where('value',  $status);
              });
           
        }
        if(isset($data['date']) && $data['date'] != null && $data['date'] != ""){
            $date= $data['date'];
            // dd($date);
            $offers->whereDate('created_at','=',$date);
        } 
        // if(isset($data['is_verify']) && $data['is_verify'] != null && $data['is_verify'] != ""){
        //     $offers->where('is_verified', $data['is_verify']);
        //   } 
        $offers =   $offers->orderBy("created_at","DESC")->get();
        
     
       
    //   dd($offers[0]->buyer->fullname);
        return view('admin.offer', compact('offers', 'system_status', 'data'));

    }
    public function chang_Offerlisting_statusweb(Request $request){
        $id = $request->input('id');
    
        $offer = Offer::find($id);
   
        $offer->update([
            'is_verified' =>  $offer->is_verified == 0 ? 1 : 0,
            'updated_source'=> 'user',
            'updated_at'=>  date("Y-m-d H:i:s"),
            'updated_by'=> Auth::user()->user_id,

        ]);
       
        return response()->json([
            'status'=>200,
             'offer'=>$offer,
        ]);
    }
}
