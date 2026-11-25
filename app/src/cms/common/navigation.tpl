<header class="mb-3 border-bottom" style="background-color:{$head_color};">
    <div class="">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">

            <a href="/cms" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
                <div class="me-2" role="img" aria-label="Eval" style="display:flex;align-items: center;">
                    <img style="width: 125px;margin:4px;" src="/images/cms/logo.svg">
                    <span style="font-size:38px;font-family: 'Arial Black';color:#4AA;vertical-align:bottom;">CMS</span>
                </div>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">

                <li class="dropdown mx-2">
                    <a href="#" class="d-flex align-items-center col-lg-4 mb-2 mb-lg-0 link-body-emphasis text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                        ページ表示管理
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="/cms/topic/">トピックス</a></li>
                        <li><a class="dropdown-item" href="/cms/news/">速報</a></li>
                        <li><a class="dropdown-item" href="/cms/info/">お知らせ</a></li>
                    </ul>
                </li>

                <li class="dropdown mx-2">
                    <a href="#" class="d-flex align-items-center col-lg-4 mb-2 mb-lg-0 link-body-emphasis text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                        ライセンス商品管理
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="/cms/tool/">攻略ツール（現時点では仕様検討中／未確定）</a></li>
                    </ul>
                </li>

                <li class="dropdown mx-2">
                    <a href="#" class="d-flex align-items-center col-lg-4 mb-2 mb-lg-0 link-body-emphasis text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                        レース関連管理
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="/cms/enquete/">アンケート</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/cms/eval_basedata/">Evalベースデータ</a></li>
                    </ul>
                </li>


                <li class="dropdown mx-2">
                    <a href="#" class="d-flex align-items-center col-lg-4 mb-2 mb-lg-0 link-body-emphasis text-decoration-none dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                        集計
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="/cms/license/point" aria-current="page">ライセンス商品集計</a></li>
                    </ul>
                </li>


            </ul>

            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start pe-2">
                <form class="mb-lg-0 me-lg-3 me-2" role="search">
                    {$User->auth_email}
                    {$User->uid}
                    <!--input type="search" class="form-control" placeholder="Search..." aria-label="Search"-->
                </form>

                <div class="dropdown text-end">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/images/cms/icon_user.png" alt="mdo" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small">
                        <!--li class="dropdown-item"><a href="#">Settings</a></li-->
                        <li class="dropdown-item">{$Admin->uid}</li>
                        <li class="dropdown-item">{$Admin->admin_name}</li>
                        <li class="dropdown-item">{$Admin->role}</li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-item"></li>
                        <li class="dropdown-item"><a href="/auth/logout.php?redirect_url=/cms">Sign out</a></li>
                    </ul>
                </div>

            </div>


        </div>
    </div>
</header>