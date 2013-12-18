[![+trail]]

[[!FormIt?
&submitVar=`[[+discuss2.properties.form_submit_var]]`
&hooks=`discuss2.newThread`
&validate=`pagetitle:required,content:required:allowTags`
]]


[[+form.preview.pagetitle:notempty=`
<div id="dis-new-thread-preview">
    <div class="preview-pagetitle">[[+form.preview.pagetitle]]</div>
    <div class="preview-content">[[+form.preview.content]]</div>
</div>
`]]

<form action="[[~[[*id]]]]&action=new/thread" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">

    <h2>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h2>
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="pagetitle" id="dis-new-thread-title" value="[[+form.pagetitle]]" />

    <label for="dis-new-thread-type">[[%discuss.thread_type]]</label>
    <select name="thread" id="dis-new-thread-type">
        <option value="disThreadDiscussion"[[+form.thread:eq=`disThreadDiscussion`:then=` selected="selected"`]]>[[%discuss.discussion]]</option>
        <option value="disThreadQuestion"[[+form.thread:eq=`disThreadQuestion`:then=` selected="selected"`]]>[[%discuss.question_and_answer]]</option>
    </select>

    <div style="margin-left: 150px;">
        <br class="clear" />
        [[+buttons]]
        <br class="clear" />
    </div>

    <label for="dis-thread-message">[[%discuss.content]]:
        <span class="error">[[!+fi.error.content]]</span>
    </label>
    <textarea name="content" id="dis-thread-message" cols="80" rows="7">[[+form.content]]</textarea>

    <input type="file" name="attachments[]">
    <input type="file" name="attachments[]">
    <input type="file" name="attachments[]">
    <br class="clear" />


    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="[[+discuss2.properties.form_submit_var]]" value="[[%discuss2.thread_post_new]]" />
        <input type="submit" class="dis-action-btn dis-new-thread-preview" name="[[+discuss2.properties.form_preview_var]]" value="[[%discuss2.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss2.cancel]]" onclick="" />
    </div>
</form>
[[+discuss.error_panel]]