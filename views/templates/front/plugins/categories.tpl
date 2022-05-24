<div id="blog-categories" class="block">
    {if isset($categories) AND !empty($categories)}
        <div id="blog-categories-left">
            <h2 class="block_title">
                <a href="{$bloglink->getBlogLink('module-asblog-bloglist')}">{l s='Blog Categories' d='Modules.Asblog.Shop'}</a>
            </h2>
            <div class="block_content list-block">
                <ul>
                    {foreach from=$categories item="category"}
                        {if $category.id_category != 1 }
                            <li>
                                <a href="{$bloglink->getBlogCategoryLink($category.id_category, $category.link_rewrite)}">{$category.name} ({$category.count})</a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </div>
    {/if}
</div>

