<?php 
$user = $conn->query("SELECT * FROM employee_list where id ='".$_settings->userdata('id')."'");
foreach($user->fetch_array() as $k =>$v){
	$$k = $v;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo $_settings->userdata('id') ?>">
				<input type="hidden" name="code" value="<?php echo $_settings->userdata('code') ?>">
				<input type="hidden" name="generated_password" value="">
			<div class="row">
				<div class="form-group col-md-4">
					<input type="text" name="firstname" id="firstname" placeholder="Juan" autofocus required class="form-control form-control-sm form-control-border" value="<?= isset($firstname) ? $firstname :"" ?>">
					<small class="mx-2">Nombre</small>
				</div>
				<div class="form-group col-md-4">
					<input type="text" name="middlename" id="middlename" placeholder="(opcional)" class="form-control form-control-sm form-control-border" value="<?= isset($middlename) ? $middlename :"" ?>">
					<small class="mx-2">Segundo Nombre</small>
				</div>
				<div class="form-group col-md-4">
					<input type="text" name="lastname" id="lastname" placeholder="Usuario" required class="form-control form-control-sm form-control-border" value="<?= isset($lastname) ? $lastname :"" ?>">
					<small class="mx-2">Apellido</small>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-4">
					<select name="gender" id="gender" class="form-control form-control-sm form-control-border" required>
						<option <?= isset($gender) && $gender =='Male' ? 'selected' : "" ?>>Masculino</option>
						<option <?= isset($gender) && $gender =='Female' ? 'selected' : "" ?>>Femenino</option>
					</select>
					<small class="mx-2">Género</small>
				</div>
				<div class="form-group col-md-4">
					<input type="text" name="department" id="department" placeholder="Departamento" required class="form-control form-control-sm form-control-border"  value="<?= isset($department) ? $department :"" ?>">
					<small class="mx-2">Departamento</small>
				</div>
				<div class="form-group col-md-4">
					<input type="text" name="position" id="position" placeholder="Tu cargo" required class="form-control form-control-sm form-control-border" value="<?= isset($position) ? $position :"" ?>">
					<small class="mx-2">Cargo</small>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-10">
					<input type="email" name="email" id="email" placeholder="tucorreo@mail.com" required class="form-control form-control-sm form-control-border" value="<?= isset($email) ? $email :"" ?>">
					<small class="mx-2">Email</small>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-10">
					<div class="input-group input-group-sm">
						<input type="password" name="password" id="password" class="form-control form-control-sm form-control-border" value="">
						<div class="input-group-append ">
						<span class="input-group-text bg-transparent border-top-0 border-left-0 border-right-0 rounded-0"><a href="javascript:void(0)" class="pass_view text-decoration-none text-muted"><i class="fas fa-eye-slash"></i></a></span>
						</div>
					</div>
					<small class="mx-2">Contraseña</small>
				</div>
			</div>
              <div class="row">
				<div class="form-group col-md-10">
						<div class="input-group input-group-sm">
							<input type="password" id="cpass" class="form-control form-control-sm form-control-border" value="">
							<div class="input-group-append ">
							<span class="input-group-text bg-transparent border-top-0 border-left-0 border-right-0 rounded-0"><a href="javascript:void(0)" class="pass_view text-decoration-none text-muted"><i class="fas fa-eye-slash"></i></a></span>
							</div>
						</div>
						<small class="mx-2">Confirma Contraseña</small>
					</div>
              </div>
			  <small class="text-muted">Deja los campos de Contraseña en blanco si no deseas actualizar tu contraseña.</small>
				<div class="form-group">
					<label for="" class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Escoger archivo</label>
		            </div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image(isset($avatar) ? $avatar :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary" form="manage-user">Actualizar</button>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			$('#cimg').attr('src', "<?php echo validate_image(isset($avatar) ? $avatar :'') ?>");
		}
	}
	$(function(){
		$('.pass_view').click(function(){
			var inp = $(this).closest('.form-group').find('#password,#cpass')
            var type = inp.attr('type')
            if(type == 'password'){
                inp.attr('type','text')
                $(this).html('<i class="fa fa-eye"></i>')
            }else{
                inp.attr('type','password')
                $(this).html('<i class="fa fa-eye-slash"></i>')
            }
        })
		$('#manage-user').submit(function(e){
			e.preventDefault();
			var _this = $(this)
			$('.pop-msg').remove()
			var el = $('<div>')
				el.addClass("pop-msg alert")
				el.hide()
			if($('#password').val() != $('#cpass').val()){
				el.addClass('alert-danger')
				el.text("Password does not match")
				$('#password').focus()
				$('#password, #cpass').addClass('is-invalid');
				$('#manage-user').append(el)
				el.show('slow')
				return false;
			}
			start_loader()
			$.ajax({
				url:_base_url_+'classes/Users.php?f=save_employee',
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("Ocurrió un error",'error');
					end_loader();
				},
				success:function(resp){
					if(resp.status == 'success'){
						location.reload();
					}else if(!!resp.msg){
						el.addClass("alert-danger")
						el.text(resp.msg)
						_this.prepend(el)
					}else{
						el.addClass("alert-danger")
						el.text("Se produjo un error debido a un motivo desconocido.")
						_this.prepend(el)
					}
					el.show('slow')
					$('html,body,.modal').animate({scrollTop:0},'fast')
					end_loader();
				}
			})
		})
	})


</script>