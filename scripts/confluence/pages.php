<?php

require_once 'includes/header.php';
require_once 'etc/config.php';

$soap   = new SoapClient($confluenceWsdl);
$token  = $soap->login($confluenceUser, $confluencePass);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (is_array($_POST['page'])) {
        foreach ($_POST['page'] as $key => $pageId) {
            $soap->removePage($token, $pageId);
        }
    }
}

$pages  = $soap->getPages($token, $_REQUEST['space']);

arsort($pages);

?>

<h1>Zend Framework - Wiki Page Manager</h1>
<p>Currently browsing <?php echo $_REQUEST['space']; ?></p>
<form name="frm_page_manager" method="post" action="pages.php">
<table width="100%">
  <tr>
    <th>x</th>
    <th>id</th>
    <th>parent id</th>
    <th>page</th>
  </tr>
<?php
foreach ($pages as $key => $page) {
    echo '<tr>';
    echo '<td><input type="checkbox" name="page[]" value="' . $page->id . '" /></td>';
    echo '<td>' . $page->id . '</td>';
    echo '<td>' . $page->parentId . '</td>';
    echo '<td><a href="' . $page->url . '" target="_blank">' . $page->title . '</a></td>';
    echo '</tr>';
}
?>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4"><input type="hidden" name="space" value="<?php echo $_REQUEST['space']; ?>" /><input type="submit" value="Delete selected pages" /></td>
  </tr>
</table>
</form>
<?php

require_once 'includes/footer.php';

?>