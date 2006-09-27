<?php

require_once 'includes/header.php';
require_once 'etc/config.php';

$soap   = new SoapClient($confluenceWsdl);
$token  = $soap->login($confluenceUser, $confluencePass);
$spaces = $soap->getSpaces($token);

arsort($spaces);

?>

<h1>Zend Framework - Wiki Space browser</h1>
<ul>
<?php
foreach ($spaces as $key => $space) {
    echo '<li><a href="pages.php?space=' . $space->key . '">' . $space->name . '</a></li>';
}
?>
</ul>

<?php

require_once 'includes/footer.php';

?>