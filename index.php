<?php

require __DIR__ . '/vendor/autoload.php';

$userId = isset( $_GET[ 'id' ] ) ? (int) $_GET[ 'id' ] : 0;

?>

<script type="text/javascript">
    var userId = <?= $userId; ?>;
</script>
<script src="/js/StatusOnline.js"></script>
<script src="/js/index.js"></script>
