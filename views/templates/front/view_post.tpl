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
            <span>{l s='All Post' d='Modules.Asblog.Shop'}</span>
          </a>
        </li>
      </ol>
    </nav>
  {/if}
{/block}

{block name="before_content"}
    <div id="before_content" class="col-xs-12 col-sm-12 col-md-12 pr-0 pl-0">
        <div id="product_navigation">
            <ul class="posts-navigation">
                {foreach from=$posts_previous item="post"}
                    {if isset($post.id_post)}
                        <li  class="pull-left">
                            <a title="{l s='Prevoius Post' d='Modules.Asblog.Shop'}"
                               href="{$bloglink->getBlogPostLink($post.id_post, $post.link_rewrite)|escape:'htmlall':'UTF-8'}"
                               class="btn">
                                <span class="navigation-icon material-icons">arrow_back_ios</span>
                                <span class="navigation-text">
                                    {l s='Previous Post' d='Modules.Asblog.Shop'}
                                </span>

                            </a>
                        </li>
                    {/if}
                {/foreach}
                {foreach from=$posts_next item="post"}
                    {if isset($post.id_post)}
                        <li class="pull-right">
                            <a title="{l s='Next Post' d='Modules.Asblog.Shop'}"
                               href="{$bloglink->getBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}"
                               class="btn">
                                <span class="navigation-icon material-icons">arrow_forward_ios</span>
                                <span class="navigation-text">
                                    {l s='Next Post' d='Modules.Asblog.Shop'}
                                </span>
                            </a>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>

        <div class="blog-post-title">
            <h1>
                {$title|escape:'htmlall':'UTF-8'}
            </h1>
        </div>
        <div class="blog-post-excerpt">
            <p>
                The man who helped give the world candy-colored computers eventually walked out the door. What does that mean for the company’s next big thing?
            </p>
        </div>
        <div class="blog-post-views-and-category">
            <div class="">
                <i class="material-icons md-48 md-dark md-inactive">speaker_group</i>
                <span itemprop="">
                            {l s='Post Category:' d='Modules.Asblog.Shop'}
                        </span>
                <a title=""
                   href="{$bloglink->getBlogCategoryLink($post.id_category, $cat_link_rewrite)}"
                   class="">
                    <span>{$title_category}</span>
                </a>
            </div>
        </div>
        <div class="blog-post-featured-image">
            {assign var="image_url" value="`$urls.img_ps_url`blog/post/`$post_img`.jpeg"}
            {if $post_img === 'no'}
                {$image_url = "`$urls.img_ps_url`blog/no.jpg"}
            {/if}

            <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                 src="{$image_url}"
                 class="imageFeatured"
            >
        </div>


    </div>


{/block}


{block name='left_column'}
  <div id="left-column" class="col-xs-12 col-sm-12 col-md-3 pr-0">
    {hook h='displayBlogCategories'}
  </div>
{/block}

{block name='content_wrapper'}
    <div id="content-wrapper" class="col-xs-12 col-sm-12 col-md-5 pr-0 pl-0">
        <div id="content" class="block">

            <div class="blog-post-author">
                <div id="post-author">
                    <i class="material-icons md-48 md-dark md-inactive">person</i>
                    <span itemprop="author">{l s='By: ' d='Modules.Asblog.Shop'}{$firstname} {$lastname}</span>
                </div>
                <div id="post-author-description">
                    <p>The man who helped give the world candy-colored computers eventually walked out the door. What does that mean for the company’s next big thing?</p>
                </div>

            </div>

            <div class="blog-post-date">
                <i class="material-icons md-48 md-dark md-inactive">event_note</i>
                <span itemprop="dateCreated">
                        {$created|date_format:"%b %d, %Y"}
                </span>
            </div>

            <div itemtype="http://schema.org/BlogPosting" itemscope="" id="" class="blog-post">

                <div itemprop="articleBody">
                    <div class="articleContent">
                        {* {if isset($ispost) && !empty($ispost)}
                        <a itemprop="url" href="{'$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)'|escape:'htmlall':'UTF-8'}" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">
                          {/if}

                         {if $smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default') != 'false'}
                            <img itemprop="image" alt="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                                 src="{$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}"
                                 class="imageFeatured">
                          {/if}

                          {if isset($ispost) && !empty($ispost)}
                        </a>
                        {/if}*}


                        <div class="blog-content">
                            {$post.content nofilter}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}


{block name='right_column'}
    <div id="right-column" class="col-xs-12 col-sm-12 col-md-3 pr-0 pl-0">
        {hook h='displayBlogRecentPostsLeft'}
        {hook h='displayBlogPopularPosts'}
        {hook h='displayBlogSearch'}
    </div>
{/block}
