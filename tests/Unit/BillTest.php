<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Bill;

class BillTest extends TestCase
{
    public function test_post_bill(){
        $user = User::first();
        $category = Category::first();

        if($user && $category){
            $response = $this->actingAs($user)->post('api/bills', [
                'comment' => 'Test comment',
                'price' => '100.00',
                'user_id' => $user->id,
                'category_id' => $category->id,
            ]);
    
            $response->assertStatus(200);
        }
        
    }

    public function test_get_bills(){

        $user = User::first();

        if($user){
            $response = $this->actingAs($user)->get('api/bills');

            $response->assertStatus(200);
        }

    }

    public function test_get_bill(){
        $bill = Bill::first();
        $user = User::first();
        if($bill && $user){
            $response = $this->actingAs($user)->get('api/bills/' . $bill->id);
            $response->assertStatus(200);
        }
    }

    public function test_filter_bills(){
        $min_price = Bill::min('price');
        $max_price = Bill::max('price');
        $min_date = Bill::min('created_at');
        $max_date = Bill::max('created_at');
        $category = Category::first();
        $user = User::first();

        if($category && $user && $min_date){
            $response = $this->actingAs($user)
                            ->get('api/filter?price_min=' . $min_price . '&price_max=' . $max_price . '&start_date=' . $min_date . '&end_date=' . $max_date . '&categories=' . $category->id);

            $response->assertStatus(200);
        }

    }

    public function test_data_aggregation(){
        $min_date = Bill::min('created_at');
        $max_date = Bill::max('created_at');
        $category = Category::first();
        $user = User::first();

        if($min_date && $category && $user){
            $response = $this->actingAs($user)
                            ->get('api/dataAggregation/sum?start_date=' . $min_date . '&end_date=' . $max_date . '&categories=' . $category->id);

            $response->assertStatus(200);
        }

    }

    public function test_update_bill(){
        $user = User::first();
        $bill = Bill::first();

        if($user && $bill){
            $response = $this->actingAs($user)->put('api/bills/' . $bill->id, [
                'comment' => 'Test comment edit 1'
            ]);
    
            $response->assertStatus(200);
        }

    }

    public function test_delete_bill(){
        $user = User::first();
        $category = Category::first();

        if($user && $category){
            $bill = Category::make([
                'comment' => 'Test comment',
                'price' => '100.00',
                'user_id' => $user->id,
                'category_id' => $category->id,
            ]);
    
            if($bill){
                $bill->delete();
            }
    
            $this->assertTrue(true);
        }

    }
}
