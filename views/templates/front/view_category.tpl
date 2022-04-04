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
  {capture name=path}
    <a href="{*{smartblog::GetSmartBlogLink('module-smartblog-list')|escape:'htmlall':'UTF-8'}*}">{l s='All Blog News' mod='smartblog'}</a>
    {if $title_category != ''}<span class="navigation-pipe"></span>{$title_category|escape:'htmlall':'UTF-8'}{/if}
  {/capture}
  {if $postcategory == ''}
    {if $title_category != ''}
      <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Category' mod='smartblog'}</li></ol></div>
    {else}
      <div class="alert alert-danger"><p>There is 1 error</p><ol><li>{l s='No Post in Blog' mod='smartblog'}</li></ol></div>
    {/if}
  {else}

      {if $title_category != ''}
        {foreach from=$categoryinfo item=category}
          <div id="sdsblogCategory" class="smartg-blog-category-banner-area">
            <div class="smartg-blog-category-banner-images-and-content">
              <img alt="{*{$category.meta_title|escape:'htmlall':'UTF-8'}*}" src="{$cat_image}" class="imageFeatured">
              <div class="smartg-blog-category-banner-content">
                <div class="smart-blog-category-banner-content-title">
                  <span class="smart_blog-cat-text">Category:</span>
                  {* {$category.meta_title|nl2br nofilter} *}
                  <span class="smart_blog-cat-text">{*{$category.name|nl2br nofilter}*}</span>
                </div>
                <div class="smart-blog-cat-description">
                  {*{$category.description|nl2br nofilter}*}
                </div>
              </div>
            </div>
          </div>
        {/foreach}
      {/if}

    <div id="smartblogcat" class="block">
      {foreach from=$postcategory item=post}
        {include file="module:asblog/views/templates/front/category_loop.tpl" postcategory=$postcategory}
      {/foreach}
    </div>
    {if !empty($pagenums)}
      <div class="row bottom-pagination-content smart-blog-bottom-pagination">
        <div class="post-page col-md-12">
          <div id="pagination_bottom" class="col-md-6">
            <ul class="pagination">
              {for $k=0 to $pagenums}
                {if ($k+1) == $c}
                  <li><span class="page-link page-active"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></span></li>
                {else}
                  {if $title_category != ''}
                    <li><a class="page-link" href="{*{$smartbloglink->getSmartBlogCategoryPagination($id_category,$cat_link_rewrite,$k+1)|escape:'htmlall':'UTF-8'}*}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li>
                  {else}
                    <li><a class="page-link" href="{*{$smartbloglink->getSmartBlogListPagination($k+1)|escape:'htmlall':'UTF-8'}*}"><span>{$k+1|escape:'htmlall':'UTF-8'}</span></a></li>
                  {/if}
                {/if}
              {/for}
            </ul>
          </div>
        </div>
        <div class="col-md-6">
          <div class="results">
            {l s='Showing' mod='smartblog'}
            {if $limit_start!=0}
              {$limit_start|escape:'htmlall':'UTF-8'}
            {else}1{/if} {l s='to' mod='smartblog'}
            {if $limit_start+$limit >= $total}
              {$total|escape:'htmlall':'UTF-8'}
            {else}
              {$limit_start+$limit|escape:'htmlall':'UTF-8'}
            {/if}
            {l s='of' mod='smartblog'}
            {$total|escape:'htmlall':'UTF-8'}
            ({$c|escape:'htmlall':'UTF-8'}
            {l s='Pages' mod='smartblog'})
          </div>
        </div>
      </div>
    {/if}
  {/if}
{/block}
