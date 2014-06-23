Discuss2.panel.ForumProperties = function(config) {
    config = config || {};
    var oc = {
        'change':{fn:MODx.fireResourceFormChange}
        ,'select':{fn:MODx.fireResourceFormChange}
    };
    Ext.applyIf(config, {
        id : 'discuss2-forum-properties',
        title : _('discuss2.forum_properties'),
        cls : 'modx-resource-tab',
        border : false,
        plain : true,
        deferredRender: false,
        anchor : '97%',
        items : [{
            title : _('discuss2.general'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items :[{
                xtype : 'textfield',
                name : "properties[time_format]",
                id : 'time-format',
                fieldLabel : _('discuss2.time_format'),
                description : _('discuss2.time_format_desc'),
                listeners : oc,
                value : config.forum.properties.time_format || 'Y-m-d H:i:s'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'time-format',
                html: _('discuss2.time_format_desc'),
                cls: 'desc-under'
            },{
                xtype : 'textfield',
                name : "properties[default_language]",
                id : 'default_language',
                fieldLabel : _('discuss2.default_language'),
                description : _('discuss2.default_language_desc'),
                listeners : oc,
                value : config.forum.properties.default_language || 'en'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'default_language',
                html: _('discuss2.default_language_desc'),
                cls: 'desc-under'
            },{
                xtype : 'combo-boolean',
                name : "properties[enable_statistics]",
                hiddenName : "properties[enable_statistics]",
                id : 'enable-statistics',
                fieldLabel : _('discuss2.enable_statistics'),
                description : _('discuss2.enable_statistics_desc'),
                listeners : oc,
                value : config.forum.properties.enable_statistics || false
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'enable-statistics',
                html: _('discuss2.enable_statistics_desc'),
                cls: 'desc-under'
            },{
                xtype : 'combo',
                name : "properties[statistics_interval]",
                hiddenName : "properties[statistics_interval]",
                id : 'statistics-interval',
                fieldLabel : _('discuss2.statistics_interval'),
                description : _('discuss2.statistics_interval_desc'),
                triggerAction : 'all',
                typeAhead : true,
                mode : 'local',
                store : [['day', _('discuss2.interval.day')],
                    ['hour', _('discuss2.interval.hour')],
                    ['minute', _('discuss2.interval.minute')]],
                listeners : {
                    render : function(field) {
                        var val = config.forum.statistics_interval ? config.forum.statistics_interval : field.getStore().getAt(0).get('field1');
                        field.setValue(val);
                    },
                    'change':{fn:MODx.fireResourceFormChange}
                    ,'select':{fn:MODx.fireResourceFormChange}
                }
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'statistics-interval',
                html: _('discuss2.statistics_interval_desc'),
                cls: 'desc-under'
            }/*,{ Not in working order right now and needs bit of redesigning on idea level
                xtype : 'combo-boolean',
                name : "properties[file_theme]",
                hiddenName : "properties[file_theme]",
                id : 'file-theme',
                fieldLabel : _('discuss2.file_theme'),
                description : _('discuss2.file_theme_desc'),
                listeners : oc,
                value : config.forum.properties.file_template || true
            },{
                xtype : 'textfield',
                name : "properties[file_theme_name]",
                id : 'file-theme-name',
                fieldLabel : _('discuss2.file_theme_name'),
                description : _('discuss2.file_theme_name_desc'),
                listeners : oc,
                value : config.forum.properties.file_theme_name || 'Discuss1'
            }*/]
        },{
            title : _('discuss2.forms'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'textfield',
                name : "properties[form_submit_var]",
                id : 'form_submit_var',
                fieldLabel : _('discuss2.forms.form_submit_var'),
                description :  _('discuss2.forms.form_submit_var_desc'),
                listeners : oc,
                value : config.forum.properties.form_submit_var || 'd2-submit'
            },{
                xtype : 'textfield',
                name : "properties[form_preview_var]",
                id : 'form_preview_var',
                fieldLabel : _('discuss2.forms.form_preview_var'),
                description :  _('discuss2.forms.form_preview_var_desc'),
                listeners : oc,
                value : config.forum.properties.form_preview_var || 'd2-preview'
            },{
                xtype : 'fieldset',
                title : _('discuss2.forms.thread_forms'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[new_thread_form]",
                    id : 'new_thread_form',
                    fieldLabel : _('discuss2.forms.new_thread_form'),
                    description :  _('discuss2.forms.new_thread_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.new_thread_form || 'sample.newThread'
                },{
                    xtype : 'textfield',
                    name : "properties[edit_thread_form]",
                    id : 'edit_thread_form',
                    fieldLabel : _('discuss2.forms.edit_thread_form'),
                    description :  _('discuss2.forms.edit_thread_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.edit_thread_form || 'sample.newThread'
                },{
                    xtype : 'textfield',
                    name : "properties[remove_thread_form]",
                    id : 'remove_thread_form',
                    fieldLabel : _('discuss2.forms.remove_thread_form'),
                    description :  _('discuss2.forms.remove_thread_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.remove_thread_form || 'sample.removeThread'
                },{
                    xtype : 'textfield',
                    name : "properties[split_thread_form]",
                    id : 'split_thread_form',
                    fieldLabel : _('discuss2.forms.split_thread_form'),
                    description :  _('discuss2.forms.split_thread_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.split_thread_form || 'sample.splitThread'
                }]
            },{
                xtype : 'fieldset',
                title : _('discuss2.forms.post_forms'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[new_post_form]",
                    id : 'new_post_form',
                    fieldLabel : _('discuss2.forms.new_post_form'),
                    description :  _('discuss2.forms.new_post_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.new_post_form || 'sample.newPost'
                },{
                    xtype : 'textfield',
                    name : "properties[edit_post_form]",
                    id : 'edit_post_form',
                    fieldLabel : _('discuss2.forms.edit_post_form'),
                    description :  _('discuss2.forms.edit_post_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.edit_post_form || 'sample.newPost'
                },{
                    xtype : 'textfield',
                    name : "properties[remove_post_form]",
                    id : 'remove_post_form',
                    fieldLabel : _('discuss2.forms.remove_post_form'),
                    description :  _('discuss2.forms.remove_post_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.remove_post_form || 'sample.removePost'
                },{
                    xtype : 'textfield',
                    name : "properties[post_reply_form]",
                    id : 'post_reply_form',
                    fieldLabel : _('discuss2.forms.post_reply_form'),
                    description :  _('discuss2.forms.post_reply_form_desc'),
                    listeners : oc,
                    value : config.forum.properties.post_reply_form || 'sample.replyform'
                }]
            }]
        },{
            title : _('discuss2.misc_chunks'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'fieldset',
                title : _('discuss2.breadcrumbs'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[breadcrumbs_container]",
                    id : 'breadcrumbs-container',
                    fieldLabel : _('discuss2.breadcrumbs_container'),
                    description :  _('discuss2.breadcrumbs_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.breadcrumbs_container || 'sample.breadcrumbContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[breadcrumbs_item]",
                    id : 'breadcrumbs-item',
                    fieldLabel : _('discuss2.breadcrumbs_item'),
                    description :  _('discuss2.breadcrumbs_item_desc'),
                    listeners : oc,
                    value : config.forum.properties.breadcrumbs_item || 'sample.breadcrumbItem'
                }]
            },{
                xtype : 'fieldset',
                title : _('discuss2.action_chunks'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[thread_actions_container]",
                    id : 'thread_actions_container',
                    fieldLabel : _('discuss2.thread_actions_container'),
                    description :  _('discuss2.thread_actions_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_actions_container || 'sample.actionsContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[thread_actions_item]",
                    id : 'thread_actions_item',
                    fieldLabel : _('discuss2.thread_actions_item'),
                    description :  _('discuss2.thread_actions_item_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_actions_item || 'sample.actionsItem'
                },{
                    xtype : 'textfield',
                    name : "properties[post_actions_container]",
                    id : 'post_actions_container',
                    fieldLabel : _('discuss2.post_actions_container'),
                    description :  _('discuss2.post_actions_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.post_actions_container || 'sample.actionsContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[post_actions_item]",
                    id : 'post_actions_item',
                    fieldLabel : _('discuss2.post_actions_item'),
                    description :  _('discuss2.post_actions_item_desc'),
                    listeners : oc,
                    value : config.forum.properties.post_actions_item || 'sample.actionsItem'
                }]
            }]
        },{
            title : _('discuss2.category'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items: [{
                xtype : 'fieldset',
                title : _('discuss2.category_chunks'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[categories_container]",
                    id : 'categories_container',
                    fieldLabel : _('discuss2.categories_container'),
                    description :  _('discuss2.categories_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.categories_container || 'sample.categoriesContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[categories_category_chunk]",
                    id : 'categories_category_chunk',
                    fieldLabel : _('discuss2.categories_category_chunk'),
                    description :  _('discuss2.categories_category_chunk_desc'),
                    listeners : oc,
                    value : config.forum.properties.categories_category_chunk || 'sample.categoryChunk'
                },{
                    xtype : 'textfield',
                    name : "properties[categories_board_row]",
                    id : 'categories_board_row',
                    fieldLabel : _('discuss2.categories_board_row'),
                    description :  _('discuss2.categories_board_row_desc'),
                    listeners : oc,
                    value : config.forum.properties.categories_board_row || 'sample.boardRow'
                },{
                    xtype : 'textfield',
                    name : "properties[categories_subboard_container]",
                    id : 'categories_subboard_container',
                    fieldLabel : _('discuss2.categories_subboard_container'),
                    description :  _('discuss2.categories_subboard_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.categories_subboard_container || 'sample.subBoardContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[categories_subboard_row]",
                    id : 'categories_subboard_row',
                    fieldLabel : _('discuss2.categories_subboard_row'),
                    description :  _('discuss2.categories_subboard_row_desc'),
                    listeners : oc,
                    value : config.forum.properties.subboard_row || 'sample.subBoardRow'
                }]
            }]
        },{
            title : _('discuss2.boards'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items: [{
                xtype : 'fieldset',
                title : _('discuss2.board_chunks'),
                items : [{
                xtype : 'textfield',
                    name : "properties[thread_row_container]",
                    id : 'thread_row_container',
                    fieldLabel : _('discuss2.thread_row_container'),
                    description :  _('discuss2.thread_row_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_row_container || 'sample.threadcontainer'
                },{
                    xtype : 'textfield',
                    name : "properties[thread_row_chunk]",
                    id : 'thread_row_chunk',
                    fieldLabel : _('discuss2.thread_row_chunk'),
                    description :  _('discuss2.thread_row_chunk_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_row_chunk || 'sample.threadRow'
                },{
                    xtype : 'textfield',
                    name : "properties[subboard_container]",
                    id : 'subboard_container',
                    fieldLabel : _('discuss2.subboard_container'),
                    description :  _('discuss2.subboard_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.subboard_container || 'sample.subboardcontainer'
                },{
                    xtype : 'textfield',
                    name : "properties[subboard_row]",
                    id : 'subboard_row',
                    fieldLabel : _('discuss2.subboard_row'),
                    description :  _('discuss2.subboard_row'),
                    listeners : oc,
                    value : config.forum.properties.subboard_row || 'sample.subboard_row'
                }]
            }]
        },{
            title : _('discuss2.threads'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'combo-boolean',
                name : "properties[threads_sticky]",
                hiddenName : "properties[threads_sticky]",
                id : 'threads-sticky',
                fieldLabel : _('discuss2.threads_sticky'),
                description : _('discuss2.threads_sticky_desc'),
                listeners : oc,
                value : config.forum.properties.threads_sticky || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-sticky',
                html: _('discuss2.threads_sticky_desc'),
                cls: 'desc-under'
            },{
                xtype : 'combo-boolean',
                name : "properties[threads_hot]",
                hiddenName : "properties[threads_hot]",
                id : 'threads-hot',
                fieldLabel : _('discuss2.threads_hot'),
                description : _('discuss2.threads_hot_desc'),
                listeners : oc,
                value : config.forum.properties.threads_hot || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-hot',
                html: _('discuss2.threads_hot_desc'),
                cls: 'desc-under'
            },{
                xtype : 'numberfield',
                name : "properties[threads_hot_threshold]",
                id : 'threads-hot-threshold',
                fieldLabel : _('discuss2.threads_hot_threshold'),
                description :  _('discuss2.threads_hot_threshold_desc'),
                listeners : oc,
                value : config.forum.properties.threads_hot_threshold || 10
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-hot-threshold',
                html: _('discuss2.threads_hot_threshold_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : "properties[threads_cool_down]",
                hiddenName : "properties[threads_cool_down]",
                id : 'threads-cool-down',
                fieldLabel : _('discuss2.threads_cool_down'),
                description : _('discuss2.threads_cool_down_Desc'),
                listeners : oc,
                value : config.forum.properties.threads_cool_down || false
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-cool-down',
                html: _('discuss2.threads_cool_down_Desc'),
                cls: 'desc-under'
            },{
                xtype : 'numberfield',
                name : "properties[threads_cool_down_time]",
                id : 'threads-cool-down-time',
                fieldLabel : _('discuss2.threads_cool_down_time'),
                description :  _('discuss2.threads_cool_down_time_desc'),
                listeners : oc,
                value : config.forum.properties.threads_cool_down_time || 7
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-cool-down-time',
                html: _('discuss2.threads_cool_down_time_desc'),
                cls: 'desc-under'

            },{
                xtype : 'fieldset',
                title : _('discuss2.thread_chunks'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[thread_posts_container]",
                    id : 'thread_posts_container',
                    fieldLabel : _('discuss2.thread_posts_container'),
                    description :  _('discuss2.thread_posts_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_posts_container || 'sample.postsContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[thread_post_chunk]",
                    id : 'thread_post_chunk',
                    fieldLabel : _('discuss2.thread_post_chunk'),
                    description :  _('discuss2.thread_post_chunk_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_post_chunk || 'sample.postRow'
                },{
                    xtype : 'textfield',
                    name : "properties[thread_mod_actions_container]",
                    id : 'thread_mod_actions_container',
                    fieldLabel : _('discuss2.thread_mod_actions_container'),
                    description :  _('discuss2.thread_mod_actions_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_mod_actions_container || 'sample.actionsContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[thread_mod_actions_button]",
                    id : 'thread_mod_actions_button',
                    fieldLabel : _('discuss2.thread_mod_actions_button'),
                    description :  _('discuss2.thread_mod_actions_button_desc'),
                    listeners : oc,
                    value : config.forum.properties.thread_mod_actions_button || 'sample.actionsAction'
                }]
            }]
        },{
            title : _('discuss2.posts'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'combo-boolean',
                name : "properties[posts_threaded]",
                hiddenName : "properties[posts_threaded]",
                id : 'posts-threaded',
                fieldLabel : _('discuss2.posts_threaded'),
                description : _('discuss2.posts_threaded_desc'),
                listeners : oc,
                value : config.forum.properties.posts_threaded || false
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'posts-threaded',
                html: _('discuss2.posts_threaded_desc'),
                cls: 'desc-under'

            },{
                xtype : 'numberfield',
                name : "properties[posts_depth]",
                id : 'posts-depth',
                fieldLabel : _('discuss2.posts_depth'),
                description :  _('discuss2.posts_depth_desc'),
                listeners : oc,
                value : config.forum.properties.posts_depth || 3
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'posts-depth',
                html: _('discuss2.posts_depth_desc'),
                cls: 'desc-under'
            },{
                xtype : 'textfield',
                name : "properties[parser_class]",
                id : 'parser-class',
                fieldLabel : _('discuss2.parser_class'),
                description :  _('discuss2.parser_class_desc'),
                listeners : oc,
                value : config.forum.properties.parser_class || 'disBBCodeParser'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'parser-class',
                html: _('discuss2.parser_class_desc'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[parser_class_path]",
                id : 'parser-class-path',
                fieldLabel : _('discuss2.parser_class_path'),
                description :  _('discuss2.parser_class_path_desc'),
                listeners : oc,
                value : config.forum.properties.parser_class_path || ''
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'parser-class-path',
                html: _('discuss2.parser_class_path_desc'),
                cls: 'desc-under'

            },{
                xtype : 'numberfield',
                name : "properties[post_excerpt]",
                id : 'post-excerpt',
                fieldLabel : _('discuss2.post_excerpt'),
                description :  _('discuss2.post_excerpt_desc'),
                listeners : oc,
                value : config.forum.properties.post_excerpt || 100
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'post-excerpt',
                html: _('discuss2.post_excerpt_desc'),
                cls: 'desc-under'
            },{
                xtype : 'numberfield',
                name : "properties[post_maximum_length]",
                id : 'post-maximum-length',
                fieldLabel : _('discuss2.post_maximum_length'),
                description :  _('discuss2.post_maximum_length_desc'),
                listeners : oc,
                value : config.forum.properties.post_maximum_length_desc || 3000
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'post-maximum-length',
                html: _('discuss2.post_maximum_length_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : "properties[allow_attachments]",
                hiddenName : "properties[allow_attachments]",
                id : 'allow-attachments',
                fieldLabel : _('discuss2.allow_attachments'),
                description : _('discuss2.allow_attachments_desc'),
                listeners : oc,
                value : config.forum.properties.allow_attachments || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'allow-attachments',
                html: _('discuss2.allow_attachments_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : "properties[allow_attachments_script]",
                hiddenName : "properties[allow_attachments_script]",
                id : 'allow-attachments-script',
                fieldLabel : _('discuss2.allow_attachments_script'),
                description : _('discuss2.allow_attachments_script_desc'),
                listeners : oc,
                value : config.forum.properties.allow_attachments_script || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'allow-attachments-script',
                html: _('discuss2.allow_attachments_script_desc'),
                cls: 'desc-under'

            },{
                xtype : 'numberfield',
                name : "properties[attachment_size]",
                id : 'attachment-size',
                fieldLabel : _('discuss2.attachment_size'),
                description :  _('discuss2.attachment_size_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_size || 1000
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-size',
                html: _('discuss2.attachment_size_desc'),
                cls: 'desc-under'

            },{
                xtype : 'numberfield',
                name : "properties[attachment_num]",
                id : 'attachment-num',
                fieldLabel : _('discuss2.attachment_num'),
                description :  _('discuss2.attachment_num_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_num || 3
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-num',
                html: _('discuss2.attachment_num_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : "properties[attachment_anonymous]",
                hiddenName : "properties[attachment_anonymous]",
                id : 'attachment-anonymous',
                fieldLabel : _('discuss2.attachment_anonymous'),
                description : _('discuss2.attachment_anonymous_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_anonymous || false
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-anonymous',
                html: _('discuss2.attachment_anonymous_desc'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[attachment_extensions]",
                id : 'attachment-extensions',
                fieldLabel : _('discuss2.attachment_extensions'),
                description :  _('discuss2.attachment_extensions_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_extensions || 'png,jpg,jpeg,gif,sql,xml'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-extension',
                html: _('discuss2.attachment_extensions_desc'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[attachment_path]",
                id : 'attachment-path',
                fieldLabel : _('discuss2.attachment_path'),
                description :  _('discuss2.attachment_path_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_path || '{base_path}/assets/discuss2/attachments'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-path',
                html: _('discuss2.attachment_path_desc'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[attachment_url]",
                id : 'attachment-url',
                fieldLabel : _('discuss2.attachment_url'),
                description :  _('discuss2.attachment_extensions_desc'),
                listeners : oc,
                value : config.forum.properties.attachment_path || '/assets/discuss2/attachments'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'attachment-url',
                html: _('discuss2.attachment_extensions_desc'),
                cls: 'desc-under'

            }]
        },{
            title : _('discuss2.moderation'),
            anchor : '100%',
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'textfield',
                name : "properties[moderation_email_chunk]",
                id : 'moderation-email-chunk',
                fieldLabel : _('discuss2.moderation_mail_chunk'),
                description :  _('discuss2.moderation_email_chunk_desc'),
                listeners : oc,
                value : config.forum.properties.moderation_email_chunk || 'dis2ModEmail'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'moderation-email-chunk',
                html: _('discuss2.moderation_mail_chunk'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[moderation_email_subject]",
                id : 'moderation-email-subject',
                fieldLabel : _('discuss2.moderation_email_subject'),
                description : _('discuss2.moderation_email_subject_desc'),
                listeners : oc,
                value : config.forum.properties.moderation_email_chunk || 'Post awaits moderation'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'moderation-email-subject',
                html: _('discuss2.moderation_email_subject_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo',
                hiddenName : "properties[global_moderators]",
                name : "properties[global_moderators]",
                id : 'global-moderators',
                fieldLabel : _('discuss2.global_moderators_group'),
                description : _('discuss2.global_moderators_group_desc'),
                listeners : oc /** TODO: implement listener or check modx core widgets if one is available **/
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'global-moderators',
                html: _('discuss2.global_moderators_group_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'combo-boolean',
                name : "properties[notify_bad_words]",
                hiddenName : "properties[notify_bad_words]",
                id : 'notify-bad-words',
                fieldLabel : _('discuss2.notify_bad_words'),
                description : _('discuss2.notify_bad_words_desc'),
                listeners : oc,
                value : config.forum.properties.notify_bad_words || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'notify-bad-words',
                html: _('discuss2.notify_bad_words_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'textarea',
                name : "properties[bad_words_list]",
                id : 'bad-words-list',
                fieldLabel : _('discuss2.bad_words_list'),
                description : _('discuss2.bad_words_list_desc'),
                listeners : oc,
                value : config.forum.properties.bad_words_list || 'porn'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'bad_words_list',
                html: _('discuss2.bad_words_list_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'combo-boolean',
                name : "properties[censor_bad_words]",
                hiddenName : "properties[censor_bad_words]",
                id : 'censor-bad-words',
                fieldLabel : _('discuss2.censor_bad_words'),
                description : _('discuss2.censor_bad_words_desc'),
                listeners : oc,
                value : config.forum.properties.censor_bad_words || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'censor-bad-words',
                html: _('discuss2.censor_bad_words_desc'),
                cls: 'desc-under'

            },{
                xtype : 'textfield',
                name : "properties[censor_replacement]",
                id : 'censor-replacement',
                fieldLabel : _('discuss2.censor_replacement'),
                description : _('discuss2.censor_replacement_desc'),
                listeners : oc,
                value : config.forum.properties.censor_replacement || '*#!?£'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'censor-replacement',
                html: _('discuss2.censor_replacement_desc'),
                cls: 'desc-under'

            }]
        },{
            title : _('discuss2.pagination'),
            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'numberfield',
                name : "properties[threads_per_page]",
                id : 'threads-per-page',
                fieldLabel : _('discuss2.threads_per_page'),
                description : _('discuss2.threads_per_page_desc'),
                listeners : oc,
                value : config.forum.properties.threads_per_page || 20
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'threads-per-page',
                html: _('discuss2.threads_per_page_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : 'properties[enable_posts_pagination]',
                id : 'enable_posts_pagination',
                hiddenName : "properties[enable_posts_pagination]",
                fieldLabel : _('discuss2.enable_posts_pagination'),
                description : _('discuss2.enable_posts_pagination_desc'),
                listeners : oc,
                value : config.forum.properties.enable_posts_pagination || true
            },{
                xtype : 'numberfield',
                name : "properties[posts_per_page]",
                id : 'posts-per-page',
                fieldLabel : _('discuss2.posts_per_page'),
                description : _('discuss2.posts_per_page_desc'),
                listeners : oc,
                value : config.forum.properties.posts_per_page || 10
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'posts-per-page',
                html: _('discuss2.posts_per_page_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'textfield',
                name : "properties[pagination_var]",
                id : 'pagination-var',
                fieldLabel : _('discuss2.pagination_var'),
                description : _('discuss2.pagination_var_desc'),
                listeners : oc,
                value : config.forum.properties.pagination_var || 'page'
            },{
                xtype : 'fieldset',
                title : _('discuss2.pagination_texts'),
                items : [{
                    xtype : 'combo-boolean',
                    name : "properties[pagination_show_prev_next]",
                    id : 'pagination-show-prev-next',
                    fieldLabel : _('discuss2.pagination_show_prev_next'),
                    listeners : oc,
                    value : config.forum.properties.pagination_show_prev_next || true
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_prev_text]",
                    id : 'pagination-prev-text',
                    fieldLabel : _('discuss2.pagination_prev_text'),
                    listeners : oc,
                    value : config.forum.properties.pagination_prev_text || _('discuss2.pagination_prev')
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_next_text]",
                    id : 'pagination-next-text',
                    fieldLabel : _('discuss2.pagination_next_text'),
                    listeners : oc,
                    value : config.forum.properties.pagination_next_text || _('discuss2.pagination_next')
                },{
                    xtype : 'combo-boolean',
                    name : "properties[pagination_show_first_last]",
                    id : 'pagination-show-first-last',
                    fieldLabel : _('discuss2.pagination_show_first_last'),
                    listeners : oc,
                    value : config.forum.properties.pagination_show_first_last || true
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_first_text]",
                    id : 'pagination-first-text',
                    fieldLabel : _('discuss2.pagination_first_text'),
                    listeners : oc,
                    value : config.forum.properties.pagination_first_text ||  _('discuss2.pagination_first')
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_last_text]",
                    id : 'pagination-last-text',
                    fieldLabel : _('discuss2.pagination_last_text'),
                    listeners : oc,
                    value : config.forum.properties.pagination_last_text ||  _('discuss2.pagination_last')
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_proximity_placeholder]",
                    id : 'pagination-promixity-placeholder',
                    fieldLabel : _('discuss2.pagination_proximity_placeholder_desc'),
                    listeners : oc,
                    value : config.forum.properties.pagination_proximity_placeholder || _('discuss2.pagination_proximity_placeholder')
                }]
            },{
                xtype : 'fieldset',
                title : _('discuss2.pagination_chunks'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[pagination_container]",
                    id : 'pagination-container',
                    fieldLabel : _('discuss2.pagination_container'),
                    listeners : oc,
                    value : config.forum.properties.pagination_container || 'sample.container'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_item_chunk]",
                    id : 'pagination-item-chunk',
                    fieldLabel : _('discuss2.pagination_item_chunk'),
                    listeners : oc,
                    value : config.forum.properties.pagination_item_chunk || 'sample.item'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_empty_item_chunk]",
                    id : 'pagination-empty-item-chunk',
                    fieldLabel : _('discuss2.pagination_empty_item_chunk'),
                    listeners : oc,
                    value : config.forum.properties.pagination_empty_item_chunk || 'sample.empty_item'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_first_class]",
                    id : 'pagination-first-class',
                    fieldLabel : _('discuss2.pagination_first_class'),
                    listeners : oc,
                    value : config.forum.properties.pagination_first_class || 'dis-pagination-first'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_last_class]",
                    id : 'pagination-last-class',
                    fieldLabel : _('discuss2.pagination_last_class'),
                    listeners : oc,
                    value : config.forum.properties.pagination_last_class || 'dis-pagination-last'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_previous_class]",
                    id : 'pagination-previous-class',
                    fieldLabel : _('discuss2.pagination_previous_class'),
                    listeners : oc,
                    value : config.forum.properties.pagination_previous_class || 'dis-pagination-prev'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_next_class]",
                    id : 'pagination-next-class',
                    fieldLabel : _('discuss2.pagination_next_class'),
                    listeners : oc,
                    value : config.forum.properties.pagination_next_class || 'dis-pagination-next'
                },{
                    xtype : 'textfield',
                    name : "properties[pagination_active_class]",
                    id : 'pagination-active-class',
                    fieldLabel : _('discuss2.pagination_active_class'),
                    listeners : oc,
                    value : config.forum.properties.pagination_active_class || 'dis-pagination-active'
                }]
            }]
        },{
            title : _('discuss2.users'),
            default : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'combo-boolean',
                name : "properties[show_online]",
                hiddenName : "properties[show_online]",
                id : 'show-online',
                fieldLabel : _('discuss2.show_online'),
                description : _('discuss2.show_online_desc'),
                listeners : oc,
                value : config.forum.properties.show_online || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'show-online',
                html: _('discuss2.show_online_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'combo-boolean',
                name : "properties[custom_titles]",
                hiddenName : "properties[custom_titles]",
                id : 'custom-titles',
                fieldLabel : _('discuss2.custom_titles'),
                description : _('discuss2.custom_titles_desc'),
                listeners : oc,
                value : config.forum.properties.custom_titles || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'custom-titles',
                html: _('discuss2.custom_titles_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'numberfield',
                name : "properties[signature_length]",
                id : 'signature-length',
                fieldLabel : _('discuss2.signature_length'),
                description : _('discuss2.signature_length_desc'),
                listeners : oc,
                value : config.forum.properties.signature_length_desc || 500
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'signature-length',
                html: _('discuss2.signature_length_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo-boolean',
                name : "properties[enable_notifications]",
                hiddenName : "properties[enable_notifications]",
                id : 'enable-notifications',
                fieldLabel : _('discuss2.enable_notifications'),
                description : _('discuss2.enable_notifications_desc'),
                listeners : oc,
                value : config.forum.properties.enable_notifications || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'enable-notifications',
                html: _('discuss2.enable_notifications_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'textfield',
                name : "properties[notifications_email]",
                id : 'notifications-email',
                fieldLabel : _('discuss2.notifications_email'),
                description : _('discuss2.notifications_email_desc'),
                listeners : oc,
                value : config.forum.properties.notifications_email || 'dis2NotificationEmail'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'notifications-email',
                html: _('discuss2.notifications_email_desc'),
                cls: 'desc-under'

            }, {
                xtype : 'textfield',
                name : "properties[notifications_subject]",
                id : 'notifications-subject',
                fieldLabel : _('discuss2.notifications_subject'),
                description : _('discuss2.notifications_subject_desc'),
                listeners : oc,
                value : config.forum.properties.notifications_subject || 'Reply to Thread [[+name]] Was Posted'
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'notifications-subject',
                html: _('discuss2.notifications_subject_desc'),
                cls: 'desc-under'

            },{
                xtype : 'fieldset',
                title : _('discuss2.user_actions'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[user_actions_container]",
                    id : 'user_actions_container',
                    fieldLabel : _('discuss2.user_actions_container'),
                    description : _('discuss2.user_actions_container_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_actions_container || 'sample.userActionContainer'
                },{
                    xtype : 'textfield',
                    name : "properties[user_actions_item]",
                    id : 'user_actions_item',
                    fieldLabel : _('discuss2.user_actions_item'),
                    description : _('discuss2.user_actions_item_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_actions_item || 'sample.userActionItem'
                }]
            },{
                xtype : 'fieldset',
                title : _('discuss2.user_pages'),
                items : [{
                    xtype : 'textfield',
                    name : "properties[user_pages_profile]",
                    id : 'user_pages_profile',
                    fieldLabel : _('discuss2.user_pages_profile'),
                    description : _('discuss2.user_pages_profile_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_pages_profile || 'sample.profile'
                },{
                    xtype : 'textfield',
                    name : "properties[user_pages_edit_profile]",
                    id : 'user_pages_edit_profile',
                    fieldLabel : _('discuss2.user_pages_edit_profile'),
                    description : _('discuss2.user_pages_edit_profile_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_pages_edit_profile || 'sample.editProfile'
                },{
                    xtype : 'textfield',
                    name : "properties[user_pages_pms]",
                    id : 'user_pages_pms',
                    fieldLabel : _('discuss2.user_pages_pms'),
                    description : _('discuss2.user_pages_pms_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_pages_pms || 'sample.privateMessages'
                },{
                    xtype : 'textfield',
                    name : "properties[user_pages_send_pm]",
                    id : 'user_pages_send_pm',
                    fieldLabel : _('discuss2.user_pages_send_pm'),
                    description : _('discuss2.user_pages_send_pm_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_pages_send_pm || 'sample.sendPrivateMessage'
                },{
                    xtype : 'textfield',
                    name : "properties[user_merge]",
                    id : 'user_merge',
                    fieldLabel : _('discuss2.user_merge'),
                    description : _('discuss2.user_merge_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_merge || 'sample.mergeUser'
                },{
                    xtype : 'textfield',
                    name : "properties[user_view_stats]",
                    id : 'user_view_stats',
                    fieldLabel : _('discuss2.user_view_stats'),
                    description : _('discuss2.user_view_stats_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_view_stats || 'sample.statistics'
                },{
                    xtype : 'textfield',
                    name : "properties[user_view_posts]",
                    id : 'user_view_posts',
                    fieldLabel : _('discuss2.user_view_posts'),
                    description : _('discuss2.user_view_posts_desc'),
                    listeners : oc,
                    value : config.forum.properties.user_view_posts || 'sample.viewPosts'
                },{
                    xtype : 'fieldset',
                    title : 'discuss2.user_posts_chunks',
                    items : [{
                        xtype : 'textfield',
                        name : "properties[view_posts_container]",
                        id : 'view_posts_container',
                        fieldLabel : _('discuss2.view_posts_container'),
                        description : _('discuss2.view_posts_container_desc'),
                        listeners : oc,
                        value : config.forum.properties.view_posts_container || 'sample.viewPostsContainer'
                    },{
                        xtype : 'textfield',
                        name : "properties[view_posts_item]",
                        id : 'view_posts_item',
                        fieldLabel : _('discuss2.view_posts_item'),
                        description : _('discuss2.view_posts_item_desc'),
                        listeners : oc,
                        value : config.forum.properties.view_posts_item || 'sample.viewPostsItem'
                    },{
                        xtype : 'textfield',
                        name : "properties[user_posts_per_page]",
                        id : 'user_posts_per_page',
                        fieldLabel : _('discuss2.user_posts_per_page'),
                        description : _('discuss2.user_posts_per_page_desc'),
                        listeners : oc,
                        value : config.forum.properties.user_posts_per_page || 20
                    }]
                }]
            }]
        }, {
            title : _('discuss2.search'),

            defaults : {
                msgTarget : 'under'
            },
            items : [{
                xtype : 'combo-boolean',
                name : "properties[use_discuss_search]",
                hiddenName : "properties[use_discuss_search]",
                id : 'use-discuss-search',
                fieldLabel : _('discuss2.use_discuss_search'),
                description : _('discuss2.use_discuss_search_desc'),
                listeners : oc,
                value : config.forum.properties.use_discuss_search || true
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'statistics-interval',
                html: _('discuss2.use_discuss_search_desc'),
                cls: 'desc-under'

            },{
                xtype : 'combo',
                name : "properties[search_engine]",
                hiddenName : "properties[search_engine]",
                id : 'search-engine',
                fieldLabel : _('discuss2.search_engine'),
                description : _('discuss2.search_engine_desc'),
                triggerAction : 'all',
                typeAhead : true,
                listeners : oc,
                store : [[_('discuss2.search_engine_discuss_value'), _('discuss2.search_engine_discuss')],
                        [_('discuss2.search_engine_solr_value'), _('discuss2.search_engine_solr')],
                        [_('discuss2.search_engine_sphinx_value'), _('discuss2.search_engine_sphinx')]],
                listeners : {
                    render : function(field) {
                        var val = config.forum.search_engine ? config.forum.search_engine : field.getStore().getAt(0).get('field1');
                        field.setValue(val);
                    },
                    'change':{fn:MODx.fireResourceFormChange}
                    ,'select':{fn:MODx.fireResourceFormChange}
                }
            },{
                xtype: MODx.expandHelp ? 'label' : 'hidden',
                forId: 'search-engine',
                html: _('discuss2.search_engine_desc'),
                cls: 'desc-under'
            }]
        }]
    });
    Discuss2.panel.ForumProperties.superclass.constructor.call(this,config);
}

Ext.extend(Discuss2.panel.ForumProperties, MODx.VerticalTabs, {});
Ext.reg('discuss2-forum-properties', Discuss2.panel.ForumProperties);