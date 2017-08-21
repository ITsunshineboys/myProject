<form enctype="multipart/form-data" action="/site/login" method="post">
    <input name="username" />
    <input name="password" type="password" />
    <input type="submit" value="Login" />
</form>

<form enctype="multipart/form-data" action="/site/admin-login" method="post">
    <input name="username" />
    <input name="password" type="password" />
    <input name="role_id" placeholder="role id" />
    <input type="submit" value="Admin Login" />
</form>