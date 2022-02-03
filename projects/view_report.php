<?php
require_once('./../config.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT r.*, w.name as work_type, p.status as project_status FROM `report_list` r inner join `work_type_list` w on r.work_type_id = w.id inner join project_list p on r.project_id = p.id where r.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
function duration($dur = 0){
    $hours = floor($dur / (60 * 60));
    $min = floor($dur / (60)) - ($hours*60);
    $dur = sprintf("%'.02d",$hours).":".sprintf("%'.02d",$min);
    return $dur;
}
?>
<style>
    #uni_modal .modal-footer{
        display:none;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <dl>
                
                <dt class="text-muted">Fecha y Hora Inicio</dt>
                <dd class='pl-4 fs-4'><?= isset($datetime_from) ? date("M d, Y h:i A",strtotime($datetime_from)) : 'N/A' ?></dd>
                <dt class="text-muted">Tipo de Trabajo</dt>
                <dd class='pl-4 fs-4 fw-bold'><?= isset($work_type) ? $work_type : 'N/A' ?></dd>
                
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                <dt class="text-muted">Fecha y Hora Fin</dt>
                <dd class='pl-4 fs-4'><?= isset($datetime_to) ? date("M d, Y h:i A",strtotime($datetime_to)) : 'N/A' ?></dd>
                <dt class="text-muted">Duration</dt>
                <dd class='pl-4 fs-4'><?= isset($duration) ? duration($duration) : 'N/A' ?></dd>
            </dl>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label for="" class="text-muted control-label">Descripción</label>
            <div><?= html_entity_decode($description) ?></div>
        </div>
    </div>
    <div class="text-right">
        <?php if(isset($project_status) && $project_status != 2): ?>
        <button class="btn btn-primary btn-sm btn-flat" type="button" id="edit_report"><i class="fa fa-edit"></i> Editar</button>
        <button class="btn btn-danger btn-sm btn-flat" type="button" id="delete_report"><i class="fa fa-trash"></i> Eliminar</button>
        <?php endif; ?>
        <button class="btn btn-dark btn-sm btn-flat" type="button" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
    </div>
</div>
<script>
    $(function(){
        $('#edit_report').click(function(){
            setTimeout(() => {
                uni_modal("Editar Reporte","projects/manage_report.php?id=<?= isset($id) ? $id : '' ?>",'mid-large')
            }, 500);
            $('.modal').modal('hide')
        })
        $('#delete_report').click(function(){
			_conf("¿Estás segur@ de eliminar este informe de forma permanente?","delete_report",["<?= isset($id) ? $id : '' ?>"])
		})
        
    })
    function delete_report($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_report",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("Ocurrió un error.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("Ocurrió un error.",'error');
					end_loader();
				}
			}
		})
	}
</script>
