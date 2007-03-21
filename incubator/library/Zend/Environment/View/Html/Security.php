    <table width="100%" summary="Section - <?php echo $this->section->getType() ?>">
        <col width="10%" />
        <col width="10%" />
        <col width="10%" />
        <col width="5%"%" />
        <col width="5%" />
        <col />
        <col width="10%" />
        <thead>
            <tr class="header">
                <th colspan="7"><?php echo ucwords($this->section->getType()) ?></th>
            </tr>
            <tr>
                <th>Group</th>
                <th>Name</th>
                <th>Result</th>
                <th>Current Value</th>
                <th>Rec. Value</th>
                <th>Details</th>
                <th>More Info</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->section as $info) { ?>    <tr class="sec-<?php echo $this->escape($this->toString($info->result)) ?>">
                <td><?php echo nl2br($this->escape($this->toString($info->group))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->name))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->result))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->current_value))) ?></td>
                <td><?php echo nl2br($this->escape($this->toString($info->recommended_value))) ?></td>
                <td><?php echo $this->toString($info->details) ?></td>
                <td><a href="<?php echo nl2br($this->escape($this->toString($info->link))) ?>">More&nbsp;Info&nbsp;&raquo;</a></td>
            </tr>
        <?php } ?></tbody>
    </table>
