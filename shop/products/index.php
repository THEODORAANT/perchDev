<?php
//phpinfo();
    include('../../perch/runtime.php');
?>
<?php

    perch_shop_product(perch_get("s"),[
                                          'template' => 'products/product_view.html',
                                          'variants' => true,
                                      ]);

?>
