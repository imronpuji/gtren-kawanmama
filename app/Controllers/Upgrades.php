<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UpgradesModel;
use App\Models\UniqueCodeModel;
use Myth\Auth\Models\UserModel;

class upgrades extends BaseController
{

	public function __construct()
	{
		$this->model = new UpgradesModel();
		$this->user = new UserModel();
		$this->uniq = new UniqueCodeModel();
		$db      = \Config\Database::connect();
		$this->builder = $db->table('auth_groups_users');
	}

	public function index()
	{
		$this->model->select('status_request, type, code, photo');
		$this->model->select('users.username, users.id as id_user, upgrades.user_id as id');
		$this->model->join('users', 'users.id = upgrades.user_id', 'left');
		$data['upgrades'] = $this->model->paginate(4, 'upgrades');
		$data['pager'] = $this->model->pager;
		return view('db_admin/upgrades/upgrades', $data);
	}

	public function save($id)
	{
		$request = $this->request;


		if($request->getPost('type') == 'affiliate')
		{
			if($this->model->where('user_id', user()->id)){

				return redirect()->back();
			}

			$file = $request->getFile('file');

			$new_name = $file->getRandomName();

			$file->move(ROOTPATH . 'public/uploads/bukti', $new_name);
			
			$data = [
				'user_id' => user()->id,
				'code' => $request->getPost('code'),
				'status_request' => 'pending',
				'type' => $request->getPost('type'),
				'photo' => $new_name
			];

		} else {

			$code = $request->getPost('code');

			if(!$this->uniq->where('code', $code)->find()){	
			session()->setFlashdata('danger', 'Code Salah');
			return redirect()->back();
		
			} 

			$this->builder->insert(["user_id" => user()->id, "group_id" => 3]);

			session()->setFlashdata('success', 'Berhasil, Anda Sekarang Adalah Stockist');
			return redirect()->back();


		}


		if(!$this->model->save($data)){
			session()->setFlashdata('danger', 'Terjadi Kesalahan');
	        return redirect()->back();
		} 

		session()->setFlashdata('success', 'Data Berhasil Disimpan Tunggu Konfirmasi Dari Admin');
		return redirect()->back();
	}

	public function delete($id)
	{
		$delete = $this->model->delete($id);
		if(!$delete){
			$delete['upgrades'] = $this->model->findAll();
			$delete['errors']     = $this->model->errors();
	        return view('db_admin/upgrades/upgrades', $data); 
		} 

		session()->setFlashdata('success', 'Data Berhasil Dihapus');

		return redirect()->to(base_url('/upgrades'));

	}

	public function edit($id)
	{
		$data['upgrades'] = $this->model->find($id);
		
		return view('db_admin/upgrades/edit_upgrades', $data);

	}

	public function update($id)
	{
		
		$request = $this->request;

		$data = [
			'id' => $id,
			'status_request' => 'active'
		];



		$type = $this->model->find($id)->type;

		$this->builder->insert(["user_id" => $id, "group_id" => 4]);

		$this->model->save($data);

		if(!$this->model->save($data)){
			$data['upgrades'] = $this->model->findAll();
	        return view('db_admin/upgrades/upgrades', $data); 
		} 

		session()->setFlashdata('success', 'Data Berhasil Diupdate');
		return redirect()->to(base_url('/upgrades'));

	}


	public function search()
	{
		$keyword            = $this->request->getPost('keyword');
		$this->model->select('status_request, type, code, photo');
		$this->model->select('users.username, users.id as id_user, upgrades.id as id');
		$this->model->join('users', 'users.id = upgrades.user_id', 'left');
		$data['upgrades'] = $this->model->like(['username' => $keyword])->paginate(2, 'upgrades');
		$data['pager'] = $this->model->pager;

		return view('db_admin/upgrades/upgrades', $data);;
	}
}
