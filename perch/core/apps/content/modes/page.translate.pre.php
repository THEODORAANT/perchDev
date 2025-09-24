<?php
    $API    = new PerchAPI(1.0, 'core');
    $Lang   = $API->get('Lang');
    $HTML   = $API->get('HTML');

    $Pages     = new PerchContent_Pages();
    $Regions   = new PerchContent_Regions();
    $Languages = new PerchContent_Languages();
    $Page      = false;

    $language_list = $Languages->find_all();

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id   = (int) $_GET['id'];
        $Page = $Pages->find($id);
    }

    if (!$Page || !is_object($Page)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
    }

    if (!$CurrentUser->has_priv('content.pages.translate')) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
    }

    $Form = $API->get('Form');

    $lang_options               = [];
    $available_language_values  = [];
    $existing_languages         = [];
    $form_defaults              = [];
    $selected_lang              = '';
    $form_disabled              = false;

    $language_codes = [];
    if (PerchUtil::count($language_list)) {
        foreach ($language_list as $Language) {
            $language_codes[] = $Language->lang();
        }
    }

    if (!PerchUtil::count($language_codes)) {
        $form_disabled = true;
        $Alert->set('notice', PerchLang::get('Enable page languages before creating translations.'));
    }

    if (!$form_disabled) {
        $page_regions = $Regions->get_for_page($Page->id());
        if (PerchUtil::count($page_regions)) {
            foreach ($page_regions as $Region) {
                $region_lang = $Region->language();
                if ($region_lang && $region_lang !== '*' && $region_lang !== '') {
                    $existing_languages[$region_lang] = true;
                    continue;
                }

                if (preg_match('/^(.*) - ([A-Za-z0-9_\\-]+)$/', $Region->regionKey(), $matches)) {
                    $base_key      = $matches[1];
                    $possible_lang = $matches[2];

                    if (in_array($possible_lang, $language_codes, true)) {
                        $BaseRegion = $Regions->find_for_page_by_key($Page->id(), $base_key);
                        if ($BaseRegion) {
                            if ($region_lang === '*' || $region_lang === '' || $region_lang === false) {
                                $Region->update(['language' => $possible_lang]);
                                $region_lang = $possible_lang;
                            }
                            $existing_languages[$possible_lang] = true;
                        }
                    }
                }
            }
        }

        if (PerchUtil::count($language_list)) {
            foreach ($language_list as $Language) {
                $code  = $Language->lang();
                $label = $Language->name();
                if ($label == '') $label = $code;

                if (!isset($existing_languages[$code])) {
                    $lang_options[] = [
                        'label' => $label,
                        'value' => $code,
                    ];
                }
            }
        }

        if (!PerchUtil::count($lang_options)) {
            $form_disabled = true;
            $Alert->set('info', PerchLang::get('Translations already exist for all configured languages.'));
        } else {
            foreach ($lang_options as $opt) {
                $available_language_values[] = $opt['value'];
            }
        }
    }

    if (!$form_disabled) {
        $Form->set_required([
            'lang' => 'Required',
        ]);
    }

    if ($Form->posted() && !$form_disabled && $Form->validate()) {
        $data = $Form->receive(['lang']);
        $selected_lang = trim($data['lang']);
        $form_defaults['lang'] = $selected_lang;

        if ($selected_lang === '' || !in_array($selected_lang, $available_language_values, true)) {
            $Alert->set('error', PerchLang::get('Please select a valid language.'));
        } else {
            $regions_to_duplicate = $Regions->get_for_translation_page($selected_lang, $Page->id());
            if (!PerchUtil::count($regions_to_duplicate)) {
                $Alert->set('info', PerchLang::get('All regions already have a %s translation.', PerchUtil::html($selected_lang)));
            } else {
                $created = false;

                foreach ($regions_to_duplicate as $Region) {
                    $data = $Region->to_array();
                    unset($data['regionID']);

                    $data['regionKey'] = $Region->regionKey() . ' - ' . $selected_lang;
                    $data['language']  = $selected_lang;
                    $data['regionNew'] = 1;

                    $Region->duplicate_region($data);
                    $created = true;
                }

                if ($created) {
                    PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/page/translate/?id=' . $Page->id() . '&created=' . urlencode($selected_lang));
                } else {
                    $Alert->set('info', PerchLang::get('All regions already have a %s translation.', PerchUtil::html($selected_lang)));
                }
            }
        }
    }

    if (PerchUtil::get('created')) {
        $created_lang = PerchUtil::get('created');
        $Alert->set('success', PerchLang::get('A %s translation has been created.', PerchUtil::html($created_lang)));
    }
