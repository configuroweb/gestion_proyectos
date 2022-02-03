<style>
    .img-thumb-path{
        width:100px;
        height:80px;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<?php 
if(isset($_GET['eid']))
$eid = $_GET['eid'];
if(isset($_GET['pid']))
$pid = $_GET['pid'];
$emp = "N/A";
$proj = "N/A";
$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d",strtotime(date("Y-m-d")." -1 week")); 
$to = isset($_GET['to']) ? $_GET['to'] : date("Y-m-d",strtotime(date("Y-m-d"))); 
function duration($dur = 0){
    if($dur == 0){
        return "00:00";
    }
    $hours = floor($dur / (60 * 60));
    $min = floor($dur / (60)) - ($hours*60);
    $dur = sprintf("%'.02d",$hours).":".sprintf("%'.02d",$min);
    return $dur;
}
?>
<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">Tiempo de empleado asignado por proyecto</h3>
		<div class="card-tools">
		</div>
	</div>
	<div class="card-body">
		<div class="callout border-primary">
			<fieldset>
				<legend>Filter</legend>
					<form action="" id="filter">
						<div class="row align-items-end">
							<div class="form-group col-md-3">
								<label for="" class="control-label">Emplead@</label>
                                <select name="eid" id="eid" class="form-control form-control-sm select2">
                                    <?php 
                                    $employee = $conn->query("SELECT *,CONCAT(firstname,' ',middlename,' ',lastname) as fullname FROM `employee_list` order by CONCAT(firstname,' ',middlename,' ',lastname) asc");
                                    while($row= $employee->fetch_assoc()):
                                        if(!isset($eid)){
                                            $eid = $row['id'];
                                        }
                                        if($eid == $row['id'])
                                            $emp = $row['code']. " - ".strtoupper($row['fullname']);
                                    ?>
                                    <option value="<?= $row['id'] ?>" <?= isset($eid) && $eid == $row['id'] ? "selected" : "" ?>><?= $row['code']. " - ".strtoupper($row['fullname']) ?></option>
                                    <?php endwhile; ?>
                                </select>
							</div>
                            <div class="form-group col-md-3">
								<label for="" class="control-label">Proyecto</label>
                                <select name="pid" id="pid" class="form-control form-control-sm select2">
                                    <?php 
                                    $project = $conn->query("SELECT * FROM `project_list` order by name asc");
                                    while($row= $project->fetch_assoc()):
										if(!isset($pid)){
                                            $pid = $row['id'];
                                        }
                                        if($pid == $row['id'])
                                            $proj = strtoupper($row['name']);
                                    ?>
                                    <option value="<?= $row['id'] ?>" <?= isset($pid) && $pid == $row['id'] ? "selected" : "" ?>><?= $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
							</div>
							<div class="form-group col-md-4">
                                <button class="btn btn-primary btn-flat btn-sm"><i class="fa fa-filter"></i> Filtro</button>
			                    <button class="btn btn-sm btn-flat btn-success" type="button" id="print"><i class="fa fa-print"></i> Imprimir</button>
							</div>
						</div>
					</form>
			</fieldset>
		</div>
		<div id="outprint">
			<style>
				#sys_logo{
					object-fit:cover;
					object-position:center center;
					width: 6.5em;
					height: 6.5em;
				}
			</style>
        <div class="container-fluid">
			<div class="row">
				<div class="col-2 d-flex justify-content-center align-items-center">
					<img src="<?= validate_image($_settings->info('logo')) ?>" class="img-circle" id="sys_logo" alt="System Logo">
				</div>
				<div class="col-8">
					<h4 class="text-center"><b><?= $_settings->info('name') ?></b></h4>
					<h3 class="text-center"><b>Tiempo Total Trabajado por Emplead@ <?= $proj ?></b></h3>
					<h5 class="text-center"><b><?= $emp ?></b></h5>
					<h5 class="text-center"><b>a partir de</b></h5>
					<h5 class="text-center"><b><?= date("F d, Y") ?></b></h5>
				</div>
				<div class="col-2"></div>
			</div>
			<table class="table table-bordered table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="30%">
					<col width="30%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr class="bg-gradient-primary text-light">
						<th>#</th>
						<th>Fecha de Ingreso</th>
						<th>Fecha Desde</th>
						<th>Fecha Desde</th>
						<th>Tiempo Total Trabajado</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$total_dur = 0;
						$qry = $conn->query("SELECT * from `report_list` where employee_id = '{$eid}' and project_id = '{$pid}' order by unix_timestamp(datetime_from) asc, unix_timestamp(datetime_to) asc ");
						while($row = $qry->fetch_assoc()):
							$total_dur += $row['duration'];  
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class=""><?= date("M d, Y h:i A",strtotime($row['datetime_from'])) ?></td>
							<td class=""><?= date("M d, Y h:i A",strtotime($row['datetime_to'])) ?></td>
							<td class=""><p class="m-0 truncate-1 text-right"><?php echo duration($row['duration']) ?></p></td>
						</tr>
					<?php endwhile; ?>
                    <?php if($qry->num_rows <= 0): ?>
                        <tr>
                            <th colspan="5"><center>Sin Datos que Mostrar</center></th>
                        </tr>
                    <?php endif; ?>
				</tbody>
				<tfoot>
					<tr class="bg-lightblue text-light bg-opacity-50">
						<th colspan="4" class="text-right"> Total de Tiempo Trabajado</th>
						<th class="text-right"><b><?= duration($total_dur) ?></b></th>
					</tr>
				</tfoot>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
        $('.select2').select2({
            width:'100%'
        })
        $('#filter').submit(function(e){
            e.preventDefault();
            location.href= './?page=reports/date_wise&'+$(this).serialize();
        })
       $('#print').click(function(){
		   start_loader()
		   var _p = $('#outprint').clone()
		   var _h = $('head').clone()
		   var _el = $('<div>')
		   _h.find("title").text("Reporte de Tiempo Trabajado por Proyecto Por Empleado - ConfiguroWeb")
		   _p.find('tr.text-light').removeClass('text-light bg-gradient-primary bg-lightblue')
		   _el.append(_h)
		   _el.append(_p)
		   var nw = window.open("","_blank","width=1000,height=900,left=300,top=200")
		   	nw.document.write(_el.html())
			nw.document.close()
			setTimeout(() => {
				nw.print()
				setTimeout(() => {
					nw.close()
					end_loader()
				}, 300);
			}, 750);
	   })
	})
</script>