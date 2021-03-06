<?php

class formAdminCtypesDataset extends cmsForm {

    public function init($do, $ctype, $cats_list, $fields_list) {

        $ctype_id = (!empty($ctype['id']) ? $ctype['id'] : $ctype['name']);

        $lists = cmsEventsManager::hookAll('ctype_lists_context', 'dataset:'.$ctype['name']);

        $ds_lists = array();

        if($lists){
            foreach ($lists as $list) {
                $ds_lists = array_merge($ds_lists, $list);
            }
        }

        $form = array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique_ctype_dataset', $ctype_id) : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_CP_DATASET_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldHtml('description', array(
                        'title' => LANG_DESCRIPTION,
                        'options' => array('editor' => cmsConfig::get('default_editor'))
                    )),
                    new fieldNumber('max_count', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 0,
                        'rules' => array(
                            array('max', 65535)
                        )
                    )),
                    new fieldCheckbox('is_visible', array(
                        'title' => LANG_CP_DATASET_IS_VISIBLE,
                        'default' => true
                    ))
                )
            ),
            'sorting' => array(
                'title'  => LANG_SORTING,
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldList('sorting', array(
                        'add_title'    => LANG_SORTING_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SORTING_FIELD,
                        'multiple_keys' => array(
                            'by' => 'field', 'to' => 'field_select'
                        ),
                        'generator' => function() use($fields_list){

                            $items = array();

                            if($fields_list){
                                foreach($fields_list as $field){
                                    $items[$field['value']] = $field['title'];
                                }
                            }

                            return $items;
                        },
                        'value_items' => array(
                            'asc'  => LANG_SORTING_ASC,
                            'desc' => LANG_SORTING_DESC
                        )
                    ))
                )
            ),
            'filter' => array(
                'title'  => LANG_FILTERS,
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldList('filters', array(
                        'add_title'    => LANG_FILTER_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_FILTER_FIELD,
                        'multiple_keys' => array(
                            'field' => 'field', 'condition' => 'field_select', 'value' => 'field_value'
                        ),
                        'generator' => function() use($fields_list){

                            $items = array();

                            if($fields_list){
                                foreach($fields_list as $field){
                                    $items[$field['value']] = array(
                                        'title' => $field['title'],
                                        'data'  => array(
                                            'ns' => $field['type']
                                        )
                                    );
                                }
                            }

                            return $items;
                        },
                        'value_items' => array(
                            'int'  => array(
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ),
                            'str'  => array(
                                'eq' => '=',
                                'lk' => LANG_FILTER_LIKE,
                                'lb' => LANG_FILTER_LIKE_BEGIN,
                                'lf' => LANG_FILTER_LIKE_END,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            ),
                            'date'  => array(
                                'eq' => '=',
                                'gt' => '&gt;',
                                'lt' => '&lt;',
                                'ge' => '&ge;',
                                'le' => '&le;',
                                'dy' => LANG_FILTER_DATE_YOUNGER,
                                'do' => LANG_FILTER_DATE_OLDER,
                                'nn' => LANG_FILTER_NOT_NULL,
                                'ni' => LANG_FILTER_IS_NULL
                            )
                        )
                    ))
                )
            ),
            'seo' => array(
                'title' => LANG_SEO,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('seo_title', array(
                        'title' => LANG_SEO_TITLE,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),
                    new fieldString('seo_keys', array(
                        'title' => LANG_SEO_KEYS,
                        'hint' => LANG_SEO_KEYS_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),
                    new fieldText('seo_desc', array(
                        'title' => LANG_SEO_DESC,
                        'hint' => LANG_SEO_DESC_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    ))
                )
            ),
            'gv' => array(
                'title' => LANG_SHOW_TO_GROUPS,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldListGroups('groups_view', array(
                        'show_all' => true,
                        'show_guests' => true
                    ))
                )
            ),
            'gh' => array(
                'title' => LANG_HIDE_FOR_GROUPS,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldListGroups('groups_hide', array(
                        'show_all' => false,
                        'show_guests' => true
                    ))
                )
            ),
            'list_show' => array(
                'title' => LANG_CP_FIELD_IN_LIST_CONTEXT,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldList('list:show', array(
                        'is_chosen_multiple' => true,
                        'items' => $ds_lists
                    )),
                )
            ),
            'list_hide' => array(
                'title' => LANG_CP_FIELD_NOT_IN_LIST_CONTEXT,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldList('list:hide', array(
                        'is_chosen_multiple' => true,
                        'items' => $ds_lists
                    )),
                )
            )
        );

        if (!empty($ctype['is_cats']) && $cats_list){
            $form['cv'] = array(
                'title' => LANG_CP_CATS_VIEW,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldList('cats_view', array(
                        'is_chosen_multiple' => true,
                        'items' => $cats_list
                    )),
                )
            );
            $form['ch'] = array(
                'title' => LANG_CP_CATS_HIDE,
                'type' => 'fieldset',
                'childs' => array(
                    new fieldList('cats_hide', array(
                        'is_chosen_multiple' => true,
                        'items' => $cats_list
                    ))
                )
            );
        }

        return $form;

    }

}
