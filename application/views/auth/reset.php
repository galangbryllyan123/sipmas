<?php echo $this->session->flashdata('validation'); ?>
<div class="wrapper">
    <a href="<?= base_url(); ?>"><h2 class="brand-name"><?= $this->Settings_model->general()["app_name"]; ?></h2></a>
    <p class="subtitle">Reset Password Akun</p>
    <?php echo $this->session->flashdata('failed'); ?>
    <form action="<?= base_url(); ?>reset-password" method="post">
        <div class="form-group">
            <input type="email" placeholder="Alamat Email"  name="email" class="form-control"  autocomplete="off" value="<?php echo set_value('email'); ?>">
            <small class="form-text text-danger pl-1"><?php echo form_error('email'); ?></small>
        </div>
        <button type="submit" class="btn btn-block btn-dark mt-3 mb-1">Reset</button>
        <hr>
        <p class="text-lead">Ke halaman <a href="<?= base_url(); ?>register">daftar</a> atau <a href="<?= base_url(); ?>login">login</a></p>
    </form>
</div>
