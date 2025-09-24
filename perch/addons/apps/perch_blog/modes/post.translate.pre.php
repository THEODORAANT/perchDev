<?php
    $Posts         = new PerchBlog_Posts($API);
    $Blogs         = new PerchBlog_Blogs($API);
    $Translations  = new PerchBlog_PostTranslations($API);
    $LanguagesRepo = new PerchContent_Languages();

    $blogs = $Blogs->all();

    if (!$CurrentUser->has_priv('perch_blog.post.create')) {
        PerchUtil::redirect($API->app_path());
    }

    $postID = (int) PerchUtil::get('id');
    if (!$postID) {
        PerchUtil::redirect($API->app_path());
    }

    $Post = $Posts->find($postID, true);
    if (!$Post) {
        PerchUtil::redirect($API->app_path());
    }

    $base_post_id = $Translations->find_base_id_for_post($Post->id());
    if ($base_post_id && $base_post_id != $Post->id()) {
        $BasePost = $Posts->find($base_post_id, true);
        if (!$BasePost) {
            $base_post_id = $Post->id();
            $BasePost = $Post;
        }
    } else {
        $base_post_id = $Post->id();
        $BasePost = $Post;
    }

    $Blog = $Post->get_blog();
    if (!$Blog && PerchUtil::count($blogs)) {
        $Blog = $Blogs->first();
    }

    $smartbar_selection = 'translate';
    $draft = ($Post->postStatus() == 'Draft');

    $language_list = $LanguagesRepo->find_all();
    $language_map  = [];
    $lang_options  = [];
    $available_language_values = [];
    $existing_languages = [];
    $existing_translations = [];

    if (PerchUtil::count($language_list)) {
        foreach ($language_list as $Language) {
            $code  = $Language->lang();
            $label = $Language->name();
            if ($label == '') $label = $code;
            $language_map[$code] = $label;
        }
    }

    $existing_translations[] = [
        'language' => null,
        'label'    => $Lang->get('Base content'),
        'post'     => $BasePost,
        'is_base'  => true,
    ];

    $TranslationRows = $Translations->get_for_base($base_post_id);
    if (PerchUtil::count($TranslationRows)) {
        foreach ($TranslationRows as $Translation) {
            $translation_post_id = (int) $Translation->translationPostID();

            if ($translation_post_id === $base_post_id) {
                continue;
            }

            $TranslationPost = $Posts->find($translation_post_id, true);
            if (!$TranslationPost) {
                $Translation->delete();
                continue;
            }

            $code = $Translation->language();
            $existing_languages[$code] = true;

            $existing_translations[] = [
                'language' => $code,
                'label'    => isset($language_map[$code]) ? $language_map[$code] : $code,
                'post'     => $TranslationPost,
                'is_base'  => false,
            ];
        }
    }

    if (PerchUtil::count($language_map)) {
        foreach ($language_map as $code => $label) {
            if (!isset($existing_languages[$code])) {
                $lang_options[] = [
                    'label' => $label,
                    'value' => $code,
                ];
                $available_language_values[] = $code;
            }
        }
    }

    $form_disabled = false;
    if (!PerchUtil::count($language_map)) {
        $form_disabled = true;
        $Alert->set('notice', PerchLang::get('Enable page languages before creating translations.'));
    } elseif (!PerchUtil::count($lang_options)) {
        $form_disabled = true;
        $Alert->set('info', PerchLang::get('Translations already exist for all configured languages.'));
    }

    $Form = $API->get('Form');
    $Form->set_name('translate');

    if (!$form_disabled) {
        $Form->set_required([
            'lang' => 'Required',
        ]);
    }

    $form_defaults = [];

    if ($Form->posted() && !$form_disabled) {
        if ($Form->validate()) {
            $data = $Form->receive(['lang']);
            $selected_lang = trim($data['lang']);
            $form_defaults['lang'] = $selected_lang;

            if ($selected_lang === '' || !in_array($selected_lang, $available_language_values, true)) {
                $Alert->set('error', $Lang->get('Please select a valid language.'));
            } else {
                $NewPost = $Posts->create_translation($BasePost, $selected_lang, $Translations, $base_post_id);
                if ($NewPost) {
                    PerchUtil::redirect($API->app_path().'/translate/?id='.$Post->id().'&created='.urlencode($selected_lang));
                } else {
                    $Alert->set('error', $Lang->get('Sorry, that translation could not be created.'));
                }
            }
        } else {
            $form_defaults['lang'] = $Form->get($form_defaults, 'lang');
        }
    }

    if (PerchUtil::get('created')) {
        $created_lang = PerchUtil::get('created');
        $Alert->set('success', PerchLang::get('A %s translation has been created.', PerchUtil::html($created_lang)));
    }
