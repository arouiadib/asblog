
{extends file='page.tpl'}
{block name='breadcrumb'}
  {if isset($breadcrumb)}
    <nav class="breadcrumb smart-blog-breadcrumb">
      <ol>
        <li>
          <a href="{$breadcrumb.links[0].url}">
            <span>{$breadcrumb.links[0].title}</span>
          </a>
        </li>
        <li>
          <a href="">
            <span>{l s='All Post' mod='smartblog'}</span>
          </a>
        </li>
      </ol>
    </nav>
  {/if}
{/block}


{block name='page_content'}
  {capture name=path}<a href="">
    {l s='All Blog News' mod='smartblog'}</a>
    <span class="navigation-pipe"></span>
    {$meta_title|escape:'htmlall':'UTF-8'}
  {/capture}
  <div id="content" class="block">
    <div itemtype="http://schema.org/BlogPosting" itemscope="" id="sdsblogArticle" class="blog-post smart-blog-single-post">
      <div class="smart-blog-posts-header-area">
        <div class="title_block smart-blog-single-post-title">{$meta_title|escape:'htmlall':'UTF-8'}</div>
        <div class="sdsarticleHeader">
						<span class="smart-blog-posts-info">
							 {l s='Posted by ' mod='smartblog'} &nbsp;<i class="icon icon-user"></i>
							<span itemprop="author">{*{if $smartshowauthorstyle != 0}{$firstname} {$lastname}{else}{$lastname} {$firstname}{/if}*}</span>
              &nbsp;
							<i class="icon icon-calendar"></i>&nbsp;
              <span itemprop="dateCreated">{$created|escape:'htmlall':'UTF-8'}</span>
							<span itemprop="articleSection">
								{*{$assocCats = BlogCategory::getPostCategoriesFull($post.id_post)}
                {$catCounts = 0}
                {if !empty($assocCats)}
                  &nbsp;&nbsp;<i class="icon icon-tags"></i>&nbsp;
									{foreach $assocCats as $catid=>$assoCat}
                  {if $catCounts > 0}, {/if}
                  {$catlink=[]}
                  {$catlink.id_category = $assoCat.id_category}
                  {$catlink.slug = $assoCat.link_rewrite}
                  <a href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">
											{$assoCat.name|escape:'htmlall':'UTF-8'}
										</a>
                  {$catCounts = $catCounts + 1}
                {/foreach}
                {/if}*}
							</span>
						</span>
          <a title="" style="display:none" itemprop="url" href="#"></a>
        </div>
      </div>
      <div itemprop="articleBody">
        <div class="articleContent">
          {if isset($ispost) && !empty($ispost)}
          <a itemprop="url" href="{'$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)'|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">
            {/if}

{*            {if $smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default') != 'false'}
              <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                   src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}"
                   class="imageFeatured">
            {/if}*}

            {if isset($ispost) && !empty($ispost)}
          </a>
          {/if}
          <div class="sdsarticle-des smart-blog-sing-blog-content" style="text-align: left;">
            {$post.content nofilter}
          </div>
        </div>

        <div class="sdsarticle-des"></div>
      </div>
      <div class="sdsarticleBottom"></div>
    </div>
  </div>


  <div id="product_comments_block_tab">
    <ul class="footer_links smart-blog-posts-navigation">
      {foreach from=$posts_previous item="post"}
        {if isset($post.id_post)}
          <li>
            <a title="{l s='Prevoius Post' d='Modules.Asblog.Shop'}"
               href="{$bloglink->getBlogPostLink($post.id_post, $post.link_rewrite)|escape:'htmlall':'UTF-8'}"
               class="btn btn-default button button-small">
              <span><i class="icon-chevron-left"></i> {l s='Prev Post' d='Modules.Asblog.Shop'}</span></a>
          </li>
        {/if}
      {/foreach}
      {foreach from=$posts_next item="post"}
        {if isset($post.id_post)}
          <li class="pull-right">
            <a title="{l s='Next Post' d='Modules.Asblog.Shop'}"
               href="{$bloglink->getBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}"
               class="btn btn-default button button-small"><span>{l s='Next Post' d='Modules.Asblog.Shop'}
                <i class="icon-chevron-right"></i>
              </span></a>
          </li>
        {/if}
      {/foreach}
    </ul>
  </div>
{/block}
