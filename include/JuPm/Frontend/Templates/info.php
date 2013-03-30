<?php $this->display('header'); ?>

<div class="span10">
    <h3>Latest version</h3>
    <?php
    $aLatestVersion = $this->get('aLatestVersion');
    ?>
    <table class="table">
    <tbody>
    <tr>
        <th>Name</th>
        <td><?=$aLatestVersion['name'];?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?=$aLatestVersion['description'];?></td>
    </tr>
    <tr>
        <th>Latest version</th>
        <td><?=$aLatestVersion['version'];?></td>
    </tr>
    <tr>
        <th>Authors</th>
        <td>
            <ul>
        <?php foreach($aLatestVersion['authors'] as $aAuthor) { ?>
                <li><?=$aAuthor['name'];?></li>
        <?php } ?>
            </ul>
        </td>
    </tr>
    </tbody>
    </table>

    <h3>All versions</h3>

    <table class="table atble-striped">
    <tbody>
    <?php foreach ($this->get('aVersions') as $aVersion) { ?>
    <tr>
        <td><?=$aVersion['version'];?></td>
        <td>#Info</td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>

<?php $this->display('footer'); ?>
