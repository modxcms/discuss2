[[!FormIt?
    &submitVar=`[[+submitVar]]`
    &hooks=`discuss2.postReply`
]]
<form action="[[+action]]" method="post">
    <input type="hidden" name="parent" value="[[+parent]]">
    <input type="text" name="pagetitle" value="[[+pagetitle]]">
    <textarea name="content"></textarea>
    <button type="submit" name="[[+submitVar]]" value="reply">Reply</button>
</form>