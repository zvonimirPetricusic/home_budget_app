<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Helpers\APIHelpers;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bills = Bill::select(['bills.id', 'bills.comment', 'bills.price', 'bills.created_at', 'categories.name', 'bills.category_id'])
                    ->where('user_id', Auth::id())
                    ->join('categories', 'categories.id','=','bills.category_id')
                    ->get();
        $count = 0;
        $data = [];

        foreach($bills as $bill){
            $data[$count]['id'] = $bill['id'];
            $data[$count]['comment'] = $bill['comment'];
            $data[$count]['price'] = $bill['price'];
            $data[$count]['created_at'] = $bill['created_at'];
            $data[$count]['category']['id'] = $bill['category_id'];
            $data[$count]['category']['name'] = $bill['name'];

            $count++;
        }
        
        $response = APIHelpers::createAPIResponse(false, 200, '', $data);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params = $request->validate([
            'comment' => 'nullable',
            'price' => 'required',
            'category_id' => 'required'
        ]);

        $user = User::where('id', Auth::id())->first();

        if($user->balance < $params['price']){
            $response = APIHelpers::createAPIResponse(true, 400, 'Not enough money on your balance!', null);
            return response()->json($response, 400);
        }else{
            $bill = Bill::create([
                'comment' => $params['comment'],
                'price' => $params['price'],
                'user_id' => Auth::id(),
                'category_id' => $params['category_id']
            ]);

            if($bill){
                $updateBalance = User::where('id', Auth::id())->update(['balance' => bcsub($user->balance, $params['price'], 2)]);

                if($updateBalance){
                    $response = APIHelpers::createAPIResponse(false, 200, 'Bill has been added! User balance has been updated!', null);
                    return response()->json($response, 200);
                } else{
                    $response = APIHelpers::createAPIResponse(true, 200, 'Bill has been added! Could not update user balance!', null);
                    return response()->json($response, 200);
                }

            }else{
                $response = APIHelpers::createAPIResponse(true, 400, 'Could not add Bill', null);
                return response()->json($response, 400);
            }
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bill = Bill::select(['bills.id', 'bills.comment', 'bills.price', 'bills.created_at', 'categories.name', 'bills.category_id'])
                    ->where([['user_id', Auth::id()],['bills.id', $id]])
                    ->join('categories', 'categories.id','=','bills.category_id')
                    ->first();

        $data = [];

        if($bill){
            $data['id'] = $bill['id'];
            $data['comment'] = $bill['comment'];
            $data['price'] = $bill['price'];
            $data['created_at'] = $bill['created_at'];
            $data['category']['id'] = $bill['category_id'];
            $data['category']['name'] = $bill['name'];
        }


        $response = APIHelpers::createAPIResponse(false, 200, '', $data);
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
        $bill = Bill::where('id', $id)->first();
        $user = User::where('id', Auth::id())->first();

        // Price to be reduced/added to user balance
        if(isset($request['price'])){
            $price = $bill->price - $request['price'];
        }
        
        $updateBill = $bill->update($request->all());

        if($updateBill){
            if(isset($request['price'])){
                $updateBalance = User::where('id', Auth::id())->update(['balance' => $user->balance + $price]);

                if($updateBalance){
                    $response = APIHelpers::createAPIResponse(false, 200, 'Bill has been updated! User balance has been updated!', null);
                    return response()->json($response, 200);
                } else{
                    $response = APIHelpers::createAPIResponse(true, 200, 'Bill has been updated! Could not update user balance!', null);
                    return response()->json($response, 200);
                }

            }else{
                $response = APIHelpers::createAPIResponse(true, 200, 'Bill has been updated!', null);
                return response()->json($response, 200);
            }
        }else{
            $response = APIHelpers::createAPIResponse(true, 400, 'Could not update Bill', null);
            return response()->json($response, 400);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bill = Bill::destroy($id);

        if($bill){
            $response = APIHelpers::createAPIResponse(false, 200, 'Bill deleted!', null);
            return response()->json($response, 200);
        } else{
            $response = APIHelpers::createAPIResponse(true, 400, 'Bill could not be deleted!', null);
            return response()->json($response, 400);
        }
    }

    public function filter(){
        $params = $_GET;
        $prepQuery = Bill::select(['bills.id', 'bills.comment', 'bills.price', 'bills.created_at', 'categories.name', 'bills.category_id']);

        $where = [];
        $count = 0;

        foreach($params as $key => $value){
            switch ($key) {
                case 'price_min' :
                    $where[$count] = ['price', '>=', $value];
                    break ;
                case 'price_max' :
                    $where[$count] = ['price', '<=', $value];
                    break ;
                case 'start_date' :
                    $where[$count] = ['bills.created_at', '>=', date('Y-m-d H:i:s' , strtotime($value))];
                    break ;
                case 'end_date' :
                    $where[$count] = ['bills.created_at', '<=', date('Y-m-d H:i:s', strtotime($value))];
                    break ;
                case 'categories':
                    $prepQuery->whereIn('category_id', explode(',', $value));
                    break;
             }

             $count++;
        }

        $result = $prepQuery->where($where)->join('categories', 'categories.id','=','bills.category_id')->get();
        $count = 0;
        $data = [];
        foreach($result as $result){
            $data[$count]['id'] = $result['id'];
            $data[$count]['comment'] = $result['comment'];
            $data[$count]['price'] = $result['price'];
            $data[$count]['created_at'] = $result['created_at'];
            $data[$count]['category']['id'] = $result['category_id'];
            $data[$count]['category']['name'] = $result['name'];

            $count++;
        }

        $response = APIHelpers::createAPIResponse(false, 200, '', $data);
        return response()->json($response, 200);
    }

    /**
     * Aggregate data by operation
     * 
     * @param str $operation
     * @return \Illuminate\Http\Response
     */

    public function dataAggregation($operation){
        $data = APIHelpers::collectData($_GET, $operation);
        $response = APIHelpers::createAPIResponse(false, 200, '', $data);
        return response()->json($response, 200);
    }

}
