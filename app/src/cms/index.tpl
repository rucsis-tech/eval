<!doctype html>
<html lang="ja">
{include file="common/head.tpl"}
<bodY>
{if ($Admin)}
    {include file="common/navigation.tpl"}
{/if}

<main class="container-fluid" style="position:relative;">
    {if (!empty($template_file))}
        {include file="$template_file" template_type="body"}
    {/if}
</main>
<script src="/js/admin/bootstrap.bundle.min.js"></script>
{if (!empty($template_file))}
    {include file="$template_file" template_type="foot"}
{/if}

<div id="loading" class="loading" style="visibility: hidden;">
    <p class="loading-text">Loading...</p>
    <div class="spinner"></div>
</div>

{literal}
    <script>
        function checkLogin() {
            if (document.location.href.endsWith('/cms')) {
                console.log('no login check');
                setTimeout(checkLogin, 1000 * 60 * 3);
                return
            }
            fetch('/cms/_check').then((data) => {
                return data.json()
            }).then((response) => {
                    console.log('login check ok');
                    if (response.status === 'OK') {
                        setTimeout(checkLogin, 1000 * 60 * 3);
                    } else {
                        alert('ログインしていません')
                    }
                }
            )
        }

        checkLogin();
    </script>
{/literal}

</bodY>
</html>