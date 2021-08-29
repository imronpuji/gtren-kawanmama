<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'address';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'App\Entities\Address';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['user_id', 'provinsi', 'kabupaten', 'kecamatan', 'kode_pos', 'detail_alamat', 'type'];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	protected $validationRules      = [];
	protected $validationMessages   = [];

	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	
}
