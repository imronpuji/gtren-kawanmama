<?php

namespace App\Controllers;
use App\Models\CartItemModel;

use App\Controllers\BaseController;

class Cart extends BaseController
{
	public function __construct(){
		$this->cart = new CartItemModel();
	}

	public function save()
	{
		$product_id = $this->request->getPost('product_id');
		$distributor_id = $this->request->getPost('distributor_id');
		$amount = $this->request->getPost('amount');
		$price = $this->request->getPost('price_sell');
		$total = $amount * $price;
		

		$data = [
			"product_id" => $product_id,
			"distributor_id" => $distributor_id,
			"user_id" => user()->id,
			"amount" => $amount,
			"total" => $total
		];
		$transaksi = $this->cart->select('user_id, product_id, distributor_id, total, amount, id')
		->where('user_id', user()->id)
		->where('product_id', $product_id)
		->where('distributor_id', $distributor_id)->find();


		// $this->cart->select('transaksi.cart_id, transaksi.status');
		// $transaksi = $this->cart->join('transaksi.cart_id', 'id = transaksi.cart_id', 'left')->where('status', 'pending');

		if(count($transaksi) == 0){
			$this->cart->save($data);
			return redirect()->to('/cart');
		} else {
			$data = [
				"id" => $transaksi[0]->id,
				"product_id" => $product_id,
				"distributor_id" => $distributor_id,
				"user_id" => user()->id,
				"amount" => $amount + $transaksi[0]->amount,
				"total" => $total + ($total / $transaksi->amount)
			];
			
			$this->cart->where('user_id', user()->id)
			->where('product_id', $product_id)
			->where('distributor_id', $distributor_id)->replace($data);
		}
		
	}

	public function delete($id)
	{	

		$this->cart->delete($id);
		return redirect()->back();
	}

	public function add($id)
	{
		$transaksi = $this->cart->first($id);

		$data = [
			"id" => $transaksi->id,
			"user_id" => user()->id,
			"amount" => $transaksi->amount  + 1,
			"total" => $transaksi->total + ($transaksi->total / $transaksi->amount)
		];

		$this->cart->save($data);
		return redirect()->back();
	}

	public function substruct($id)
	{
		$transaksi = $this->cart->first($id);
		if($transaksi->amount == 1){
			return redirect()->back();	
		}
		$data = [
			"id" => $transaksi->id,
			"user_id" => user()->id,
			"amount" => $transaksi->amount  - 1,
			"total" => $transaksi->total - ($transaksi->total / $transaksi->amount)
		];

		$this->cart->save($data);
		return redirect()->back();
	}
}
