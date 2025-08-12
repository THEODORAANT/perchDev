<?php
    echo $HTML->title_panel([
    'heading' => PerchLang::get('Reordering Products')
    ], $CurrentUser);

    $Alert->set('info', PerchLang::get('Drag and drop the products to reorder them.'));
    $Alert->output();
    	/* ----------------------------------------- SMART BAR ----------------------------------------- */
            include ('_products_smartbar.php');
    	/* ----------------------------------------- /SMART BAR ----------------------------------------- */

?>
<div class="inner">
    <form method="post" action="<?php echo PerchUtil::html($Form->action(), true); ?>" class="reorder form-simple">
    <?php
        echo render_tree($Products,  0, 'sortable sortable-tree');

        function render_tree($Products, $parentID=0, $class=false)
        {
            $products = $Products->get_by_parent($parentID);

            $s = '';
            $s = '<ol class="'.$class.'">';

            if (PerchUtil::count($products)) {

                foreach($products as $Product) {
                    $s .= '<li id="product_'.$Product->id().'" data-parent="'.$parentID.'"><div class="product">';
                    $s .= '<input type="text" name="d-'.$Product->id().'" value="'.$Product->productOrder().'" />';
                    $s .= PerchUI::icon('core/chart-pie');
                    $s .= ''.PerchUtil::html($Product->title()).'</div>';

                    $s .= render_tree($Products, $Product->id());
                    $s .= '</li>';
                }

            }
            $s .= '</ol>';

            return $s;
        }
    ?>
        <div class="submit-bar">
            <?php
            echo $Form->submit('reorder', 'Save Changes', 'button action');
            echo $Form->hidden('orders', '');
            ?>
        </div>
    </form>
</div>
