<style>
    .img-thumb-path{
        width:100px;
        height:80px;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<?php 
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
		<h3 class="card-title">Rendered Time Per Project Report</h3>
		<div class="card-tools">
			<button class="btn btn-sm btn-flat btn-success" id="print"><i class="fa fa-print"></i> Print</button>
		</div>
	</div>
	<div class="card-body">
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
					<h3 class="text-center"><b>Rendered Time Per Project Report</b></h3>
					<h5 class="text-center"><b>as of</b></h5>
					<h5 class="text-center"><b><?= date("F d, Y") ?></b></h5>
				</div>
				<div class="col-2"></div>
			</div>
			<table class="table table-bordered table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr class="bg-gradient-primary text-light">
						<th>#</th>
						<th>Date Added</th>
						<th>Project Name</th>
						<th>Total Time Rendered</th>
						<th>Total Employees</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT * from `project_list` where delete_flag = 0 order by `name` asc ");
						while($row = $qry->fetch_assoc()):
                            $row['total_duration'] = $conn->query("SELECT SUM(duration) FROM `report_list` where project_id = '{$row['id']}'")->fetch_array()[0];
                            $row['total_employee'] = $conn->query("SELECT distinct(employee_id) FROM `report_list` where project_id = '{$row['id']}'")->num_rows;
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['name'] ?></p></td>
							<td class=""><p class="m-0 truncate-1 text-right"><?php echo duration($row['total_duration']) ?></p></td>
							<td class=""><p class="m-0 truncate-1 text-right"><?php echo number_format($row['total_employee']) ?></p></td>
							<td class="text-center">
								<?php 
									switch ($row['status']){
										case 0:
											echo '<span class="rounded-pill badge badge-success bg-gradient-teal px-3">New</span>';
											break;
										case 1:
											echo '<span class="rounded-pill badge badge-primary bg-gradient-primary px-3">In-Progress</span>';
											break;
										case 2:
											echo '<span class="rounded-pill badge badge-dark bg-gradient-dark px-3 text-light">Closed</span>';
											break;
									}
								?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
       $('#print').click(function(){
		   start_loader()
		   var _p = $('#outprint').clone()
		   var _h = $('head').clone()
		   var _el = $('<div>')
		   _h.find("title").text("Rendered Time Per Project Report - Print View")
		   _p.find('tr.text-light').removeClass('text-light bg-gradient-primary')
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