<?php

namespace App\Http\Controllers;

use App\Models\ProductionRequisition;
use App\Models\ProductionRequisitionItem;
use App\Models\SupplyItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use function GuzzleHttp\Promise\all;

class ProductionUnloadController extends Controller
{
    public function index()
    {
        return view('backend.production.unload.index');
    }
    public function unloadShow(Request $request)
    {
        $items = SupplyItem::with('grade')->get();
        // dd($items->toArray());
        $production_requistion = ProductionRequisition::with([
                                    'production_supplier'=>function($q){
                                        $q->with(['supplier_items']);
                                    },
                                    'production_requisition_items'=>function($q){
                                        $q->with(['grade']);
                                    }
                                ])
                                ->where('invoice_code',$request->invoice_no)
                                ->Where('status',"Unload")->first();
        if ($production_requistion) {
            return view('backend.production.unload.unload_list',compact('production_requistion','items'));
        } else {
            return redirect()->back()->withMsg('Input Valid Code');
        }
    }
    public function store(Request $request)
    {
        // dd($request->toArray());
        ProductionRequisition::where('id',$request->requisition_id)->update(['receive_date'=>Carbon::now(),'status'=>'Received']);
        foreach ($request->id as $key => $id) {
            // dd($id);
            if ($id == "no_id") {
                // dd($id);
                $production_requisition_item = ProductionRequisitionItem::create([
                    'production_requisition_id' => $request->requisition_id,
                    'supply_item_id' => $request->supply_item_id[$key],
                    'quantity' => $request->total_quantity[$key],
                    'alive_quantity'=>$request->alive_quantity[$key],
                    'dead_quantity'=>$request->dead_quantity[$key],
                    'return_quantity'=>$request->return_quantity[$key],
                    'received_quantity'=>$request->total_quantity[$key],
                    'received_remark'=>$request->received_remark[$key],
                    // 'amount' => floatval($product->total_price),
                ]);
            }
            $item = ProductionRequisitionItem::where('id',$id)->update(['alive_quantity'=>$request->alive_quantity[$key],'dead_quantity'=>$request->dead_quantity[$key],'return_quantity'=>$request->return_quantity[$key],'received_quantity'=>$request->total_quantity[$key],'received_remark'=>$request->received_remark[$key]]);
        }
        return redirect()->route('production-unload-index')->withMsg('Successfully Send to Chill Room');
    }

    public function gateman_general_item(){
        $production_requistion = ProductionRequisition::with([
            'production_supplier'=>function($q){
                $q->with(['supplier_items']);
            },
            'production_requisition_items'=>function($q){
                $q->with(['grade']);
            }
        ])
        ->Where('status',"InGateman")->latest();
        return view('backend.production.unload.gate_man.general_item.index',compact('production_requistion'));
    }
    public function gateman_raw_item(){
        $production_requistion = ProductionRequisition::with([
            'production_supplier'=>function($q){
                $q->with(['supplier_items']);
            },
            'production_requisition_items'=>function($q){
                $q->with(['grade']);
            }
        ])
        ->Where('status',"InGateman")->get();
        return view('backend.production.unload.gate_man.raw_item.index',compact('production_requistion'));
    }
    public function check_raw_item(Request $request){
        // dd($request);
        ProductionRequisition::where('id',$request->requisition_id)
        ->update(
            ['status'=>'Unload','vehicle_number'=>$request->vehicle_number,'challan_number'=>$request->challan_number,'gateman_remark'=>$request->remark]
        );
        return redirect()->back()->withmsg('Successfully Send For Unloading');
    }
}
