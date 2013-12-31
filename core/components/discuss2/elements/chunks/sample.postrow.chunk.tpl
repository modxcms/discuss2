<li class="[[+classes]] group-fix" id="dis-post-[[+id]]" data-author="[[+user.username:htmlent]]" data-date="[[+createdon_raw]]" data-message="[[+content_raw]]">
    [[+answer:neq=``:then=`
    <div class="dis-post-answer-marker">
        [[+answer_count:gt=`1`:then=`
        <nav>
            [[+answer_prev.link]]
            [[+answer_next.link]]
        </nav>
        `:else=``]]

        [[+url_mark_as_answer:eq=``:then=`
        <span title="[[%discuss.answer]]">[[%discuss.answer]]</span>
        `:else=`
        <a href="[[+url_mark_as_answer]]">
            <span title="[[%discuss.unflag_answer]]">[[%discuss.unflag_answer]]</span>
        </a>
        `]]
    </div>
    `:else=`
    <div class="dis-post-answer-marker dis-post-notanswer">
        [[+url_mark_as_answer:eq=``:then=``:else=`
        <div class="dis-post-answer-marker dis-post-notanswer">
            <p>[[%discuss.flag_answer]]</p>
            <a href="[[+url_mark_as_answer]]">
                <span>[[%discuss.flag_answer]]</span>
            </a>
        </div>
        `]]
    </div>
    `]]
    <!-- mark answer-->
    <div class="dis-post-left">
        <ul>
            <li class="dis-usr-icon">
                <a href=" class="auth-avatar" title="[[%discuss.view_author_profile]]">
                    [[+user.avatar]]
                    [[+user.title:notempty=`<span class="dis-usr-title">[[+user.title]]</span>`]]
                </a>
            </li>
            <li class="dis-usr-post-count">[[+user.posts]] [[%discuss.posts]]</li>
            <a href="" class="dis-pm-btn">Send PM</a>
        </ul>
    </div>
    <div class="dis-post-right">
        <div class="title">
            <strong>[[+user.username]]</strong> <a class="normal-type" href="[[+url]]" title="[[%discuss.post_link]]">Reply #[[+idx]], <span title="[[+createdon]]">[[+createdon:date=`%d-%m-%Y`:ago]]</span></a>
            [[+actions]]
        </div>
        <div class="dis-content">
            [[+content]]
            [[+discuss.user.shouldMarkAnAnswer:eq=`1`:then=`
            [[+idx:eq=`1`:then=`
            <div class="dis-info"><p>[[%discuss.mark_answer_instructions]] <a id="Show-answer-link" href="#">[[%discuss.mark_answer_link]]</a></p></div>
            `:else=``]]
            `:else=``]]
            [[+idx:eq=`1`:then=`
            [[+answer_count:neq=`0`:then=`
            <div class="dis-info"><p>[[+jump_to_first_answer.explanation]] [[+jump_to_first_answer.link]]</p></div>
            `:else=``]]
            `:else=``]]
            [[+attachments:notempty=`<div class="dis-post-attachments"><ul class="dis-attachments">[[+attachments]]</ul></div>`]]
            [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">[[%discuss.editedon_post? &on=`[[+editedon:ago]]` &user=`[[+editedby.username]]`]]</span>`]]
        </div>
        <ul class="dis-action-btn">[[+report_link]][[+action_reply]]</ul>
    </div>
    [[+user.signature:notempty=`<div class="dis-signature">[[+user.signature]]</div>`]]
    [[+replies]]

    [[+form]]

</li>
