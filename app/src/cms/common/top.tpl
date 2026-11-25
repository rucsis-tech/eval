{if $template_type=='body'}
    <h3>USER</h3>
    <div class="container-fluid mb-2 border border-4">
        uid: {$Admin->uid}
        <br>
        email: {$User->auth_email}
        <br>
        name: {$Admin->admin_name} ({$Admin->name})
        <br>
        role: {$Admin->role}
        <br>
    </div>
    <h3>ENV</h3>
    <div class="container-fluid mb-2 border border-4">
        BASE_URL: {BASE_URL}
    </div>
    {if ($Admin->developer == User::DEVELOPER_ADMIN)}
        <h3>ERRORS</h3>
        <div class="container-fluid mb-2 border border-4" style="overflow:hidden;">
            <table class="table table-sm" style="max-width:100%;overflow: hidden;">
                {foreach $Errors AS $Error}
                    <tr>
                        <td style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">{$Error->created_at}</td>
                        <td style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;max-width:10rem;">{$Error->uniq}</td>
                        <td style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">{$Error->kind}</td>
                        <td style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">{$Error->message|escape}</td>
                    </tr>
                {/foreach}
            </table>
        </div>
        <h3>SERVER</h3>
        <div class="container-fluid mb-2 border border-4">
            {foreach $_SERVER AS $key=>$value}
                {$key}: {$value}
                <br>
            {/foreach}
        </div>
        <h3>PHPINFO</h3>
        <iframe class="container-fluid mb-2 border border-4" style="height:500px" srcdoc="{phpinfo|escape}"></iframe>
    {/if}
{/if}
