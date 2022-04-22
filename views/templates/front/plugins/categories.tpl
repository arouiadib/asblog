<div class="block">
    {if isset($categories) AND !empty($categories)}
        <div id="category_blog_block_left"  class="block blogModule boxPlain">
            <h2 class='sdstitle_block'>
                <a href="{*{smartblog::GetSmartBlogLink('smartblog_list')}*}">{l s='Blog Categories' d='Modules.Asblog.Shop'}</a>
            </h2>
            <div class="block_content list-block">
                <ul>
                    {foreach from=$categories item="category"}
                        {assign var="options" value=null}
                        {$options.id_category = $category.id_category}
                        {$options.slug = $category.link_rewrite}
                        <li>
                            <a href="{*{smartblog::GetSmartBlogLink('smartblog_category',$options)}*}">{$category.name} [{$category.count}]</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {/if}
</div>

