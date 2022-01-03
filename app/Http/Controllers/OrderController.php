<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Category;
use App\Models\Cutomer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$order = Order::all();
        $order = Order::with(['products','customer'])->where('status',$request->status)->latest()->paginate(10);
        $customer = Cutomer::all();
        $warehouse = Warehouse::all();
        $area  = Area::all();
        $products = Product::all();
        $category = Category::all();
        $confirmcount = Order::select('id','status')->where('status','Confirm')->count();
        $pendingcount = Order::select('id','status')->where('status','Pending')->count();
        $delivery_count = Order::select('id','status')->where('status','Delivered')->count();
        return view('backend.Order.index', compact('order','customer', 'warehouse','area','products','category','confirmcount','pendingcount','delivery_count'));
    }
    public function confirm($id)
    {
        $order_status = Order::where('id',$id)->update(['status'=>'Confirm']);
        return redirect()->back()->withMsg('Successfully Confirmed');
    }
    public function orderDiscount(Request $request)
    {
        $order_status = Order::where('id',$request->order_id)->update(['total_discount'=>$request->total_discount]);
        return redirect()->back()->withMsg('Successfully Add Discount');
    }
    public function orderPayment(Request $request)
    {
        $order_status = Order::where('id',$request->order_id)->update(['payment_method'=>$request->payment_method,'trx_number'=>$request->trx_number, 'trx_id'=>$request->trx_id,'paid_amount'=>$request->paid_amount]);
        return redirect()->back()->withMsg('Successfully Add Payment Information');
    }
    public function orderDelivery(Request $request)
    {
        $order_status = Order::where('id',$request->order_id)->update(['status'=>'Delivered']);
        return redirect()->back()->withMsg('Successfully Confirmed');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);
        DB::table('product_orders')->insert($data);
        return redirect()->back()->withmsg('Successfully Added');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //dd($order);
        $order = Order::all();
        $customer = Cutomer::all();
        return view('backend.Order.edit',compact('customer','order'));
    }

    public function orderEdit($id)
    {
        // dd($id);
        $order = Order::find($id);
        $customer = Cutomer::all();
        return view('backend.Order.edit',compact('customer','order'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Order::whereId($id)
        ->update([
            'delivery_charge' => $request->delivery_charge,
            'remark' => $request->remark,
        ]);
        return redirect()->back()->withMsg("Successfully Updated");
    }
    public function OrderUpdate(Request $request, $id)
    {
        // dd($request->customer_id);
        Order::whereId($id)
        ->update([
            'customer_id' => $request->customer_id,
            'delivery_charge' => $request->delivery_charge,
            'remark' => $request->remark,
            'payment_method' => $request->payment_method,
        ]);
        return redirect()->route('order-history.index',"status=Pending")->withMsg("Successfully Updated");;
        //return redirect()->back()->withMsg("Successfully Updated");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::whereId($id)->delete();
        return redirect()->back()->withMsg("Successfully Deleted");
    }
    
    public function product_pass(Request $request){
        $id = $request->id;
        $warehouse = Warehouse::with('products')->find($id);
        $process_product = $this->processWarehouseProduct($warehouse);
        // $output ="";
        // foreach($process_product as $value){
        //     // $output.= '<option value="'.$value['id'].'">'.$value['product_name'].'('.$value['stock_quantity'].')</option>';
        //     $output.= '<option>value</option>'; 
        // }
        // $data['output'] = $output;
        return response($process_product);
    }
    public function processWarehouseProduct($warehouse)
    {
        $process_array = [];
        $unique_ids = [];
        $total_quantity = 0;
        foreach ($warehouse->products as $key => $product) {
            if (!in_array($product->pivot->product_id,$unique_ids)){
                array_push($unique_ids,$product->pivot->product_id);
                array_push($process_array,['id'=> $product->id,'product_name' => $product->product_name,'stock_quantity' => $product->pivot->quantity]);
            }
        }
        return $process_array;
    }
}
