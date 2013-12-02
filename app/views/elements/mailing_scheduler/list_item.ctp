<?php if ((!in_array($userDetails['samaccountname'], $superusers) && !in_array('Geeks', $userDetails['groups'])) || !isset($mailingPackageSectionRelId)): ?>
    <li><?php echo $name; ?></li>
<?php else: ?>
    <li id="listItem_<?php echo $mailingPackageSectionRelId; ?>"><img src="/img/drag_handle.gif" class="handle"><?php echo $name; ?><img class="delete-item" src="/img/delete.png" /></li>
<?php endif; ?>