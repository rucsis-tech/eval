{if $template_type=='body'}
    <form action="/cms/login" method="POST" class="form-signin w-100 m-auto">
        <!--h1 class="h3 mb-3 fw-normal">Please sign in</h1-->

        <div class="form-floating">
            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com">
            <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
            <label for="floatingPassword">Password</label>
        </div>

        <input type="hidden" name="redirect_url" value="/cms">

        <!--div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Remember me
            </label>
        </div-->
        <button class="btn btn-primary w-100 py-2" type="submit">ログイン</button>
    </form>
{/if}