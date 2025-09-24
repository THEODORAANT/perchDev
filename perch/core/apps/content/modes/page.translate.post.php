<?php
    echo $HTML->title_panel([
        'heading' => sprintf(PerchLang::get('Translate %s Page'), ' &#8216;' . PerchUtil::html($Page->pageNavText()) . '&#8217; '),
    ]);

    $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
    $Smartbar->add_item([
        'title'  => 'Regions',
        'link'   => '/core/apps/content/page/?id='.$Page->id(),
        'icon'   => 'core/o-grid',
    ]);

    if ($Page->pagePath() != '*') {
        $Smartbar->add_item([
            'title'  => 'Details',
            'link'   => '/core/apps/content/page/details/?id='.$Page->id(),
            'priv'   => 'content.pages.attributes',
            'icon'   => 'core/o-toggles',
        ]);

        $Smartbar->add_item([
            'title'  => 'Location',
            'link'   => '/core/apps/content/page/url/?id='.$Page->id(),
            'priv'   => 'content.pages.manage_urls',
            'icon'   => 'core/o-signs',
        ]);

        $Smartbar->add_item([
            'active' => true,
            'title'  => 'Translate Page',
            'link'   => '/core/apps/content/page/translate/?id='.$Page->id(),
            'priv'   => 'content.pages.translate',
            'icon'   => 'core/lang',
        ]);

        $Smartbar->add_item([
            'title'         => 'View Page',
            'link'          => rtrim($Settings->get('siteURL')->val(), '/').$Page->pagePath(),
            'icon'          => 'core/document',
            'position'      => 'end',
            'new-tab'       => true,
            'link-absolute' => true,
        ]);

        $Smartbar->add_item([
            'title'    => 'Settings',
            'link'     => '/core/apps/content/page/edit/?id='.$Page->id(),
            'priv'     => 'content.pages.edit',
            'icon'     => 'core/gear',
            'position' => 'end',
        ]);
    }

    echo $Smartbar->render();

    $Alert->output();

    if ($form_disabled) {
        echo '<div class="inner">';
        echo '<p>'.PerchLang::get('There are no additional languages available for this page.').'</p>';
        echo '</div>';
    } else {
        echo $Form->form_start();

        echo '<h2 class="divider"><div>'.PerchLang::get('Languages').'</div></h2>';
        echo '<div class="field-wrap">';
        echo $Form->label('lang', PerchLang::get('Translate page to'));
        echo '<div class="form-entry">';
        echo $Form->select('lang', $lang_options, $Form->get($form_defaults, 'lang'));
        echo '</div>';
        echo '</div>';

        echo $HTML->submit_bar([
            'button' => $Form->submit('btnsubmit', PerchLang::get('Create translation'), 'button'),
        ]);

        echo $Form->form_end();
    }
