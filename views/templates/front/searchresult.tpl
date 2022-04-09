{extends file='page.tpl'}
{block name='breadcrumb'}
  {if isset($breadcrumb)}
    <nav class="breadcrumb smart-blog-breadcrumb">
      <ol>
          <li>
            <a href="{$breadcrumb.links[0].url}">
              <span itemprop="name">{$breadcrumb.links[0].title}</span>
            </a>
          </li>
          <li>
            <a href="{*{smartblog::GetSmartBlogLink('module-smartblog-list')}*}">
            <span itemprop="name">{l s='All Post' mod='smartblog'}</span>
            </a>
          </li>
          {if $title_category != ''}
          {assign var="link_category" value=null}
          {$link_category.id_category = $id_category}
          {$link_category.slug = $cat_link_rewrite}
          <li>
            <a href="{*{smartblog::GetSmartBlogLink('module-smartblog-category',$link_category)}*}">
            <span itemprop="name">{$title_category}</span>
            </a>
          </li>
        {/if}
      </ol>
    </nav>
  {/if}
{/block}
{block name='page_content'}
    {if $postcategory == ''}
        {include file="module:smartblog/views/templates/front/search-not-found.tpl" postcategory=$postcategory}
    {else}
        <div id="smartblogcat" class="block">
            {foreach from=$postcategory item=post}
                {include file="module:smartblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
            {/foreach}
        </div>
    {/if}
{/block}
