<?php
require_once('./../../config.php');
$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : "";
if(isset($_GET['id'])){
    // $qry = $conn->query("SELECT * FROM `report_list` where id = '{$_GET['id']}'");
    $qry = $conn->query("SELECT r.*, w.name as work_type, e.code as ecode, CONCAT(e.firstname,' ',e.middlename,' ', e.lastname) as fullname FROM `report_list` r inner join `work_type_list` w on r.work_type_id = w.id inner join employee_list e on r.employee_id = e.id where r.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:block;
    }
</style>
<div class="container-fluid">
    <form action="" id="report-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="project_id" value="<?php echo isset($project_id) ? $project_id : '' ?>">
        <div class="row">
            <div class="col-md-12">
                <dl>
                    <dt class="text-muted">Emplead@</dt>
                    <dd class='pl-4 fs-4'><?= isset($ecode) ? $ecode. " - " . $fullname : 'N/A' ?></dd>
                </dl>
            </div>
        </div>
        <div class="form-group">
            <label for="work_type_id" class="control-label">Tipo de Trabajo</label>
            <select name="work_type_id" id="work_type_id" class="form-control form-control-border select2"  required>
                <option value="" disabled <?= !isset($work_type) ? 'selected' : '' ?>></option>
                <?php 
                $work_types = $conn->query("SELECT * FROM `work_type_list` where `status` = 1 and `delete_flag` = '0' ".(isset($work_type_id) ? " or `id` = '{$work_type_id}' " : "")." order by `name` asc");
                while($row = $work_types->fetch_assoc()):
                ?>
                    <option value="<?= $row['id'] ?>" <?= isset($work_type_id) && $work_type_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?> <?= $row['delete_flag'] == 1 ? "<small><em>(Eliminado)</em></small>" : "" ?> <?= $row['status'] == 0 ? "<small><em>(Inactivo)</em></small>" : "" ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="datetime" class="control-label">Fecha Desde</label>
            <input type="datetime-local" name="datetime_from" id="datetime_from" class="form-control form-control-sm rounded-0" value="<?php echo isset($datetime_from) ? date("Y-m-d\TH:i",strtotime($datetime_from)) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="datetime" class="control-label">Fecha Hasta</label>
            <input type="datetime-local" name="datetime_to" id="datetime_to" class="form-control form-control-sm rounded-0" value="<?php echo isset($datetime_to) ? date("Y-m-d\TH:i",strtotime($datetime_to)) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Descripción</label>
            <textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? html_entity_decode($description) : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal').on('shown.bs.modal',function(){
            $('.select2').select2({
                placeholder:"Selecciona aquí",
                width:'100%',
                dropdownParent:$('#uni_modal')
            })
            $('#description').summernote({
                placeholder:"Escribe tu reporte aquí",
                height:'20vh',
                toolbar: [
                    [ 'style', [ 'style' ] ],
                    [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                    [ 'color', [ 'color' ] ],
                    [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                    [ 'table', [ 'table' ] ],
                    ['insert', ['link', 'picture']],
                    [ 'view', [ 'undo', 'redo'] ]
                ]
            })
        })
        
        $('#uni_modal #report-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_report",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("Ocurrió un error.",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        location.href = _base_url_+"?page=projects/view_project&id=<?= isset($project_id) ? $project_id : '' ?>";
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