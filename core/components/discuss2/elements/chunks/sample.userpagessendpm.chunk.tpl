[[FormIt?
    &hooks=`discuss2.sendPM`
]]

<form id="d2-send-pm" action="[[+action]]" method="post">
    <div>
        <label for="d2-user">Users</label>
        <input type="text" id="d2-user" name="users" value="[[+username:empty=``]]">
    </div>
    <div>
        <label for="d2-title">Title</label>
        <input type="text" id="d2-title" name="title">
    </div>
    <div>
        <label for="d2-message">Message</label>
        <textarea name="content"></textarea>
    </div>
    <div>
        <input type="hidden" name="thread" value="[[+thread:empty=``]]">
        <input type="submit" name="post" value="Send PM">
    </div>
</form>