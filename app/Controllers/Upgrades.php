<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UpgradesModel;
use App\Models\UniqueCodeModel;
use App\Models\GenerateModel;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Authorization\GroupModel;

class upgrades extends BaseController
{

	public function __construct()
	{
		$this->model = new UpgradesModel();
		$this->user = new UserModel();
		$this->uniq = new UniqueCodeModel();
		$this->generate = new GenerateModel();
		$this->group = new GroupModel();
		$this->db      = \Config\Database::connect();
		$this->builder = $this->db->table('auth_groups_users');
	}

	public function index()
	{
		$this->model->select('status_request, type, code, photo, total, bill');
		$this->model->select('users.username, users.id as id_user, upgrades.user_id as id');
		$this->model->join('users', 'users.id = upgrades.user_id', 'left');
		$data['upgrades'] = $this->model->paginate(4, 'upgrades');
		$data['pager'] = $this->model->pager;
		return view('db_admin/upgrades/upgrades', $data);
	}

	public function save($id)
	{
		$request = $this->request;

		if($this->model->where('user_id', $id)->find()){
			session()->setFlashdata('success', 'Menunggu persetujuan Admin');
			return redirect()->back();
		}
		if($request->getPost('type') == 'affiliate')
		{

			$file = $request->getFile('file');

			$new_name = $file->getRandomName();

			$file->move(ROOTPATH . 'public/uploads/bukti', $new_name);
			
			$data = [
				'user_id' => user()->id,
				'code' => $request->getPost('code'),
				'status_request' => 'pending',
				'type' => $request->getPost('type'),
				'total' => $request->getPost('total'),
				'bill' => $request->getPost('bill'),
				'photo' => $new_name
			];

			$generate = $this->generate->find()[0]['nomor'];

			$this->generate->save(['id' => 1, 'nomor' => $generate + 1]);

		} else {

			$code = $request->getPost('code');
			$unique_id = $this->uniq->where('code', $code)->find();

			if(!$unique_id)
			{	
				session()->setFlashdata('danger', 'Code Salah');
				return redirect()->back();
			} 

			if($unique_id[0]->used > 0)
			{	
				session()->setFlashdata('danger', 'Code Sudah Digunakan');
				return redirect()->back();
			} 


			$this->group->addUserToGroup(user()->id, 3);
			
			$this->uniq->save(["id" => $unique_id[0]->id, "used" => user()->id]);

			session()->setFlashdata('successs', 'Berhasil, Anda Sekarang Adalah Stockist');
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
			'status_request' => 'active'
		];

		$this->group->addUserToGroup($id, 4);

		$upgrades = $this->db->table('upgrades');
		$upgrades->where('user_id', $id);
		$upgrades->update($data);
		
		$data['upgrades'] = $this->model->findAll();
	
		session()->setFlashdata('success', 'Data Berhasil Diupdate');
		return redirect()->to(base_url('/upgrades'));

	}


	public function search()
	{
		$keyword            = $this->request->getPost('keyword');
		$this->model->select('status_request, type, code, photo');
		$this->model->select('users.username, users.id as id_user, upgrades.user_id as id');
		$this->model->join('users', 'users.id = upgrades.user_id', 'left');
		$data['upgrades'] = $this->model->like(['username' => $keyword])->paginate(2, 'upgrades');
		$data['pager'] = $this->model->pager;

		return view('db_admin/upgrades/upgrades', $data);;
	}
}
