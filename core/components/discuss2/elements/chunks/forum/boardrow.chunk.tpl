<div class="Depth2 row dis-category h-group dis-category-[[+parent]]">
    <a href="[[+link]]" class="h-group">
        <div class="f1-f7">
            <div class="wrap">
                <span class="folder">UNREAD COUNT HERE</span>
                <strong>[[+pagetitle]]</strong>
                <p class="dis-board-description">[[+description]]</p>
            </div>
        </div>
        <div class="f8-f10">
            [[+lastpost.id:notempty=`
            <span class="clickable" data-link="[[+lastpost.link]]">[[+lastpost.thread_title:default=`&nbsp;`]]</span>
            `]]
        </div>
        <div class="f11 l-txtcenter">[[+total_posts:empty=`0`]]</div>
        <div class="f12 l-txtcenter">[[+total_threads:empty=`0`]]</div>
    </a>
    <!--[![+subforums:notempty=`<div class="h-group f-all"><p class="dis-board-subs [[+unread-cls]]">[[+subforums]]</p></div>`]]-->
</div>
