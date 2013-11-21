<div class="row l-table h-group dis-category-[[+parent]] ">
    <a class="h-group" href="[[+link]]">
        <div class="f1-f7 l-vmiddle m-title">
            <div class="wrap">
                [[+class_key:eq=`disThreadQuestion`:then=`<span class="question tag">[[%discuss.question]]</span>`:else=``]]
                <strong>[[+pagetitle]]</strong>
                [[+thread_pagination]]
            </div>
        </div>
        <div class="f8 l-vmiddle l-txtcenter">[![+views]]</div>
        <div class="f9 l-vmiddle l-txtcenter">[[+total_posts]]</div>
        <div class="f10-f12">
            <p class="posted-by">[[%discuss.board_by]]: [[+username]], [[+createdon:date=`%d-%m-%Y`:ago]]</p>
            <p class="posted-by">[[%discuss.board_last]]: [[+lastpost.author_uname]], [[+lastpost.createdon:date=`%d-%m-%Y`:ago]]</p>
        </div>
    </a>
</div>
