<?php $this->display('header'); ?>

<div class="span10">
    <table class="table table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>Latest version</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($this->get('aPackages') as $aPackage) {
    ?>
    <tr>
        <td><a href="/?action=info&package=<?=$aPackage['name'];?>"><?=$aPackage['name'];?></a></td>
        <td><?=$aPackage['latestVersion']['version'];?></td>
        <td><?=$aPackage['latestVersion']['description'];?></td>
    </tr>
    <?php
    }
    ?>
    </table>
</div>

<?php $this->display('footer'); ?>
