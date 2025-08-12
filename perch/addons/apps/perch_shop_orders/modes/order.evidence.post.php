<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Viewing order tax evidence'),
    ], $CurrentUser);

    $smartbar_selection = 'evidence';
    include('_order_smartbar.php');


    if (PerchUtil::count($exhibits)) {

        echo '<div class="inner"><table class="">';

        echo '<thead>';
        echo '<tr>';
                echo '<th>'.$Lang->get('Type').'</th>';
                echo '<th>'.$Lang->get('Detail').'</th>';
                echo '<th>'.$Lang->get('Country').'</th>';
                echo '<th>'.$Lang->get('Source').'</th>';
        echo '</tr>';
        echo '</thead>';

        foreach($exhibits as $Item) {
            #PerchUtil::debug($Item);
            echo '<tr>';
                echo '<td>'.$Item->exhibitType().'</td>';
                echo '<td>'.$Item->exhibitDetail().'</td>';
                echo '<td>'.$Countries->country_name((int)$Item->countryID()).'</td>';
                echo '<td>'.$Item->exhibitSource().'</td>';
                
            echo '</tr>';
        }

        echo '</table></div>';

    }


    function echo_if($val, $HTML)
    {
        if (isset($val) && $val) {
            echo $HTML->encode($val).'<br>';
        }
    }
