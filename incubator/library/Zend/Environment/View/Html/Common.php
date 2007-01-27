    <table width="100%" summary="Section - <?php echo $this->section->getType() ?>">
        <col width="30%" />
        <col width="10%" />
        <col width="20%" />
        <col />
        <thead>
            <tr class="header">
                <th colspan="4"><?php echo ucwords($this->section->getType()) ?></th>
            </tr>
            <tr>
                <th>Title</th>
                <th>Version</th>
                <th>Value</th>
                <th>Info</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->section as $info) { ?>    <tr>
                <td><?php echo nl2br($this->escape($this->toString($info->title))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->version))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString(join(PATH_SEPARATOR . "\n", explode(PATH_SEPARATOR, $info->value))))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->info))) ?></td>
            </tr>
        <?php } ?></tbody>
    </table>
