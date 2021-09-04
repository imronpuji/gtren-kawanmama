<?php $this->extend('dashboard') ?>

<?php $this->section('content') ?>
<div class="tab-pane fade active show" id="account-detail" role="tabpanel" aria-labelledby="account-detail-tab">
    <div class="row" style="padding:20px">        
        <div class="card col-lg-6 ">
             <?php if(!empty(session()->getFlashdata('success'))){ ?>
                <div class="alert alert-success bg-success text-white">
                    <?php echo session()->getFlashdata('success');?>
                </div>
            <?php } ?>
            <?php if(!empty(session()->getFlashdata('danger'))){ ?>
                <div class="alert alert-danger bg-danger text-white">
                    <?php echo session()->getFlashdata('danger');?>
                </div>
            <?php } ?>
            <div class="card-header">
                <h5>Tambah Alamat Distributor</h5>
            </div>
            <div class="card-body">
                <?php $id = user()->id; ?>
                <?php if(count($address) == 0): ?>
                    <form method="post" action="/distributor">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Provinsi<span class="required">*</span></label>
                                <select required="" class="form-control square" name="provinsi" id="provinsi">
                                    
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kabupaten<span class="required">*</span></label>
                                 <select required="" class="form-control square" name="kabupaten" id="kabupaten">
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kecamatan<span class="required">*</span></label>
                                 <select required="" class="form-control square" name="kecamatan" id="kecamatan">
                                    
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kode Pos<span class="required">*</span></label>
                                <input id="kode_pos" required="" class="form-control square" name="kode_pos" type="text">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Detail Alamat<span class="required">*</span></label>
                                <input required="" class="form-control square" name="detail_alamat" type="text">
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-fill-out submit" name="submit" value="Submit">Save</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <form method="post" action="/distributor/<?= $address[0]->id ?>">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Provinsi<span class="required">*</span></label>
                                <select required="" class="form-control square" name="provinsi" id="provinsi">
                                    <option selected value="<?php $address[0]->provinsi ?>" disabled=""><?= $address[0]->provinsi ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kabupaten<span class="required">*</span></label>
                                <select required="" class="form-control square" name="kabupaten" id="kabupaten">
                                    <option selected value="<?= $address[0]->kabupaten  ?>" disabled=""><?= $address[0]->kabupaten ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kecamatan<span class="required">*</span></label>
                                <select required="" class="form-control square" name="kecamatan" id="kecamatan">
                                    <option selected value="<?= $address[0]->kecamatan  ?>" disabled=""><?= $address[0]->kecamatan ?></option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Kode Pos<span class="required">*</span></label>
                                <input value="<?= $address[0]->kode_pos ?>" required="" class="form-control square" name="kode_pos" type="text">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Detail Alamat<span class="required">*</span></label>
                                <input value="<?= $address[0]->detail_alamat ?>" required="" class="form-control square" name="detail_alamat" type="text">
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-fill-out submit" name="submit" value="Submit">Ubah</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>    
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">

</script>
<script type="text/javascript">
    $.get( "https://pro.rajaongkir.com/api/province?key=bfacde03a85f108ca1e684ec9c74c3a9",
        function( data ) {
        $.each(data.rajaongkir.results, function (i, item) {
            console.log(item)
            $('#provinsi').append($('<option>', { 
                value: [item.province_id, item.province],
                text : item.province
            }));
        });
    });


    $('#provinsi').change(function(data) {
        const val = data.target.value;
        const arr = val.split(',');
        $.get( `https://pro.rajaongkir.com/api/city?province=${arr[0]}&key=bfacde03a85f108ca1e684ec9c74c3a9`, function( data ) {
            $('#kabupaten')
            .find('option')
            .remove()
            .end()

            $('#kecamatan')
            .find('option')
            .remove()
            .end()

            $.each(data.rajaongkir.results, function (i, item) {
                console.log(item)
                $('#kabupaten')
                .append($('<option>', { 
                    value: [item.city_id, item.city_name, item.postal_code],
                    text : item.city_name,
                }));
            });
        });
    });

    $('#kabupaten').change(function(data) {
        const val = data.target.value;
        const arr = val.split(',');

        $.get( `https://pro.rajaongkir.com/api/subdistrict?city=${arr[0]}&key=bfacde03a85f108ca1e684ec9c74c3a9`, function( data ) {
            
            $('#kecamatan')
            .find('option')
            .remove()
            .end()
           
           $('#kode_pos').val(arr[2]);

            $.each(data.rajaongkir.results, function (i, item) {
                console.log(item)
                $('#kecamatan').append($('<option>', { 
                    value: [item.subdistrict_id, item.subdistrict_name],
                    text : item.subdistrict_name 
                }));
            });
        });
    });

</script>
<?php $this->endSection(); ?>
