{extends file="layout.tpl"}
{block name='head:title'}geminiai{/block}
{block name='body:id'}geminiai{/block}
{block name='article:header'}
    <h1 class="h2">geminiai</h1>
{/block}
{block name='article:content'}
    {if {employee_access type="view" class_name=$cClass} eq 1}
        <div class="panels row">
            <section class="panel col-ph-12">
                {if $debug}
                    {$debug}
                {/if}
                <header class="panel-header">
                    <h2 class="panel-heading h5">{#geminiai_management#}</h2>
                </header>
                <div class="panel-body panel-body-form">
                    <div class="mc-message-container clearfix">
                        <div class="mc-message"></div>
                    </div>
                    <div class="row">
                        <form id="geminiai_config" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit" method="post" class="validate_form edit_form col-xs-12 col-md-10">
                            <div class="row">
                                <div class="col-xs-12 col-sm-10">
                                    <div class="form-group">
                                        <label for="api_key_gc">{#api_key_gc#} *:</label>
                                        <input type="text" class="form-control required" required id="api_key_gc" name="geminiai[api_key_gc]" value="{$geminiai.api_key_gc}" size="50" />
                                    </div>
                                </div>
                            </div>
                            <div id="submit">
                                <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}