<?php
    echo $HTML->title_panel([
        'heading' => sprintf($Lang->get('Translate %s Post'), ' &#8216;' . $HTML->encode($Post->postTitle()) . '&#8217;'),
    ], $CurrentUser);

    include(__DIR__.'/_post_smartbar.php');

    $Alert->output();

    if ($form_disabled) {
        echo '<div class="inner">';
        echo '<p>'.$Lang->get('No additional languages are available for this post.').'</p>';
        echo '</div>';
    } else {
        echo $Form->form_start();

        echo '<h2 class="divider"><div>'.$Lang->get('Languages').'</div></h2>';
        echo '<div class="field-wrap">';
        echo $Form->label('lang', $Lang->get('Translate post to'));
        echo '<div class="form-entry">';
        echo $Form->select('lang', $lang_options, $Form->get($form_defaults, 'lang'));
        echo '</div>';
        echo '</div>';

        echo $HTML->submit_bar([
            'button' => $Form->submit('btnsubmit', $Lang->get('Create translation'), 'button'),
        ]);

        echo $Form->form_end();
    }

    if (PerchUtil::count($existing_translations)) {
        echo '<h2 class="divider"><div>'.$Lang->get('Existing translations').'</div></h2>';
        echo '<div class="inner">';
        echo '<table class="d">';
        echo '<thead><tr>';
        echo '<th>'.$Lang->get('Language').'</th>';
        echo '<th>'.$Lang->get('Status').'</th>';
        echo '<th class="action">'.$Lang->get('Actions').'</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($existing_translations as $TranslationItem) {
            if (!isset($TranslationItem['post']) || !$TranslationItem['post']) {
                continue;
            }

            $TranslationPost = $TranslationItem['post'];
            $language_label  = $TranslationItem['label'];

            $status = $TranslationPost->postStatus();
            if (strtotime($TranslationPost->postDateTime()) > time() && $status == 'Published') {
                $status_label = $Lang->get('Will publish on date');
            } else {
                $status_label = $Lang->get($status);
            }

            $edit_label = $TranslationItem['is_base'] ? $Lang->get('Edit Post') : $Lang->get('Edit translation');
            $edit_link  = $API->app_nav('perch_blog').'/edit/?id='.$TranslationPost->id();

            $view_label = $TranslationPost->postStatus() == 'Draft' ? $Lang->get('Preview Draft') : $Lang->get('View Post');
            $view_url   = $TranslationPost->postStatus() == 'Draft' ? $TranslationPost->previewURL() : $TranslationPost->postURL();

            $actions = [];
            $actions[] = '<a class="button button-small" href="'.$HTML->encode($edit_link).'">'.$HTML->encode($edit_label).'</a>';
            $actions[] = '<a class="button button-small" href="'.$HTML->encode($view_url).'" target="_blank">'.$HTML->encode($view_label).'</a>';

            echo '<tr>';
            echo '<td>'.$HTML->encode($language_label).'</td>';
            echo '<td>'.$HTML->encode($status_label).'</td>';
            echo '<td class="action">'.implode(' ', $actions).'</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
