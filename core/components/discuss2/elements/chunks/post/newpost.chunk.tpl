[[!FormIt?
&submitVar=`dis-post-reply`
&hooks=`discuss2.newPost`
&validate=`pagetitle:required,content:required:allowTags`
]]


<form action="[[~[[*id]]]]" method="post" class="dis-form dis-thread-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
    <input type="hidden" id="dis-quick-reply-board"  name="parent"  value="" />
    <input type="text" id="dis-quick-reply-title"  name="pagetitle"  value="Re: [[*pagetitle]]" />

    <div class="wysi-buttons">[[+reply_buttons]]</div>
    <div class="h-group">
        <textarea name="content" id="dis-thread-message" tabindex="10">[[+message]]</textarea>
    </div>
    <span class="error">[[!+fi.error.content]]</span>
    <div class="h-group below-wysi">
        <div class="l-left">[[+attachment_fields]]</div>
        <div class="dis-form-buttons l-right">
            <input class="a-reply" type="submit" name="dis-post-reply" value="Reply" tabindex="40"/>
            <div class="group">
                [[+locked_cb]]
                [[+sticky_cb]]
                <label class="dis-cb">
                    <input type="checkbox" name="notify" value="1" [[+subscribed]]  tabindex="38"/>[[%discuss.subscribe_by_email]]
                </label>
            </div>
        </div>
    </div>
    [[+discuss.error_panel]]
</form>