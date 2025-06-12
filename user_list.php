<?php
session_start();

// Restrict access to admin only
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] != 1) {
    echo "<script>alert('Access Denied. Admins only.'); location.href='index.php';</script>";
    exit();
}

include 'db_connect.php';

// Fetch all users
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>

<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-success">
		<div class="card-header">
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
			</div> -->
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Name</th>
						<th>Contact #</th>
						<th>Role</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$type = array('',"Admin","Subscriber");
					$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users order by concat(firstname,' ',lastname) asc");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['contact'] ?></b></td>
						<td><b><?php echo $type[$row['type']] ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td class="text-center">
							<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
		                      Action
		                    </button>
		                    <div class="dropdown-menu">
		                      <button class="dropdown-item view_user" button="button" data-id="<?php echo $row['id'] ?>">View</button>
		                      <div class="dropdown-divider"></div>
		                      <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id'] ?>">Edit</a>
		                      <div class="dropdown-divider"></div>
		                      <button class="dropdown-item delete_user" data-id="<?php echo $row['id'] ?>">Delete</button>
		                    </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('#list').dataTable()
	$('.view_user').click(function(){
		uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
	})
	$('.delete_user').click(function(){
	let id = $(this).attr('data-id');
		if(confirm("Are you sure to delete this user?")) {
		delete_user(id);
		}
	})
	})
	function delete_user(id){
	start_load()
	$.ajax({
		url: 'ajax.php?action=delete_user',
		method: 'POST',
		data: { id: id },
		success: function(resp){
			end_load(); // ✅ always stop the loading indicator

			if(resp == 1){
				alert_toast("User successfully deleted", 'success');
				setTimeout(function(){
					location.reload();
				}, 1500);
			}
			else if(resp == 2){
				alert_toast(" Cannot delete the last remaining admin!", 'error');
			}
			else {
				alert_toast("⚠️ Deletion failed", 'error');
			}
		},
		error: function(){
			end_load(); // ✅ in case of error, also stop loader
			alert_toast("🚫 Server error occurred", 'error');
		}
	});
}

</script>