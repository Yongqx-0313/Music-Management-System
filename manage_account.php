<?php session_start() ?>
<?php include 'db_connect.php'; ?>
<?php
$qry = $conn->query("SELECT * FROM users where id = " . $_SESSION['login_id'])->fetch_array();
foreach ($qry as $k => $v) {
	$$k = $v;
}
?>
<div class="container-fluid">
	<form action="" id="update-account">
		<input type="hidden" name="id" value="<?php echo $_SESSION['login_id'] ?>">
		<div class="col-lg-12">
			<div id="msg"></div>
			<div class="row">
				<div class="col-md-6 border-right">
					<b class="text-muted">Personal Information</b>
					<div class="form-group">
						<label for="" class="control-label">First Name</label>
						<input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8') : '' ?>">
					</div>
					<div class="form-group">
						<label for="" class="control-label">Last Name</label>
						<input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8') : '' ?>">
					</div>
					<div class="form-group">
						<label for="" class="control-label">Last Name</label>
						<select class="custom-select custom-select-sm">
							<option <?php echo  isset($gender) && $gender == 'Female' ? 'selected' : "" ?>>Female</option>
							<option <?php echo  isset($gender) && $gender == 'Male' ? 'selected' : "" ?>>Male</option>
						</select>
					</div>
					<div class="form-group">
						<label for="" class="control-label">Contact No.</label>
						<input type="text" name="contact" class="form-control form-control-sm" required value="<?php echo isset($contact) ? $contact : '' ?>">
					</div>
					<div class="form-group">
						<label class="control-label">Address</label>
						<textarea name="address" id="" cols="30" rows="4" class="form-control" required><?php echo isset($address) ? $address : '' ?></textarea>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="control-label">Profile Picture</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label" for="customFile">Choose file</label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img src="<?php echo isset($profile_pic) ? 'assets/uploads/' . $profile_pic : '' ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
					</div>
					<b class="text-muted">System Credentials</b>
					<?php if ($_SESSION['login_type'] == 1): ?>
						<div class="form-group">
							<label for="" class="control-label">User Role</label>
							<select name="type" id="type" class="custom-select custom-select-sm">
								<option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Subscriber</option>
								<option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Admin</option>
							</select>
						</div>
					<?php else: ?>
						<input type="hidden" name="type" value="<?php echo $type ?>">
					<?php endif; ?>
					<div class="form-group">
						<label class="control-label">Email</label>
						<input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
						<small id="#msg"></small>
					</div>
					<div class="form-group">
						<label class="control-label">Password</label>
						<input type="password" class="form-control form-control-sm" name="password" <?php echo !isset($id) ? "required" : '' ?>>
						<small><i><?php echo isset($id) ? "Leave this blank if you dont want to change you password" : '' ?></i></small>
					</div>
					<div class="form-group">
						<label class="label control-label">Confirm Password</label>
						<input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
						<small id="pass_match" data-status=''></small>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script>
	$('#update-account').submit(function(e) {
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if ($('[name="password"]').val() != '' && $('[name="cpass"]').val() != '') {
			if ($('#pass_match').attr('data-status') != 1) {
				if ($("[name='password']").val() != '') {
					$('[name="password"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url: "ajax.php?action=signup",
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast("Account successfully updated", "success")
					$('.modal').modal('hide')
					end_load()
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
	$('[name="password"],[name="cpass"]').keyup(function() {
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if (cpass == '' || pass == '') {
			$('#pass_match').attr('data-status', '')
		} else {
			if (cpass == pass) {
				$('#pass_match').attr('data-status', '1').html('<i class="text-success">Password Matched.</i>')
			} else {
				$('#pass_match').attr('data-status', '2').html('<i class="text-danger">Password does not match.</i>')
			}
		}
	})

	function displayImgCover(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cover').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
</script>