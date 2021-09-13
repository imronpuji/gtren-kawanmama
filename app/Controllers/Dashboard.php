<?php

namespace App\Controllers;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;
use App\Models\BillModel;
use App\Models\UpgradesModel;
class Dashboard extends BaseController
{
	public function __construct()
	{
		$this->model = new DetailTransaksiModel();
		$this->bill = new BillModel();
		$this->transaksi = new TransaksiModel();
		$this->upgrades = new UpgradesModel();
	}
	public function index()
	{
		$data['segments'] = $this->request->uri->getSegments();

		$data['user'] = $this->model->select('sum(COALESCE(admin_commission,0)) + sum(COALESCE(stockist_commission,0)) + sum(COALESCE(affiliate_commission,0)) as user_total')->where('status_barang', null)->join('transaksi', 'transaksi.id = detailtransaksi.transaksi_id', 'inner')
		->where('transaksi.status_pembayaran', 'paid')
         ->where('status_barang =', null)->orWhere('status_barang =', 'refund')->orWhere('status_barang =', 'dikirim')
          ->findAll();
          
		$data['upgrades'] = $this->upgrades->select('sum(total) as total_upgrades')->where('status_request', 'active')->findAll();
		
		$data['admin'] = $this->model->select('sum(COALESCE(admin_commission,0)) AS admin_total')
		->where('status_barang =', 'diterima_pembeli')->find();

		$data['kode_unik'] = $this->transaksi->select('sum(COALESCE(kode_unik,0)) AS kode_unik_admin')->where('status_pembayaran', 'paid')->find();
		$kode_unik  = $data['kode_unik'][0]->kode_unik_admin;

		$data['admin'] = [["admin_total" => $data['admin'][0]['admin_total'] + $data['upgrades'][0]->total_upgrades + $kode_unik]];

		$data['stockist'] = $this->model->select('sum(COALESCE(stockist_commission,0) - COALESCE(pendapatan.keluar,0)) AS stockist_total')
		->join('distributor', 'distributor.id = detailtransaksi.distributor_id ', 'left')
		->join('pendapatan', 'pendapatan.user_id = distributor.user_id ', 'left')
		->where('status_barang =', 'diterima_pembeli')
		->where('pendapatan.status_dana =', 'distributor')
		->find();

		$data['affiliate'] = $this->model->select('sum(COALESCE(affiliate_commission,0) - COALESCE(pendapatan.keluar,0)) AS affiliate_total')
		->join('cart_item', 'cart_item.id = detailtransaksi.cart_id ', 'left')
		->join('users', 'users.affiliate_link = cart_item.affiliate_link', 'left')
		->join('pendapatan', 'pendapatan.user_id = users.id', 'left')
		->where('status_barang =', 'diterima_pembeli')
		->where('pendapatan.status_dana =', 'affiliate')
		->find();

		$data['bills'] = $this->bill->findAll();
		$data['bills']   = $this->bill->paginate(4, 'bills');
		$data['pager']    = $this->bill->pager;
		return view('dashboard', $data);
	}

}
