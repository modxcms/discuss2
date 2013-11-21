[![+trail]]

[[!FormIt?
&submitVar=`dis-post-new`
&hooks=`discuss2.newThread`
&validate=`pagetitle:required,content:required:allowTags`
]]


<div id="dis-new-thread-preview"></div>
<br />
<form action="[[~[[*id]]]]&action=new/thread" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">

    <h2>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h2>
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="pagetitle" id="dis-new-thread-title" value="[[!+fi.title]]" />

    <label for="dis-new-thread-type">[[%discuss.thread_type]]</label>
    <select name="thread" id="dis-new-thread-type">
        <option value="disThreadDiscussion">[[%discuss.discussion]]</option>
        <option value="disThreadQuestion">[[%discuss.question_and_answer]]</option>
    </select>

    <div style="margin-left: 150px;">
        <br class="clear" />
        [[+buttons]]
        <br class="clear" />
    </div>

    <label for="dis-thread-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="content" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

    <input type="file" name="attachments[]">
    <input type="file" name="attachments[]">
    <input type="file" name="attachments[]">
    <br class="clear" />


    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-post-new" value="[[%discuss.thread_post_new]]" />
        <input type="button" class="dis-action-btn dis-new-thread-preview" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="" />
    </div>
</form>
[[+discuss.error_panel]]